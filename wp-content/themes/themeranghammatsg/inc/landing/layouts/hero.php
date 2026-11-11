<?php
/**
 * Layout "Banner đầu trang": breadcrumb + H1 + đoạn nổi bật + mô tả + nút (trái), ảnh tràn mép phải. Hiển thị: template-parts/landing/hero.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_hero_fields( string $prefix ): array {
	return array_merge(
		array(
			rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'instructions' => __( 'Tùy chọn.', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H1)', 'themeranghammatsg' ), 'text', array( 'instructions' => __( 'Để trống: dùng tiêu đề trang.', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'lead', __( 'Đoạn nổi bật (chữ đậm)', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
			rhm_landing_field( $prefix, 'description', __( 'Mô tả', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => __( 'Mỗi dòng là 1 đoạn.', 'themeranghammatsg' ) ) ),
			rhm_landing_image_field( $prefix, 'image', __( 'Ảnh desktop — khuyến nghị 1600 × 1000', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'instructions' => __( 'Nửa phải màn hình, mép trái tự mờ dần vào nền; chủ thể nên nằm lệch phải. Ảnh tải đầu tiên: nén WebP, nên ≤ 150 kB. Để trống: banner chỉ có chữ.', 'themeranghammatsg' ) ) ),
			rhm_landing_image_field( $prefix, 'image_mobile', __( 'Ảnh mobile — khuyến nghị 828 × 620', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'instructions' => __( 'Hiện dưới chữ, màn hình < 768px. Để trống: dùng ảnh desktop.', 'themeranghammatsg' ) ) ),
			rhm_landing_field(
				$prefix,
				'booking',
				__( 'Nút "Đặt lịch hẹn"', 'themeranghammatsg' ),
				'true_false',
				array(
					'ui'            => 1,
					'default_value' => 0,
				)
			),
		),
		rhm_landing_button_fields( $prefix, __( 'Chữ nút phụ', 'themeranghammatsg' ) )
	);
}
