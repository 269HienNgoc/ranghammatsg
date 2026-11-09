<?php
/**
 * Mẫu mặc định cho trang soạn nội dung trong editor (không chọn template riêng). CSS: page.css
 *
 * Bố cục: tiêu đề trang (breadcrumb + H1) → cột nội dung 868px (cùng độ rộng bài viết, dòng chữ dễ đọc):
 * ảnh đại diện (nếu có, là ảnh LCP) → the_content (style .entry-content) → phân trang <!--nextpage-->.
 * Mục lục: chèn [toc] hoặc <!--rhm-toc--> vào nội dung (inc/toc.php; trang không tự chèn như bài viết).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	get_template_part( 'template-parts/components/page-title' );
	?>
	<div class="site-container max-w-[868px] py-10 lg:py-14">
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="mb-8 overflow-hidden rounded-2xl">
					<?php
					// Cột 868px − lề 2 × 16px = 836px; nhỏ hơn thì rộng màn hình − 32px.
					the_post_thumbnail(
						'full',
						array(
							'class'         => 'block h-auto w-full',
							'sizes'         => '(min-width: 868px) 836px, calc(100vw - 32px)',
							'loading'       => 'eager',
							'fetchpriority' => 'high',
						)
					);
					?>
				</figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php
				the_content();
				wp_link_pages();
				?>
			</div>
		</article>
	</div>
	<?php
endwhile;

get_footer();
