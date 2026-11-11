<?php
/**
 * Landing – Chứng nhận: dùng lại section trang chủ (template-parts/sections/certificates.php), chỉ đổi tiêu đề.
 * Field: inc/landing/layouts/certificates.php.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

get_template_part(
	'template-parts/sections/certificates',
	null,
	array(
		'id'    => $args['id'],
		'title' => rhm_landing_text( $args['data'] ?? array(), 'title' ),
	)
);
