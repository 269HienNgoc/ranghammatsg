<?php
/**
 * Template Name: Giới thiệu
 * Template Post Type: page
 *
 * CSS: page-about.css. Section riêng của trang Giới thiệu sẽ thêm vào $rhm_sections khi có thiết kế
 * (nhớ thêm @source của section vào src/css/pages/page-about.css).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-title' );
	?>
	<div class="site-container py-12 lg:py-20">
		<article id="post-<?php the_ID(); ?>" <?php post_class( 'entry-content' ); ?>>
			<?php the_content(); ?>
		</article>
	</div>
	<?php
endwhile;

$rhm_sections = array(
	// 'about-intro',
	// 'mission-vision',
	// 'doctors',
	// 'facilities',
	'booking-cta',
);
foreach ( $rhm_sections as $rhm_section ) {
	get_template_part( 'template-parts/sections/' . $rhm_section );
}

get_footer();
