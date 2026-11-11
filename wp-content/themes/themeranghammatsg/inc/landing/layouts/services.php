<?php
/**
 * Layout "Danh sách dịch vụ": thẻ ảnh + tên (có link). Hiển thị: template-parts/landing/services.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_services_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Đa dạng dịch vụ nha khoa', 'themeranghammatsg' ) ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả ngắn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Dịch vụ', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 10,
				'layout'       => 'table',
				'button_label' => __( 'Thêm dịch vụ', 'themeranghammatsg' ),
				'instructions' => __( 'Nên 5 dịch vụ (1 hàng desktop; mobile vuốt ngang). Ảnh khung 16:10 (khuyến nghị 640 × 400), cắt giữa. Dòng thiếu ảnh hoặc tên sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_image_field( $item, 'image', __( 'Ảnh', 'themeranghammatsg' ), array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 25 ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tên dịch vụ', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 35 ) ) ),
					rhm_landing_field( $item, 'link', __( 'Link', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ), 'placeholder' => 'https://…/trong-rang-implant/' ) ),
				),
			)
		),
	);
}
