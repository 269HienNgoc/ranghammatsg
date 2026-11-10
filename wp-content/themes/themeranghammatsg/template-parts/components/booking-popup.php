<?php
/**
 * Popup đặt lịch (inc/booking-popup.php). <dialog> native: lớp trên cùng, Esc đóng, giữ focus bên trong.
 * Mở/đóng: src/js/main.js. Style: src/css/components/_booking-popup.css (+ _booking-form.css cho form), nạp qua footer.css.
 * Desktop: hộp giữa màn hình; mobile: trượt lên từ đáy (bottom sheet), phần form cuộn riêng.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

if ( ! rhm_booking_popup_enabled() ) {
	return;
}

$rhm_popup_form = rhm_render_fluent_form( rhm_booking_popup_form_id() );
if ( '' === trim( $rhm_popup_form ) ) {
	return;
}

$rhm_popup_desc    = rhm_booking_popup_text( 'desc' );
$rhm_popup_hotline = rhm_get_option( 'rhm_hotline' );
?>
<dialog id="rhm-booking-popup" class="booking-popup" aria-labelledby="rhm-booking-popup-title" closedby="any">
	<div class="booking-popup__panel">
		<div class="booking-popup__head">
			<span class="booking-popup__icon"><?php echo rhm_get_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
			<div class="booking-popup__heading">
				<h2 id="rhm-booking-popup-title" class="booking-popup__title"><?php echo esc_html( rhm_booking_popup_text( 'title' ) ); ?></h2>
				<?php if ( $rhm_popup_desc ) : ?>
					<p class="booking-popup__desc"><?php echo esc_html( $rhm_popup_desc ); ?></p>
				<?php endif; ?>
			</div>
			<button class="booking-popup__close" type="button" data-rhm-popup-close>
				<?php echo rhm_get_icon( 'close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				<span class="screen-reader-text"><?php esc_html_e( 'Đóng', 'themeranghammatsg' ); ?></span>
			</button>
		</div>

		<div class="booking-popup__body rhm-booking">
			<?php echo $rhm_popup_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fluent Forms output. ?>

			<?php if ( $rhm_popup_hotline ) : ?>
				<a class="booking-popup__hotline" href="<?php echo esc_url( rhm_get_tel_href( $rhm_popup_hotline ) ); ?>">
					<?php echo rhm_get_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<span><?php esc_html_e( 'Hoặc gọi ngay', 'themeranghammatsg' ); ?> <strong><?php echo esc_html( $rhm_popup_hotline ); ?></strong></span>
				</a>
			<?php endif; ?>
		</div>
	</div>
</dialog>
