/**
 * Trang chủ — slider hero banner (Swiper). Chỉ nạp ở trang chủ khi có từ 2 slide
 * (template-parts/sections/hero.php). Chỉ import các module Swiper cần dùng để file nhỏ nhất.
 *
 * Tải ảnh theo nhu cầu: slide 2+ in ra data-srcset (inc/hero.php), chỉ đổi thành srcset khi:
 * - trang tải xong + trình duyệt rảnh → slide kế tiếp (kịp cho lần tự chuyển đầu tiên);
 * - mỗi lần chuyển slide → slide đang hiện + slide kế tiếp;
 * - người dùng chạm / rê chuột / Tab vào banner → thêm slide trước (phòng khi bấm lùi).
 */
import Swiper from 'swiper';
import { A11y, Autoplay, Navigation, Pagination } from 'swiper/modules';

const hero = document.querySelector( '.hero-swiper' );

/** Đổi data-srcset → srcset cho <source> và <img> của slide (source trước để <picture> chọn đúng ảnh). */
const loadSlide = ( slide ) => {
	slide?.querySelectorAll( '[data-srcset]' ).forEach( ( element ) => {
		element.srcset = element.dataset.srcset;
		element.removeAttribute( 'data-srcset' );
	} );
};

/** Slide đang hiện + slide kế tiếp (+ slide trước nếu withPrev). Loop: đầu/cuối nối vòng. */
const loadAround = ( swiper, withPrev = false ) => {
	const { slides, activeIndex } = swiper;
	loadSlide( slides[ activeIndex ] );
	loadSlide( slides[ activeIndex + 1 ] ?? slides[ 0 ] );
	if ( withPrev ) {
		loadSlide( slides[ activeIndex - 1 ] ?? slides[ slides.length - 1 ] );
	}
};

if ( hero ) {
	const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	const swiper = new Swiper( hero, {
		modules: [ A11y, Autoplay, Navigation, Pagination ],
		loop: true,
		speed: reducedMotion ? 0 : 600,
		autoplay: reducedMotion ? false : { delay: 5000, disableOnInteraction: false, pauseOnMouseEnter: true },
		pagination: { el: hero.querySelector( '.hero-pagination' ), clickable: true },
		navigation: { prevEl: hero.querySelector( '.hero-prev' ), nextEl: hero.querySelector( '.hero-next' ), addIcons: false },
		a11y: {
			prevSlideMessage: 'Banner trước',
			nextSlideMessage: 'Banner sau',
			paginationBulletMessage: 'Chuyển tới banner {{index}}',
			slideLabelMessage: 'Banner {{index}} / {{slidesLength}}',
		},
		on: {
			slideChange: ( instance ) => loadAround( instance ),
			touchStart: ( instance ) => loadAround( instance, true ),
		},
	} );

	const loadWithPrev = () => loadAround( swiper, true );
	hero.addEventListener( 'pointerenter', loadWithPrev, { once: true } );
	hero.addEventListener( 'focusin', loadWithPrev, { once: true } );

	// Nạp sẵn slide kế tiếp sau khi trang đã tải xong, không tranh băng thông với ảnh LCP.
	const whenIdle = window.requestIdleCallback ?? ( ( callback ) => setTimeout( callback, 200 ) );
	const preloadNext = () => whenIdle( () => loadAround( swiper ) );
	if ( document.readyState === 'complete' ) {
		preloadNext();
	} else {
		window.addEventListener( 'load', preloadNext, { once: true } );
	}
}
