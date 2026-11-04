<?php
/**
 * Tắt hoàn toàn bình luận + pingback/trackback (site không dùng bình luận).
 *
 * - Frontend: luôn đóng, ẩn bình luận cũ; không gửi được qua form, REST API hay XML-RPC.
 * - Admin: bỏ menu Bình luận / cài đặt Thảo luận, ô thảo luận khi sửa bài, mục trên thanh admin, widget bình luận.
 * - Bỏ feed bình luận và header X-Pingback.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gỡ hỗ trợ bình luận / trackback khỏi mọi post type (ẩn ô Thảo luận và cột Bình luận trong admin).
 */
function rhm_disable_comments_support(): void {
	foreach ( get_post_types() as $post_type ) {
		remove_post_type_support( $post_type, 'comments' );
		remove_post_type_support( $post_type, 'trackbacks' );
	}
}
add_action( 'init', 'rhm_disable_comments_support', 100 );

// Luôn đóng; ẩn và không đếm bình luận đã có.
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );
add_filter( 'get_comments_number', '__return_zero', 20 );

/**
 * REST API: bỏ endpoint /wp/v2/comments.
 */
function rhm_disable_comments_rest( array $endpoints ): array {
	foreach ( array_keys( $endpoints ) as $route ) {
		if ( str_starts_with( $route, '/wp/v2/comments' ) ) {
			unset( $endpoints[ $route ] );
		}
	}
	return $endpoints;
}
add_filter( 'rest_endpoints', 'rhm_disable_comments_rest' );

/**
 * XML-RPC: bỏ phương thức pingback.
 */
function rhm_disable_pingback_methods( array $methods ): array {
	unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'] );
	return $methods;
}
add_filter( 'xmlrpc_methods', 'rhm_disable_pingback_methods' );

/**
 * Bỏ header X-Pingback.
 */
function rhm_disable_pingback_header( array $headers ): array {
	unset( $headers['X-Pingback'] );
	return $headers;
}
add_filter( 'wp_headers', 'rhm_disable_pingback_header' );

// Không in link feed bình luận trong <head>; truy cập feed bình luận → về trang chủ.
add_filter( 'feed_links_show_comments_feed', '__return_false' );

function rhm_disable_comments_feed(): void {
	if ( is_comment_feed() ) {
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'template_redirect', 'rhm_disable_comments_feed' );

/**
 * Admin: bỏ menu Bình luận và trang cài đặt Thảo luận.
 */
function rhm_disable_comments_admin_menu(): void {
	remove_menu_page( 'edit-comments.php' );
	remove_submenu_page( 'options-general.php', 'options-discussion.php' );
}
add_action( 'admin_menu', 'rhm_disable_comments_admin_menu' );

/**
 * Admin: truy cập thẳng trang Bình luận / Thảo luận → về Bảng tin.
 */
function rhm_disable_comments_admin_pages(): void {
	global $pagenow;
	if ( in_array( $pagenow, array( 'edit-comments.php', 'comment.php', 'options-discussion.php' ), true ) ) {
		wp_safe_redirect( admin_url() );
		exit;
	}
}
add_action( 'admin_init', 'rhm_disable_comments_admin_pages' );

/**
 * Thanh admin: bỏ biểu tượng Bình luận.
 */
function rhm_disable_comments_admin_bar( WP_Admin_Bar $admin_bar ): void {
	$admin_bar->remove_node( 'comments' );
}
add_action( 'admin_bar_menu', 'rhm_disable_comments_admin_bar', 100 );

/**
 * Bỏ widget "Bình luận gần đây".
 */
function rhm_disable_comments_widget(): void {
	unregister_widget( 'WP_Widget_Recent_Comments' );
}
add_action( 'widgets_init', 'rhm_disable_comments_widget' );
