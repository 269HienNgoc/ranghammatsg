/**
 * Tùy biến → "Schema tùy chỉnh (head)": control danh sách schema (type rhm-schema-list,
 * inc/class-rhm-customize-schema-list-control.php). Giá trị setting rhm_head_schemas = chuỗi JSON [{ name, code, enabled }].
 *
 * - Mỗi mục = khung thu gọn/mở: tên, bật/tắt, ↑ ↓, xóa; thân gồm ô Tên + ô Nội dung (CodeMirror JSON qua wp.codeEditor).
 * - Ô nội dung kéo đổi chiều cao (CSS resize: vertical) → cm.refresh(); chiều cao lưu localStorage (chỉ trình duyệt này).
 * - Lint JSON theo khối, khớp rhm_head_schema_raw_blocks() (inc/head-schema.php): có thẻ <script>…</script> → kiểm phần
 *   JSON trong thẻ (dòng báo lỗi = dòng thật trong ô); không có → kiểm cả ô. Lỗi ở bất kỳ mục nào → notification error trên
 *   setting → Customizer không cho Đăng (server cũng chặn bằng validate_callback).
 * Chỉ chạy trong Customizer (admin) nên không build qua Vite.
 */
( function ( api ) {
	'use strict';

	const HEIGHT_KEY = 'rhmHeadSchemaHeight';
	const NOTICE = 'rhm_head_schemas_lint';

	/** Tạo phần tử: el( 'button', { class, text, onclick, …thuộc tính }, [ con ] ). */
	const el = ( tag, attrs = {}, children = [] ) => {
		const node = document.createElement( tag );
		Object.entries( attrs ).forEach( ( [ key, value ] ) => {
			if ( key === 'class' ) {
				node.className = value;
			} else if ( key === 'text' ) {
				node.textContent = value;
			} else if ( key.startsWith( 'on' ) ) {
				node.addEventListener( key.slice( 2 ), value );
			} else {
				node.setAttribute( key, value );
			}
		} );
		node.append( ...children.filter( Boolean ) );
		return node;
	};

	/** Các khối JSON + vị trí bắt đầu (index ký tự) trong ô. */
	const blocksOf = ( text ) => {
		const list = [];
		const re = /<script\b[^>]*>([\s\S]*?)<\/script\s*>/gi;
		let m;
		while ( ( m = re.exec( text ) ) ) {
			list.push( { code: m[ 1 ], start: m.index + m[ 0 ].indexOf( '>' ) + 1 } );
		}
		return list.length ? list : [ { code: text, start: 0 } ];
	};

	/** Lint 1 ô → mảng annotation CodeMirror ({ from, to, message, severity }). Pos = wp.CodeMirror.Pos (null khi không có editor). */
	const lintSchema = ( text, multiBlockMessage ) => {
		const found = [];
		const parser = window.jsonlint && ( window.jsonlint.parser || window.jsonlint );
		if ( ! text.trim() ) {
			return found;
		}
		const Pos = window.wp?.CodeMirror?.Pos ?? ( ( line, ch ) => ( { line, ch } ) );
		const blocks = blocksOf( text ).filter( ( block ) => block.code.trim() );

		if ( blocks.length > 1 ) {
			const line = text.slice( 0, blocks[ 1 ].start ).split( '\n' ).length - 1;
			found.push( { from: Pos( line, 0 ), to: Pos( line, 1 ), message: multiBlockMessage, severity: 'error' } );
		}

		blocks.forEach( ( block ) => {
			const before = text.slice( 0, block.start ).split( '\n' );
			const lineOffset = before.length - 1; // Dòng (0-based) của ký tự đầu khối.
			const colOffset = before[ before.length - 1 ].length; // Cột của ký tự đầu khối (chỉ áp cho dòng đầu khối).
			const toPos = ( line, col ) => Pos( lineOffset + line - 1, ( line === 1 ? colOffset : 0 ) + col );

			if ( ! parser ) {
				// Không có jsonlint (tắt tô màu cú pháp) → kiểm bằng JSON.parse, không có vị trí dòng.
				try {
					JSON.parse( block.code );
				} catch ( e ) {
					found.push( { from: toPos( 1, 0 ), to: toPos( 1, 1 ), message: e.message, severity: 'error' } );
				}
				return;
			}

			parser.parseError = ( message, hash ) => {
				const loc = hash.loc;
				found.push( {
					from: toPos( loc.first_line, loc.first_column ),
					to: toPos( loc.last_line, loc.last_column ),
					message: message.replace( /line (\d+)/, ( all, n ) => 'line ' + ( lineOffset + parseInt( n, 10 ) ) ),
					severity: 'error',
				} );
				throw new Error( 'rhm-schema-lint' ); // Dừng parse khối này ở lỗi đầu tiên.
			};
			try {
				parser.parse( block.code );
			} catch ( e ) {}
		} );
		return found;
	};

	/** Ô kéo đổi chiều cao: khôi phục chiều cao đã lưu, refresh CodeMirror + lưu lại mỗi lần đổi. */
	const makeResizable = ( box, onResize ) => {
		try {
			const saved = parseInt( window.localStorage.getItem( HEIGHT_KEY ), 10 );
			if ( saved >= 120 ) {
				box.style.height = saved + 'px';
			}
		} catch ( e ) {}

		let timer;
		new ResizeObserver( () => {
			onResize?.();
			clearTimeout( timer );
			timer = setTimeout( () => {
				if ( box.offsetHeight >= 120 ) {
					try {
						window.localStorage.setItem( HEIGHT_KEY, String( box.offsetHeight ) );
					} catch ( e ) {}
				}
			}, 300 );
		} ).observe( box );
	};

	api.controlConstructor[ 'rhm-schema-list' ] = api.Control.extend( {
		ready() {
			const control = this;
			const { l10n, editorSettings } = control.params;
			const root = control.container[ 0 ];
			const list = root.querySelector( '.rhm-schema-list' );
			const empty = root.querySelector( '.rhm-schema-empty' );

			let items = [];
			try {
				items = JSON.parse( control.setting.get() || '[]' );
			} catch {
				items = [];
			}
			items = ( Array.isArray( items ) ? items : [] )
				.filter( ( item ) => item && typeof item === 'object' )
				.map( ( item ) => ( { name: String( item.name ?? '' ), code: String( item.code ?? '' ), enabled: item.enabled !== false } ) );

			const open = new Set(); // Mục đang mở (giữ qua các lần render lại).
			const errors = new Map(); // Mục → số lỗi JSON.

			const save = () => control.setting.set( JSON.stringify( items.map( ( { name, code, enabled } ) => ( { name, code, enabled } ) ) ) );
			const title = ( item, index ) => item.name.trim() || l10n.untitled.replace( '%d', index + 1 );

			/** Gom lỗi mọi mục → 1 notification error (chặn Đăng) + đánh dấu khung lỗi. */
			const notify = () => {
				control.setting.notifications.remove( NOTICE );
				const bad = items.map( ( item, index ) => ( errors.get( item ) ? title( item, index ) : null ) ).filter( Boolean );
				if ( bad.length ) {
					control.setting.notifications.add( new api.Notification( NOTICE, { type: 'error', message: l10n.lintError.replace( '%s', bad.join( ', ' ) ) } ) );
				}
				list.querySelectorAll( '.rhm-schema-item' ).forEach( ( li, index ) => li.classList.toggle( 'has-error', !! errors.get( items[ index ] ) ) );
			};

			const relint = ( item ) => errors.set( item, lintSchema( item.code, l10n.multiBlock ).length );

			/** Ô nội dung: CodeMirror JSON (lint theo khối) hoặc textarea thường khi người dùng tắt tô màu cú pháp. */
			const initEditor = ( item, textarea ) => {
				if ( ! editorSettings || ! window.wp?.codeEditor ) {
					makeResizable( textarea );
					textarea.addEventListener( 'input', () => {
						item.code = textarea.value;
						relint( item );
						notify();
						save();
					} );
					return;
				}

				const cm = window.wp.codeEditor.initialize( textarea, editorSettings ).codemirror;
				cm.setOption( 'lint', {
					getAnnotations: ( text ) => lintSchema( text, l10n.multiBlock ),
					onUpdateLinting: ( annotations ) => {
						errors.set( item, annotations.filter( ( a ) => a.severity === 'error' ).length );
						notify();
					},
				} );
				cm.on( 'change', () => {
					item.code = cm.getValue();
					save();
				} );
				makeResizable( cm.getWrapperElement(), () => cm.refresh() );
				cm.refresh();
			};

			let render;

			const renderItem = ( item, index ) => {
				const isOpen = open.has( item );
				const update = () => {
					save();
					render();
				};
				const action = ( icon, label, onclick, disabled = false ) => {
					const button = el( 'button', { type: 'button', class: `rhm-schema-btn dashicons dashicons-${ icon }`, title: label, 'aria-label': `${ label }: ${ title( item, index ) }`, onclick } );
					button.disabled = disabled;
					return button;
				};
				const move = ( step ) => () => {
					items.splice( index + step, 0, items.splice( index, 1 )[ 0 ] );
					update();
				};

				const heading = el( 'span', { class: 'rhm-schema-title', text: title( item, index ) } );
				const bodyId = `rhm-schema-body-${ control.id }-${ index }`;

				const enabled = el( 'input', { type: 'checkbox' } );
				enabled.checked = item.enabled;
				enabled.addEventListener( 'change', () => {
					item.enabled = enabled.checked;
					li.classList.toggle( 'is-disabled', ! item.enabled );
					save();
				} );

				const name = el( 'input', { type: 'text', class: 'widefat', placeholder: l10n.namePlaceholder } );
				name.value = item.name;
				name.addEventListener( 'input', () => {
					item.name = name.value;
					heading.textContent = title( item, index );
					save();
				} );

				const textarea = el( 'textarea', { class: 'widefat code rhm-schema-code', rows: 12, spellcheck: 'false' } );
				textarea.value = item.code;

				const li = el( 'li', { class: 'rhm-schema-item' + ( isOpen ? ' is-open' : '' ) + ( item.enabled ? '' : ' is-disabled' ) }, [
					el( 'div', { class: 'rhm-schema-head' }, [
						el(
							'button',
							{
								type: 'button',
								class: 'rhm-schema-toggle',
								'aria-expanded': String( isOpen ),
								'aria-controls': bodyId,
								onclick: () => {
									if ( isOpen ) {
										open.delete( item );
									} else {
										open.add( item );
									}
									render();
								},
							},
							[ el( 'span', { class: 'dashicons dashicons-arrow-right-alt2', 'aria-hidden': 'true' } ), heading, el( 'span', { class: 'rhm-schema-badge rhm-schema-badge--error', text: l10n.hasError } ), el( 'span', { class: 'rhm-schema-badge rhm-schema-badge--off', text: l10n.disabled } ) ]
						),
						el( 'label', { class: 'rhm-schema-enabled' }, [ enabled, el( 'span', { text: l10n.enabled } ) ] ),
						action( 'arrow-up-alt2', l10n.moveUp, move( -1 ), index === 0 ),
						action( 'arrow-down-alt2', l10n.moveDown, move( 1 ), index === items.length - 1 ),
						action( 'trash', l10n.remove, () => {
							if ( window.confirm( l10n.confirmRemove ) ) {
								items.splice( index, 1 );
								open.delete( item );
								errors.delete( item );
								update();
							}
						} ),
					] ),
					el( 'div', { class: 'rhm-schema-body', id: bodyId }, [
						el( 'label', { class: 'rhm-schema-field' }, [ el( 'span', { class: 'rhm-schema-label', text: l10n.name } ), name ] ),
						el( 'div', { class: 'rhm-schema-field' }, [ el( 'span', { class: 'rhm-schema-label', text: l10n.code } ), textarea ] ),
					] ),
				] );
				if ( ! isOpen ) {
					li.querySelector( '.rhm-schema-body' ).hidden = true;
				}
				return li;
			};

			render = () => {
				const nodes = items.map( renderItem );
				list.replaceChildren( ...nodes );
				// CodeMirror cần phần tử đã nằm trong DOM → khởi tạo sau khi gắn; chỉ cho mục đang mở.
				nodes.forEach( ( li, index ) => {
					if ( open.has( items[ index ] ) ) {
						initEditor( items[ index ], li.querySelector( '.rhm-schema-code' ) );
					}
				} );
				empty.textContent = items.length ? '' : l10n.empty;
				empty.hidden = items.length > 0;
				notify();
			};

			root.querySelector( '.rhm-schema-add' ).addEventListener( 'click', () => {
				const item = { name: '', code: '', enabled: true };
				items.push( item );
				open.add( item );
				save();
				render();
				list.lastElementChild?.querySelector( '.rhm-schema-body input' )?.focus();
			} );

			items.forEach( relint ); // Lỗi của mục đang thu gọn cũng được báo ngay.
			render();
		},
	} );
} )( wp.customize );
