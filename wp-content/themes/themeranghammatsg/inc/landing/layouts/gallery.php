<?php
/**
 * Layout "Thư viện ảnh (slider)": cơ sở vật chất, không gian phòng khám… Hiển thị: template-parts/landing/gallery.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_gallery_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field( $prefix, 'description', __( 'Mô tả ngắn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 2, 'new_lines' => '' ) ),
		rhm_landing_field(
			$prefix,
			'images',
			__( 'Ảnh — khuyến nghị 1200 × 900 (4:3)', 'themeranghammatsg' ),
			'gallery',
			array(
				'return_format' => 'id',
				'preview_size'  => 'thumbnail',
				'mime_types'    => 'jpg,jpeg,png,webp,avif',
				'instructions'  => __( 'Kéo thả để sắp xếp. Khung 4:3, ảnh khác tỉ lệ sẽ được cắt giữa. Nhập "Văn bản thay thế" cho từng ảnh (mô tả cho Google).', 'themeranghammatsg' ),
			)
		),
	);
}
