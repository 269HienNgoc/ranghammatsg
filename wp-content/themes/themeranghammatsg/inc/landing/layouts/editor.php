<?php
/**
 * Layout "Nội dung trình soạn thảo": in nội dung soạn ở ô Classic Editor của trang. Hiển thị: template-parts/landing/editor.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

function rhm_landing_layout_editor_fields( string $prefix ): array {
	return array(
		rhm_landing_field(
			$prefix,
			'note',
			'',
			'message',
			array( 'message' => __( 'Section này in nội dung soạn ở ô trình soạn thảo phía trên (bảng, ảnh, shortcode…). Chỉ nên thêm 1 lần mỗi trang.', 'themeranghammatsg' ) )
		),
		rhm_landing_field(
			$prefix,
			'narrow',
			__( 'Cột chữ hẹp (868px, dễ đọc)', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'default_value' => 1,
			)
		),
	);
}
