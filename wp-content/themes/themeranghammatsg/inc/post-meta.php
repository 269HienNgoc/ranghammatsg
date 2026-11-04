<?php
/**
 * Dòng thông tin dưới H1 banner bài viết (template-parts/components/page-banner.php):
 * tác giả · ngày đăng · ngày cập nhật · thời gian đọc — bật/tắt từng mục ở Customizer → "Bài viết".
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Mục hiển thị mặc định (dùng chung cho Customizer và giao diện). Setting: rhm_post_meta_{khóa}. */
const RHM_POST_META_DEFAULTS = array(
	'author'  => true,
	'date'    => true,
	'updated' => true, // Chỉ hiện khi bài được sửa vào ngày khác ngày đăng.
	'reading' => true,
);

/**
 * Mục được tích chọn hiển thị.
 *
 * @return array{author:bool, date:bool, updated:bool, reading:bool}
 */
function rhm_post_meta_show(): array {
	$show = array();
	foreach ( RHM_POST_META_DEFAULTS as $key => $default ) {
		$show[ $key ] = (bool) get_theme_mod( "rhm_post_meta_{$key}", $default );
	}
	return $show;
}
