<?php
/**
 * Theme bootstrap: chỉ nạp các module trong inc/.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

define( 'RHM_THEME_DIR', get_template_directory() );
define( 'RHM_THEME_URI', get_template_directory_uri() );

require_once RHM_THEME_DIR . '/inc/setup.php';
require_once RHM_THEME_DIR . '/inc/enqueue.php';
require_once RHM_THEME_DIR . '/inc/cleanup.php';
require_once RHM_THEME_DIR . '/inc/plugins.php';
require_once RHM_THEME_DIR . '/inc/icon-library.php';
require_once RHM_THEME_DIR . '/inc/icons.php';
require_once RHM_THEME_DIR . '/inc/template-tags.php';
require_once RHM_THEME_DIR . '/inc/external-links.php';
require_once RHM_THEME_DIR . '/inc/menus.php';
require_once RHM_THEME_DIR . '/inc/flag-links.php';
require_once RHM_THEME_DIR . '/inc/hero.php';
require_once RHM_THEME_DIR . '/inc/quick-services.php';
require_once RHM_THEME_DIR . '/inc/about.php';
require_once RHM_THEME_DIR . '/inc/news.php';
require_once RHM_THEME_DIR . '/inc/category.php';
require_once RHM_THEME_DIR . '/inc/doctors.php';
require_once RHM_THEME_DIR . '/inc/home-doctors.php';
require_once RHM_THEME_DIR . '/inc/post-extras.php';
require_once RHM_THEME_DIR . '/inc/doctor-box.php';
require_once RHM_THEME_DIR . '/inc/post-meta.php';
require_once RHM_THEME_DIR . '/inc/toc.php';
require_once RHM_THEME_DIR . '/inc/content.php';
require_once RHM_THEME_DIR . '/inc/certificates.php';
require_once RHM_THEME_DIR . '/inc/footer.php';
require_once RHM_THEME_DIR . '/inc/contact.php';
require_once RHM_THEME_DIR . '/inc/booking-popup.php';
require_once RHM_THEME_DIR . '/inc/floating-cta.php';
require_once RHM_THEME_DIR . '/inc/svg-upload.php';
require_once RHM_THEME_DIR . '/inc/disable-comments.php';
require_once RHM_THEME_DIR . '/inc/customize-list.php';
require_once RHM_THEME_DIR . '/inc/customizer.php';
require_once RHM_THEME_DIR . '/inc/schema.php';

add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

