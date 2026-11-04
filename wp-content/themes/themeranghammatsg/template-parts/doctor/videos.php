<?php
defined( 'ABSPATH' ) || exit;
get_template_part( 'template-parts/doctor/video-grid', null, array( 'items' => $args['doctor']['videos'], 'id' => 'doctor-videos-title', 'title' => __( 'Video về bác sĩ', 'themeranghammatsg' ) ) );
