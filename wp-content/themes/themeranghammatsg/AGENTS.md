# Theme Răng Hàm Mặt SG (`themeranghammatsg`)

Classic theme (không phải block theme) cho phòng khám Răng Hàm Mặt SG — tác giả Aimedis. PHP ≥ 8.1, WP ≥ 6.5.
Text domain `themeranghammatsg`; tiền tố hàm/hằng: `rhm_` / `RHM_` (`RHM_THEME_DIR`, `RHM_THEME_URI`).
Gutenberg bị tắt cho mọi post type → nội dung soạn bằng Classic Editor.

Plugin phụ thuộc: ACF PRO (field bác sĩ / bài viết), Fluent Forms (form đặt lịch), kk Star Ratings, Yoast SEO (schema).

## Build

```
npm install
npm run build   # build 1 lần ra dist/
npm run dev     # watch (khởi động lại khi thêm entry mới — danh sách entry đọc lúc Vite start)
```

- Vite 8 + Tailwind CSS 4 (`@tailwindcss/vite`) + Swiper 14.
- **Không sửa `dist/`** — luôn sửa `src/` rồi build.
- Mỗi file KHÔNG bắt đầu bằng `_` trong `src/css/pages/` và `src/js/` là 1 entry → `dist/css/<tên>.css`, `dist/js/<tên>.js`. File `_*.css` / `_*.js` là partial.
- Chunk Swiper dùng chung có hash trong tên (`dist/js/swiper-<hash>.js`) vì file tĩnh cache immutable 1 năm; entry giữ tên cố định, PHP nạp kèm `?ver=filemtime`.
- **CSS được inline vào `<head>`** (không có request CSS chặn render): `rhm_enqueue_assets()` gắn `path` cho mỗi bundle, filter `styles_inline_size_limit` (`rhm_styles_inline_size_limit()`, 120 kB raw) để WP core tự in `<style id="rhm-*-inline-css">`. Bundle vượt giới hạn tự quay về `<link>`. Vì CSS nằm trong HTML: **sau mỗi lần build phải xóa page cache** (Swift AI ở local; LiteSpeed + Cloudflare ở production).

## Cấu trúc thư mục

```
functions.php          chỉ require các module trong inc/
style.css              chỉ khai báo theme (CSS thật ở src/)
front-page.php         trang chủ = mảng section → template-parts/sections/<tên>.php
single-bac-si.php      hồ sơ bác sĩ = mảng section → template-parts/doctor/<tên>.php (truyền $args['doctor'])
single.php, page.php, archive.php, archive-bac-si.php, category.php, search.php, 404.php, index.php
header.php, footer.php, searchform.php
page-templates/        tpl-gioi-thieu.php (Giới thiệu), tpl-lien-he.php (Liên hệ)
inc/                   logic PHP, 1 file = 1 tính năng
inc/plugin/            tích hợp plugin, đặt tên theo thư mục plugin (fluentform.php, kk-star-ratings.php)
template-parts/
  sections/            section trang chủ: hero, quick-services, about, news, certificates, booking-cta
  doctor/              section hồ sơ bác sĩ: hero, intro, qualifications, certificates, videos, cases, case-videos, booking, related, news (+ partial card/carousel/grid)
  components/          doctor-box, featured-grid, category-filter, page-banner, page-title, post-faq, post-references, booking-popup, floating-cta
  content/             card-post, featured-post, content-none
src/css/
  _tailwind.css        thứ tự layer + import tokens (import ở đầu MỌI entry pages/)
  theme.css            design tokens @theme
  pages/               entry CSS theo trang
  components/          partial (_booking-form, _booking-popup, _floating-cta, _doctor-box, _entry-content, _fonts, _pagination, _toc)
src/js/                main.js (header sticky, menu mobile/submenu), carousel, home, dropdown, embed, fluent-lazy
assets/admin/          CSS/JS cho control "list" (repeater) trong Customizer
assets/icons/          thư viện icon .svg của theme (menu/ + cta/ 24px inline, services/ 48px <img>) — đồng bộ nguyên thư mục lên CDN
docs/                  doctor-profiles.md — hướng dẫn nhập hồ sơ bác sĩ cho admin
```

### Các module trong `inc/`

