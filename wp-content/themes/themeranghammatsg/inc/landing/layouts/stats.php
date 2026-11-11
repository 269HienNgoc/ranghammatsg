<?php
/**
 * Layout "Số liệu nổi bật". Hiển thị: template-parts/landing/stats.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_stats_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2, tùy chọn)', 'themeranghammatsg' ), 'text' ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Số liệu', 'themeranghammatsg' ),
			'repeater',
			array(
				'min'          => 0,
				'max'          => 8,
				'layout'       => 'table',
				'button_label' => __( 'Thêm số liệu', 'themeranghammatsg' ),
				'instructions' => __( 'Tối đa 8 ô; dòng thiếu số sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'number', __( 'Số', 'themeranghammatsg' ), 'text', array( 'placeholder' => '15+' ) ),
					rhm_landing_field( $item, 'label', __( 'Nhãn', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Năm kinh nghiệm', 'themeranghammatsg' ) ) ),
				),
			)
		),
	);
}
