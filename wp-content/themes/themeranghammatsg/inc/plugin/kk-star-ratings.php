<?php
/**
 * Plugin kk-star-ratings (sao đánh giá).
 *
 * Plugin nạp CSS + JS ở MỌI trang. Theme chỉ in sao ở page-banner (template-parts/components/page-banner.php)
 * → chỉ giữ file của plugin khi trang có banner VÀ plugin thực sự trả về markup (cài đặt plugin có thể tắt sao ở 1 số trang).
 * rhm_get_star_rating() luôn tồn tại (trả '' khi plugin tắt) để page-banner gọi an toàn.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Trang có in page-banner (template-parts/components/page-banner.php) hay không.
 * Phải khớp các template gọi page-banner: index.php (Tin tức), search.php, single.php, page.php, tpl-gioi-thieu.php.
 */
function rhm_has_page_banner(): bool {
	return is_home()
		|| is_search()
		|| is_singular( 'post' )
		|| ( is_page() && ! is_front_page() && ! is_page_template( 'page-templates/tpl-lien-he.php' ) );
}

/**
 * Markup sao đánh giá của bài hiện tại (rỗng nếu plugin tắt / trang không có banner).
 * Tính 1 lần mỗi request: wp_head dùng để quyết định nạp CSS/JS, page-banner dùng lại để in.
 */
function rhm_get_star_rating(): string {
	static $markup = null;

	if ( null === $markup ) {
		$markup = function_exists( 'kk_star_ratings' ) && rhm_has_page_banner() ? trim( (string) kk_star_ratings() ) : '';
	}

	return $markup;
}

/**
 * Bỏ CSS/JS của kk-star-ratings ở trang không hiện sao. Chạy sau plugin (ưu tiên 10).
 */
function rhm_star_rating_assets(): void {
	if ( '' === rhm_get_star_rating() ) {
		wp_dequeue_style( 'kk-star-ratings' );
		wp_dequeue_script( 'kk-star-ratings' );
	}
}
add_action( 'wp_enqueue_scripts', 'rhm_star_rating_assets', 20 );
