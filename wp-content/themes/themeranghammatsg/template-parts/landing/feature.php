<?php
/**
 * Landing – Ô thông tin (dạng ô, xếp cạnh ô khác theo Độ rộng): tiêu đề vạch dọc, ảnh 40% trái + chữ + nút phải.
 * Field: inc/landing/layouts/feature.php. Thiếu tiêu đề hoặc (ảnh lẫn chữ) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_image = rhm_landing_image( $rhm_data, 'image' );
$rhm_lines = rhm_landing_lines( $rhm_data, 'text' );
if ( '' === $rhm_title || ( ! $rhm_image && ! $rhm_lines ) ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="h-full rounded-2xl border border-line bg-white/80 p-5 shadow-card lg:p-6" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'box' ); ?>
	<div class="<?php echo esc_attr( $rhm_image ? 'grid grid-cols-[40%_minmax(0,1fr)] items-center gap-4 lg:gap-6' : '' ); ?>">
		<?php if ( $rhm_image ) : ?>
			<div class="overflow-hidden rounded-lg bg-soft">
				<?php
				// Ảnh 40% phần nội dung ô (trừ phần khoảng cách 16/24px chia đôi).
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, rhm_landing_box_sizes( $args['span'] ?? 10, 0.4, 0.4, 8 ), 'block h-auto w-full', (bool) $args['is_first'] );
				?>
			</div>
		<?php endif; ?>
		<div class="min-w-0">
			<?php foreach ( $rhm_lines as $rhm_line ) : ?>
				<p class="mb-3 text-sm leading-relaxed text-body/85 lg:text-[15px]"><?php echo esc_html( $rhm_line ); ?></p>
			<?php endforeach; ?>
			<?php rhm_landing_button( $rhm_data, 'btn btn-primary mt-2 h-10 gap-2 rounded-full px-5 text-sm font-bold' ); ?>
		</div>
	</div>
</section>
