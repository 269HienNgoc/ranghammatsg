<?php
/**
 * Layout "Ảnh lớn + chữ + ô nổi bật": ảnh 4:3 (có thể kèm video "bấm mới tải") + tiêu đề + nội dung + nút,
 * kèm ô nổi bật (icon, tiêu đề, mô tả, nút). Vd Đầu tư cơ sở vật chất + Chi phí hợp lý. Hiển thị: template-parts/landing/intro.php.
 * Kiểu hiển thị (style, 2026-11-26): '' = ảnh 4:3 cạnh chữ | card = ảnh hiện trọn (vd ảnh ghép sẵn) đè lên mép thẻ trắng chứa chữ,
 * thẻ có ảnh trang trí nền tùy chọn (card_bg).
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
					'wrapper'       => array( 'width' => 40 ),
				)
			),
			rhm_landing_field(
				$prefix,
				'style',
				__( 'Kiểu hiển thị', 'themeranghammatsg' ),
				'button_group',
				array(
					'choices'       => array(
						''     => __( 'Ảnh 4:3 cạnh chữ', 'themeranghammatsg' ),
						'card' => __( 'Ảnh tự do + nội dung trong thẻ', 'themeranghammatsg' ),
					),
					'default_value' => '',
					'allow_null'    => 0,
					'wrapper'       => array( 'width' => 60 ),
					'instructions'  => __( 'Ảnh tự do: ảnh hiện trọn đúng tỉ lệ file (dùng được ảnh ghép sẵn, PNG/WebP nền trong suốt), đè lên mép thẻ trắng chứa chữ. Khuyến nghị ảnh rộng khoảng 900px.', 'themeranghammatsg' ),
				)
			),
			rhm_landing_image_field(
				$prefix,
				'card_bg',
				__( 'Ảnh trang trí nền thẻ (tùy chọn, vd bản đồ mờ)', 'themeranghammatsg' ),
				array(
					'instructions'      => __( 'Ảnh đã làm mờ sẵn, phủ kín thẻ, canh góc dưới – phải; không chứa chữ quan trọng. Khuyến nghị 1800 × 1200. Để trống: thẻ trắng trơn.', 'themeranghammatsg' ),
					'conditional_logic' => array( array( array( 'field' => 'field_rhm_landing_' . $prefix . '_style', 'operator' => '==', 'value' => 'card' ) ) ),
				)
			),
		),
		rhm_landing_button_fields( $prefix ),
		array(
			rhm_landing_field( $prefix, 'tab_callout', __( 'Ô nổi bật', 'themeranghammatsg' ), 'tab' ),
			rhm_landing_icon_field( $prefix, 'callout_icon', array( 'wrapper' => array( 'width' => 30 ) ) ),
			rhm_landing_field( $prefix, 'callout_title', __( 'Tiêu đề ô (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ), 'instructions' => __( 'Để trống: không hiện ô nổi bật.', 'themeranghammatsg' ) ) ),
			rhm_landing_rich_field( $prefix, 'callout_text', __( 'Mô tả ô', 'themeranghammatsg' ) ),
			rhm_landing_field( $prefix, 'callout_button_text', __( 'Chữ nút ô', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => __( 'Tư vấn chi tiết', 'themeranghammatsg' ) ) ),
			rhm_landing_field( $prefix, 'callout_button_link', __( 'Link nút ô', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ), 'placeholder' => 'https://… hoặc #neo-section' ) ),
			rhm_landing_field( $prefix, 'tab_settings', __( 'Cài đặt', 'themeranghammatsg' ), 'tab' ),
		)
	);
}
