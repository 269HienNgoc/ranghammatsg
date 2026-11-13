/**
 * Landing "Hệ thống phòng khám" (template-parts/landing/clinics.php): dải ảnh nhỏ ↔ slider ảnh lớn.
 * - Bấm ảnh nhỏ → slider (native scroll-snap, mỗi slide rộng hết khung) cuộn tới slide tương ứng.
 * - Slider cuộn (vuốt, nút ‹ › của _tiles.js, phím) → ảnh nhỏ của slide đang xem có aria-current="true" và nằm trong khung dải.
 * Nạp động từ carousel.js. Đọc layout trước, ghi sau, gom theo requestAnimationFrame → không forced reflow, INP thấp.
 */
const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );
const behavior = () => ( reducedMotion.matches ? 'instant' : 'smooth' );

document.querySelectorAll( '[data-rhm-showcase-thumbs]' ).forEach( ( strip ) => {
	const thumbs = [ ...strip.querySelectorAll( 'button[aria-controls]' ) ];
	const track = thumbs.length ? document.getElementById( thumbs[ 0 ].getAttribute( 'aria-controls' ) ) : null;
	if ( ! track ) {
		return;
	}

	let current = 0;
	const mark = ( index ) => {
		if ( index === current || ! thumbs[ index ] ) {
			return;
		}
		thumbs[ current ].removeAttribute( 'aria-current' );
		thumbs[ index ].setAttribute( 'aria-current', 'true' );
		current = index;

		// Đưa ảnh nhỏ vào giữa dải (chỉ cuộn ngang dải, không kéo trang).
		const box = strip.getBoundingClientRect();
		const item = thumbs[ index ].getBoundingClientRect();
		strip.scrollBy( { left: item.left - box.left - ( box.width - item.width ) / 2, behavior: behavior() } );
	};

	strip.addEventListener( 'click', ( event ) => {
		const index = thumbs.indexOf( event.target.closest( 'button' ) );
		if ( index < 0 ) {
			return;
		}
		track.scrollTo( { left: index * track.clientWidth, behavior: behavior() } );
		mark( index );
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
				mark( Math.round( track.scrollLeft / track.clientWidth ) );
			} );
		},
		{ passive: true }
	);
} );
