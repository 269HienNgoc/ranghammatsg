<?php
/**
 * Banner đầu trang: tiêu đề + breadcrumb. Dùng cho mọi trang trừ trang chủ (CSS trong base.css).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="bg-soft py-10 lg:py-12">
	<div class="site-container">
		<?php rhm_breadcrumb(); ?>
		<h1 class="mt-2 mb-2 text-2xl leading-tight font-bold text-dark lg:text-4xl"><?php echo esc_html( rhm_get_page_title() ); ?></h1>
		<p class="mb-6 flex flex-wrap gap-4 text-sm text-muted">
			<?php if(function_exists('kk_star_ratings')){ echo kk_star_ratings(); } ?>
				<?php rhm_posted_on(); ?>
				<?php if ( has_category() ) : ?>
					<span><?php the_category( ', ' ); ?></span>
				<?php endif; ?>
			</p>
	</div>
</section>
