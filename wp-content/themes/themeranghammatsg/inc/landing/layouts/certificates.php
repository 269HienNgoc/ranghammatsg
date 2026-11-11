<?php
/**
 * Layout "Chứng nhận": dùng lại slider của trang chủ (template-parts/sections/certificates.php, dữ liệu inc/certificates.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_certificates_fields( string $prefix ): array {
	return array(
		rhm_landing_field(
			$prefix,
			'note',
			'',
			'message',
			array( 'message' => __( 'Ảnh chứng nhận lấy từ Tùy biến → Chứng nhận (Trang chủ).', 'themeranghammatsg' ) )
		),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => rhm_certificates_default_title(), 'instructions' => __( 'Để trống: dùng tiêu đề của trang chủ.', 'themeranghammatsg' ) ) ),
	);
}
