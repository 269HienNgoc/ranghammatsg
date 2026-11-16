<?php
/**
 * Landing – Ô ảnh slider + chữ (dạng ô, xếp cạnh ô khác theo Độ rộng): thẻ nền gradient mint → trắng, tiêu đề + nội dung,
 * slider nhiều ảnh ở trái / phải / trên / dưới (image_position, mặc định phải). < 640px luôn ảnh trên, chữ dưới.
 * Field: inc/landing/layouts/media-slider.php. Thiếu tiêu đề, hoặc thiếu cả ảnh lẫn nội dung → không hiện.
 *
 * Hiệu năng / truy cập:
 * - DOM luôn ảnh trước, chữ sau (= thứ tự mobile); vị trí phải / dưới đảo bằng sm:order-last (chỉ CSS).
 * - Slider native scroll-snap (vuốt được khi chưa có JS), khung ảnh 4:3 cố định + object-cover → mọi slide cao bằng nhau, CLS = 0.
 * - ≥ 2 ảnh: nút ‹ › do _tiles.js xử lý (carousel.js nạp động khi có [data-rhm-tiles-controls]), không Swiper.
 * - Chỉ ảnh đầu, khi ô đứng đầu trang, mới là ảnh LCP; còn lại lazy.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data   = $args['data'] ?? array();
$rhm_title  = rhm_landing_text( $rhm_data, 'title' );
$rhm_text   = rhm_landing_rich( $rhm_data, 'text' );
$rhm_images = rhm_landing_gallery( $rhm_data, 'images' );
if ( '' === $rhm_title || ( ! $rhm_images && '' === $rhm_text ) ) {
	return;
}

$rhm_position = (string) ( $rhm_data['image_position'] ?? 'right' );
$rhm_position = in_array( $rhm_position, array( 'left', 'right', 'top', 'bottom' ), true ) ? $rhm_position : 'right';
$rhm_side     = $rhm_images && in_array( $rhm_position, array( 'left', 'right' ), true );
$rhm_id       = $args['id'];
$rhm_head_id  = $rhm_id . '-title';
$rhm_track    = $rhm_id . '-track';
$rhm_count    = count( $rhm_images );
$rhm_multi    = $rhm_count > 1;
$rhm_span     = (int) ( $args['span'] ?? 10 );
if ( $rhm_multi ) {
	rhm_landing_enqueue_module( 'carousel' );
}

// < 640px xếp dọc: ảnh rộng hết ô = màn hình − lề khung 32 − đệm ô 40 − viền 2 = 100vw − 74px.
// ≥ 640px cạnh nhau: ảnh = 1/2 phần nội dung ô trừ nửa khoảng cách cột (16px → 8, lg 24px → 12; lấy 8).
// Trên / dưới: ảnh rộng hết phần nội dung ô.
$rhm_sizes = '(max-width: 639px) calc(100vw - 74px), ' . ( $rhm_side ? rhm_landing_box_sizes( $rhm_span, 0.5, 0.5, 8 ) : rhm_landing_box_sizes( $rhm_span, 1 ) );

$rhm_box = match ( true ) {
	'left' === $rhm_position && $rhm_side => 'h-full overflow-hidden rounded-2xl border border-line bg-linear-to-l from-mint to-white p-5 shadow-card lg:p-6',
	default                               => 'h-full overflow-hidden rounded-2xl border border-line bg-linear-to-r from-mint to-white p-5 shadow-card lg:p-6',
};
// Cạnh nhau: khung cao hết ô (ô cùng hàng cao hơn → ảnh + chữ vẫn canh giữa theo chiều dọc).
$rhm_layout = $rhm_side ? 'flex h-full flex-col gap-4 sm:grid sm:grid-cols-2 sm:items-center lg:gap-6' : 'flex flex-col gap-4 lg:gap-5';
$rhm_media  = in_array( $rhm_position, array( 'right', 'bottom' ), true ) ? 'relative min-w-0 sm:order-last' : 'relative min-w-0';
$rhm_arrow  = 'pointer-events-auto flex size-9 cursor-pointer items-center justify-center rounded-full border border-line bg-white/90 text-primary shadow-card transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-0';
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="<?php echo esc_attr( $rhm_box ); ?>" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_layout ); ?>">
		<?php if ( $rhm_images ) : ?>
			<div class="<?php echo esc_attr( $rhm_media ); ?>" data-rhm-tiles>
				<div id="<?php echo esc_attr( $rhm_track ); ?>" class="flex snap-x snap-mandatory overflow-x-auto overscroll-x-contain rounded-lg [scrollbar-width:none]" aria-label="<?php echo esc_attr( $rhm_title ); ?>" data-rhm-tiles-track<?php echo $rhm_multi ? ' tabindex="0"' : ''; ?>>
					<?php foreach ( $rhm_images as $rhm_index => $rhm_image ) : ?>
						<div class="relative block aspect-[4/3] w-full shrink-0 snap-start overflow-hidden bg-soft"<?php echo $rhm_multi ? ' role="group" aria-roledescription="' . esc_attr__( 'ảnh', 'themeranghammatsg' ) . '" aria-label="' . esc_attr( ( $rhm_index + 1 ) . ' / ' . $rhm_count ) . '"' : ''; ?>>
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
							echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', $args['is_first'] && 0 === $rhm_index );
							?>
						</div>
					<?php endforeach; ?>
				</div>

				<?php if ( $rhm_multi ) : ?>
					<div class="pointer-events-none invisible absolute inset-x-0 top-0 flex aspect-[4/3] items-center justify-between px-2" data-rhm-tiles-controls>
						<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-prev disabled>
							<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</button>
						<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Ảnh tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-next disabled>
							<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</button>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="min-w-0">
			<h2 id="<?php echo esc_attr( $rhm_head_id ); ?>" class="text-lg leading-snug font-bold text-pretty text-primary lg:text-xl"<?php echo rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'title_color' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape trong hàm. ?>><?php echo esc_html( $rhm_title ); ?></h2>
			<?php if ( '' !== $rhm_text ) : ?>
				<div class="rhm-rich mt-2 text-sm leading-relaxed text-body/85 lg:text-[15px]"><?php echo $rhm_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
			<?php endif; ?>
		</div>
	</div>
</section>
