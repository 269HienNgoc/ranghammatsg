<?php
/**
 * Chi tiết bài viết. CSS: single-post.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner' );
	?>
	<div class="site-container max-w-[852px] py-12 lg:py-20">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			
			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>

			<?php if ( has_tag() ) : ?>
				<p class="mt-8 text-sm text-muted"><?php the_tags( esc_html__( 'Từ khóa: ', 'themeranghammatsg' ), ', ' ); ?></p>
			<?php endif; ?>
		</article>

		<?php
		the_post_navigation(
			array(
				'prev_text' => esc_html__( '« Bài trước', 'themeranghammatsg' ) . '<br><span>%title</span>',
				'next_text' => esc_html__( 'Bài sau »', 'themeranghammatsg' ) . '<br><span>%title</span>',
			)
		);
		?>
	</div>
	<?php
endwhile;

get_template_part( 'template-parts/sections/booking-cta' );
get_footer();
