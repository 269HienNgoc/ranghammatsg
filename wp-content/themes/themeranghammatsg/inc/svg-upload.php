<?php
/**
 * Cho phép upload SVG (icon) — chỉ tài khoản quản trị (manage_options).
 *
 * An toàn:
 * - SVG có thể chứa mã độc → từ chối file có <script>, thuộc tính sự kiện on…=, javascript:, <foreignObject>,
 *   tham chiếu tài nguyên ngoài (xlink:href / href tới http) hoặc khai báo DOCTYPE/ENTITY.
 * - Theme luôn hiển thị SVG bằng <img> (không nhúng inline) → mã trong SVG không chạy trong trang.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Thêm svg vào danh sách định dạng được upload (chỉ admin).
 */
function rhm_svg_upload_mimes( array $mimes ): array {
	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}
	return $mimes;
}
add_filter( 'upload_mimes', 'rhm_svg_upload_mimes' );

/**
 * WordPress đoán sai MIME của SVG (vd text/plain, image/svg) → xác nhận lại theo phần mở rộng.
 */
function rhm_svg_check_filetype( array $data, string $file, string $filename ): array {
	if ( current_user_can( 'manage_options' ) && 'svg' === strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) ) ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}
	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'rhm_svg_check_filetype', 10, 3 );

/**
 * Từ chối SVG chứa nội dung nguy hiểm trước khi lưu.
 */
function rhm_svg_reject_unsafe( array $file ): array {
	if ( 'svg' !== strtolower( pathinfo( (string) $file['name'], PATHINFO_EXTENSION ) ) ) {
		return $file;
	}

	$content = (string) file_get_contents( $file['tmp_name'] ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local upload tmp file.
	$unsafe  = '/<script|<foreignObject|<!DOCTYPE|<!ENTITY|\son[a-z]+\s*=|javascript:|(?:xlink:)?href\s*=\s*["\']\s*(?:https?:|data:|\/\/)/i';

	if ( ! str_contains( $content, '<svg' ) || preg_match( $unsafe, $content ) ) {
		$file['error'] = __( 'File SVG không hợp lệ hoặc chứa mã không an toàn (script, sự kiện, liên kết ngoài). Hãy xuất lại SVG chỉ gồm hình vẽ.', 'themeranghammatsg' );
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'rhm_svg_reject_unsafe' );
add_filter( 'wp_handle_sideload_prefilter', 'rhm_svg_reject_unsafe' );
