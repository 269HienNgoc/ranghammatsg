/**
 * Nút "Xem thêm" / "Thu gọn" của landing "Tiêu đề + nội dung dài + ảnh lớn bên dưới" (template-parts/landing/text-image.php).
 * Phần nội dung sau N ký tự đã được PHP đánh dấu [data-rhm-more] (rhm_landing_readmore_split()); CSS ẩn khi khối
 * [data-rhm-readmore] chưa có .is-open (page-about.css). Chỉ nạp khi trang có nội dung bị rút gọn.
 * 1 listener ủy quyền cho mọi nút trên trang; handler chỉ đổi class/thuộc tính (INP thấp).
 */
const reducedMotion = window.matchMedia( '(prefers-reduced-motion: reduce)' );

document.addEventListener( 'click', ( event ) => {
	const button = event.target instanceof Element ? event.target.closest( '[data-rhm-readmore-toggle]' ) : null;
	const box = button?.closest( '[data-rhm-readmore]' );
	if ( ! box ) {
		return;
	}

	const open = button.getAttribute( 'aria-expanded' ) !== 'true';

	// Thu gọn: đo TRƯỚC khi đổi class (tránh forced reflow). Đầu section khuất sau header sticky / trên màn hình → cuộn về.
	let offset = 0;
	if ( ! open ) {
		const section = box.closest( 'section' ) ?? box;
		const header = document.querySelector( '.site-header' );
		const headerBottom = header ? Math.max( 0, header.getBoundingClientRect().bottom ) : 0;
		offset = section.getBoundingClientRect().top - headerBottom;
	}

	box.classList.toggle( 'is-open', open );
	button.setAttribute( 'aria-expanded', String( open ) );
	const label = button.querySelector( '.rhm-readmore-label' );
	if ( label ) {
		label.textContent = ( open ? button.dataset.labelLess : button.dataset.labelMore ) ?? '';
	}

	if ( offset < 0 ) {
		window.scrollBy( { top: offset, behavior: reducedMotion.matches ? 'auto' : 'smooth' } );
	}
} );
