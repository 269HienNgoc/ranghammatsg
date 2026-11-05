<?php
/**
 * Chi tiết bài viết: banner → mô tả bài viết (excerpt) → mục lục (inc/toc.php) → nội dung. CSS: single-post.css
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-banner' );
	?>
	<div class="site-container max-w-[868px] py-5 lg:py-6">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<?php echo do_shortcode('[bac_si_tham_van]') ?>

			<?php if ( has_excerpt() ) : ?>
				<?php // Mô tả bài viết (sapo). Mục lục tự chèn ngay dưới (đầu the_content, inc/toc.php). ?>
				<p class="mb-6 text-lg leading-relaxed font-medium text-dark lg:text-xl"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>

			<?php
			// ACF "Thông tin chuyên môn bài viết" (inc/post-extras.php) — cũng là nguồn schema FAQPage / citation (inc/schema.php).
			get_template_part( 'template-parts/components/post-faq' );
			get_template_part( 'template-parts/components/post-references' );
			?>

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
