<?php
/**
 * Trang danh mục. Phần 1 (chỉ trang 1): bài mới nhất của danh mục, template-parts/content/featured-post.php.
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

		<?php if ( have_posts() ) : ?>
			<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4<?php echo is_paged() ? '' : ' mt-10 lg:mt-12'; ?>">
				<?php
				while ( have_posts() ) :
					the_post();
					get_template_part(
						'template-parts/content/card',
						'post',
						array( 'sizes' => '(min-width: 1440px) 334px, (min-width: 1280px) calc((100vw - 104px) / 4), (min-width: 1024px) calc((100vw - 80px) / 3), (min-width: 640px) calc((100vw - 56px) / 2), calc(100vw - 32px)' )
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
