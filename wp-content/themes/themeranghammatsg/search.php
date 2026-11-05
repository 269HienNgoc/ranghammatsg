<?php
/**
 * Kết quả tìm kiếm (bài viết, trang). CSS: archive.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/components/page-title' );
?>
<div class="site-container py-12 lg:py-20">
	<div class="mb-6 max-w-xl"><?php get_search_form(); ?></div>

	<?php if ( have_posts() ) : ?>
		<p class="mb-6 text-muted">
			<?php
			/* translators: %d: number of results */
			echo esc_html( sprintf( _n( 'Tìm thấy %d kết quả', 'Tìm thấy %d kết quả', (int) $wp_query->found_posts, 'themeranghammatsg' ), (int) $wp_query->found_posts ) );
			?>
		</p>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/card', 'post', array( 'loading' => rhm_list_image_loading() ) );
			endwhile;
			?>
		</div>
		<?php rhm_pagination(); ?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content/content-none' ); ?>
	<?php endif; ?>
</div>
<?php
get_footer();
