<?php
/**
 * Layout "Danh sách dịch vụ": thẻ ảnh + tên (có link). Hiển thị: template-parts/landing/services.php.
 * Số dịch vụ mỗi hàng desktop (columns: Tự động | 3 | 4 | 5) + chế độ slider (slider) — 2026-11-25.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_services_fields( string $prefix ): array {
	$item = $prefix . '_items';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Đa dạng dịch vụ nha khoa', 'themeranghammatsg' ) ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả ngắn', 'themeranghammatsg' ) ),
		rhm_landing_field(
			$prefix,
			'columns',
			__( 'Số dịch vụ mỗi hàng (máy tính)', 'themeranghammatsg' ),
			'button_group',
			array(
				'choices'       => array(
					''  => __( 'Tự động', 'themeranghammatsg' ),
					'3' => '3',
					'4' => '4',
					'5' => '5',
				),
				'default_value' => '',
				'allow_null'    => 0,
				'wrapper'       => array( 'width' => 60 ),
				'instructions'  => __( 'Tự động: không để lẻ 1 dịch vụ ở hàng cuối (6 → 3 / hàng, 7–8 → 4, 9 → 3, 10 → 5, 11–12 → 4). Chọn tay: dư thì xuống hàng mới. Mobile/tablet luôn là 1 hàng vuốt ngang.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'slider',
			__( 'Hiển thị dạng slider', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'default_value' => 0,
				'wrapper'       => array( 'width' => 40 ),
				'instructions'  => __( 'Bật: 1 hàng trượt ngang, số thẻ mỗi khung = số dịch vụ mỗi hàng, có nút trái/phải. Ít dịch vụ hơn số cột thì vẫn hiện dạng lưới.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Dịch vụ', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 12,
				'layout'       => 'table',
				'button_label' => __( 'Thêm dịch vụ', 'themeranghammatsg' ),
				'instructions' => __( 'Tối đa 12 dịch vụ. Số cột / slider chọn ở trên. Khung ảnh 4:3 cắt bớt ảnh dọc / vuông: dùng ô "Vị trí ảnh" để chọn phần giữ lại. Ảnh khung 4:3 (khuyến nghị 800 × 600), cắt giữa. Dòng thiếu ảnh hoặc tên sẽ bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_image_field( $item, 'image', __( 'Ảnh', 'themeranghammatsg' ), array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 20 ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tên dịch vụ', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 25 ) ) ),
					rhm_landing_field( $item, 'link', __( 'Link', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 30 ), 'placeholder' => 'https://…/trong-rang-implant/' ) ),
					// Phần ảnh giữ lại khi khung 4:3 cắt bớt (object-position): ảnh người dọc / vuông → Trên để không mất đầu.
					rhm_landing_field(
						$item,
						'position',
						__( 'Vị trí ảnh trong khung', 'themeranghammatsg' ),
						'button_group',
						array(
							'choices'       => array(
								''       => __( 'Giữa', 'themeranghammatsg' ),
								'top'    => __( 'Trên', 'themeranghammatsg' ),
								'bottom' => __( 'Dưới', 'themeranghammatsg' ),
							),
							'default_value' => '',
							'allow_null'    => 0,
							'instructions'  => __( 'Ảnh người (dọc/vuông): chọn Trên để không mất đầu.', 'themeranghammatsg' ),
							'wrapper'       => array( 'width' => 25 ),
						)
					),
				),
			)
		),
	);
}
