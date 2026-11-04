<?php
defined( 'ABSPATH' ) || exit;
$rhm_form_id = absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
$rhm_hotline = rhm_get_option( 'rhm_hotline' );
?>
<section id="dat-lich" class="doctor-section doctor-section--mint" aria-labelledby="doctor-booking-title">
	<div class="site-container doctor-booking">
		<div>
			<p class="doctor-eyebrow"><?php esc_html_e( 'Đồng hành cùng nụ cười của bạn', 'themeranghammatsg' ); ?></p>
			<h2 id="doctor-booking-title" class="doctor-title"><?php esc_html_e( 'Đăng ký lịch tư vấn', 'themeranghammatsg' ); ?></h2>
			<p class="doctor-booking__intro"><?php echo esc_html( rhm_contact_text( 'form_note' ) ); ?></p>
			<?php if ( $rhm_hotline ) : ?>
				<a class="doctor-hotline" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>"><?php echo rhm_get_icon( 'phone', 'size-6' ); ?><span><span><?php esc_html_e( 'Hotline tư vấn', 'themeranghammatsg' ); ?></span><strong><?php echo esc_html( $rhm_hotline ); ?></strong></span></a>
			<?php endif; ?>
		</div>
		<div class="rhm-booking doctor-booking__form">
			<?php if ( $rhm_form_id && shortcode_exists( 'fluentform' ) ) : ?>
				<?php echo do_shortcode( sprintf( '[fluentform id="%d"]', $rhm_form_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plugin renders form. ?>
			<?php elseif ( $rhm_hotline ) : ?>
				<p><?php esc_html_e( 'Vui lòng gọi hotline để được hỗ trợ đặt lịch tư vấn.', 'themeranghammatsg' ); ?></p>
			<?php elseif ( current_user_can( 'edit_theme_options' ) ) : ?>
				<p><?php esc_html_e( 'Cấu hình ID form tại Giao diện → Tùy biến → Thông tin phòng khám.', 'themeranghammatsg' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</section>
