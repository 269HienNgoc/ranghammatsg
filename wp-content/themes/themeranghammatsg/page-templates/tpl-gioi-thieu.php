<?php
/**
 * Template Name: Giới thiệu
 * Template Post Type: page
 *
 * Landing page chủ đề "Giới thiệu": các section do content chọn và sắp xếp trong editor
 * (ACF Flexible Content "Landing page – Giới thiệu", inc/landing/). Chưa có section nào → tiêu đề + nội dung editor.
 * CSS: page-about.css (khai báo trong rhm_landing_templates()).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	rhm_landing_render( get_the_ID() );
endwhile;

get_footer();
