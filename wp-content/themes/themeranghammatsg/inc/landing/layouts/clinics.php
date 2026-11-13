<?php
/**
 * Layout "Hệ thống phòng khám": tiêu đề + mô tả, hàng chi nhánh (tên + nút Chỉ đường), slider ảnh lớn (1 ảnh / lần,
 * mỗi ảnh có tiêu đề + chú thích riêng), dải ảnh nhỏ để chọn ảnh. Hiển thị: template-parts/landing/clinics.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_clinics_fields( string $prefix ): array {
	$branch = $prefix . '_branches';
	$item   = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Hệ thống phòng khám' ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => __( 'Xuống dòng = đoạn mới. Hiện canh giữa dưới tiêu đề.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'branches',
			__( 'Chi nhánh', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 6,
				'layout'       => 'table',
				'button_label' => __( 'Thêm chi nhánh', 'themeranghammatsg' ),
				'instructions' => __( 'Hàng tên chi nhánh dưới mô tả. Có link Google Maps → hiện nút "Chỉ đường" (mở tab mới); trống link → chỉ hiện tên.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $branch, 'name', __( 'Tên chi nhánh', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
					rhm_landing_field( $branch, 'map_url', __( 'Link Google Maps', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ), 'placeholder' => 'https://maps.app.goo.gl/…' ) ),
				),
			)
		),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Ảnh', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 12,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_title',
				'button_label' => __( 'Thêm ảnh', 'themeranghammatsg' ),
				'instructions' => __( 'Mỗi ảnh = 1 slide rộng hết khung, có tiêu đề + chú thích riêng bên dưới. Từ 2 ảnh: có nút trái/phải và dải ảnh nhỏ để chọn; mobile vuốt ngang. Ảnh thiếu tiêu đề sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_image_field( $item, 'image', __( 'Ảnh — khuyến nghị 1600 × 900 (16:9)', 'themeranghammatsg' ), array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 30 ), 'instructions' => __( 'Desktop/tablet khung 16:9, mobile 4:3 (cắt 2 bên) → để chủ thể ở giữa ảnh. Nhập "Văn bản thay thế".', 'themeranghammatsg' ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề ảnh', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ), 'placeholder' => 'Tổng thể mặt tiền' ) ),
					rhm_landing_field( $item, 'text', __( 'Chú thích', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'wrapper' => array( 'width' => 70 ) ) ),
				),
			)
		),
	);
}
