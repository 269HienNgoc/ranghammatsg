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
require_once RHM_THEME_DIR . '/inc/icons.php';
require_once RHM_THEME_DIR . '/inc/template-tags.php';
require_once RHM_THEME_DIR . '/inc/menus.php';
require_once RHM_THEME_DIR . '/inc/flag-links.php';
require_once RHM_THEME_DIR . '/inc/hero.php';
require_once RHM_THEME_DIR . '/inc/quick-services.php';
require_once RHM_THEME_DIR . '/inc/about.php';
require_once RHM_THEME_DIR . '/inc/news.php';
require_once RHM_THEME_DIR . '/inc/category.php';
require_once RHM_THEME_DIR . '/inc/certificates.php';
require_once RHM_THEME_DIR . '/inc/svg-upload.php';
require_once RHM_THEME_DIR . '/inc/disable-comments.php';
require_once RHM_THEME_DIR . '/inc/customize-list.php';
require_once RHM_THEME_DIR . '/inc/customizer.php';

add_filter('use_block_editor_for_post', '__return_false', 10);
add_filter('use_block_editor_for_post_type', '__return_false', 10);

