<?php
/**
 * Layout "Đội ngũ bác sĩ": dùng lại slider của trang chủ (template-parts/sections/doctors.php, dữ liệu inc/home-doctors.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_doctors_fields( string $prefix ): array {
	return array(
		rhm_landing_field(
			$prefix,
			'note',
			'',
			'message',
			array( 'message' => __( 'Danh sách bác sĩ giống section trang chủ: các hồ sơ bật "Hiện ở section Đội ngũ bác sĩ" (tab Trang chủ của hồ sơ bác sĩ), sắp theo ô Thứ tự.', 'themeranghammatsg' ) )
		),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => rhm_home_doctors_text( 'eyebrow' ) ) ),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => rhm_home_doctors_text( 'title' ), 'instructions' => __( 'Để trống: dùng chữ của trang chủ (Tùy biến → Đội ngũ bác sĩ).', 'themeranghammatsg' ) ) ),
	);
}
