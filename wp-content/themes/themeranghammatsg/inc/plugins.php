<?php
/**
 * Tích hợp plugin: mỗi plugin 1 file trong inc/plugin/, đặt tên theo thư mục plugin (wp-content/plugins/<tên>).
 * Thêm plugin mới: tạo inc/plugin/<tên-plugin>.php rồi thêm 1 dòng require bên dưới.
 * File nào gọi hàm của plugin phải tự kiểm tra plugin có bật không (function_exists / shortcode_exists…).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

require_once RHM_THEME_DIR . '/inc/plugin/kk-star-ratings.php'; // Sao đánh giá: chỉ nạp CSS/JS ở trang có sao.
require_once RHM_THEME_DIR . '/inc/plugin/fluentform.php';      // Fluent Forms: jQuery + JS "chạm mới tải".
