<?php
/**
 * Gỡ CSS / JS thừa WordPress tự in ra ở frontend (site dùng Classic Editor, theme tự có CSS riêng).
 *
 * - Emoji: <style id="wp-emoji-styles-inline-css">, <script id="wp-emoji-settings"> + script module loader nội tuyến
 *   (tải wp-emoji-release.min.js và ảnh emoji từ s.w.org). Trình duyệt hiện đại tự vẽ emoji, không cần.
 * - Global styles (theme.json): <style id="global-styles-inline-css"> ~9 kB biến --wp--preset--* — theme không dùng.
 *   Core gắn wp_enqueue_global_styles() ở CẢ wp_enqueue_scripts và wp_footer (rồi tự đẩy lên <head>) → phải gỡ cả 2 hook,
 *   chỉ wp_dequeue_style( 'global-styles' ) là không đủ.
 * - CSS block library / classic theme styles (block editor đã tắt trong functions.php).
 * - jQuery: bỏ jquery-migrate (Fluent Forms chỉ gọi API còn trong jQuery 3.7: jQuery.trim, jQuery.isFunction) + đưa xuống cuối trang.
 *
 * Chỉ ảnh hưởng frontend; emoji trong nội dung vẫn hiển thị bằng font emoji của hệ điều hành.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

// Emoji (wp-includes/default-filters.php).
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_emoji_styles' );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'embed_head', 'print_emoji_detection_script' );
remove_action( 'enqueue_embed_scripts', 'wp_enqueue_emoji_styles' );

// Global styles (theme.json).
remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
remove_action( 'wp_footer', 'wp_enqueue_global_styles', 1 );

// removes WordPress core CSS for auto width images
add_filter('wp_img_tag_add_auto_sizes', '__return_false');

/**
 * CSS của block library / classic theme styles.
 */
function rhm_dequeue_block_styles(): void {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'classic-theme-styles' );
}
add_action( 'wp_enqueue_scripts', 'rhm_dequeue_block_styles', 100 );

/**
 * jQuery ở frontend (admin giữ nguyên cho plugin cũ):
 * - bỏ jquery-migrate khỏi phụ thuộc của "jquery";
 * - đưa jquery xuống cuối trang (group 1). Fluent Forms tự enqueue jQuery ở <head> khi phát hiện shortcode trong nội dung
 *   → jQuery chặn hiển thị và không hoãn được (inc/plugin/fluentform.php). WordPress vẫn tự in jQuery ở <head> nếu có script <head>
 *   nào phụ thuộc nó, nên không làm hỏng plugin khác.
 */
function rhm_frontend_jquery( WP_Scripts $scripts ): void {
	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}
	$scripts->registered['jquery']->deps = array_values( array_diff( $scripts->registered['jquery']->deps, array( 'jquery-migrate' ) ) );
	$scripts->add_data( 'jquery', 'group', 1 );
	$scripts->add_data( 'jquery-core', 'group', 1 );
}
add_action( 'wp_default_scripts', 'rhm_frontend_jquery' );
