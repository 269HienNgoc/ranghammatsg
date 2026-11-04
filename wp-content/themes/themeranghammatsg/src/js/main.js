/**
 * Răng Hàm Mặt SG — main.js (vanilla, không phụ thuộc jQuery).
 */
( function () {
	'use strict';

	// Header sticky: gắn .is-stuck khi cuộn (đổ bóng + thu gọn trên desktop),
	// và cập nhật --header-h (dùng cho scroll-padding-top khi nhảy tới anchor).
	const header = document.getElementById( 'site-header' );

	if ( header ) {
		const root = document.documentElement;
		let stuck = false;

		const updateStuck = () => {
			// Ngưỡng bật/tắt khác nhau để header không bật/tắt liên tục khi cuộn quanh 1 điểm.
			const next = stuck ? window.scrollY > 20 : window.scrollY > 120;
			if ( next !== stuck ) {
				stuck = next;
				header.classList.toggle( 'is-stuck', stuck );
			}
		};

		const updateHeight = () => root.style.setProperty( '--header-h', `${ header.offsetHeight }px` );

		window.addEventListener( 'scroll', updateStuck, { passive: true } );
		new ResizeObserver( updateHeight ).observe( header );
		updateStuck();
		updateHeight();
	}

	const mobileQuery = window.matchMedia( '(max-width: 1023.98px)' );
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	// Ngăn kéo menu mobile: class html.nav-open điều khiển hoạt ảnh (src/css/pages/header.css).
	const toggle = document.querySelector( '.menu-toggle' );
	const nav = document.getElementById( 'primary-menu' );

	if ( toggle && nav ) {
		const root = document.documentElement;
		const closeButton = nav.querySelector( '.nav-close' );
		const backdrop = document.querySelector( '.nav-backdrop' );

		// Phần nằm ngoài ngăn kéo: khóa (inert) khi mở để không bấm/Tab/đọc được nội dung phía sau.
		const outside = () =>
			[ document.getElementById( 'main' ), document.querySelector( '.site-footer' ), document.querySelector( '.header-bar' ), document.querySelector( '.skip-link' ) ].filter( Boolean );

		// Thứ tự xuất hiện lần lượt của các mục menu (tối đa 10 bước).
		nav.querySelectorAll( '.main-nav > li' ).forEach( ( item, index ) => item.style.setProperty( '--i', String( Math.min( index, 10 ) ) ) );

		const isOpen = () => root.classList.contains( 'nav-open' );

		// Ngăn kéo đóng trên mobile = inert (không bấm / Tab / đọc được); desktop luôn dùng được.
		const syncNavInert = () => ( nav.inert = mobileQuery.matches && ! isOpen() );

		const setOpen = ( open, restoreFocus = true ) => {
			if ( open ) {
				// Bỏ inert và chốt style hiện tại TRƯỚC khi gắn .nav-open. Nếu gắn class khi ngăn kéo còn inert,
				// Chrome không tạo transition → ngăn kéo hiện ra tức thì thay vì trượt vào.
				nav.inert = false;
				void nav.offsetWidth;
			}

			root.classList.toggle( 'nav-open', open );
			toggle.setAttribute( 'aria-expanded', String( open ) );
			syncNavInert();
			outside().forEach( ( element ) => ( element.inert = open ) );

			if ( open ) {
				nav.setAttribute( 'role', 'dialog' );
				nav.setAttribute( 'aria-modal', 'true' );
				closeButton?.focus( { preventScroll: true } );
			} else {
				nav.removeAttribute( 'role' );
				nav.removeAttribute( 'aria-modal' );
				if ( restoreFocus ) {
					toggle.focus( { preventScroll: true } );
				}
			}
		};

		toggle.addEventListener( 'click', () => setOpen( true ) );
		closeButton?.addEventListener( 'click', () => setOpen( false ) );
		backdrop?.addEventListener( 'click', () => setOpen( false ) );

		document.addEventListener( 'keydown', ( event ) => {
			if ( event.key === 'Escape' && isOpen() ) {
				setOpen( false );
			}
		} );

		// Xoay ngang / phóng to lên desktop khi đang mở → đóng, gỡ khóa.
		mobileQuery.addEventListener( 'change', ( event ) => {
			if ( ! event.matches && isOpen() ) {
				setOpen( false, false );
			}
			syncNavInert();
		} );

		syncNavInert();
	}

	// Menu con: nút .submenu-toggle (thêm bởi inc/menus.php) mở/đóng li.is-open.
	// Mobile: mở/đóng có hoạt ảnh chiều cao; desktop: bật/tắt ngay (dropdown).
	const menu = document.querySelector( '.main-nav' );

	if ( menu ) {
		const animations = new WeakMap();

		const setSubmenu = ( item, open, animate = false ) => {
			const submenu = item.querySelector( ':scope > .sub-menu' );
			item.querySelector( ':scope > .submenu-toggle' )?.setAttribute( 'aria-expanded', String( open ) );
			animations.get( item )?.cancel();

			if ( ! open ) {
				// Đóng luôn các menu cháu bên trong (không cần hoạt ảnh).
				item.querySelectorAll( 'li.is-open' ).forEach( ( child ) => setSubmenu( child, false ) );
			}

			if ( ! submenu || ! animate || reducedMotion.matches ) {
				item.classList.toggle( 'is-open', open );
				return;
			}

			item.classList.add( 'is-open' );
			const height = `${ submenu.scrollHeight }px`;
			submenu.style.overflow = 'hidden';
			const animation = submenu.animate(
				open
					? [ { height: '0px', opacity: 0 }, { height, opacity: 1 } ]
					: [ { height, opacity: 1 }, { height: '0px', opacity: 0 } ],
				{ duration: open ? 260 : 200, easing: open ? 'cubic-bezier(.32,.72,0,1)' : 'ease-in' }
			);
			animations.set( item, animation );
			animation.onfinish = () => {
				submenu.style.overflow = '';
				if ( ! open ) {
					item.classList.remove( 'is-open' );
				}
			};
		};

		const closeAll = () => menu.querySelectorAll( 'li.is-open' ).forEach( ( item ) => setSubmenu( item, false ) );

		menu.addEventListener( 'click', ( event ) => {
			const button = event.target.closest( '.submenu-toggle' );
			if ( ! button ) {
				return;
			}
			const item = button.parentElement;
			const open = button.getAttribute( 'aria-expanded' ) !== 'true';
			const animate = mobileQuery.matches;
			// Chỉ mở 1 nhánh mỗi cấp.
			item.parentElement.querySelectorAll( ':scope > li.is-open' ).forEach( ( sibling ) => {
				if ( sibling !== item ) {
					setSubmenu( sibling, false, animate );
				}
			} );
			setSubmenu( item, open, animate );
		} );

		// Desktop: bấm ra ngoài menu → đóng dropdown đang mở bằng click/cảm ứng.
		document.addEventListener( 'click', ( event ) => {
			if ( ! mobileQuery.matches && ! menu.contains( event.target ) ) {
				closeAll();
			}
		} );

		// Escape: đóng menu con đang mở và trả focus về nút mũi tên của nó.
		menu.addEventListener( 'keydown', ( event ) => {
			if ( event.key !== 'Escape' ) {
				return;
			}
			const item = event.target.closest( 'li.is-open' ) ?? event.target.closest( '.sub-menu' )?.parentElement;
			if ( item ) {
				event.stopPropagation();
				setSubmenu( item, false );
				item.querySelector( ':scope > .submenu-toggle' )?.focus();
			}
		} );

		window.matchMedia( '(min-width: 1024px)' ).addEventListener( 'change', closeAll );
	}
} )();
