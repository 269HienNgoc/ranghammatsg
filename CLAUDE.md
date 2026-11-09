# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Tổng quan repo

Repo này chứa **toàn bộ site WordPress** (core + plugin + theme) cho phòng khám Răng Hàm Mặt SG, chạy local bằng Laragon (`D:\laragon\www\bactrung`, DB `bactrung`, user `root`, prefix `wp_`). WP core (`wp-admin/`, `wp-includes/`, file `wp-*.php` ở gốc) được commit nhưng **không sửa**. Code tự viết nằm ở:

| Đường dẫn | Nội dung |
|---|---|
| `wp-content/themes/themeranghammatsg/` | Theme classic tự viết, nơi diễn ra gần như mọi thay đổi. **Đọc `wp-content/themes/themeranghammatsg/CLAUDE.md`** trước khi sửa theme: build Vite/Tailwind, cấu trúc `inc/`, quy ước CSS theo vùng DOM, link external, icon, CPT `bac-si`. |
| `wp-content/plugins/rhm-demo-data/` | Plugin tự viết: sinh dữ liệu mẫu (bài viết + hồ sơ bác sĩ, ảnh vẽ bằng GD, field ACF), đánh dấu bằng meta `_rhm_demo` để xóa sạch mà không đụng dữ liệu thật. Admin: Công cụ → Dữ liệu mẫu RHM. |

Code bên thứ ba được commit, không sửa: `plugins/advanced-custom-fields-pro`, `plugins/swift-ai` (Swift Performance AI, cache/tối ưu), `mu-plugins/swift-performance-loader.php` (chứa đường dẫn production `/var/www/html/...`), `migrate/` (interconnect/it Search-Replace-DB, dùng khi chuyển domain/DB). `wp-config.php`, `uploads/`, `cache/`, `node_modules/` bị gitignore.

Theme khai báo phụ thuộc Fluent Forms, kk Star Ratings, Yoast SEO nhưng các plugin này **không có trong repo/local**; code tích hợp trong theme (`inc/plugin/*`) phải luôn tự kiểm tra plugin có bật hay không.

## Lệnh

Không có test suite, lint hay WP-CLI cài sẵn (PHP 8.4 và Composer có trong Laragon).

```bash
# Build asset theme (chạy trong wp-content/themes/themeranghammatsg)
npm install
npm run build   # build ra dist/
npm run dev     # watch

# Kiểm tra cú pháp PHP sau khi sửa
php -l wp-content/themes/themeranghammatsg/inc/<file>.php

# Dữ liệu mẫu (khi có WP-CLI)
wp rhm-demo generate --posts=100 --doctors=100 --fresh
wp rhm-demo status
wp rhm-demo delete
```

`dist/` của theme **được commit** (server không build): sửa `src/` → `npm run build` → commit cả `dist/`.

## Hiệu năng là yêu cầu cứng

Mục tiêu dự án: PageSpeed Insights **100/100 ở cả Mobile và Desktop** (Performance, Accessibility, Best Practices, SEO). Các rule chi tiết ở `.claude/rules/` (LCP, CLS, INP/TBT, FCP/CSS/font, TTFB, third-party, ảnh responsive, cách đo) tự áp dụng khi sửa file trong theme/plugin; tuân theo chúng khi thêm ảnh, script, font hay nhúng bên thứ ba.

## Ngôn ngữ

Giao diện, comment code và tài liệu viết bằng tiếng Việt; chuỗi hiển thị dùng text domain `themeranghammatsg` (theme) / `rhm-demo-data` (plugin).
