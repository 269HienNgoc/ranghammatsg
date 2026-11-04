<?php
/**
 * Trang mặc định. CSS: page.css
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
			<?php
			the_content();
			wp_link_pages();
			?>
		</article>
	</div>
	<?php
endwhile;

get_footer();
