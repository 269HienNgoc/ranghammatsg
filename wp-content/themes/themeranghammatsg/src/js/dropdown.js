/**
 * Menu thả xuống dạng <details class="js-dropdown"> (vd nút "…" của thanh lọc chuyên mục,
 * template-parts/components/category-filter.php). <details> tự mở/đóng khi bấm, không cần JS;
 * file này chỉ thêm: bấm ra ngoài → đóng, Esc → đóng và trả focus về nút.
 */
const dropdowns = document.querySelectorAll( '.js-dropdown' );

document.addEventListener( 'click', ( event ) => {
	dropdowns.forEach( ( dropdown ) => {
		if ( dropdown.open && ! dropdown.contains( event.target ) ) {
			dropdown.open = false;
		}
	} );
} );

document.addEventListener( 'keydown', ( event ) => {
	if ( event.key !== 'Escape' ) {
		return;
	}
	dropdowns.forEach( ( dropdown ) => {
		if ( dropdown.open ) {
			dropdown.open = false;
			dropdown.querySelector( 'summary' )?.focus();
		}
	} );
} );
