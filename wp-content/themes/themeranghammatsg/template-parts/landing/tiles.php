<?php
/**
 * Landing – Ô lưới ảnh nhỏ (dạng ô): tiêu đề vạch dọc + lưới ảnh vuông object-contain + nhãn 2 dòng.
 * Cột: < 640px 3; ≥ 640px 6 (ô rộng hết hàng); ≥ 1024px 6 nếu ô ≥ 3/5 trang, còn lại 3.
 * Field: inc/landing/layouts/tiles.php. Thiếu tiêu đề hoặc không có ô hợp lệ → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_image = rhm_landing_image( $rhm_row, 'image' );
	$rhm_label = rhm_landing_lines( $rhm_row, 'label' );
	if ( $rhm_image && $rhm_label ) {
		$rhm_items[] = array( $rhm_image, array_slice( $rhm_label, 0, 2 ) );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_span    = (int) ( $args['span'] ?? 10 );
$rhm_wide    = $rhm_span >= 6;
$rhm_head_id = $args['id'] . '-title';
// Ảnh = 1/6 (hoặc 1/3) phần nội dung ô − khoảng cách lưới 8/12px − đệm ô 2 × 8px.
$rhm_sizes = rhm_landing_box_sizes( $rhm_span, $rhm_wide ? 1 / 6 : 1 / 3, 1 / 3, 26 );
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="h-full rounded-2xl border border-line bg-white/80 p-5 shadow-card lg:p-6" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'box' ); ?>
	<ul class="<?php echo esc_attr( $rhm_wide ? 'grid grid-cols-3 gap-2 sm:grid-cols-6 lg:gap-3' : 'grid grid-cols-3 gap-2 sm:grid-cols-6 lg:grid-cols-3 lg:gap-3' ); ?>">
		<?php foreach ( $rhm_items as [ $rhm_image, $rhm_label ] ) : ?>
			<li class="rounded-lg border border-line bg-white p-2 text-center">
				<span class="relative block aspect-square">
					<?php
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
					echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-contain', false );
					?>
				</span>
				<span class="mt-1.5 block text-xs leading-snug text-dark"><?php echo implode( '<br>', array_map( 'esc_html', $rhm_label ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- từng dòng đã esc_html. ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
</section>
