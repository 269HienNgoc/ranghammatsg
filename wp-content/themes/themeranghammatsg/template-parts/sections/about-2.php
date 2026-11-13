<?php
/**
 * Section: Giới thiệu bản 2 — cùng giao diện section Giới thiệu nhưng desktop ảnh bên phải, chữ bên trái.
 * Dữ liệu: Customizer → "Giới thiệu 2 (Trang chủ)" (theme mod rhm_about_2_*, inc/about.php). Để trống Tiêu đề → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/sections/about', null, array( 'prefix' => 'rhm_about_2' ) );
