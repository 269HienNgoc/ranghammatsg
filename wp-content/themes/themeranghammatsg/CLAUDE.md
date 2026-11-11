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
│   ├── tpl-gioi-thieu.php    Template "Giới thiệu" = landing page (rhm_landing_render, xem inc/landing/)
│   └── tpl-lien-he.php       Template "Liên hệ – Đặt lịch" (form Fluent ngay trên trang, không có popup)
├── inc/                      logic PHP, 1 file = 1 tính năng (bảng bên dưới)
│   ├── landing/              landing page theo chủ đề: landing.php (điểm vào), fields.php (ACF), layouts/<layout>.php
│   └── plugin/               tích hợp plugin, tên = thư mục plugin: fluentform.php, kk-star-ratings.php (nạp qua inc/plugins.php)
├── template-parts/
│   ├── sections/             trang chủ (thứ tự trong front-page.php): hero, quick-services, about, doctors, news, certificates, booking-cta
│   │                         (doctors, certificates, booking-cta nhận $args id/title… để landing dùng lại)
│   ├── landing/              section landing page: hero, highlights, cards, services, doctor-cards, feature/tiles/promo (dạng ô, xếp hàng theo Độ rộng), intro,
│   │                         stats, timeline, gallery, doctors, certificates, faq, editor, booking
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
│   │   ├── landing/          24px nét stroke currentColor, inline — section landing page
│   │   └── cta/              24px currentColor, inline — nút liên hệ nhanh
│   └── images/flags/         vn.svg, gb.svg (cờ ngôn ngữ ở header)
├── languages/                (trống) file dịch
├── docs/                     doctor-profiles.md (hướng dẫn nhập hồ sơ bác sĩ), doctor-profiles-verification.md (kết quả đo), landing-pages.md (landing page: nhập liệu + thêm layout/chủ đề)
├── AGENTS.md                 bản hướng dẫn cho agent khác (bản cũ của file này; KHÔNG có trong zip theme)
└── package.json, vite.config.js, node_modules/   build (KHÔNG có trong zip theme)
```

Đóng gói: tăng `Version` trong `style.css` → `npm run build` → zip thư mục theme (gốc zip là `themeranghammatsg/`), bỏ `src/`, `node_modules/`, `docs/`, `package*.json`, `vite.config.js`, `.gitignore`, `CLAUDE.md`, `AGENTS.md`. File zip lưu ở `docs/` gốc repo: `docs/themeranghammatsg-<version>.zip` (bản gần nhất 1.3.0). Mu-plugin `wp-content/mu-plugins/0-rhm-head-order.php` nằm ngoài theme → deploy riêng.

Sau khi upload theme lên server: xóa cache Swift + LiteSpeed + Cloudflare; loại `themeranghammatsg/dist/js/main.js` khỏi Delay JS của Swift (nếu không, popup đặt lịch và menu mobile chỉ chạy sau tương tác đầu tiên).

### Các module trong `inc/`

| File | Vai trò |
|---|---|
| `setup.php` | theme supports, menu (`primary`, `footer`), image sizes `rhm-*`, `rhm_custom_logo()` (thay `the_custom_logo()`), `RHM_LOGO_HEIGHTS` (cao logo theo breakpoint: ≥1280px 84 · ≥1024px 64 · ≥360px 60 · <360px 50; phải khớp class `h-*` của `.custom-logo` trong `header.css`, logo gốc 1409×592) |
| `enqueue.php` | nạp CSS/JS từ `dist/`, preload font Roboto, cảnh báo admin khi chưa build |
| `cleanup.php` | gỡ block styles, chỉ cho jQuery khi cần |
| `template-tags.php` | helper chung: `rhm_get_option`, `rhm_get_booking_url`, `rhm_booking_button`, `rhm_breadcrumb`, `rhm_pagination`, `rhm_get_picture`, `rhm_list_image_loading`… |
| `external-links.php` | link external tự có `target="_blank" rel="nofollow"`: `rhm_external_link_attrs()`, `rhm_external_links()`, filter `the_content` + menu, filter `rhm_internal_hosts` |
| `hero.php` | slide hero, `rhm_hero_picture`, `rhm_html_attrs` |
| `icons.php` | SVG icon UI inline dùng trong template: `rhm_get_icon()` (không có thì tìm trong thư viện icon menu) |
| `icon-library.php` | thư viện `assets/icons/<bộ>/`: `rhm_icon_list()`, `rhm_icon_url()` (CDN qua theme mod `rhm_icon_base_url`), `rhm_icon_svg()` |
| `menus.php` | icon menu (ô chọn icon ở Giao diện → Menu, meta `_rhm_menu_icon`; class `icon-<tên>` vẫn dùng được) + nút `.submenu-toggle` |
| `doctors.php` / `doctor-fields.php` | CPT `bac-si` (có `page-attributes`), field ACF (các tab hồ sơ + tab "Trang chủ": `home`, `degree`, `years`, `case_count`, `credentials`), `rhm_get_doctor()`, ảnh bác sĩ |
| `doctor-box.php` | shortcode `[bac_si_tham_van id="" label=""]` trong bài viết |
| `post-extras.php` / `post-fields.php` | ACF bài viết: người tham vấn, FAQ, tài liệu tham khảo; nút Hiện/Ẩn FAQ và tài liệu tham khảo từng bài (`rhm_post_section_visible()`) |
| `post-meta.php` | bật/tắt meta bài viết |
| `toc.php` | mục lục tự động (marker `<!--rhm-toc-->`, shortcode) |
| `content.php` | bọc `<table>` để cuộn ngang |
| `category.php` | query/lưới danh mục (`RHM_CATEGORY_GRID = 12`), bộ lọc chuyên mục |
| `booking-popup.php` | popup đặt lịch (`<dialog>` + form Fluent, in cuối trang qua `template-parts/components/booking-popup.php`): `rhm_booking_popup_enabled()`, `rhm_booking_popup_form_id()`, Customizer "Popup đặt lịch". Không in ở trang Liên hệ (nút đặt lịch ở đó trỏ `#dat-lich`) |
| `floating-cta.php` | nút liên hệ nhanh (desktop cột phải, mobile thanh đáy): `rhm_get_floating_cta_items()`, Customizer "Nút liên hệ nhanh" (5 nút: nhãn, icon bộ `cta/`, hành động popup/link, link, màu) |
| `home-doctors.php` | section "Đội ngũ bác sĩ" trang chủ (`template-parts/sections/doctors.php`, slider 1 bác sĩ/slide, nằm trước `news`): `rhm_home_doctor_ids()` + `rhm_get_home_doctors()` (hồ sơ bật field `rhm_doctor_home` và có ảnh đại diện, sắp `menu_order ASC, date DESC`, tối đa `RHM_HOME_DOCTORS_MAX` = 8; chưa bật hồ sơ nào → `RHM_HOME_DOCTORS_FALLBACK` = 6 hồ sơ mới nhất; chứng chỉ tối đa `RHM_HOME_DOCTORS_CREDENTIALS` = 5 dòng, trống thì lấy "Học vấn"), `rhm_home_doctors_text()`, Customizer "Đội ngũ bác sĩ (Trang chủ)" (eyebrow, tiêu đề, chữ + link "Xem tất cả bác sĩ") |
| `news.php`, `about.php`, `quick-services.php`, `certificates.php`, `footer.php`, `contact.php`, `flag-links.php` | dữ liệu từng khối (đọc từ Customizer) |
| `customizer.php`, `customize-list.php`, `class-rhm-customize-list-control.php`, `class-rhm-customize-icon-control.php` | panel Customizer + control repeater dạng list (JSON) + control chọn icon (kiểu field `rhm-icon`) |
| `schema.php` | bổ sung Person/bác sĩ vào graph Yoast (`wpseo_schema_graph`) |
| `svg-upload.php` | cho upload SVG, chặn SVG không an toàn |
| `disable-comments.php` | tắt hoàn toàn bình luận/pingback |
| `landing/` | Landing page theo chủ đề (Page Template + ACF Flexible Content `rhm_landing_sections`): `rhm_landing_templates()` (chủ đề → bundle CSS + layout được dùng; `rhm_page_style_bundle()` đọc từ đây), `rhm_landing_layouts()`, `rhm_landing_render()`; field 1 group/chủ đề ở `fields.php` + `layouts/<layout>.php`. Hướng dẫn: `docs/landing-pages.md` |
| `plugins.php` | require các file `inc/plugin/*` |
| `plugin/fluentform.php` | Fluent Forms: jQuery + JS "chạm mới tải", `rhm_render_fluent_form()` (form của theme), gỡ CSS Fluent khi trang chỉ có form của theme |
| `plugin/kk-star-ratings.php` | kk Star Ratings: `rhm_get_star_rating()` (luôn tồn tại, trả rỗng khi plugin tắt), chỉ giữ CSS/JS của plugin ở bài viết có sao |

