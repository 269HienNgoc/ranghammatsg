<?php
/**
 * Landing – Tiêu đề + nội dung dài + ảnh lớn bên dưới: cụm tiêu đề canh giữa → nội dung soạn thảo (.entry-content, rộng hết
 * khung .site-container) → ảnh rộng hết khung, giữ nguyên tỉ lệ file (không cắt).
 * Field: inc/landing/layouts/text-image.php. Thiếu tiêu đề, hoặc thiếu cả nội dung lẫn ảnh → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_title   = rhm_landing_text( $rhm_data, 'title' );
$rhm_content = rhm_landing_html( $rhm_data, 'content' );
$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
if ( '' === $rhm_title || ( '' === $rhm_content && ! $rhm_image ) ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
// Ảnh rộng hết khung: 1408px (≥ 1440px), còn lại 100vw − lề 2 × 16px. width/height = kích thước file → giữ tỉ lệ, CLS = 0.
$rhm_sizes = '(min-width: 1440px) 1408px, calc(100vw - 32px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center' ); ?>

		<?php if ( '' !== $rhm_content ) : ?>
			<div class="entry-content"><?php echo $rhm_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post trong rhm_landing_html(). ?></div>
		<?php endif; ?>

		<?php if ( $rhm_image ) : ?>
			<div class="<?php echo esc_attr( '' !== $rhm_content ? 'mt-6 overflow-hidden rounded-2xl bg-soft shadow-card lg:mt-8' : 'overflow-hidden rounded-2xl bg-soft shadow-card' ); ?>">
				<?php
				// Section đứng đầu trang → ảnh là LCP (fetchpriority=high), còn lại lazy.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', (bool) $args['is_first'] );
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
