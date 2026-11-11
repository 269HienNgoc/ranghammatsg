/**
 * Slider dạng băng chuyền (.js-carousel), vd section Chứng nhận (template-parts/sections/certificates.php).
 *
 * File này rất nhỏ: chỉ chờ slider sắp vào màn hình (IntersectionObserver) rồi mới tải Swiper (_carousel.js)
 * → không tải / chạy JS slider lúc trang mới mở (tốt cho TBT, "Reduce unused JavaScript" của PageSpeed).
 * Bố cục đã dựng sẵn bằng CSS nên chưa có JS vẫn hiển thị đúng, chỉ chưa vuốt / bấm ‹ › được.
 */
const carousels = document.querySelectorAll( '.js-carousel' );

// Ô ảnh dùng native scroll: chỉ tải phần xử lý nút khi có khối cần điều hướng.
if ( document.querySelector( '[data-rhm-tiles-controls]' ) ) {
	import( './_tiles.js' );
}

if ( carousels.length ) {
	const observer = new IntersectionObserver(
		( entries ) => {
			entries.forEach( ( entry ) => {
				if ( ! entry.isIntersecting ) {
					return;
				}
				observer.unobserve( entry.target );
				import( './_carousel.js' ).then( ( { initCarousel } ) => initCarousel( entry.target ) );
			} );
		},
		{ rootMargin: '300px 0px' }
	);

	carousels.forEach( ( carousel ) => observer.observe( carousel ) );
}
