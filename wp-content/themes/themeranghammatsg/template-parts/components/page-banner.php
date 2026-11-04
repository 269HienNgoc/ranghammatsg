<?php

/**
 * Banner đầu trang: tiêu đề + breadcrumb. Dùng cho mọi trang trừ trang chủ (CSS trong base.css).
 *
 * @package themeranghammatsg
 */

defined('ABSPATH') || exit;
?>
<section class="bg-soft py-10 lg:py-12">
	<div class="site-container">
		<?php rhm_breadcrumb(); ?>
		<h1 class="mt-2 mb-2 text-2xl leading-tight font-bold text-dark lg:text-4xl"><?php echo esc_html(rhm_get_page_title()); ?></h1>
		<!-- Meta -->
			<?php echo rhm_get_star_rating(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- markup của plugin kk-star-ratings (inc/plugin/kk-star-ratings.php: tính 1 lần, quyết định nạp CSS/JS của plugin). ?>
			<?php rhm_posted_on(); ?>
			<?php if (has_category()) : ?>
				<span><?php the_category(', '); ?></span>
			<?php endif; ?>
		<!-- End Meta -->
	</div>
</section>