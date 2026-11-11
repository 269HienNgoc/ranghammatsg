<?php
/**
 * Danh sách động trong Customizer: nút "Thêm mới" / "Thêm nhiều ảnh", sắp xếp ↑↓, xóa — không giới hạn số mục.
 * Khác rhm_customize_repeater() (số mục cố định N): dùng cho nội dung thay đổi số lượng (chứng nhận, bác sĩ…).
 *
 * Lưu: 1 theme_mod dạng chuỗi JSON [{ field: value, … }]. Đọc: rhm_get_list( $id ).
 * Giao diện control: inc/class-rhm-customize-list-control.php + assets/admin/customize-list.{js,css}.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Giải mã giá trị danh sách (chuỗi JSON hoặc mảng) → mảng các mục (mỗi mục là mảng).
 */
function rhm_decode_list( mixed $value ): array {
	$items = is_string( $value ) ? json_decode( $value, true ) : $value;
	return is_array( $items ) ? array_values( array_filter( $items, 'is_array' ) ) : array();
}

/**
 * Các mục của 1 danh sách Customizer (đã sanitize khi lưu).
 */
function rhm_get_list( string $id ): array {
	return rhm_decode_list( get_theme_mod( $id, '[]' ) );
}

/**
 * Sanitize theo kiểu field; chỉ giữ field đã khai báo. Giữ cả mục trống (vừa bấm "Thêm mới") —
 * phần hiển thị tự bỏ qua mục thiếu dữ liệu.
 *
 * @param array<string, array{0:string, 1:string, 2?:array}> $fields
 */
function rhm_sanitize_list( mixed $value, array $fields ): string {
	$clean = array();

	foreach ( rhm_decode_list( $value ) as $item ) {
		$row = array();
		foreach ( $fields as $key => $field ) {
			$type        = $field[1];
			$raw         = $item[ $key ] ?? '';
			$row[ $key ] = match ( $type ) {
				'image'    => absint( $raw ),
				'url'      => esc_url_raw( (string) $raw ),
				'textarea' => sanitize_textarea_field( (string) $raw ),
				// Chỉ nhận giá trị có trong danh sách lựa chọn (vd ID chi nhánh đã xuất bản); còn lại = chưa chọn.
				'select'   => array_key_exists( (string) $raw, (array) ( $field[2] ?? array() ) ) ? (string) $raw : '',
				default    => sanitize_text_field( (string) $raw ),
			};
		}
		$clean[] = $row;
	}

	return (string) wp_json_encode( $clean, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

/**
 * Thêm setting + control danh sách động.
 *
 * @param array<string, array{0:string, 1:string, 2?:array}> $fields field => [nhãn, 'image'|'text'|'textarea'|'url'|'select', choices].
 *                                                         'select': choices = [ giá trị => nhãn ] (ô thả xuống, theo đúng thứ tự mảng).
 *                                                         Field 'text' đầu tiên được điền sẵn từ alt/tiêu đề ảnh khi "Thêm nhiều ảnh".
 */
function rhm_customize_list( WP_Customize_Manager $wp_customize, string $section, string $id, string $label, string $item_label, array $fields, string $description = '' ): void {
	require_once RHM_THEME_DIR . '/inc/class-rhm-customize-list-control.php';

	$wp_customize->add_setting(
		$id,
		array(
			'type'              => 'theme_mod',
			'default'           => '[]',
			'sanitize_callback' => static fn( $value ): string => rhm_sanitize_list( $value, $fields ),
		)
	);

	$wp_customize->add_control(
		new RHM_Customize_List_Control(
			$wp_customize,
			$id,
			array(
				'label'       => $label,
				'description' => $description,
				'section'     => $section,
				'fields'      => $fields,
				'item_label'  => $item_label,
			)
		)
	);
}
