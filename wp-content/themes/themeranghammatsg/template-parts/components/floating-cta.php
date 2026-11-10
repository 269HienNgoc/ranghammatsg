<?php
/**
 * Nút liên hệ nhanh (inc/floating-cta.php). 1 bộ markup cho cả 2 bố cục, CSS đổi kiểu theo breakpoint
 * (src/css/components/_floating-cta.css): < 1024px thanh dưới đáy, ≥ 1024px cột nổi bên phải (nhãn hiện khi rê chuột/focus).
 * Nút "popup" có data-rhm-popup → main.js mở popup đặt lịch; href vẫn là link thật (dự phòng khi không có JS).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_cta_items = rhm_get_floating_cta_items();
if ( ! $rhm_cta_items ) {
	return;
}

// Số nút lẻ ≥ 3 → nút chính giữa nổi lên trên thanh mobile.
$rhm_cta_count = count( $rhm_cta_items );
$rhm_cta_main  = ( $rhm_cta_count >= 3 && 1 === $rhm_cta_count % 2 ) ? intdiv( $rhm_cta_count, 2 ) : -1;
?>
<nav class="floating-cta" aria-label="<?php esc_attr_e( 'Liên hệ nhanh', 'themeranghammatsg' ); ?>" style="--cta-count: <?php echo esc_attr( (string) $rhm_cta_count ); ?>">
	<ul class="floating-cta__list">
		<?php foreach ( $rhm_cta_items as $rhm_cta_index => $rhm_cta ) : ?>
			<li>
				<a class="floating-cta__item floating-cta__item--<?php echo esc_attr( $rhm_cta['color'] ); ?><?php echo $rhm_cta['ring'] ? ' floating-cta__item--ring' : ''; ?><?php echo $rhm_cta_main === $rhm_cta_index ? ' floating-cta__item--main' : ''; ?>" href="<?php echo esc_url( $rhm_cta['href'] ); ?>"<?php echo $rhm_cta['popup'] ? ' data-rhm-popup="booking"' : rhm_external_link_attrs( $rhm_cta['href'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
					<span class="floating-cta__icon"><?php echo rhm_icon_svg( 'cta', $rhm_cta['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?></span>
					<span class="floating-cta__label"><?php echo esc_html( $rhm_cta['label'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