| File | Vai trò |
|---|---|
| `setup.php` | theme supports, menu (`primary`, `footer`), image sizes `rhm-*`, `rhm_custom_logo()` (thay `the_custom_logo()`), `RHM_LOGO_HEIGHTS` |
| `enqueue.php` | nạp CSS/JS từ `dist/`, preload font Roboto, cảnh báo admin khi chưa build |
| `cleanup.php` | gỡ block styles, chỉ cho jQuery khi cần |
| `template-tags.php` | helper chung: `rhm_get_option`, `rhm_get_booking_url`, `rhm_booking_button`, `rhm_breadcrumb`, `rhm_pagination`, `rhm_get_picture`, `rhm_list_image_loading`… |
| `external-links.php` | link external tự có `target="_blank" rel="nofollow"`: `rhm_external_link_attrs()`, `rhm_external_links()`, filter `the_content` + menu, filter `rhm_internal_hosts` |
| `hero.php` | slide hero, `rhm_hero_picture`, `rhm_html_attrs` |
| `icons.php` | SVG icon UI inline dùng trong template: `rhm_get_icon()` (không có thì tìm trong thư viện icon menu) |
| `icon-library.php` | thư viện `assets/icons/<bộ>/`: `rhm_icon_list()`, `rhm_icon_url()` (CDN qua theme mod `rhm_icon_base_url`), `rhm_icon_svg()` |
| `menus.php` | icon menu (ô chọn icon ở Giao diện → Menu, meta `_rhm_menu_icon`; class `icon-<tên>` vẫn dùng được) + nút `.submenu-toggle` |
| `doctors.php` / `doctor-fields.php` | CPT `bac-si`, field ACF, `rhm_get_doctor()`, ảnh bác sĩ |
| `doctor-box.php` | shortcode `[bac_si_tham_van id="" label=""]` trong bài viết |
| `post-extras.php` / `post-fields.php` | ACF bài viết: người tham vấn, FAQ, tài liệu tham khảo |
| `post-meta.php` | bật/tắt meta bài viết |
| `toc.php` | mục lục tự động (marker `<!--rhm-toc-->`, shortcode) |
| `content.php` | bọc `<table>` để cuộn ngang |
| `category.php` | query/lưới danh mục (`RHM_CATEGORY_GRID = 12`), bộ lọc chuyên mục |
| `booking-popup.php` | popup đặt lịch (`<dialog>` + form Fluent, in cuối trang qua `template-parts/components/booking-popup.php`): `rhm_booking_popup_enabled()`, `rhm_booking_popup_form_id()`, Customizer "Popup đặt lịch". Không in ở trang Liên hệ (nút đặt lịch ở đó trỏ `#dat-lich`) |
| `floating-cta.php` | nút liên hệ nhanh (desktop cột phải, mobile thanh đáy): `rhm_get_floating_cta_items()`, Customizer "Nút liên hệ nhanh" (5 nút: nhãn, icon bộ `cta/`, hành động popup/link, link, màu) |
| `news.php`, `about.php`, `quick-services.php`, `certificates.php`, `footer.php`, `contact.php`, `flag-links.php` | dữ liệu từng khối (đọc từ Customizer) |
| `customizer.php`, `customize-list.php`, `class-rhm-customize-list-control.php`, `class-rhm-customize-icon-control.php` | panel Customizer + control repeater dạng list (JSON) + control chọn icon (kiểu field `rhm-icon`) |
| `schema.php` | bổ sung Person/bác sĩ vào graph Yoast (`wpseo_schema_graph`) |
| `svg-upload.php` | cho upload SVG, chặn SVG không an toàn |
| `disable-comments.php` | tắt hoàn toàn bình luận/pingback |
| `plugins.php` | require các file `inc/plugin/*` |

## Quy ước quan trọng

### CSS
- `footer.css` còn chứa form đặt lịch, popup đặt lịch và nút liên hệ nhanh (in sau `</footer>`, ngoài mọi vùng utility → viết bằng class component, không dùng utility Tailwind trong 2 template đó).
- Mọi trang nạp `base` + `header` + `footer` + **đúng 1** file theo loại trang do `rhm_page_style_bundle()` (`inc/enqueue.php`) chọn:
  `page-home`, `page-about`, `page-contact`, `single-post`, `single-doctor`, `page`, `404`, `archive`.
  Thêm loại trang mới: tạo `src/css/pages/<tên>.css` + thêm nhánh `match` trong `rhm_page_style_bundle()`.
- **Tailwind chia theo vùng DOM**: `header.css` → utility chỉ trong `.site-header`; các file trang → `.site-main`; `footer.css` → `.site-footer`. Lý do: tránh utility thường ở file nạp sau đè utility responsive ở file nạp trước. Đặt class Tailwind vào đúng vùng tương ứng với file CSS sẽ biên dịch nó.
- Dùng `.site-container` (không dùng `.container` của Tailwind — đã bị loại).
- Token trong `src/css/theme.css`: màu `primary`, `dark`, `teal`, `mint`, `deep`, `muted`, `line`, `soft`, `footer-*`…; font duy nhất Roboto (tự host); breakpoint thêm `xs` = 560px; `max-w-site` = 1440px; `shadow-card`. Ưu tiên token thay vì mã màu cứng.

