<?php
/**
 * Layout "Ảnh trước – sau khách hàng (slider)": tiêu đề + dải checklist + slider thẻ khách hàng (chân dung | trước / sau,
 * tên, tỉnh, dịch vụ, loại implant). Hiển thị: template-parts/landing/before-after.php (thẻ dùng chung với trang chủ:
 * template-parts/components/before-after-card.php).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_before_after_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ) ),
		rhm_landing_field(
			$prefix,
			'points',
			__( 'Checklist dưới tiêu đề', 'themeranghammatsg' ),
			'textarea',
			array(
				'rows'         => 4,
				'instructions' => __( 'Mỗi dòng 1 mục (vd: Mất răng đơn lẻ). Để trống = không hiện dải checklist.', 'themeranghammatsg' ),
				'wrapper'      => array( 'width' => 70 ),
			)
		),
		rhm_landing_icon_field(
			$prefix,
			'points_icon',
			array(
				'label'         => __( 'Icon checklist', 'themeranghammatsg' ),
				'default_value' => 'tich',
				'wrapper'       => array( 'width' => 30 ),
			)
		),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Khách hàng', 'themeranghammatsg' ),
			'repeater',
			array(
				'layout'       => 'block',
				'max'          => 12,
				'button_label' => __( 'Thêm khách hàng', 'themeranghammatsg' ),
				'collapsed'    => 'field_rhm_landing_' . $item . '_name',
				'instructions' => __( 'Mỗi khách cần đủ tên + ảnh chân dung + ảnh trước + ảnh sau mới hiển thị. Kéo để sắp xếp. Tối đa 12 khách.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_image_field( $item, 'portrait', __( 'Ảnh chân dung', 'themeranghammatsg' ), array( 'instructions' => __( 'Ảnh dọc (~3:4), cao bằng 2 ảnh bên phải.', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 33 ) ) ),
					rhm_landing_image_field( $item, 'before', __( 'Ảnh trước điều trị', 'themeranghammatsg' ), array( 'instructions' => __( 'Ảnh ngang 3:2.', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 33 ) ) ),
					rhm_landing_image_field( $item, 'after', __( 'Ảnh sau điều trị', 'themeranghammatsg' ), array( 'instructions' => __( 'Ảnh ngang 3:2, cùng góc chụp với ảnh trước.', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 34 ) ) ),
					rhm_landing_field( $item, 'name', __( 'Tên khách hàng (H3)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Cô Phương Nguyễn', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 50 ) ) ),
					rhm_landing_field( $item, 'location', __( 'Tỉnh / thành phố', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'TP Hồ Chí Minh', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 50 ) ) ),
					rhm_landing_field( $item, 'service', __( 'Dịch vụ (dòng icon răng)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Trồng răng Implant All on 4 2 hàm', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 50 ) ) ),
					rhm_landing_field( $item, 'product', __( 'Loại implant / vật liệu (dòng icon trụ)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Implant Straumann Thụy Sĩ', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 50 ) ) ),
				),
			)
		),
	);
}
