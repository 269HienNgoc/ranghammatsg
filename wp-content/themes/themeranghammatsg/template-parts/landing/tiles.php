<?php
/**
 * Landing – Ô lưới ảnh nhỏ (dạng ô): tiêu đề vạch dọc + lưới ảnh vuông object-contain + nhãn 2 dòng.
 * Mobile < 640px: một hàng vuốt ngang (scroll-snap), khoảng 2 ô + hé ô kế tiếp, không cần JS.
 * Từ 640px: một hàng 6 ô; ≥ 1024px: 6 ô nếu khối ≥ 3/5 trang, còn lại 3. Dư ô → cuộn ngang + nút điều hướng.
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

$rhm_span     = (int) ( $args['span'] ?? 10 );
$rhm_wide     = $rhm_span >= 6;
$rhm_head_id  = $args['id'] . '-title';
$rhm_count    = count( $rhm_items );
$rhm_track_id = $args['id'] . '-tiles';
$rhm_controls = $rhm_count > ( $rhm_wide ? 6 : 3 );
if ( $rhm_controls ) {
	rhm_landing_enqueue_module( 'carousel' );
}
// Mobile: phần trong section = 100vw − lề khung 32 − đệm 40 − viền 2; ảnh trừ thêm đệm + viền thẻ 18px.
// 1 ô lấp đầy hàng; 2 ô chia đôi, cách 8px; từ 3 ô: mỗi thẻ rộng 45% để hé ô kế tiếp.
$rhm_mobile_sizes = match ( $rhm_count ) {
	1       => 'calc(100vw - 92px)',
	2       => 'calc(50vw - 59px)',
	default => 'calc(45vw - 51px)',
};
// 640–1023px: 6 cột, 5 khoảng cách 8px → ảnh = (100vw − 74 − 40) / 6 − 18px.
// Desktop: dùng độ rộng ô, trừ khoảng cách lưới 12px và đệm + viền thẻ 18px (6 cột: 28px; 3 cột: 26px).
$rhm_sizes = '(max-width: 639px) ' . $rhm_mobile_sizes
	. ', (max-width: 1023px) calc(16.67vw - 37px), '
	. rhm_landing_box_sizes( $rhm_span, $rhm_wide ? 1 / 6 : 1 / 3, null, $rhm_wide ? 28 : 26 );
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="h-full rounded-2xl border border-line bg-white/80 p-5 shadow-card lg:p-6" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="flex items-start gap-3" data-rhm-tiles>
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'box' ); ?>
		<?php if ( $rhm_controls ) : ?>
			<div class="<?php echo esc_attr( 'invisible ml-auto shrink-0 gap-2 ' . ( $rhm_count > 6 ? 'hidden sm:flex' : 'hidden lg:flex' ) ); ?>" data-rhm-tiles-controls>
				<button type="button" class="flex size-8 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-primary" aria-label="<?php esc_attr_e( 'Xem các ô trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track_id ); ?>" data-rhm-tiles-prev disabled>
					<?php echo rhm_get_icon( 'chevron', 'size-4 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG của theme. ?>
				</button>
				<button type="button" class="flex size-8 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-primary" aria-label="<?php esc_attr_e( 'Xem các ô tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track_id ); ?>" data-rhm-tiles-next disabled>
					<?php echo rhm_get_icon( 'chevron', 'size-4 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG của theme. ?>
				</button>
			</div>
		<?php endif; ?>
	</div>
	<ul id="<?php echo esc_attr( $rhm_track_id ); ?>" class="<?php echo esc_attr( 'flex snap-x snap-mandatory gap-2 overflow-x-auto overscroll-x-contain pb-2 sm:grid sm:auto-cols-[calc((100%-2.5rem)/6)] sm:grid-flow-col lg:gap-3 ' . ( $rhm_wide ? 'lg:auto-cols-[calc((100%-3.75rem)/6)]' : 'lg:auto-cols-[calc((100%-1.5rem)/3)]' ) ); ?>" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>" data-rhm-tiles-track<?php echo $rhm_count > 2 ? ' tabindex="0"' : ''; ?>>
		<?php foreach ( $rhm_items as [ $rhm_image, $rhm_label ] ) : ?>
			<li class="min-w-0 flex-[1_0_45%] snap-start rounded-lg border border-line bg-white p-2 text-center wrap-break-word">
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
