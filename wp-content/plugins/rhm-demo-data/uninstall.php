<?php
/**
 * Xóa plugin (Plugin → Xóa): CỐ Ý KHÔNG LÀM GÌ.
 *
 * Mọi dữ liệu plugin đã tạo được giữ nguyên: bài viết, hồ sơ bác sĩ, trang landing mẫu, ảnh (cả file), danh mục/thẻ mẫu,
 * banner trang chủ đã gán, option rhm_demo_terms / rhm_demo_hero_backup. Lý do: nội dung thật có thể đang dùng
 * ảnh / bác sĩ mẫu, hoặc content đã sửa dữ liệu mẫu thành nội dung dùng thật.
 * Ngừng kích hoạt plugin cũng không đụng tới dữ liệu (plugin không đăng ký hook deactivation).
 *
 * Muốn dọn dữ liệu mẫu: TRƯỚC KHI xóa plugin, vào Công cụ → Dữ liệu mẫu RHM → "Xóa toàn bộ dữ liệu mẫu"
 * (hoặc `wp rhm-demo delete`). Dữ liệu mẫu vẫn mang meta _rhm_demo nên cài lại plugin là dọn được.
 *
 * @package rhm-demo-data
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;
