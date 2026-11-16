<?php
/**
 * Layout "Ảnh lớn + slider tab": tiêu đề H2 canh giữa → 1 bên ảnh lớn (trái/phải), 1 bên thẻ slider có hàng tab;
 * mỗi slide = ảnh tròn (trái/phải) + tiêu đề H3 + danh sách tích + nút Tư vấn (mở popup đặt lịch).
 * Hiển thị: template-parts/landing/tab-slider.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_tab_slider_fields( string $prefix ): array {
	$item     = $prefix . '_items';
	$position = static fn( string $p, string $label, string $note, int $width ): array => rhm_landing_field(
		$p,
		'image_position',
		$label,
		'button_group',
		array(
			'choices'       => array(
				'left'  => __( 'Trái', 'themeranghammatsg' ),
				'right' => __( 'Phải', 'themeranghammatsg' ),
			),
			'default_value' => 'left',
			'instructions'  => $note,
			'wrapper'       => array( 'width' => $width ),
		)
	);

	return array(
		rhm_landing_field( $prefix, 'title', __( 'Tiêu đề (H2)', 'themeranghammatsg' ), 'text', array( 'placeholder' => 'Các loại dán sứ Veneer trên thị trường' ) ),
		rhm_landing_rich_field( $prefix, 'description', __( 'Mô tả', 'themeranghammatsg' ), array( 'instructions' => __( 'Tùy chọn, hiện canh giữa dưới tiêu đề.', 'themeranghammatsg' ) ) ),
		rhm_landing_image_field( $prefix, 'image', __( 'Ảnh lớn — khuyến nghị ảnh dọc khoảng 800 × 1000', 'themeranghammatsg' ), array( 'wrapper' => array( 'width' => 40 ), 'instructions' => __( 'Máy tính: ảnh được cắt cho cao bằng khung slider (để chủ thể ở giữa, không để chữ trong ảnh). Mobile: hiện đủ ảnh, nằm trên slider. Trống → slider rộng hết khung. Nhập "Văn bản thay thế".', 'themeranghammatsg' ) ) ),
		$position( $prefix, __( 'Vị trí ảnh lớn (desktop)', 'themeranghammatsg' ), __( 'Mobile ảnh luôn nằm trên slider.', 'themeranghammatsg' ), 30 ),
		rhm_landing_field( $prefix, 'button_text', __( 'Chữ nút', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 30 ), 'placeholder' => 'Tư vấn', 'instructions' => __( 'Nút mở popup đặt lịch. Trống → "Tư vấn".', 'themeranghammatsg' ) ) ),
		rhm_landing_field(
			$prefix,
			'items',
			__( 'Slide', 'themeranghammatsg' ),
			'repeater',
			array(
				'max'          => 8,
				'layout'       => 'block',
				'collapsed'    => 'field_rhm_landing_' . $item . '_title',
				'button_label' => __( 'Thêm slide', 'themeranghammatsg' ),
				'instructions' => __( 'Mỗi slide = 1 tab. Slide thiếu tiêu đề hoặc danh sách sẽ bị bỏ qua. Từ 2 slide: có hàng tab + nút trái/phải, mobile vuốt ngang.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					rhm_landing_field( $item, 'tab', __( 'Nhãn tab', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 35 ), 'placeholder' => 'Veneer Emax', 'instructions' => __( 'Trống → dùng tiêu đề.', 'themeranghammatsg' ) ) ),
					rhm_landing_field( $item, 'title', __( 'Tiêu đề (H3)', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 65 ), 'placeholder' => 'Veneer Emax' ) ),
					rhm_landing_image_field( $item, 'image', __( 'Ảnh tròn — khuyến nghị vuông 600 × 600', 'themeranghammatsg' ), array( 'preview_size' => 'thumbnail', 'wrapper' => array( 'width' => 35 ), 'instructions' => __( 'Cắt thành hình tròn → để chủ thể ở giữa ảnh.', 'themeranghammatsg' ) ) ),
					$position( $item, __( 'Vị trí ảnh tròn', 'themeranghammatsg' ), __( 'Mobile ảnh luôn ở trên.', 'themeranghammatsg' ), 25 ),
					rhm_landing_field( $item, 'points', __( 'Danh sách (mỗi dòng 1 ý)', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'wrapper' => array( 'width' => 100 ), 'placeholder' => "Được chế tác từ gốm thủy tinh Lithium Disilicate.\nĐộ trong mờ tự nhiên." ) ),
				),
			)
		),
	);
}
