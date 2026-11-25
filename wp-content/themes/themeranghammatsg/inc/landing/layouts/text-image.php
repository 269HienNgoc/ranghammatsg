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
		// Nút "Xem thêm": rút gọn nội dung dài còn N ký tự chữ (rhm_landing_readmore_split(), inc/landing/landing.php).
		rhm_landing_field(
			$prefix,
			'readmore',
			__( 'Rút gọn nội dung (nút Xem thêm)', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'default_value' => 0,
				'wrapper'       => array( 'width' => 25 ),
			)
		),
		rhm_landing_field(
			$prefix,
			'readmore_chars',
			__( 'Số ký tự hiện trước nút', 'themeranghammatsg' ),
			'number',
			array(
				'default_value'     => 600,
				'min'               => 100,
				'max'               => 5000,
				'step'              => 50,
				'instructions'      => __( 'Đếm chữ (không tính thẻ HTML), cắt ở hết từ gần nhất. Nội dung chỉ dài hơn số này một chút (≤ 80 ký tự) thì không rút gọn.', 'themeranghammatsg' ),
				'wrapper'           => array( 'width' => 25 ),
				'conditional_logic' => array( array( array( 'field' => 'field_rhm_landing_' . $prefix . '_readmore', 'operator' => '==', 'value' => '1' ) ) ),
			)
		),
		rhm_landing_field(
			$prefix,
			'readmore_text',
			__( 'Chữ nút (đang thu gọn)', 'themeranghammatsg' ),
			'text',
			array(
				'placeholder'       => __( 'Xem thêm', 'themeranghammatsg' ),
				'instructions'      => __( 'Trống = "Xem thêm".', 'themeranghammatsg' ),
				'wrapper'           => array( 'width' => 25 ),
				'conditional_logic' => array( array( array( 'field' => 'field_rhm_landing_' . $prefix . '_readmore', 'operator' => '==', 'value' => '1' ) ) ),
			)
		),
		rhm_landing_field(
			$prefix,
			'readmore_less_text',
			__( 'Chữ nút (đang mở)', 'themeranghammatsg' ),
			'text',
			array(
				'placeholder'       => __( 'Thu gọn', 'themeranghammatsg' ),
				'instructions'      => __( 'Trống = "Thu gọn".', 'themeranghammatsg' ),
				'wrapper'           => array( 'width' => 25 ),
				'conditional_logic' => array( array( array( 'field' => 'field_rhm_landing_' . $prefix . '_readmore', 'operator' => '==', 'value' => '1' ) ) ),
			)
		),
		rhm_landing_image_field(
			$prefix,
			'image',
			__( 'Ảnh lớn bên dưới — khuyến nghị rộng 1600px', 'themeranghammatsg' ),
			array( 'instructions' => __( 'Rộng hết khung nội dung (tối đa 1248px), giữ nguyên tỉ lệ ảnh, không cắt. Ảnh ngang (vd 1600 × 700 hoặc 1600 × 900) hiển thị đẹp nhất. Nhập "Văn bản thay thế".', 'themeranghammatsg' ) )
		),
	);
}
