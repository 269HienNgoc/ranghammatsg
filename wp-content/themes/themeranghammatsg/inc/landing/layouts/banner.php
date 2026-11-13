<?php
/**
 * Layout "Banner ảnh": 1 ảnh desktop 1920 × 650 + ảnh mobile 828 × 620 (art direction < 768px), link tùy chọn.
 * Hiển thị: template-parts/landing/banner.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_banner_fields( string $prefix ): array {
	return array(
		rhm_landing_image_field(
			$prefix,
			'image',
			__( 'Ảnh desktop — 1920 × 650 px', 'themeranghammatsg' ),
			array(
				'required'     => 1,
				'wrapper'      => array( 'width' => 50 ),
				'instructions' => __( 'Hiện từ 768px trở lên. Nhập "Văn bản thay thế" (mô tả nội dung banner, cũng là tên link nếu có link). Nén WebP ≤ 200 kB.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_image_field(
			$prefix,
			'image_mobile',
			__( 'Ảnh mobile — 828 × 620 px (tùy chọn)', 'themeranghammatsg' ),
			array(
				'wrapper'      => array( 'width' => 50 ),
				'instructions' => __( 'Hiện dưới 768px. Trống → dùng ảnh desktop (thu nhỏ, chữ trong ảnh có thể khó đọc).', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'link',
			__( 'Link khi bấm banner (tùy chọn)', 'themeranghammatsg' ),
			'text',
			array(
				'wrapper'      => array( 'width' => 60 ),
				'placeholder'  => 'https://… hoặc #neo-section',
				'instructions' => __( 'Trống: banner chỉ là ảnh. Link sang site khác tự mở tab mới.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'layout',
			__( 'Độ rộng', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					'full'      => __( 'Tràn màn hình', 'themeranghammatsg' ),
					'container' => __( 'Trong khung nội dung', 'themeranghammatsg' ),
				),
				'default_value' => 'full',
				'wrapper'       => array( 'width' => 40 ),
				'instructions'  => __( 'Trong khung: rộng tối đa 1408px, bo góc.', 'themeranghammatsg' ),
			)
		),
	);
}
