<?php
/**
 * Trang danh mục (category.php): trang 1 có 1 bài nổi bật + lưới, các trang sau chỉ có lưới.
 *
 * Lưới luôn đủ hàng (12 chia hết cho 2, 3 và 4 cột): trang 1 lấy RHM_CATEGORY_GRID + 1 bài (bài đầu làm nổi bật),
 * trang n lấy RHM_CATEGORY_GRID bài, bỏ qua bài nổi bật và các trang trước.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_CATEGORY_GRID = 12;

function rhm_category_is_main( WP_Query $query ): bool {
	return ! is_admin() && $query->is_main_query() && $query->is_category();
}

function rhm_category_query( WP_Query $query ): void {
	if ( ! rhm_category_is_main( $query ) ) {
		return;
	}

	$paged = max( 1, (int) $query->get( 'paged' ) );
	$query->set( 'posts_per_page', 1 === $paged ? RHM_CATEGORY_GRID + 1 : RHM_CATEGORY_GRID );
	if ( $paged > 1 ) {
		$query->set( 'offset', 1 + ( $paged - 1 ) * RHM_CATEGORY_GRID );
	}
}
add_action( 'pre_get_posts', 'rhm_category_query' );

/**
 * Số trang tính theo lưới: bỏ bài nổi bật khỏi tổng rồi chia RHM_CATEGORY_GRID.
 * WordPress chia found_posts cho posts_per_page của trang đang xem (13 hoặc 12) nên tính lại ở
 * the_posts, hook chạy sau bước đó. found_posts giữ nguyên số bài thật.
 */
function rhm_category_pages( array $posts, WP_Query $query ): array {
	if ( rhm_category_is_main( $query ) && $query->found_posts > 0 ) {
		$query->max_num_pages = max( 1, (int) ceil( ( $query->found_posts - 1 ) / RHM_CATEGORY_GRID ) );
	}
	return $posts;
}
add_filter( 'the_posts', 'rhm_category_pages', 10, 2 );
