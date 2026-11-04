<?php
/**
 * Tin tức trang chủ: 2 khối bài viết theo chuyên mục (Customizer → "Tin tức (Trang chủ)").
 * Hiển thị: template-parts/sections/news.php. CSS: page-home.css (@source file này).
 *
 * Hiệu năng:
 * - Mỗi khối 1 truy vấn, không đếm tổng (no_found_rows), nạp sẵn ảnh đại diện 1 lần (update_post_thumbnail_cache).
 * - Ảnh: srcset từ các size rhm-*, sizes = độ rộng hiển thị thật, lazy + decoding async, khung tỉ lệ cố định (không CLS).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_NEWS_MAIN_COUNT = 5; // Khối trái: 1 bài lớn + 4 bài nhỏ.
const RHM_NEWS_SIDE_COUNT = 4; // Khối phải.

/** Thông tin hiển thị mặc định (dùng chung cho Customizer và giao diện). */
const RHM_NEWS_SHOW_DEFAULTS = array(
	'date'     => true,
	'author'   => false,
	'category' => false,
	'excerpt'  => true,
);

/**
 * Tiêu đề mặc định của 2 khối.
 */
function rhm_news_default_titles(): array {
	return array(
		'main' => __( 'Hoạt động và sự kiện', 'themeranghammatsg' ),
		'side' => __( 'Tin tức nổi bật', 'themeranghammatsg' ),
	);
}

/**
 * Thông tin bài viết được tích chọn hiển thị.
 *
 * @return array{date:bool, author:bool, category:bool, excerpt:bool}
 */
function rhm_news_show(): array {
	$show = array();
	foreach ( RHM_NEWS_SHOW_DEFAULTS as $key => $default ) {
		$show[ $key ] = (bool) get_theme_mod( "rhm_news_show_{$key}", $default );
	}
	return $show;
}

/**
 * Dữ liệu 1 khối: tiêu đề, link "Xem tất cả", truy vấn bài viết.
 *
 * @param string $key     'main' | 'side'.
 * @param int[]  $exclude ID bài đã hiện ở khối khác.
 * @return array{title:string, link:string, query:WP_Query}
 */
function rhm_news_block( string $key, int $count, array $exclude = array() ): array {
	$category = absint( get_theme_mod( "rhm_news_{$key}_cat" ) );

	$query = new WP_Query(
		array(
			'post_type'              => 'post',
			'post_status'            => 'publish',
			'cat'                    => $category,
			'posts_per_page'         => $count,
			'post__not_in'           => $exclude,
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_term_cache' => rhm_news_show()['category'],
		)
	);
	update_post_thumbnail_cache( $query );

	$posts_page = (int) get_option( 'page_for_posts' );

	return array(
		'title' => (string) get_theme_mod( "rhm_news_{$key}_title", rhm_news_default_titles()[ $key ] ),
		'link'  => (string) ( $category ? get_category_link( $category ) : ( $posts_page ? get_permalink( $posts_page ) : home_url( '/' ) ) ),
		'query' => $query,
	);
}

/**
 * Ảnh đại diện của bài hiện tại trong khung tỉ lệ 3:2 (bài chưa có ảnh → khung màu nền + icon).
 * alt rỗng: ảnh nằm trong link trùng với link tiêu đề ngay bên cạnh.
 */
function rhm_news_thumbnail( string $sizes ): string {
	$thumbnail_id = (int) get_post_thumbnail_id();

	if ( ! $thumbnail_id ) {
		return '<span class="flex aspect-[3/2] w-full items-center justify-center bg-soft text-primary/30">' . rhm_get_icon( 'tooth', 'size-1/3' ) . '</span>';
	}

	return wp_get_attachment_image(
		$thumbnail_id,
		'full',
		false,
		array(
			'class'    => 'aspect-[3/2] w-full object-cover transition-transform duration-500 group-hover:scale-105',
			'alt'      => '',
			'sizes'    => $sizes,
			'loading'  => 'lazy',
			'decoding' => 'async',
		)
	);
}

/**
 * Dòng thông tin bài viết (ngày / tác giả / chuyên mục) theo tùy chọn hiển thị.
 */
function rhm_news_meta( array $show ): void {
	$items = array();

	if ( $show['date'] ) {
		$items[] = rhm_get_icon( 'calendar', 'size-4 shrink-0' ) . sprintf( '<time datetime="%1$s">%2$s</time>', esc_attr( get_the_date( DATE_W3C ) ), esc_html( get_the_date( 'd/m/Y' ) ) );
	}
	$author = $show['author'] ? get_the_author() : '';
	if ( '' !== $author ) {
		$items[] = rhm_get_icon( 'user', 'size-4 shrink-0' ) . esc_html( $author );
	}
	if ( $show['category'] ) {
		$categories = get_the_category();
		if ( $categories ) {
			$items[] = rhm_get_icon( 'folder', 'size-4 shrink-0' ) . esc_html( $categories[0]->name );
		}
	}

	if ( ! $items ) {
		return;
	}

	echo '<p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-muted">';
	foreach ( $items as $item ) {
		echo '<span class="inline-flex items-center gap-1.5">' . $item . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG + escaped text.
	}
	echo '</p>';
}

/**
 * 1 bài dạng danh sách: ảnh nhỏ (96px, từ 640px là 110px) + tiêu đề + thông tin.
 */
function rhm_news_list_item( array $show ): void {
	?>
	<li class="flex gap-4">
		<a class="group block w-24 shrink-0 overflow-hidden rounded-lg sm:w-[110px]" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php echo rhm_news_thumbnail( '(min-width: 640px) 110px, 96px' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image() / static markup. ?>
		</a>
		<div class="min-w-0">
			<h3 class="line-clamp-3 text-base leading-snug font-medium text-dark">
				<a class="text-dark hover:text-primary" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h3>
			<?php rhm_news_meta( $show ); ?>
		</div>
	</li>
	<?php
}

/**
 * Tiêu đề khối (gạch chân xanh) + link "Xem tất cả".
 */
function rhm_news_heading( array $block ): void {
	?>
	<div class="mb-5 flex items-start justify-between gap-4">
		<h2 class="text-lg leading-tight font-bold text-dark uppercase lg:text-[22px]">
			<?php echo esc_html( $block['title'] ); ?>
			<span class="mt-2.5 block h-[3px] w-24 rounded-full bg-linear-to-r from-primary to-green/40" aria-hidden="true"></span>
		</h2>
		<a class="inline-flex shrink-0 items-center gap-1.5 pt-1 text-sm font-medium text-primary hover:text-primary-dark" href="<?php echo esc_url( $block['link'] ); ?>">
			<?php esc_html_e( 'Xem tất cả', 'themeranghammatsg' ); ?>
			<span class="sr-only"><?php echo esc_html( $block['title'] ); ?></span>
			<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</a>
	</div>
	<?php
}
