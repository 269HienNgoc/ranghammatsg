<?php
/**
 * Layout "Bảng giá": tiêu đề (H2) + mô tả → bảng (cột tên dịch vụ + 1–6 cột giá + cột nút Tư vấn) → ghi chú.
 * Mỗi ô giá = giá + giá gốc (gạch ngang, tùy chọn). Bảng ≥ 4 cột (tên + giá + nút) có thể hiển thị dạng thẻ trên mobile
 * (công tắc mobile_cards). Nền hàng tiêu đề cột chỉnh được (head_bg_*, có gradient). Hiển thị: template-parts/landing/price-table.php, CSS thẻ: .price-table--cards (page-about.css).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_price_table_fields( string $prefix ): array {
	$column = $prefix . '_columns';
	$row    = $prefix . '_rows';
	$cell   = $row . '_cells';
	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 60 ), 'placeholder' => 'Bảng giá trồng răng Implant' ) ),
		rhm_landing_field( $prefix, 'eyebrow', __( 'Dòng nhỏ trên tiêu đề', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 40 ) ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả (tùy chọn)', 'themeranghammatsg' ) ),
		rhm_landing_field(
			$prefix,
			'first_column',
			__( 'Tiêu đề cột tên dịch vụ', 'themeranghammatsg' ),
			'text',
			array(
				'wrapper'     => array( 'width' => 40 ),
				'placeholder' => 'Trụ Implant',
			)
		),
		rhm_landing_field(
			$prefix,
			'columns',
			__( 'Cột giá', 'themeranghammatsg' ),
			'repeater',
			array(
				'min'          => 1,
				'max'          => 6,
				'layout'       => 'table',
				'button_label' => __( 'Thêm cột giá', 'themeranghammatsg' ),
				'wrapper'      => array( 'width' => 60 ),
				'instructions' => __( 'Kéo để đổi thứ tự cột. Cột không có tiêu đề bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $column, 'title', __( 'Tiêu đề cột', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Trụ lẻ' ) ),
					rhm_landing_field( $column, 'note', __( 'Dòng phụ', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Đơn vị: VNĐ' ) ),
				),
			)
		),
		// Nền hàng tiêu đề cột (+ thanh đầu/cuối thẻ mobile): cùng bộ chọn với Nền section, field head_bg_*.
		...rhm_landing_background_fields( $prefix, 'head_bg', __( 'Nền tiêu đề cột', 'themeranghammatsg' ), true ),
		rhm_landing_field(
			$prefix,
			'rows',
			__( 'Dòng bảng giá', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 40,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $row . '_name',
				'button_label' => __( 'Thêm dòng', 'themeranghammatsg' ),
				'instructions' => __( 'Mỗi dòng = 1 dịch vụ. Dòng không có tên bị bỏ qua.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					// Tên + ô giá là trình soạn thảo (2026-11-25, content cần xuống dòng / danh sách): toolbar có danh sách, TinyMCE chỉ
					// khởi tạo khi bấm vào ô (delay) → 40 dòng × 6 ô vẫn nhẹ. Dữ liệu chữ thuần cũ vẫn đọc được (ACF tự bọc <p>).
					rhm_landing_rich_field( $row, 'name', __( 'Tên dịch vụ', 'themeranghammatsg' ), array( 'toolbar' => 'rhm_content' ) ),
					rhm_landing_field(
						$row,
						'cells',
						__( 'Giá theo cột', 'themeranghammatsg' ),
						'repeater',
						array(
							'max'          => 6,
							'layout'       => 'block',
							'button_label' => __( 'Thêm ô giá', 'themeranghammatsg' ),
							'instructions' => __( 'Ô thứ 1 = cột giá thứ 1, ô thứ 2 = cột giá thứ 2… Cột không có giá: để trống ô đó (vẫn giữ ô để các ô sau đúng cột). Ô "Giá / nội dung ô" là trình soạn thảo: giá (vd 15.000.000, Liên hệ) hoặc nội dung dài (mô tả, ưu điểm…) — Enter = đoạn mới, Shift + Enter = xuống dòng, dùng danh sách cho các ưu điểm. Cột có ô dài (trên 40 ký tự), nhiều dòng hoặc danh sách → cả cột canh trái.', 'themeranghammatsg' ),
							'sub_fields'   => array(
								rhm_landing_rich_field( $cell, 'price', __( 'Giá / nội dung ô', 'themeranghammatsg' ), array( 'toolbar' => 'rhm_content', 'wrapper' => array( 'width' => 70 ) ) ),
								rhm_landing_field( $cell, 'old_price', __( 'Giá gốc (gạch ngang, tùy chọn)', 'themeranghammatsg' ), 'text', array( 'placeholder' => '130.000.000', 'wrapper' => array( 'width' => 30 ) ) ),
							),
						)
					),
				),
			)
		),
		rhm_landing_field(
			$prefix,
			'button',
			__( 'Cột nút Tư vấn', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'ui_on_text'    => __( 'Hiện', 'themeranghammatsg' ),
				'ui_off_text'   => __( 'Ẩn', 'themeranghammatsg' ),
				'default_value' => 1,
				'wrapper'       => array( 'width' => 25 ),
				'instructions'  => __( 'Nút mở popup đặt lịch.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'button_text',
			__( 'Chữ nút', 'themeranghammatsg' ),
			'text',
			array(
				'wrapper'           => array( 'width' => 25 ),
				'placeholder'       => __( 'Tư vấn', 'themeranghammatsg' ),
				'conditional_logic' => array( array( array( 'field' => 'field_rhm_landing_' . $prefix . '_button', 'operator' => '==', 'value' => '1' ) ) ),
			)
		),
		rhm_landing_field(
			$prefix,
			'mobile_cards',
			__( 'Mobile dạng thẻ', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'ui_on_text'    => __( 'Bật', 'themeranghammatsg' ),
				'ui_off_text'   => __( 'Tắt', 'themeranghammatsg' ),
				'default_value' => 1,
				'wrapper'       => array( 'width' => 50 ),
				'instructions'  => __( 'Chỉ áp dụng khi bảng từ 4 cột trở lên (tính cột tên + cột giá + cột nút). Bật: dưới 768px mỗi dòng thành 1 thẻ. Tắt: bảng cuộn ngang.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_rich_field( $prefix, 'note', __( 'Ghi chú dưới bảng (tùy chọn)', 'themeranghammatsg' ) ),
	);
}
