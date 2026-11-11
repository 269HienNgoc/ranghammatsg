<?php
/**
 * Landing – Kêu gọi đặt lịch: dùng lại template-parts/sections/booking-cta.php. Field: inc/landing/layouts/booking.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data = $args['data'] ?? array();
get_template_part(
	'template-parts/sections/booking-cta',
	null,
	array(
		'id'    => $args['id'],
		'title' => rhm_landing_text( $rhm_data, 'title' ),
		'text'  => rhm_landing_text( $rhm_data, 'text' ),
	)
);
