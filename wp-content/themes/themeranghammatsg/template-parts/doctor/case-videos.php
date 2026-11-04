<?php
defined( 'ABSPATH' ) || exit;
get_template_part( 'template-parts/doctor/video-grid', null, array( 'items' => $args['doctor']['case_videos'], 'id' => 'doctor-case-videos-title', 'title' => __( 'Video ca điều trị', 'themeranghammatsg' ) ) );
