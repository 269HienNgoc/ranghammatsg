<?php
/** Hồ sơ bác sĩ: mười section, dữ liệu riêng của từng hồ sơ. */
defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	$rhm_doctor = rhm_get_doctor( get_the_ID() );
	foreach ( array( 'hero', 'intro', 'qualifications', 'certificates', 'videos', 'cases', 'case-videos', 'booking', 'related', 'news' ) as $rhm_section ) {
		get_template_part( 'template-parts/doctor/' . $rhm_section, null, array( 'doctor' => $rhm_doctor ) );
	}
endwhile;
get_footer();
