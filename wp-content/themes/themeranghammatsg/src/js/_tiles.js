/**
 * Nút điều hướng cho ô ảnh cuộn ngang: bố cục và thao tác vuốt do CSS/native scroll xử lý.
 * Nạp động từ carousel.js khi có nút điều hướng; không tạo entry Vite mới.
 * Dùng ở: landing "Ô lưới ảnh", "Hệ thống phòng khám"; trang chủ "Trước – sau" (template-parts/sections/before-after.php).
 * Track = phần tử mà nút trước trỏ tới qua aria-controls (1 section có thể có nhiều track, vd landing "Hệ thống phòng khám"),
 * không có thì lấy [data-rhm-tiles-track] đầu tiên của section. Track trong khối đang ẩn: ResizeObserver cập nhật khi hiện.
 */
document.querySelectorAll( '[data-rhm-tiles]' ).forEach( ( heading ) => {
	const controls = heading.querySelector( '[data-rhm-tiles-controls]' );
	const previous = heading.querySelector( '[data-rhm-tiles-prev]' );
	const next = heading.querySelector( '[data-rhm-tiles-next]' );
	const target = previous?.getAttribute( 'aria-controls' );
	const track = ( target && document.getElementById( target ) ) || heading.closest( 'section' )?.querySelector( '[data-rhm-tiles-track]' );
	if ( ! track || ! controls || ! previous || ! next ) {
		return;
	}

	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
	const update = () => {
		const maximum = track.scrollWidth - track.clientWidth;
		controls.classList.toggle( 'invisible', maximum <= 1 );
		previous.disabled = maximum <= 1 || track.scrollLeft <= 1;
		next.disabled = maximum <= 1 || track.scrollLeft >= maximum - 1;
	};

	const move = ( direction ) => {
		// Phần tử con đầu có thể là wrapper display: contents (rộng 0, vd trang 4 thẻ của section Trước – sau dưới 1024px)
		// → lấy con đầu của nó: desktop cuộn 1 trang, mobile cuộn 1 thẻ.
		let item = track.firstElementChild;
		while ( item && ! item.getBoundingClientRect().width ) {
			item = item.firstElementChild;
		}
		if ( ! item ) {
			return;
		}
		const gap = parseFloat( getComputedStyle( track ).columnGap ) || 0;
		track.scrollBy( {
			left: direction * ( item.getBoundingClientRect().width + gap ),
			behavior: reducedMotion.matches ? 'instant' : 'smooth',
		} );
	};

	let frame = 0;
	const scheduleUpdate = () => {
		if ( frame ) {
			return;
		}
		frame = requestAnimationFrame( () => {
			frame = 0;
			update();
		} );
	};

	previous.addEventListener( 'click', () => move( -1 ) );
	next.addEventListener( 'click', () => move( 1 ) );
	track.addEventListener( 'scroll', scheduleUpdate, { passive: true } );
	new ResizeObserver( scheduleUpdate ).observe( track );
	update();
} );
