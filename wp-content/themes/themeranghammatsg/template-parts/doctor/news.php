<?php
defined( 'ABSPATH' ) || exit;
$rhm_query = new WP_Query( array( 'post_type' => 'post', 'post_status' => 'publish', 'posts_per_page' => 3, 'orderby' => 'date', 'order' => 'DESC', 'no_found_rows' => true, 'ignore_sticky_posts' => true ) );
if ( ! $rhm_query->have_posts() ) {
	return;
}
update_post_thumbnail_cache( $rhm_query );
?>
<section class="doctor-section doctor-section--soft" aria-labelledby="doctor-news-title">
	<div class="site-container">
		<h2 id="doctor-news-title" class="doctor-title"><?php esc_html_e( 'Bài viết mới nhất', 'themeranghammatsg' ); ?></h2>
		<div class="doctor-news">
			<?php while ( $rhm_query->have_posts() ) : ?>
				<?php $rhm_query->the_post(); ?>
				<?php get_template_part( 'template-parts/content/card', 'post' ); ?>
			<?php endwhile; ?>
		</div>
	</div>
</section>
<?php wp_reset_postdata(); ?>
