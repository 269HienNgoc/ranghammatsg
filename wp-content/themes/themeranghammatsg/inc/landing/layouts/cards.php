<?php
/**
 * Layout "Thẻ giá trị" (sứ mệnh, tầm nhìn, giá trị cốt lõi, lý do chọn…) + ảnh bên cạnh (tùy chọn).
 * Hiển thị: template-parts/landing/cards.php. Icon: bộ nét assets/icons/landing/.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_cards_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ), 'placeholder' => __( 'Sứ mệnh – Tầm nhìn – Giá trị cốt lõi', 'themeranghammatsg' ) ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả ngắn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
		rhm_landing_image_field( $prefix, 'image', __( 'Ảnh bên phải (tùy chọn) — khuyến nghị 900 × 675 (4:3)', 'themeranghammatsg' ), array( 'instructions' => __( 'Desktop: ảnh chiếm 1/3 bên phải, cao bằng khối thẻ (cắt giữa). Để trống: thẻ rộng hết hàng.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Thẻ', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 8,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_title',
				'button_label' => __( 'Thêm thẻ', 'themeranghammatsg' ),
				'instructions' => __( 'Nên 3 thẻ khi có ảnh. Thẻ thiếu tiêu đề sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_icon_field( $item, 'icon', array( 'wrapper' => array( 'width' => 30 ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề thẻ (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ) ) ),
					rhm_landing_field( $item, 'text', __( 'Mô tả', 'themeranghammatsg' ), 'textarea', array( 'rows' => 3, 'new_lines' => '' ) ),
				),
			)
		),
	);
}