## Quy ước quan trọng

### CSS
- Trang trí/icon tích của section bác sĩ trang chủ là pseudo-element trong `page-home.css` (`.home-doctor-card`, `.home-doctor-photo`, `.home-doctor-check`) thay vì phần tử HTML → giữ DOM nhỏ (~35 node/bác sĩ; DOM trang chủ hiện ~825 node, mục tiêu < 800).
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
- Landing page: chủ đề mới / layout mới → làm theo `docs/landing-pages.md` (không viết cứng section trong `page-templates/`). Partial CSS dùng chung với trang chủ: `_carousel.css`, `_home-doctors.css`.
- Section mới cho trang chủ → `template-parts/sections/<tên>.php` + thêm vào mảng trong `front-page.php` + `@source` file `inc/` của nó trong `src/css/pages/page-home.css`.
- Slider dùng chung `src/js/carousel.js` (`.js-carousel` > `.swiper`, Swiper chỉ tải khi sắp vào màn hình). Nút ‹ › của section bác sĩ nằm ở hàng tiêu đề cạnh link "Xem tất cả" (không đè mép thẻ → không chồng lên cột nút liên hệ nhanh bên phải); chỉ in khi ≥ 2 bác sĩ.
- Popup đặt lịch: `<dialog id="rhm-booking-popup" closedby="any">`; ID form Fluent nhập ở Customizer "Popup đặt lịch" (theme mod `rhm_popup_form_id`, trống thì dùng `rhm_booking_form_id`). `main.js` gọi `showModal()`, phát `focusin` để `fluent-lazy` tải script form, chỉ tự focus ô đầu tiên khi `(pointer: fine)`.
- Nút liên hệ nhanh: tối đa `RHM_FLOATING_CTA_MAX` nút, màu trong `RHM_FLOATING_CTA_COLORS`; nút "link" chưa nhập link (vd Messenger, Zalo) bị ẩn. Số nút lẻ → nút giữa có class `floating-cta__item--main` (nổi lên trên thanh đáy mobile).
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
