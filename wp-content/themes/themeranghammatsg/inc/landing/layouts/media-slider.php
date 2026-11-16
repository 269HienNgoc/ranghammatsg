<?php
/**
 * Layout "Ảnh slider + chữ (nhiều item)": tiêu đề section (H2) + mô tả, rồi danh sách item; mỗi item = thẻ có slider ảnh
 * (nút ‹ › + vuốt) + tiêu đề (H3) + nội dung. Vị trí ảnh (trái / phải / trên / dưới) và số cột chung cho cả section.
 * Mobile (< 640px) luôn ảnh trên, chữ dưới. Hiển thị: template-parts/landing/media-slider.php.
 * Bản dạng ô trước đó (text/images ở cấp section) không còn được đọc: section cũ phải nhập lại ảnh + nội dung vào Item.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_media_slider_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề section (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ), 'placeholder' => 'Trang thiết bị hiện đại' ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ), array( 'instructions' => __( 'Hiện canh giữa dưới tiêu đề section.', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'columns',
			__( 'Số cột (máy tính)', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					'1' => __( '1 cột', 'themeranghammatsg' ),
					'2' => __( '2 cột', 'themeranghammatsg' ),
					'3' => __( '3 cột', 'themeranghammatsg' ),
				),
				'default_value' => '1',
				'wrapper'       => array( 'width' => 50 ),
				'instructions'  => __( 'Từ 1024px. Dưới 1024px: 1 item / hàng. 3 cột thẻ hẹp → nên chọn ảnh Trên/Dưới.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'image_position',
			__( 'Vị trí ảnh (mọi item)', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					'left'   => __( 'Trái', 'themeranghammatsg' ),
					'right'  => __( 'Phải', 'themeranghammatsg' ),
					'top'    => __( 'Trên', 'themeranghammatsg' ),
					'bottom' => __( 'Dưới', 'themeranghammatsg' ),
				),
				'default_value' => 'right',
				'wrapper'       => array( 'width' => 50 ),
				'instructions'  => __( 'Áp dụng từ màn hình ≥ 640px. Mobile luôn: ảnh trên, chữ dưới.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Item', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 12,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_title',
				'button_label' => __( 'Thêm item', 'themeranghammatsg' ),
				'instructions' => __( 'Mỗi item = 1 thẻ: slider ảnh + tiêu đề + nội dung. Kéo để sắp xếp. Item không có ảnh lẫn chữ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field(
						$item,
						'images',
						__( 'Ảnh slider — khuyến nghị 1200 × 900 (4:3)', 'themeranghammatsg' ),
						'gallery',
						array(
							'return_format' => 'id',
							'preview_size'  => 'thumbnail',
							'max'           => 12,
							'mime_types'    => 'jpg,jpeg,png,webp,avif',
							'instructions'  => __( 'Chọn nhiều ảnh, kéo thả để sắp thứ tự. Từ 2 ảnh: có nút trái/phải, mobile vuốt ngang. Khung 4:3, ảnh khác tỉ lệ bị cắt quanh tâm → để chủ thể ở giữa ảnh. Nhập "Văn bản thay thế".', 'themeranghammatsg' ),
						)
					),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề item (H3)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Máy chụp X-quang Vatech' ) ),
					rhm_landing_rich_field( $item, 'text', __( 'Nội dung', 'themeranghammatsg' ) ),
				),
			)
		),
	);
}
