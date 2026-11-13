<?php
/**
 * Layout "Form đăng ký (Fluent Forms)": khung nền mint, bên trái tiêu đề + mô tả + lợi ích + hotline, bên phải thẻ form.
 * Hiển thị: template-parts/landing/form.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_form_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ), 'placeholder' => 'Đặt lịch online' ) ),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ), 'placeholder' => 'Đăng ký tư vấn miễn phí' ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả', 'themeranghammatsg' ), 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => __( 'Xuống dòng = đoạn mới.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'points',
			__( 'Lợi ích (mỗi dòng 1 ý)', 'themeranghammatsg' ),
			'textarea',
			array(
				'rows'         => 4,
				'new_lines'    => '',
				'placeholder'  => "Tư vấn miễn phí với bác sĩ chuyên khoa\nGọi lại xác nhận trong 15 phút",
				'instructions' => __( 'Hiện thành danh sách có dấu tích. Để trống: không hiện.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'hotline',
			__( 'Hiện hotline', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'default_value' => 1,
				'wrapper'       => array( 'width' => 30 ),
				'instructions'  => __( 'Số lấy từ Tùy biến → Thông tin phòng khám.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'form_id',
			__( 'Form Fluent Forms', 'themeranghammatsg' ),
			'select',
			array(
				'choices'       => rhm_fluent_form_choices(),
				'allow_null'    => 1,
				'ui'            => 1,
				'return_format' => 'value',
				'wrapper'       => array( 'width' => 70 ),
				'instructions'  => __( 'Chọn form đã tạo trong Fluent Forms. Để trống: dùng form đặt lịch mặc định (Tùy biến → Thông tin phòng khám).', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field( $prefix, 'form_title', __( 'Tiêu đề trên form (tùy chọn)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Để lại thông tin, chúng tôi gọi lại ngay' ) ),
	);
}
