<?php
/**
 * Layout "Ảnh lớn + chữ + ô nổi bật": ảnh 4:3 (có thể kèm video "bấm mới tải") + tiêu đề + nội dung + nút,
 * kèm ô nổi bật (icon, tiêu đề, mô tả, nút). Vd Đầu tư cơ sở vật chất + Chi phí hợp lý. Hiển thị: template-parts/landing/intro.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_intro_fields( string $prefix ): array {
	return array_merge(
		array(
			rhm_landing_field( $prefix, 'tab_main', __( 'Nội dung chính', 'themeranghammatsg' ), 'tab' ),
			rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) ),
			rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
			rhm_landing_wysiwyg_field( $prefix, 'content', __( 'Nội dung', 'themeranghammatsg' ) ),
			rhm_landing_image_field( $prefix, 'image', __( 'Ảnh — khuyến nghị 1200 × 900 (4:3)', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 50 ), 'instructions' => __( 'Khung 4:3, ảnh khác tỉ lệ sẽ được cắt giữa.', 'themeranghammatsg' ) ) ),
			rhm_landing_field(
				$prefix,
				'video',
				__( 'Link video YouTube / .mp4 (tùy chọn)', 'themeranghammatsg' ),
				'url',
				array(
					'wrapper'      => array( 'width' => 50 ),
					'instructions' => __( 'Cần có ảnh: ảnh làm bìa, video chỉ tải khi người xem bấm ▶.', 'themeranghammatsg' ),
				)
			),
			rhm_landing_field(
				$prefix,
				'image_position',
				__( 'Vị trí ảnh (desktop)', 'themeranghammatsg' ),
				'button_group',
				array(
					'choices'       => array(
						'left'  => __( 'Trái', 'themeranghammatsg' ),
						'right' => __( 'Phải', 'themeranghammatsg' ),
					),
					'default_value' => 'left',
				)
			),
		),
		rhm_landing_button_fields( $prefix ),
		array(
			rhm_landing_field( $prefix, 'tab_callout', __( 'Ô nổi bật', 'themeranghammatsg' ), 'tab' ),
			rhm_landing_icon_field( $prefix, 'callout_icon', array( 'wrapper' => array( 'width' => 30 ) ) ),
			rhm_landing_field( $prefix, 'callout_title', __( 'Tiêu đề ô (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ), 'instructions' => __( 'Để trống: không hiện ô nổi bật.', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'callout_text', __( 'Mô tả ô', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
			rhm_landing_field( $prefix, 'callout_button_text', __( 'Chữ nút ô', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => __( 'Tư vấn chi tiết', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'callout_button_link', __( 'Link nút ô', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => 'https://… hoặc #neo-section' ) ),
			rhm_landing_field( $prefix, 'tab_settings', __( 'Cài đặt', 'themeranghammatsg' ), 'tab' ),
		)
	);
}
