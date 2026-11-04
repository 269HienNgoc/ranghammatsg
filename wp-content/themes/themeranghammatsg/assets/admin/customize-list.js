/**
 * Control "danh sách động" trong Customizer (type rhm-list) — inc/class-rhm-customize-list-control.php.
 * Giá trị setting: chuỗi JSON [{ field: value, … }]. Thêm mới / thêm nhiều ảnh / sắp xếp / xóa đều ghi lại setting
 * → Customizer tự xem trước và lưu khi bấm "Đăng".
 * Chỉ chạy trong Customizer (admin) nên không build qua Vite.
 */
( function ( api, media ) {
	'use strict';

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

	/** URL ảnh xem trước từ đối tượng attachment của thư viện Media. */
	const previewUrl = ( attachment ) => attachment.sizes?.medium?.url ?? attachment.sizes?.thumbnail?.url ?? attachment.url;

	api.controlConstructor[ 'rhm-list' ] = api.Control.extend( {
		ready() {
			const control = this;
			const { fields, itemLabel, l10n } = control.params;
			const images = { ...control.params.images };
			const root = control.container[ 0 ];
			const list = root.querySelector( '.rhm-list' );
			const empty = root.querySelector( '.rhm-list-empty' );
			const keys = Object.keys( fields );
			const imageKey = keys.find( ( key ) => fields[ key ].type === 'image' );
			const textKey = keys.find( ( key ) => fields[ key ].type === 'text' );

			let items = [];
			try {
				items = JSON.parse( control.setting.get() || '[]' );
			} catch {
				items = [];
			}
			if ( ! Array.isArray( items ) ) {
				items = [];
			}

			const blank = () => Object.fromEntries( keys.map( ( key ) => [ key, fields[ key ].type === 'image' ? 0 : '' ] ) );
			const save = () => control.setting.set( JSON.stringify( items ) );

			/** Mở thư viện Media; multiple = chọn nhiều ảnh cùng lúc. */
			const pickImages = ( multiple, onSelect ) => {
				const frame = media( {
					title: multiple ? l10n.addImages : l10n.chooseImage,
					library: { type: 'image' },
					button: { text: l10n.select },
					multiple: multiple ? 'add' : false,
				} );
				frame.on( 'select', () => {
					const selection = frame.state().get( 'selection' ).toJSON();
					selection.forEach( ( attachment ) => ( images[ attachment.id ] = previewUrl( attachment ) ) );
					onSelect( selection );
				} );
				frame.open();
			};

			const imageField = ( item, key, field, rerender ) => {
				const id = Number( item[ key ] ) || 0;
				return el( 'div', { class: 'rhm-list-field' }, [
					el( 'span', { class: 'rhm-list-label', text: field.label } ),
					el(
						'button',
						{
							type: 'button',
							class: 'rhm-list-image',
							'aria-label': `${ field.label }: ${ l10n.chooseImage }`,
							onclick: () =>
								pickImages( false, ( [ attachment ] ) => {
									item[ key ] = attachment.id;
									if ( textKey && ! item[ textKey ] ) {
										item[ textKey ] = attachment.alt || attachment.title || '';
									}
									rerender();
								} ),
						},
						[ id && images[ id ] ? el( 'img', { src: images[ id ], alt: '' } ) : el( 'span', { text: id ? `#${ id }` : l10n.noImage } ) ]
					),
					id &&
						el( 'button', {
							type: 'button',
							class: 'button-link button-link-delete',
							text: l10n.removeImage,
							onclick: () => {
								item[ key ] = 0;
								rerender();
							},
						} ),
				] );
			};

			const textField = ( item, key, field ) => {
				const multiline = field.type === 'textarea';
				const input = el( multiline ? 'textarea' : 'input', multiline ? { rows: 3 } : { type: field.type === 'url' ? 'url' : 'text' } );
				input.className = 'widefat';
				input.value = item[ key ] ?? '';
				input.addEventListener( 'input', () => {
					item[ key ] = input.value;
					save();
				} );
				return el( 'label', { class: 'rhm-list-field' }, [ el( 'span', { class: 'rhm-list-label', text: field.label } ), input ] );
			};

			let render;

			const renderItem = ( item, index ) => {
				const update = () => {
					save();
					render();
				};
				const action = ( icon, label, onclick, disabled = false ) => {
					const button = el( 'button', { type: 'button', class: `rhm-list-btn dashicons dashicons-${ icon }`, title: label, 'aria-label': `${ label } (${ itemLabel } ${ index + 1 })`, onclick } );
					button.disabled = disabled;
					return button;
				};
				const move = ( step ) => () => {
					items.splice( index + step, 0, items.splice( index, 1 )[ 0 ] );
					update();
				};

				return el( 'li', { class: 'rhm-list-item' }, [
					el( 'div', { class: 'rhm-list-head' }, [
						el( 'strong', { text: `${ itemLabel } ${ index + 1 }` } ),
						action( 'arrow-up-alt2', l10n.moveUp, move( -1 ), index === 0 ),
						action( 'arrow-down-alt2', l10n.moveDown, move( 1 ), index === items.length - 1 ),
						action( 'trash', l10n.remove, () => {
							if ( window.confirm( l10n.confirmRemove ) ) {
								items.splice( index, 1 );
								update();
							}
						} ),
					] ),
					...keys.map( ( key ) => ( fields[ key ].type === 'image' ? imageField( item, key, fields[ key ], update ) : textField( item, key, fields[ key ] ) ) ),
				] );
			};

			render = () => {
				list.replaceChildren( ...items.map( renderItem ) );
				empty.textContent = items.length ? '' : l10n.empty;
				empty.hidden = items.length > 0;
			};

			root.querySelector( '.rhm-list-add' ).addEventListener( 'click', () => {
				items.push( blank() );
				save();
				render();
				list.lastElementChild?.querySelector( '.rhm-list-image, input, textarea' )?.focus();
			} );

			root.querySelector( '.rhm-list-add-images' )?.addEventListener( 'click', () =>
				pickImages( true, ( selection ) => {
					selection.forEach( ( attachment ) => {
						const item = { ...blank(), [ imageKey ]: attachment.id };
						if ( textKey ) {
							item[ textKey ] = attachment.alt || attachment.title || '';
						}
						items.push( item );
					} );
					save();
					render();
				} )
			);

			render();
		},
	} );
} )( wp.customize, wp.media );
