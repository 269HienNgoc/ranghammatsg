<?php
/**
 * Layout dạng ô "Ô lưới ảnh nhỏ": ảnh vuông/nhãn trên một hàng; mobile vuốt ngang, desktop có nút khi dư ô.
 * Hiển thị: template-parts/landing/tiles.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_tiles_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text' ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Ô', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 12,
				'layout'       => 'table',
				'button_label' => __( 'Thêm ô', 'themeranghammatsg' ),
				'instructions' => __( 'Ảnh vuông nền trắng (khuyến nghị 400 × 400, PNG/WebP), không bị cắt. Mobile: vuốt ngang. Từ 640px: 6 ô/hàng; desktop từ 1024px: 6 ô cho khối 3/5 hoặc toàn trang, 3 ô cho khối hẹp. Nhiều hơn số ô của hàng: cuộn ngang và dùng nút trái/phải. Ô thiếu ảnh hoặc nhãn sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_image_field( $item, 'image', __( 'Ảnh', 'themeranghammatsg' ), array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 30 ) ) ),
					rhm_landing_field( $item, 'label', __( 'Nhãn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '', 'placeholder' => "CT\nCone Beam", 'instructions' => __( 'Tối đa 2 dòng ngắn.', 'themeranghammatsg' ) ) ),
				),
			)
		),
	);
}
