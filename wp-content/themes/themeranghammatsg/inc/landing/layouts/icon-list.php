<?php
/**
 * Layout "Danh sách có icon + ảnh": tiêu đề + mô tả + danh sách (icon chung, từng dòng có thể đổi icon riêng)
 * + ảnh tùy chọn ở trái / phải / trên / dưới. Hiển thị: template-parts/landing/icon-list.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_icon_list_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ), 'textarea', array( 'rows' => 3, 'new_lines' => '', 'instructions' => __( 'Hiện dưới tiêu đề, trước danh sách. Xuống dòng = đoạn mới.', 'themeranghammatsg' ) ) ),
		rhm_landing_icon_field(
			$prefix,
			'icon',
			array(
				'label'         => __( 'Icon của danh sách', 'themeranghammatsg' ),
				'default_value' => 'tich',
				'instructions'  => __( 'Icon đứng trước mỗi dòng (từng dòng có thể chọn icon khác ở cột "Icon riêng"). Trống → dấu tích. Thêm icon: chép file .svg vào assets/icons/landing/ của theme.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Danh sách', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 30,
				'layout'       => 'table',
				'button_label' => __( 'Thêm dòng', 'themeranghammatsg' ),
				'instructions' => __( 'Mỗi dòng 1 ý, kéo để sắp xếp. Dòng trống bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'text', __( 'Nội dung', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '', 'wrapper' => array( 'width' => 70 ) ) ),
					rhm_landing_icon_field( $item, 'icon', array( 'label' => __( 'Icon riêng (tùy chọn)', 'themeranghammatsg' ), 'instructions' => '', 'wrapper' => array( 'width' => 30 ) ) ),
				),
			)
		),
		rhm_landing_image_field( $prefix, 'image', __( 'Ảnh (tùy chọn)', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'instructions' => __( 'Giữ nguyên tỉ lệ, không cắt. Trái/phải: khuyến nghị 1200 × 900; trên/dưới (rộng hết khung): rộng 1600px.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'image_position',
			__( 'Vị trí ảnh', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					'left'   => __( 'Trái', 'themeranghammatsg' ),
					'right'  => __( 'Phải', 'themeranghammatsg' ),
					'top'    => __( 'Trên', 'themeranghammatsg' ),
					'bottom' => __( 'Dưới', 'themeranghammatsg' ),
				),
				'default_value' => 'right',
				'wrapper'       => array( 'width' => 50 ),
				'instructions'  => __( 'Trái/phải: 2 cột trên máy tính (mobile: ảnh trái → nằm trên danh sách, ảnh phải → nằm dưới). Trên/dưới: ảnh và danh sách rộng hết khung, danh sách chia 2 cột trên máy tính.', 'themeranghammatsg' ),
			)
		),
	);
}
