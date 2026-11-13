<?php
/**
 * Landing – Banner ảnh: <picture> ảnh desktop (1920 × 650) + ảnh mobile (828 × 620, < 768px), link tùy chọn bao cả banner.
 * Độ rộng: "full" tràn màn hình (không bo góc) | "container" trong .site-container (tối đa 1408px, bo góc).
 * Ảnh giữ nguyên tỉ lệ file (h-auto w-full; width/height của từng nhánh <source>/<img> = kích thước file → CLS 0).
 * Field: inc/landing/layouts/banner.php. Không có ảnh desktop → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_image = rhm_landing_image( $rhm_data, 'image' );
if ( ! $rhm_image ) {
	return;
}

$rhm_mobile = rhm_landing_image( $rhm_data, 'image_mobile' );
$rhm_url    = rhm_landing_url( $rhm_data, 'link' );
$rhm_full   = 'container' !== ( $rhm_data['layout'] ?? 'full' );

// sizes: tràn màn hình = 100vw. Trong khung = khung 1408px (≥ 1440px) hoặc 100vw − lề 2 × 16px.
// Nhánh mobile (< 768px) dùng ảnh 828px: 100vw (tràn) | 100vw − 32px (trong khung).
$rhm_sizes        = $rhm_full ? '100vw' : '(min-width: 1440px) 1408px, calc(100vw - 32px)';
$rhm_mobile_sizes = $rhm_full ? '100vw' : 'calc(100vw - 32px)';

// Section đứng đầu trang → banner là ảnh LCP (fetchpriority=high, không lazy).
$rhm_picture = rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', (bool) $args['is_first'], $rhm_mobile, $rhm_mobile_sizes );
$rhm_frame   = $rhm_full ? 'block' : 'block overflow-hidden rounded-2xl shadow-card';
?>
<div id="<?php echo esc_attr( $args['id'] ); ?>" class="pt-0 pb-6 lg:pb-8 lg:pt-0">
	<div class="<?php echo esc_attr( $rhm_full ? '' : 'site-container' ); ?>">
		<?php if ( '' !== $rhm_url ) : ?>
			<?php // Link chỉ chứa ảnh: alt của ảnh là tên link (rhm_landing_image_alt: alt → tiêu đề attachment, không bao giờ rỗng). ?>
			<a class="<?php echo esc_attr( $rhm_frame . ' transition-opacity hover:opacity-95' ); ?>" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
				<?php echo $rhm_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính. ?>
			</a>
		<?php else : ?>
			<div class="<?php echo esc_attr( $rhm_frame ); ?>">
				<?php echo $rhm_picture; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính. ?>
			</div>
		<?php endif; ?>
	</div>
</div>
