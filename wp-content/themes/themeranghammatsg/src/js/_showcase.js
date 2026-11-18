/**
 * Landing "Hệ thống phòng khám" (template-parts/landing/clinics.php): dải ảnh nhỏ ↔ slider ảnh lớn.
 * Landing "Ảnh lớn + slider tab" (template-parts/landing/tab-slider.php): hàng tab ↔ slide (cùng cơ chế, nút = tab).
 * - Bấm ảnh nhỏ → slider (native scroll-snap, mỗi slide rộng hết khung) cuộn tới slide tương ứng.
 * - Slider cuộn (vuốt, nút ‹ › của _tiles.js, phím) → ảnh nhỏ của slide đang xem có aria-current="true" và nằm trong khung dải.
 * - Track có [data-rhm-autoheight] (tab-slider): chiều cao track = slide đang xem (CSS transition height). Template để slide 2+ cao 0
 *   tới khi có [data-ready] → lúc khởi tạo track đã cao đúng slide 1, không nhảy. Track touch-pan-y → tự nhận cử chỉ vuốt ngang
 *   (pointerdown/up) để đổi chiều cao được Chrome coi là do tương tác (hadRecentInput), không tính CLS như cuộn native.
 * Nạp động từ carousel.js. Đọc layout trước, ghi sau, gom theo requestAnimationFrame → không forced reflow, INP thấp.
 */
const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
const behavior = () => ( reducedMotion.matches ? 'instant' : 'smooth' );

/** Độ lệch ngang tối thiểu (px) để coi là vuốt chuyển slide. */
const SWIPE_MIN = 40;

document.querySelectorAll( '[data-rhm-showcase-thumbs]' ).forEach( ( strip ) => {
	const thumbs = [ ...strip.querySelectorAll( 'button[aria-controls]' ) ];
	const track = thumbs.length ? document.getElementById( thumbs[ 0 ].getAttribute( 'aria-controls' ) ) : null;
	if ( ! track ) {
		return;
	}

	const slides = [ ...track.children ];
	const autoHeight = track.hasAttribute( 'data-rhm-autoheight' );
	let current = 0;

	// Chiều cao track = slide đang xem (chỉ track autoheight).
	let fitFrame = 0;
	const fit = () => {
		if ( ! autoHeight || fitFrame ) {
			return;
		}
		fitFrame = requestAnimationFrame( () => {
			fitFrame = 0;
			const height = slides[ current ]?.offsetHeight;
			if ( height ) {
				track.style.height = `${ height }px`;
			}
		} );
	};

	const mark = ( index ) => {
		if ( index === current || ! thumbs[ index ] ) {
			return;
		}
		thumbs[ current ].removeAttribute( 'aria-current' );
		thumbs[ index ].setAttribute( 'aria-current', 'true' );
		current = index;
		fit();

		// Đưa ảnh nhỏ vào giữa dải (chỉ cuộn ngang dải, không kéo trang).
		const box = strip.getBoundingClientRect();
		const item = thumbs[ index ].getBoundingClientRect();
		strip.scrollBy( { left: item.left - box.left - ( box.width - item.width ) / 2, behavior: behavior() } );
	};

	// Đang cuộn tới slide đích do bấm/vuốt → bỏ qua các slide đi ngang qua (không đổi tab / chiều cao giữa chừng).
	let pending = -1;
	let pendingTimer = 0;
	const release = () => {
		pending = -1;
		clearTimeout( pendingTimer );
	};
	const go = ( index ) => {
		index = Math.max( 0, Math.min( slides.length - 1, index ) );
		pending = index;
		clearTimeout( pendingTimer );
		pendingTimer = setTimeout( release, 1000 ); // Dự phòng khi trình duyệt không có sự kiện scrollend.
		track.scrollTo( { left: index * track.clientWidth, behavior: behavior() } );
		mark( index );
	};

	strip.addEventListener( 'click', ( event ) => {
		const index = thumbs.indexOf( event.target.closest( 'button' ) );
		if ( index >= 0 ) {
			go( index );
		}
	} );

	let frame = 0;
	track.addEventListener(
		'scroll',
		() => {
			if ( frame ) {
				return;
			}
			frame = requestAnimationFrame( () => {
				frame = 0;
				const index = Math.round( track.scrollLeft / track.clientWidth );
				if ( pending >= 0 ) {
					if ( index !== pending ) {
						return;
					}
					release();
				}
				mark( index );
			} );
		},
		{ passive: true }
	);
	track.addEventListener( 'scrollend', release, { passive: true } );

	if ( ! autoHeight ) {
		return;
	}

	// Khởi tạo: khóa chiều cao = slide 1 rồi mới trả slide 2+ về chiều cao thật → khung không đổi.
	track.style.height = `${ slides[ 0 ].offsetHeight }px`;
	track.setAttribute( 'data-ready', '' );

	// Đổi cỡ màn hình, font tải xong, ảnh tròn đổi cỡ → tính lại.
	const observer = new ResizeObserver( () => {
		const height = slides[ current ]?.offsetHeight;
		if ( height && `${ height }px` !== track.style.height ) {
			fit();
		}
	} );
	slides.forEach( ( slide ) => observer.observe( slide ) );

	// Vuốt ngang bằng tay / bút (chuột kéo không áp dụng). Vuốt dọc → trình duyệt cuộn trang và phát pointercancel.
	let start = null;
	track.addEventListener(
		'pointerdown',
		( event ) => {
			start = 'mouse' === event.pointerType ? null : { id: event.pointerId, x: event.clientX, y: event.clientY };
		},
		{ passive: true }
	);
	track.addEventListener(
		'pointerup',
		( event ) => {
			if ( ! start || start.id !== event.pointerId ) {
				return;
			}
			const dx = event.clientX - start.x;
			const dy = event.clientY - start.y;
			start = null;
			if ( Math.abs( dx ) >= SWIPE_MIN && Math.abs( dx ) > Math.abs( dy ) ) {
				go( current + ( dx < 0 ? 1 : -1 ) );
			}
		},
		{ passive: true }
	);
	track.addEventListener( 'pointercancel', () => ( start = null ), { passive: true } );
} );
