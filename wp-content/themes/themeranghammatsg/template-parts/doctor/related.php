<?php
defined( 'ABSPATH' ) || exit;
$rhm_query = new WP_Query( array( 'post_type' => 'bac-si', 'post_status' => 'publish', 'post__not_in' => array( get_the_ID() ), 'posts_per_page' => 6, 'no_found_rows' => true, 'ignore_sticky_posts' => true, 'update_post_term_cache' => false ) );
if ( ! $rhm_query->have_posts() ) {
	return;
}
update_post_thumbnail_cache( $rhm_query );
?>
<section class="doctor-section" aria-labelledby="doctor-related-title">
	<div class="site-container">
		<h2 id="doctor-related-title" class="doctor-title"><?php esc_html_e( 'Những bác sĩ khác', 'themeranghammatsg' ); ?></h2>
		<?php get_template_part( 'template-parts/doctor/carousel', null, array( 'items' => $rhm_query->posts, 'variant' => 'doctors' ) ); ?>
	</div>
</section>
