<?php
/**
 * Layout "Chặng đường phát triển" (mốc thời gian). Hiển thị: template-parts/landing/timeline.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_timeline_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Các mốc', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 20,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_year',
				'button_label' => __( 'Thêm mốc', 'themeranghammatsg' ),
				'instructions' => __( 'Sắp theo thứ tự thời gian; mốc thiếu năm hoặc tiêu đề sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'year', __( 'Năm / thời điểm', 'themeranghammatsg' ), 'text', array( 'placeholder' => '2015', 'wrapper' => array( 'width' => 30 ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề mốc (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ) ) ),
					rhm_landing_field( $item, 'text', __( 'Mô tả', 'themeranghammatsg' ), 'textarea', array( 'rows' => 3, 'new_lines' => '' ) ),
				),
			)
		),
	);
}
