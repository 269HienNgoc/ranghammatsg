<?php
/**
 * Trang chủ (Cài đặt → Đọc → Trang tĩnh). CSS: page-home.css
 * Mỗi section là 1 file trong template-parts/sections/, thêm dần theo ảnh thiết kế.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

// Thứ tự section trang chủ — cập nhật khi nhận thiết kế.
$rhm_sections = array(
	'hero',
	'quick-services',
	'about',
	'about-2',
	'doctors',
	'doctors-2',
	'news',
	'branches',
	// 'why-choose-us',
	// 'testimonials',
	'booking-cta',
	// 'latest-posts',
);

foreach ( $rhm_sections as $rhm_section ) {
	get_template_part( 'template-parts/sections/' . $rhm_section );
}

// Nội dung soạn trong trình soạn thảo của trang "Trang chủ" (nếu có).
while ( have_posts() ) :
	the_post();
	if ( '' !== trim( get_the_content() ) ) :
		?>
		<div class="site-container entry-content py-12 lg:py-20"><?php the_content(); ?></div>
		<?php
	endif;
endwhile;

get_footer();