### PHP
- Mỗi file mở đầu `defined( 'ABSPATH' ) || exit;`, có type hint, theo WordPress Coding Standards.
- Tính năng mới → file mới trong `inc/` + `require_once` trong `functions.php`. Tích hợp plugin mới → `inc/plugin/<tên-thư-mục-plugin>.php` + require trong `inc/plugins.php`; tự kiểm tra plugin bật (`function_exists`, `shortcode_exists`…).
- Section mới cho trang chủ → `template-parts/sections/<tên>.php` + thêm vào mảng trong `front-page.php`.
- Escape đầu ra (`esc_html`, `esc_url`, `esc_attr`); chuỗi hiển thị bọc `__()` với text domain `themeranghammatsg`.
- Nút đặt lịch luôn dùng `rhm_get_booking_url()` / `rhm_booking_button()` (filter `rhm_booking_url`; trên hồ sơ bác sĩ và trang Liên hệ trỏ `#dat-lich`). Có popup → nút có `data-rhm-popup="booking"`, `main.js` mở popup thay vì chuyển trang (href vẫn là link thật). Phần tử nào có thuộc tính này cũng mở popup.
- Form Fluent do theme in luôn qua `rhm_render_fluent_form( $id )` (`inc/plugin/fluentform.php`), đặt trong khối `.rhm-booking`: trang chỉ có form của theme thì CSS Fluent bị gỡ (style ở `_booking-form.css`, nạp mọi trang qua `footer.css`); form chèn bằng shortcode trong nội dung vẫn giữ CSS của plugin.
- Field ACF bác sĩ có tiền tố `rhm_doctor_`; đọc qua `rhm_get_doctor_field()` (fallback `get_post_meta` khi không có ACF). ACF trả **ID ảnh**, không trả mảng.
- Section/dòng thiếu dữ liệu hợp lệ thì bỏ qua (không render khung rỗng).
- **Link external luôn có `target="_blank" rel="nofollow"`** (`inc/external-links.php`). Không viết `target`/`rel` cứng trong template:
  - Thẻ `<a>` trong template với URL có thể ra ngoài (nhập ở Customizer/ACF) → thêm `<?php echo rhm_external_link_attrs( $url ); ?>`.
  - Chuỗi HTML tự in (không qua `the_content`) → `rhm_external_links( $html )` (sau `wp_kses` nếu có).
  - `the_content` và menu đã tự xử lý. Domain khác muốn coi là nội bộ → filter `rhm_internal_hosts`.

### Icon
- Icon menu / dịch vụ nổi bật / nút liên hệ nhanh là FILE trong `assets/icons/menu/`, `assets/icons/services/`, `assets/icons/cta/` (24×24 `currentColor`, inline như `menu/`); tên file `a-z0-9-`, `<title>` tiếng Việt = nhãn trong ô chọn. Thêm icon = chép file, không sửa PHP.
- `menu/`: 24×24, `fill="currentColor"`, in inline bằng `rhm_icon_svg()` (đổi màu theo CSS, không request). `services/`: 48×48, màu thương hiệu vẽ sẵn, in `<img src="rhm_icon_url()">`.
- CDN: đồng bộ nguyên `assets/icons/` lên Cloudflare, nhập URL thư mục ở Tùy biến → Thư viện icon (`rhm_icon_base_url`). URL luôn kèm `?ver=filemtime`.
- Icon UI trong template (home, phone, chevron…) vẫn là chuỗi SVG trong `inc/icons.php`.

### Hiệu năng (xem thêm `.claude/rules/` ở gốc repo)
- Ảnh: dùng `rhm_get_picture()` / `wp_get_attachment_image()` với `sizes` chính xác; ảnh LCP `loading="eager"` + `fetchpriority="high"`, còn lại lazy. Không dùng `the_custom_logo()`.
- Swift Performance AI (bật *Lazyload images*) ghi đè MỌI `<img>` thành `loading="lazy" fetchpriority="low"`, kể cả ảnh LCP. Ảnh LCP (hero slide 1, ảnh bài nổi bật danh mục) và logo phải nằm trong danh sách loại trừ ảnh của Swift (cấu hình trong admin, không sửa code plugin). Kiểm tra: HTML phải có đúng 1 `fetchpriority="high"`.
- Fluent Forms được tải kiểu "chạm mới tải" (`inc/plugin/fluentform.php` + `src/js/fluent-lazy.js`); CSS Fluent bị gỡ, style riêng ở `_booking-form.css`.
- Video YouTube chỉ kết nối sau khi bấm phát (`embed.js`); slider không tự chạy.
- JS vanilla, không phụ thuộc jQuery; nạp `defer` ở footer.

## CPT Bác sĩ
- Post type `bac-si`, archive `/bac-si/`, single `/bac-si/<slug>/`, không có taxonomy.
- Rewrite flush 1 lần theo option `rhm_doctor_rewrite_version` — đổi cấu trúc URL thì tăng giá trị này.
- Chi tiết nhập liệu: `docs/doctor-profiles.md`.
