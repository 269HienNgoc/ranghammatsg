<?php
/**
 * Landing – Đội ngũ bác sĩ: dùng lại section trang chủ (template-parts/sections/doctors.php), chỉ đổi chữ tiêu đề.
 * Field: inc/landing/layouts/doctors.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data = $args['data'] ?? array();
get_template_part(
	'template-parts/sections/doctors',
	null,
	array(
		'id'      => $args['id'],
		'eyebrow' => rhm_landing_text( $rhm_data, 'eyebrow' ),
		'title'   => rhm_landing_text( $rhm_data, 'title' ),
	)
);
