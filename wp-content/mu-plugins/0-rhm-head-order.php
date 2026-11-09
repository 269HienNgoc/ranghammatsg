<?php
/**
 * Plugin Name: RHM – Thứ tự <head>
 * Description: Đưa khối Yoast SEO (title, meta, og, JSON-LD) lên trên <meta id="swift3-viewport"> + observer script mà Swift Performance AI chèn ngay sau <head>.
 * Author:      Aimedis
 *
 * Vì sao là mu-plugin: Swift chèn bằng output buffer mở lúc plugin nạp (Swift3_Optimizer::early_nodes), sau đó không có filter.
 * Buffer mở trong mu-plugin (nạp trước mọi plugin) nằm ngoài cùng → chạy sau cùng, kể cả khi Swift trả trang từ cache
 * bằng PHP (readfile + die, chế độ Cloudflare proxy cache). Không sửa code của Swift, chỉ sắp lại HTML cuối.
 * Tên file bắt đầu bằng "0-": WordPress nạp mu-plugin theo thứ tự tên (sort), và tính năng Code Optimizer của Swift tạo
 * mu-plugin __swift3-loader.php nạp Swift sớm. "0" < "_" → file này nạp TRƯỚC Swift, buffer của nó mới là buffer ngoài cùng.
 * Đừng đổi tên thành chữ cái (vd rhm-…): Swift sẽ nạp trước và thứ tự quay về như cũ.
 * File nằm ngoài theme → deploy riêng vào wp-content/mu-plugins/.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Chuyển khối Yoast SEO lên ngay trước <meta id="swift3-viewport">. Không đủ 2 mốc hoặc regex lỗi → trả nguyên HTML.
 */
function rhm_head_order_move_yoast( string $html ): string {
	$viewport = '<meta id="swift3-viewport"';
	$position = strpos( $html, $viewport );
	if ( false === $position || ! preg_match( '~<!-- This site is optimized with the Yoast SEO.*?<!-- / Yoast SEO[^>]*-->~s', $html, $match, PREG_OFFSET_CAPTURE ) ) {
		return $html;
	}

	[ $block, $offset ] = $match[0];
	if ( $offset < $position ) {
		return $html; // Đã nằm trên.
	}

	$html = substr_replace( $html, '', $offset, strlen( $block ) );
	return substr_replace( $html, $block . "\n", $position, 0 );
}

/**
 * Callback output buffer: chỉ xử lý trang front-end.
 */
function rhm_head_order_buffer( string $html ): string {
	if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || defined( 'REST_REQUEST' ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
		return $html;
	}
	return rhm_head_order_move_yoast( $html );
}

ob_start( 'rhm_head_order_buffer' );
