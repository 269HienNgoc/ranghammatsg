<?php
/**
 * Layout "Chặng đường phát triển" (timeline ngang): tiêu đề + mô tả → hàng năm → slider nội dung, mỗi năm 1 slide gồm
 * tiêu đề (H3), nội dung và ảnh (≤ 3 ảnh: lưới; > 3 ảnh: slider). Hiển thị: template-parts/landing/timeline.php.
 * Bản cũ chỉ có year / title / text → vẫn đọc được (key/name giữ nguyên).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_timeline_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ), array( 'instructions' => __( 'Đoạn chữ nhỏ canh giữa dưới tiêu đề.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Các mốc', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 20,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_year',
				'button_label' => __( 'Thêm mốc', 'themeranghammatsg' ),
				'instructions' => __( 'Sắp theo thứ tự thời gian; mốc thiếu năm hoặc tiêu đề sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'year', __( 'Năm / thời điểm', 'themeranghammatsg' ), 'text', array( 'placeholder' => '2015', 'instructions' => __( 'Hiện trên hàng năm nằm ngang → nên ngắn (vd 2014, 2015 - 2022).', 'themeranghammatsg' ), 'wrapper' => array( 'width' => 30 ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề mốc (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 70 ) ) ),
					rhm_landing_rich_field( $item, 'text', __( 'Mô tả', 'themeranghammatsg' ) ),
					rhm_landing_field(
						$item,
						'images',
						__( 'Ảnh (tùy chọn) — khuyến nghị 1200 × 900 (4:3)', 'themeranghammatsg' ),
						'gallery',
						array(
							'return_format' => 'id',
							'preview_size'  => 'thumbnail',
							'max'           => 12,
							'mime_types'    => 'jpg,jpeg,png,webp,avif',
							'instructions'  => __( 'Kéo thả để sắp thứ tự. 1–2 ảnh: 2 cột; 3 ảnh: 3 cột; từ 4 ảnh: tự chuyển thành slider (nút trái/phải, mobile vuốt ngang). Khung 4:3, ảnh khác tỉ lệ bị cắt quanh tâm. Chú thích dưới ảnh = ô "Chú thích" của ảnh (bấm ảnh để sửa; trống thì không hiện). Nhập "Văn bản thay thế".', 'themeranghammatsg' ),
						)
					),
				),
			)
		),
	);
}
