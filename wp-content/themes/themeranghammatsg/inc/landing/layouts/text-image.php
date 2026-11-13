<?php
/**
 * Layout "Tiêu đề + nội dung dài + ảnh lớn bên dưới": bài giới thiệu nhiều chữ (đoạn văn, danh sách, tiêu đề phụ)
 * rồi 1 ảnh rộng hết khung. Hiển thị: template-parts/landing/text-image.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_text_image_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_wysiwyg_field(
			$prefix,
			'content',
			__( 'Nội dung', 'themeranghammatsg' ),
			array(
				// Nội dung dài: cho tiêu đề phụ (H3, H4), danh sách, trích dẫn, căn lề. Không chèn ảnh trong ô này (ảnh nhập ở field bên dưới).
				'toolbar'      => 'full',
				'instructions' => __( 'Dùng "Tiêu đề 3" cho tiêu đề phụ (không dùng Tiêu đề 1/2 — trang đã có H1, section đã có H2).', 'themeranghammatsg' ),
			)
		),
		rhm_landing_image_field(
			$prefix,
			'image',
			__( 'Ảnh lớn bên dưới — khuyến nghị rộng 1600px', 'themeranghammatsg' ),
			array( 'instructions' => __( 'Rộng hết khung nội dung (tối đa 1408px), giữ nguyên tỉ lệ ảnh, không cắt. Ảnh ngang (vd 1600 × 700 hoặc 1600 × 900) hiển thị đẹp nhất. Nhập "Văn bản thay thế".', 'themeranghammatsg' ) )
		),
	);
}
