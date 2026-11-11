<?php
/**
 * Layout dạng ô "Ô thông tin": tiêu đề vạch dọc + ảnh (trái) + chữ + nút (phải). Vd Giấy phép hoạt động, Hệ thống vô trùng.
 * Hiển thị: template-parts/landing/feature.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_feature_fields( string $prefix ): array {
	return array_merge(
		array(
			rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text' ),
			rhm_landing_image_field( $prefix, 'image', __( 'Ảnh (giữ nguyên tỉ lệ) — rộng ≥ 600px', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 40 ), 'instructions' => __( 'Chiếm 40% bề ngang ô, không bị cắt.', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'text', __( 'Nội dung', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'wrapper' => array( 'width' => 60 ), 'instructions' => __( 'Mỗi dòng là 1 đoạn.', 'themeranghammatsg' ) ) ),
		),
		rhm_landing_button_fields( $prefix )
	);
}
