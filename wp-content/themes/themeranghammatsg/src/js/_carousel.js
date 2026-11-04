/**
 * Khởi tạo Swiper cho 1 .js-carousel (tải động từ carousel.js). Chỉ import module cần: A11y, Navigation.
 *
 * slidesPerView 'auto': độ rộng mỗi slide lấy từ CSS (utility w-* trong template) — cùng bố cục với lúc chưa có JS.
 * Khoảng cách giữa ảnh là padding của slide (spaceBetween 0). Đủ chỗ cho mọi ảnh → Swiper tự khóa
 * và gắn .swiper-button-lock cho nút ‹ › (ẩn trong page-home.css).
 */
import Swiper from 'swiper';
import { A11y, Navigation } from 'swiper/modules';

export function initCarousel( carousel ) {
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	const label = carousel.closest( 'section' )?.querySelector( 'h2' )?.textContent.trim() ?? '';

	return new Swiper( carousel.querySelector( '.swiper' ), {
		modules: [ A11y, Navigation ],
		slidesPerView: 'auto',
		speed: reducedMotion ? 0 : 450,
		navigation: { prevEl: carousel.querySelector( '.carousel-prev' ), nextEl: carousel.querySelector( '.carousel-next' ), addIcons: false },
		a11y: {
			containerMessage: label,
			prevSlideMessage: carousel.querySelector( '.carousel-prev' )?.getAttribute( 'aria-label' ) ?? '',
			nextSlideMessage: carousel.querySelector( '.carousel-next' )?.getAttribute( 'aria-label' ) ?? '',
			slideLabelMessage: '{{index}} / {{slidesLength}}',
		},
	} );
}
