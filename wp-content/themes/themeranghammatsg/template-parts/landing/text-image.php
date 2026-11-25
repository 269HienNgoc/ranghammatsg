<?php
/**
 * Landing – Tiêu đề + nội dung dài + ảnh lớn bên dưới: cụm tiêu đề canh giữa → nội dung soạn thảo (.entry-content, rộng hết
 * khung .site-container) → ảnh rộng hết khung, giữ nguyên tỉ lệ file (không cắt).
 * Field: inc/landing/layouts/text-image.php. Thiếu tiêu đề, hoặc thiếu cả nội dung lẫn ảnh → không hiện.
 * Bật "Rút gọn nội dung": chỉ hiện N ký tự chữ đầu + "…" + nút Xem thêm / Thu gọn (rhm_landing_readmore_split(),
 * CSS .rhm-readmore trong page-about.css, JS src/js/readmore.js — chỉ nạp khi nội dung thật sự bị rút gọn). Ảnh luôn hiện.
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

// Nút "Xem thêm": số ký tự kẹp 100–5000 (trống → 600); '' = tắt hoặc nội dung đủ ngắn → in như thường.
$rhm_more = '';
if ( '' !== $rhm_content && ! empty( $rhm_data['readmore'] ) ) {
	$rhm_chars = absint( $rhm_data['readmore_chars'] ?? 0 );
	$rhm_more  = rhm_landing_readmore_split( $rhm_content, $rhm_chars ? min( 5000, max( 100, $rhm_chars ) ) : 600 );
}
// Ảnh rộng hết khung: 1248px (≥ 1280px), còn lại 100vw − lề 2 × 16px. width/height = kích thước file → giữ tỉ lệ, CLS = 0.
$rhm_sizes = '(min-width: 1280px) 1248px, calc(100vw - 32px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>

		<?php
		if ( '' !== $rhm_more ) :
			rhm_landing_enqueue_module( 'readmore' );
			$rhm_more_text = rhm_landing_text( $rhm_data, 'readmore_text' );
			$rhm_less_text = rhm_landing_text( $rhm_data, 'readmore_less_text' );
			$rhm_more_text = '' !== $rhm_more_text ? $rhm_more_text : __( 'Xem thêm', 'themeranghammatsg' );
			$rhm_less_text = '' !== $rhm_less_text ? $rhm_less_text : __( 'Thu gọn', 'themeranghammatsg' );
			?>
			<div class="rhm-readmore" data-rhm-readmore>
				<div id="<?php echo esc_attr( $args['id'] . '-content' ); ?>" class="entry-content"><?php echo $rhm_more; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post trong rhm_landing_html(), DOMDocument chỉ thêm span/thuộc tính. ?></div>
				<button type="button" class="rhm-readmore-toggle mx-auto mt-4 flex h-11 w-fit cursor-pointer items-center gap-2 rounded-full border border-current px-5 text-sm font-bold text-primary transition-colors hover:border-primary hover:bg-primary hover:text-white" aria-expanded="false" aria-controls="<?php echo esc_attr( $args['id'] . '-content' ); ?>" data-rhm-readmore-toggle data-label-more="<?php echo esc_attr( $rhm_more_text ); ?>" data-label-less="<?php echo esc_attr( $rhm_less_text ); ?>">
					<span class="rhm-readmore-label"><?php echo esc_html( $rhm_more_text ); ?></span>
					<?php echo rhm_get_icon( 'chevron', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG tĩnh của theme. ?>
				</button>
			</div>
		<?php elseif ( '' !== $rhm_content ) : ?>
			<div class="entry-content"><?php echo $rhm_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post trong rhm_landing_html(). ?></div>
		<?php endif; ?>

		<?php if ( $rhm_image ) : ?>
			<div class="<?php echo esc_attr( '' !== $rhm_content ? 'mt-6 overflow-hidden rounded-lg bg-soft shadow-card lg:mt-8' : 'overflow-hidden rounded-lg bg-soft shadow-card' ); ?>">
				<?php
				// Section đứng đầu trang → ảnh là LCP (fetchpriority=high), còn lại lazy.
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', (bool) $args['is_first'] );
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
