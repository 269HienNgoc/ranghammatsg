<?php
/**
 * Layout "Ảnh lớn xếp dọc (kèm mô tả ảnh)": tiêu đề rồi lần lượt từng ảnh rộng hết khung, mô tả ảnh ngay dưới mỗi ảnh.
 * Mô tả = ô "Chú thích" (caption) của ảnh trong thư viện Media. Hiển thị: template-parts/landing/image-stack.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_image_stack_fields( string $prefix ): array {
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ) ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_field(
			$prefix,
			'images',
			__( 'Ảnh — khuyến nghị rộng 1600px', 'themeranghammatsg' ),
			'gallery',
			array(
				'return_format' => 'id',
				'preview_size'  => 'medium',
				'max'           => 30,
				'mime_types'    => 'jpg,jpeg,png,webp,avif',
				'instructions'  => __( 'Chọn nhiều ảnh cùng lúc, kéo thả để sắp thứ tự (hiển thị từ trên xuống). Mỗi ảnh rộng hết khung, giữ nguyên tỉ lệ, không cắt. Mô tả dưới ảnh = ô "Chú thích" của ảnh: bấm vào ảnh trong khung này để sửa (trống → không hiện mô tả). Nhập cả "Văn bản thay thế".', 'themeranghammatsg' ),
			)
		),
	);
}
