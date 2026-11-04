<?php
/**
 * Danh mục / thẻ / tác giả / ngày của bài viết. CSS: archive.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();
// get_template_part( 'template-parts/components/page-banner' );
?>
<div class="site-container py-12 lg:py-20">
	<?php if ( get_the_archive_description() ) : ?>
		<div class="mb-8 text-muted"><?php the_archive_description(); ?></div>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/content/card', 'post' );
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
