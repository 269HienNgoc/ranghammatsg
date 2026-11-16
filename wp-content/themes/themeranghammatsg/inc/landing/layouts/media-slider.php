<?php
/**
 * Layout dạng ô "Ô ảnh slider + chữ": tiêu đề + nội dung, nhiều ảnh dạng slider (nút ‹ › + vuốt) ở trái / phải / trên / dưới.
 * Mobile (< 640px) luôn ảnh trên, chữ dưới. Hiển thị: template-parts/landing/media-slider.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_media_slider_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Máy chụp X-quang Vatech' ) ),
		rhm_landing_rich_field( $prefix, 'text', __( 'Nội dung', 'themeranghammatsg' ), array( 'instructions' => __( 'Đoạn chữ dưới tiêu đề (tùy chọn).', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'images',
			__( 'Ảnh slider — khuyến nghị 1200 × 900 (4:3)', 'themeranghammatsg' ),
			'gallery',
			array(
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'max'           => 12,
				'mime_types'    => 'jpg,jpeg,png,webp,avif',
				'wrapper'       => array( 'width' => 60 ),
				'instructions'  => __( 'Chọn nhiều ảnh, kéo thả để sắp thứ tự. Từ 2 ảnh: có nút trái/phải, mobile vuốt ngang. Khung 4:3, ảnh khác tỉ lệ bị cắt quanh tâm → để chủ thể ở giữa ảnh. Nhập "Văn bản thay thế".', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'image_position',
			__( 'Vị trí ảnh', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					'left'   => __( 'Trái', 'themeranghammatsg' ),
					'right'  => __( 'Phải', 'themeranghammatsg' ),
					'top'    => __( 'Trên', 'themeranghammatsg' ),
					'bottom' => __( 'Dưới', 'themeranghammatsg' ),
				),
				'default_value' => 'right',
				'wrapper'       => array( 'width' => 40 ),
				'instructions'  => __( 'Áp dụng từ màn hình ≥ 640px. Mobile luôn: ảnh trên, chữ dưới.', 'themeranghammatsg' ),
			)
		),
	);
}
