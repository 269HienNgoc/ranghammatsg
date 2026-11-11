<?php
/**
 * Layout "Kêu gọi đặt lịch": dùng lại template-parts/sections/booking-cta.php (nút đặt lịch + hotline).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_booking_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => rhm_booking_cta_default( 'title' ) ) ),
		rhm_landing_field( $prefix, 'text', __( 'Mô tả', 'themeranghammatsg' ), 'text', array( 'placeholder' => rhm_booking_cta_default( 'text' ), 'instructions' => __( 'Để trống: dùng chữ mặc định.', 'themeranghammatsg' ) ) ),
	);
}
