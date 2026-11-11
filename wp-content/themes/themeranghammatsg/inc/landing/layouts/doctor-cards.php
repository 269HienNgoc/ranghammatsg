<?php
/**
 * Layout "Bác sĩ tiêu biểu": thẻ ngang (ảnh + tên + mô tả) chọn từ hồ sơ bác sĩ (CPT bac-si).
 * Hiển thị: template-parts/landing/doctor-cards.php. Hàm in field: rhm_landing_layout_doctor_cards_fields (gạch ngang → gạch dưới).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_doctor_cards_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Đội ngũ bác sĩ chuyên môn cao', 'themeranghammatsg' ) ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả ngắn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Bác sĩ', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 6,
				'layout'       => 'block',
				'button_label' => __( 'Thêm bác sĩ', 'themeranghammatsg' ),
				'instructions' => __( 'Ảnh, tên và link hồ sơ lấy từ hồ sơ bác sĩ (cần có Ảnh đại diện). 2 bác sĩ / hàng trên desktop.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field(
						$item,
						'doctor',
						__( 'Hồ sơ bác sĩ', 'themeranghammatsg' ),
						'post_object',
						array(
							'post_type'     => array( 'bac-si' ),
							'post_status'   => array( 'publish' ),
							'return_format' => 'id',
							'ui'            => 1,
							'wrapper'       => array( 'width' => 40 ),
						)
					),
					rhm_landing_field( $item, 'text', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ), 'textarea', array( 'rows' => 3, 'new_lines' => '', 'wrapper' => array( 'width' => 60 ), 'instructions' => __( 'Để trống: dùng ô Tóm tắt của hồ sơ, không có thì dùng Chức danh.', 'themeranghammatsg' ) ) ),
				),
			)
		),
	);
}
