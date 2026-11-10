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
themeranghammatsg/
├── style.css                 chỉ khai báo theme (Version — tăng khi đóng gói zip); CSS thật ở src/
├── functions.php             chỉ require các module trong inc/
├── header.php                <head>, hàng logo/tìm kiếm/cờ/đặt lịch/hotline, menu chính + ngăn kéo mobile
├── footer.php                chân trang 3 cột; sau </footer>: nút liên hệ nhanh + popup đặt lịch, rồi wp_footer()
├── searchform.php            ô tìm kiếm (biến thể header / ngăn kéo)
├── front-page.php            trang chủ = mảng section → template-parts/sections/<tên>.php
├── single.php                bài viết (mục lục, người tham vấn, FAQ, tài liệu tham khảo)
├── single-bac-si.php         hồ sơ bác sĩ = mảng section → template-parts/doctor/<tên>.php (truyền $args['doctor'])
├── page.php                  trang thường: tiêu đề + cột 868px (ảnh đại diện = LCP) + .entry-content
├── archive.php, archive-bac-si.php, category.php, search.php, 404.php, index.php
├── page-templates/
│   ├── tpl-gioi-thieu.php    Template "Giới thiệu"
│   └── tpl-lien-he.php       Template "Liên hệ – Đặt lịch" (form Fluent ngay trên trang, không có popup)
├── inc/                      logic PHP, 1 file = 1 tính năng (bảng bên dưới)
│   └── plugin/               tích hợp plugin, tên = thư mục plugin: fluentform.php, kk-star-ratings.php (nạp qua inc/plugins.php)
├── template-parts/
│   ├── sections/             trang chủ (thứ tự trong front-page.php): hero, quick-services, about, doctors, news, certificates, booking-cta
│   ├── doctor/               hồ sơ bác sĩ: hero, intro, qualifications, certificates, videos, cases, case-videos,
│   │                         booking, related, news + partial carousel, profile-card, case-card, video-grid
│   ├── components/           doctor-box, featured-grid, category-filter, page-banner, page-title, post-faq,
│   │                         post-references, booking-popup (popup đặt lịch), floating-cta (nút liên hệ nhanh)
│   └── content/              card-post, featured-post, content-none
├── src/                      mã nguồn asset (KHÔNG có trong zip theme)
│   ├── css/
│   │   ├── _tailwind.css     thứ tự layer + import tokens (import ở đầu MỌI entry pages/)
│   │   ├── theme.css         design tokens @theme
│   │   ├── pages/            entry CSS: base, header, footer (+ popup, nút liên hệ nhanh, form đặt lịch), page-home,
│   │   │                     page-about, page-contact, page, single-post, single-doctor, archive, 404
│   │   └── components/       partial: _booking-form, _booking-popup, _floating-cta, _doctor-box, _entry-content,
│   │                         _fonts, _pagination, _toc
│   └── js/                   main.js (header sticky, menu mobile/submenu, mở popup đặt lịch), home (hero), carousel
│                             (+ _carousel nạp động), dropdown, embed (facade video/bản đồ), fluent-lazy
├── dist/                     kết quả build — ĐƯỢC COMMIT (server không build): css/, js/, fonts/ (Roboto latin + vietnamese)
├── assets/
│   ├── admin/                customize-list.css/js: control "list" (repeater) trong Customizer
│   ├── icons/                thư viện icon .svg (README.md) — đồng bộ nguyên thư mục lên CDN
│   │   ├── menu/             24px currentColor, inline — icon mục menu
│   │   ├── services/         48px màu vẽ sẵn, <img> — Dịch vụ nổi bật
│   │   └── cta/              24px currentColor, inline — nút liên hệ nhanh
│   └── images/flags/         vn.svg, gb.svg (cờ ngôn ngữ ở header)
├── languages/                (trống) file dịch
├── docs/                     doctor-profiles.md (hướng dẫn nhập hồ sơ bác sĩ), doctor-profiles-verification.md (kết quả đo)
├── AGENTS.md                 bản hướng dẫn cho agent khác (nội dung gốc giống file này)
└── package.json, vite.config.js, node_modules/   build (KHÔNG có trong zip theme)
```

Đóng gói: zip thư mục theme, bỏ `src/`, `node_modules/`, `docs/`, `package*.json`, `vite.config.js`, `.gitignore`, `CLAUDE.md` (build trước khi zip). Mu-plugin `wp-content/mu-plugins/0-rhm-head-order.php` nằm ngoài theme → deploy riêng.

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
| `post-extras.php` / `post-fields.php` | ACF bài viết: người tham vấn, FAQ, tài liệu tham khảo; nút Hiện/Ẩn FAQ và tài liệu tham khảo từng bài (`rhm_post_section_visible()`) |
| `post-meta.php` | bật/tắt meta bài viết |
| `toc.php` | mục lục tự động (marker `<!--rhm-toc-->`, shortcode) |
| `content.php` | bọc `<table>` để cuộn ngang |
| `category.php` | query/lưới danh mục (`RHM_CATEGORY_GRID = 12`), bộ lọc chuyên mục |
| `booking-popup.php` | popup đặt lịch (`<dialog>` + form Fluent, in cuối trang qua `template-parts/components/booking-popup.php`): `rhm_booking_popup_enabled()`, `rhm_booking_popup_form_id()`, Customizer "Popup đặt lịch". Không in ở trang Liên hệ (nút đặt lịch ở đó trỏ `#dat-lich`) |
| `floating-cta.php` | nút liên hệ nhanh (desktop cột phải, mobile thanh đáy): `rhm_get_floating_cta_items()`, Customizer "Nút liên hệ nhanh" (5 nút: nhãn, icon bộ `cta/`, hành động popup/link, link, màu) |
| `home-doctors.php` | section "Đội ngũ bác sĩ" trang chủ (`template-parts/sections/doctors.php`, slider 1 bác sĩ/slide): `rhm_get_home_doctors()` (hồ sơ bật field `rhm_doctor_home`, sắp theo `menu_order`; chưa bật hồ sơ nào → 6 hồ sơ mới nhất), Customizer "Đội ngũ bác sĩ (Trang chủ)". Field tab "Trang chủ" của hồ sơ: `degree`, `years`, `case_count`, `credentials` |
| `news.php`, `about.php`, `quick-services.php`, `certificates.php`, `footer.php`, `contact.php`, `flag-links.php` | dữ liệu từng khối (đọc từ Customizer) |
| `customizer.php`, `customize-list.php`, `class-rhm-customize-list-control.php`, `class-rhm-customize-icon-control.php` | panel Customizer + control repeater dạng list (JSON) + control chọn icon (kiểu field `rhm-icon`) |
| `schema.php` | bổ sung Person/bác sĩ vào graph Yoast (`wpseo_schema_graph`) |
| `svg-upload.php` | cho upload SVG, chặn SVG không an toàn |
| `disable-comments.php` | tắt hoàn toàn bình luận/pingback |
| `plugins.php` | require các file `inc/plugin/*` |
| `plugin/fluentform.php` | Fluent Forms: jQuery + JS "chạm mới tải", `rhm_render_fluent_form()` (form của theme), gỡ CSS Fluent khi trang chỉ có form của theme |
| `plugin/kk-star-ratings.php` | kk Star Ratings: `rhm_get_star_rating()` (luôn tồn tại, trả rỗng khi plugin tắt), chỉ giữ CSS/JS của plugin ở bài viết có sao |

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
- Có `page-attributes` (ô "Thứ tự" = `menu_order`): dùng để sắp bác sĩ trong section trang chủ.
- Rewrite flush 1 lần theo option `rhm_doctor_rewrite_version` — đổi cấu trúc URL thì tăng giá trị này.
- Chi tiết nhập liệu: `docs/doctor-profiles.md`.
