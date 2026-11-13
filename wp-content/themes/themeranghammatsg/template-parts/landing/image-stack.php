<?php
/**
 * Landing – Ảnh lớn xếp dọc (kèm mô tả ảnh): cụm tiêu đề canh giữa → lần lượt từng <figure> (ảnh rộng hết khung
 * .site-container, giữ nguyên tỉ lệ file, không cắt + <figcaption> mô tả). Mô tả = caption của attachment (trống → không in).
 * Field: inc/landing/layouts/image-stack.php. Thiếu tiêu đề hoặc không có ảnh hợp lệ → không hiện.
 *
 * Mô tả canh giữa, tối đa max-w-4xl (896px): dễ đọc và không chạy tới mép phải, nơi cột nút liên hệ nhanh (fixed, ≥ 1024px) đè lên.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data   = $args['data'] ?? array();
$rhm_title  = rhm_landing_text( $rhm_data, 'title' );
$rhm_images = rhm_landing_gallery( $rhm_data, 'images' ); // Đã nạp sẵn cache attachment (1 truy vấn).
if ( '' === $rhm_title || ! $rhm_images ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
// Ảnh rộng hết khung: 1408px (≥ 1440px), còn lại 100vw − lề 2 × 16px. width/height = kích thước file → giữ tỉ lệ, CLS = 0.
$rhm_sizes = '(min-width: 1440px) 1408px, calc(100vw - 32px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center' ); ?>

		<div class="space-y-8 lg:space-y-12">
			<?php foreach ( $rhm_images as $rhm_index => $rhm_image ) : ?>
				<?php $rhm_caption = rhm_landing_lines( array( 'caption' => (string) wp_get_attachment_caption( $rhm_image ) ), 'caption' ); ?>
				<figure>
					<div class="overflow-hidden rounded-2xl bg-soft shadow-card">
						<?php
						// Chỉ ảnh đầu, khi section đứng đầu trang, mới là ảnh LCP; còn lại lazy.
						// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
						echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', $args['is_first'] && 0 === $rhm_index );
						?>
					</div>
					<?php if ( $rhm_caption ) : ?>
						<figcaption class="mx-auto mt-3 max-w-4xl text-center leading-relaxed text-body/85 lg:mt-4">
							<?php foreach ( $rhm_caption as $rhm_line_index => $rhm_line ) : ?>
								<?php echo $rhm_line_index ? '<br>' : ''; ?><?php echo esc_html( $rhm_line ); ?>
							<?php endforeach; ?>
						</figcaption>
					<?php endif; ?>
				</figure>
			<?php endforeach; ?>
		</div>
	</div>
</section>
