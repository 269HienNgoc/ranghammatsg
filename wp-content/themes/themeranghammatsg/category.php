<?php
/**
 * Trang danh mục. Đầu trang: thanh lọc chuyên mục con (template-parts/components/category-filter.php, chỉ hiện khi có con).
 * Phần 1 (chỉ trang 1): bài mới nhất của danh mục, template-parts/content/featured-post.php.
 * Mobile (< 640px): sau bài nổi bật có tiêu đề "Bài viết mới nhất" (+ "Xem tất cả" về chuyên mục cha); card-post dạng hàng.
 * Phần 2: lưới card-post, 4 cột từ 1280px (3 cột từ 1024px, 2 cột từ 640px). Số bài mỗi trang: inc/category.php. CSS: archive.css
 *
 * Thẻ, tác giả, ngày vẫn dùng archive.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();
// get_template_part( 'template-parts/components/page-banner' );
?>
<div class="site-container py-4 lg:py-4">

	<?php if ( have_posts() ) : ?>
		<?php
		if ( ! is_paged() ) {
			the_post();
			get_template_part( 'template-parts/content/featured-post' );
		}
		?>

		<?php
		// Mobile (< 640px): tiêu đề "Bài viết mới nhất" trên thanh lọc; "Xem tất cả" về chuyên mục cha khi đang ở chuyên mục con.
		$rhm_filter   = rhm_get_category_filter();
		$rhm_view_all = $rhm_filter && ! $rhm_filter['all']['active'] ? $rhm_filter['all']['url'] : '';
		?>
		<div class="mt-6 mb-3 flex items-center justify-between gap-4 sm:hidden">
			<h2 class="flex items-center gap-2 text-lg leading-tight font-bold text-teal-dark">
				<span class="h-5 w-1 shrink-0 rounded-full bg-[#008775]" aria-hidden="true"></span>
				<?php esc_html_e( 'Bài viết mới nhất', 'themeranghammatsg' ); ?>
			</h2>
			<?php if ( $rhm_view_all ) : ?>
				<a class="inline-flex shrink-0 items-center gap-1 text-sm text-teal-dark hover:text-primary" href="<?php echo esc_url( $rhm_view_all ); ?>">
					<?php esc_html_e( 'Xem tất cả', 'themeranghammatsg' ); ?>
					<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			<?php endif; ?>
		</div>

		<?php get_template_part( 'template-parts/components/category-filter' ); ?>

		<?php if ( have_posts() ) : ?>
			<div class="grid gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3 xl:grid-cols-4<?php echo is_paged() ? '' : ' mt-4 sm:mt-10 lg:mt-12'; ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/content/card',
						'post',
						array( 'sizes' => '(min-width: 1440px) 334px, (min-width: 1280px) calc((100vw - 104px) / 4), (min-width: 1024px) calc((100vw - 80px) / 3), (min-width: 640px) calc((100vw - 56px) / 2), calc(38vw - 12px)' )
					);
				endwhile;
				?>
			</div>
		<?php endif; ?>
		<?php rhm_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content-none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
