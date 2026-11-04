<?php
/**
 * Tiêu đề đầu trang: breadcrumb + H1 (rhm_get_page_title). Dùng cho trang thường, trang Giới thiệu, Tin tức, tìm kiếm.
 * Bài viết dùng template-parts/components/page-banner.php. CSS: utility do bundle của từng trang sinh (page / page-about / archive).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="bg-soft py-10 lg:py-12">
	<div class="site-container">
		<?php rhm_breadcrumb(); ?>
		<h1 class="mt-2 text-2xl leading-tight font-bold text-dark lg:text-4xl"><?php echo esc_html( rhm_get_page_title() ); ?></h1>
	</div>
</section>
