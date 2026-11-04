<?php
/**
 * Fallback cuối cùng của template hierarchy; cũng dùng cho trang Tin tức (page_for_posts). CSS: archive.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();
get_template_part( 'template-parts/components/page-title' );
?>
<div class="site-container py-12 lg:py-20">
	<?php if ( have_posts() ) : ?>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
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
