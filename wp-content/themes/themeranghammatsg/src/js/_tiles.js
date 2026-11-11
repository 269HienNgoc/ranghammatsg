/**
 * Nút điều hướng cho ô ảnh cuộn ngang: bố cục và thao tác vuốt do CSS/native scroll xử lý.
 * Nạp động từ carousel.js khi có nút điều hướng; không tạo entry Vite mới.
 */
document.querySelectorAll( '[data-rhm-tiles]' ).forEach( ( heading ) => {
	const section = heading.closest( 'section' );
	const track = section.querySelector( '[data-rhm-tiles-track]' );
	const controls = heading.querySelector( '[data-rhm-tiles-controls]' );
	const previous = heading.querySelector( '[data-rhm-tiles-prev]' );
	const next = heading.querySelector( '[data-rhm-tiles-next]' );
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
		const item = track.firstElementChild;
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
