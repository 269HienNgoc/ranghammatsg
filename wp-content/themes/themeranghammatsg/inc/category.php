<?php
/**
 * Trang danh mục (category.php): trang 1 có 1 bài nổi bật + lưới, các trang sau chỉ có lưới.
 * Kèm dữ liệu thanh lọc chuyên mục con: rhm_get_category_filter() (cuối file).
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

/**
 * Thanh lọc chuyên mục con (template-parts/components/category-filter.php).
 * - Chuyên mục đang xem có con → "Tất cả" (chính nó, đang chọn) + các con.
 * - Chuyên mục con (không có con) → "Tất cả" (chuyên mục cha) + các con của cha, mục đang xem được chọn.
 * - Không có con / cha → mảng rỗng (không hiện thanh lọc).
 * Mục đang chọn luôn đưa lên vị trí đầu danh sách con nếu nằm sau 2 mục đầu, để không bị giấu vào nút "…"
 * (tablet chỉ hiện 2 mục con).
 *
 * @return array{all?:array{name:string, url:string, active:bool}, items?:array<int, array{name:string, url:string, active:bool}>}
 */
function rhm_get_category_filter(): array {
	static $filter = null; // Gọi 2 lần mỗi trang (tiêu đề mobile + thanh lọc) → chỉ truy vấn 1 lần.
	if ( null !== $filter ) {
		return $filter;
	}
	$filter = array();

	$current = get_queried_object();
	if ( ! $current instanceof WP_Term || 'category' !== $current->taxonomy ) {
		return array();
	}

	$children = static fn( int $parent ): array => get_terms(
		array(
			'taxonomy'   => 'category',
			'parent'     => $parent,
			'hide_empty' => false, // Hiện cả chuyên mục con chưa có bài.
		)
	);

	$root  = $current;
	$terms = $children( $current->term_id );
	if ( ! $terms && $current->parent ) {
		$root  = get_term( $current->parent, 'category' );
		$terms = $root instanceof WP_Term ? $children( $root->term_id ) : array();
	}
	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	$items = array_map(
		static fn( WP_Term $term ): array => array(
			'name'   => $term->name,
			'url'    => (string) get_term_link( $term ),
			'active' => $term->term_id === $current->term_id,
		),
		$terms
	);

	$active = array_search( true, array_column( $items, 'active' ), true );
	if ( false !== $active && $active >= 2 ) {
		array_unshift( $items, ...array_splice( $items, $active, 1 ) );
	}

	$filter = array(
		'all'   => array(
			'name'   => __( 'Tất cả', 'themeranghammatsg' ),
			'url'    => (string) get_term_link( $root ),
			'active' => $root->term_id === $current->term_id,
		),
		'items' => $items,
	);

	return $filter;
}
