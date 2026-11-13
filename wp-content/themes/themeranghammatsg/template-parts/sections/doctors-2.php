<?php
/**
 * Section: Đội ngũ bác sĩ bản 2 — cùng giao diện slider "Đội ngũ bác sĩ", bác sĩ của chuyên khoa chọn ở
 * Customizer → "Đội ngũ bác sĩ 2 (Trang chủ)" (theme mod rhm_home_doctors_2_*, inc/home-doctors.php). Chưa chọn chuyên khoa → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_template_part( 'template-parts/sections/doctors', null, array( 'prefix' => 'rhm_home_doctors_2' ) );
