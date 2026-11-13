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
│   ├── sections/             trang chủ (thứ tự trong front-page.php): hero, quick-services, about, about-2, doctors, doctors-2, before-after, news, branches, booking-cta
│   │                         (doctors, booking-cta nhận $args id/title… để landing dùng lại; certificates hiện không dùng ở đâu)
│   ├── landing/              section landing page: hero, banner, highlights, cards, services, doctor-cards, feature/tiles/promo (dạng ô, xếp hàng theo Độ rộng), intro,
│   │                         text-image, image-stack, icon-list, photo-groups, stats, timeline, gallery, clinics, doctors, form, faq, editor, booking
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
│   │   │                     page-about (landing "Giới thiệu"), page-contact, page, single-post, single-doctor, archive, 404
│   │   └── components/       partial: _booking-form, _booking-popup, _floating-cta, _doctor-box, _entry-content,
│   │                         _fonts, _pagination, _toc, _carousel + _home-doctors (dùng chung page-home + page-about)
│   └── js/                   main.js (header sticky, menu mobile/submenu, mở popup đặt lịch), home (hero), carousel
│                             (+ _carousel nạp động; + _tiles: nút ‹ › của landing "Ô lưới ảnh", nạp động khi có
│                             [data-rhm-tiles-controls]; + _showcase: dải ảnh nhỏ ↔ slider landing "Hệ thống phòng khám"), dropdown, embed (facade video/bản đồ), fluent-lazy
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

Đóng gói: tăng `Version` trong `style.css` → `npm run build` → zip thư mục theme (gốc zip là `themeranghammatsg/`), bỏ `src/`, `node_modules/`, `docs/`, `package*.json`, `vite.config.js`, `.gitignore`, `CLAUDE.md`, `AGENTS.md`. File zip lưu ở `docs/` gốc repo: `docs/themeranghammatsg-<version>.zip` (bản gần nhất **1.4.15**, 2026-11-13). Các bản gần đây:
- 1.4.15: sửa PHP Warning `Undefined array key "link"/"name"` ở `tpl-lien-he.php` (khối chi nhánh trang Liên hệ còn đọc cấu trúc danh sách nhập tay cũ): `rhm_get_footer_branches()` trả thêm `name` + `map_url`, trang Liên hệ in tên chi nhánh (link Google Maps nếu có).
- 1.4.14: thêm `screenshot.png` (ảnh theme trong Giao diện → Giao diện: logo phòng khám 591 × 592, 87 kB; WP khuyến nghị 1200 × 900, ảnh vuông bị thu nhỏ/cắt trong khung 4:3). Code giống 1.4.13.
- 1.4.13: landing thêm `icon-list`.
- 1.4.12: landing thêm `banner`, `text-image`, `image-stack`, `photo-groups`, `clinics`, `form` (+ không in popup đặt lịch khi trang landing có section `form` trùng form popup).

Mu-plugin `wp-content/mu-plugins/0-rhm-head-order.php` nằm ngoài theme → deploy riêng.

Tạo zip bằng Python `zipfile` (đường dẫn trong zip dùng `/`, giải nén trên server Linux không lỗi) — KHÔNG dùng `Compress-Archive` của Windows PowerShell 5.1 (ghi `\` vào đường dẫn). Kiểm tra trước khi zip: `php -l` mọi file PHP, `node --check assets/admin/customize-list.js`. Không ghi đè zip đã có: phiên bản mới = file mới. Plugin dữ liệu mẫu đóng gói tương tự: `docs/rhm-demo-data-<version>.zip`, gốc zip `rhm-demo-data/` (bản gần nhất 1.1.1).

Sau khi upload theme lên server: xóa cache Swift + LiteSpeed + Cloudflare; loại `themeranghammatsg/dist/js/main.js` khỏi Delay JS của Swift (nếu không, popup đặt lịch và menu mobile chỉ chạy sau tương tác đầu tiên). Chi nhánh không tự hiện: chọn ở Tùy biến → Chân trang → "Chi nhánh hiển thị" (footer) và Tùy biến → "Chi nhánh (Trang chủ)" (section trang chủ); để trống thì cột/section bị ẩn.

### Các module trong `inc/`

| File | Vai trò |
|---|---|
| `setup.php` | theme supports, menu (`primary`, `footer`), image sizes `rhm-*` (gồm `rhm-672`: lấp khoảng 640→768 cho PSI mobile 412px × DPR 1.75 = ảnh khung nội dung cần 665px; thêm size mới thì phải tạo lại thumbnail ảnh cũ trên server), `rhm_custom_logo()` (thay `the_custom_logo()`), `RHM_LOGO_HEIGHTS` (cao logo theo breakpoint: ≥1280px 84 · ≥1024px 64 · ≥360px 60 · <360px 50; phải khớp class `h-*` của `.custom-logo` trong `header.css`, logo gốc 1409×592) |
| `enqueue.php` | nạp CSS/JS từ `dist/`, preload font Roboto, cảnh báo admin khi chưa build |
| `cleanup.php` | gỡ block styles, chỉ cho jQuery khi cần |
| `template-tags.php` | helper chung: `rhm_get_option`, `rhm_get_booking_url`, `rhm_booking_button`, `rhm_breadcrumb`, `rhm_pagination`, `rhm_get_picture`, `rhm_list_image_loading`… |
| `external-links.php` | link external tự có `target="_blank" rel="nofollow"`: `rhm_external_link_attrs()`, `rhm_external_links()`, filter `the_content` + menu, filter `rhm_internal_hosts` |
| `hero.php` | slide hero, `rhm_hero_picture`, `rhm_html_attrs` |
| `icons.php` | SVG icon UI inline dùng trong template: `rhm_get_icon()` (không có thì tìm trong thư viện icon menu) |
| `icon-library.php` | thư viện `assets/icons/<bộ>/`: `rhm_icon_list()`, `rhm_icon_url()` (CDN qua theme mod `rhm_icon_base_url`), `rhm_icon_svg()` |
| `menus.php` | icon menu (ô chọn icon ở Giao diện → Menu, meta `_rhm_menu_icon`; class `icon-<tên>` vẫn dùng được) + nút `.submenu-toggle` |
| `doctors.php` / `doctor-fields.php` | CPT `bac-si` (có `page-attributes`) + taxonomy `chuyen-khoa` (chỉ admin), field ACF (các tab hồ sơ + tab "Trang chủ": `degree`, `years`, `case_count`, `credentials`), `rhm_get_doctor()`, ảnh bác sĩ |
| `branches.php` | CPT `chi-nhanh` "Chi nhánh" (chỉ dữ liệu, không có trang công khai) + taxonomy `danh-muc-chi-nhanh` + ảnh đại diện + field ACF `rhm_branch_address` / `rhm_branch_hotline` / `rhm_branch_map_url`: `rhm_get_branches( [ 'term' => slug ] )` (cache transient), `rhm_get_branch_field()`, cột Địa chỉ/Hotline trong admin. Footer đọc qua `rhm_get_footer_branches()` (`footer.php`) |
| `doctor-box.php` | shortcode `[bac_si_tham_van id="" label=""]` trong bài viết |
| `post-extras.php` / `post-fields.php` | ACF bài viết: người tham vấn, FAQ, tài liệu tham khảo; nút Hiện/Ẩn FAQ và tài liệu tham khảo từng bài (`rhm_post_section_visible()`) |
| `post-meta.php` | bật/tắt meta bài viết |
| `toc.php` | mục lục tự động (marker `<!--rhm-toc-->`, shortcode) |
| `content.php` | bọc `<table>` để cuộn ngang |
| `category.php` | query/lưới danh mục (`RHM_CATEGORY_GRID = 12`), bộ lọc chuyên mục |
| `booking-popup.php` | popup đặt lịch (`<dialog>` + form Fluent, in cuối trang qua `template-parts/components/booking-popup.php`): `rhm_booking_popup_enabled()`, `rhm_booking_popup_form_id()`, Customizer "Popup đặt lịch". Không in ở trang Liên hệ (nút đặt lịch ở đó trỏ `#dat-lich`) và ở trang landing có section `form` dùng cùng form với popup (`rhm_landing_booking_form_anchor()`, nút đặt lịch trỏ `#<id section>`) — tránh trùng id HTML của Fluent |
| `floating-cta.php` | nút liên hệ nhanh (desktop cột phải, mobile thanh đáy): `rhm_get_floating_cta_items()`, Customizer "Nút liên hệ nhanh" (5 nút: nhãn, icon bộ `cta/`, hành động popup/link, link, màu) |
| `home-doctors.php` | section "Đội ngũ bác sĩ" trang chủ (`template-parts/sections/doctors.php`, slider 1 bác sĩ/slide, nằm trước `news`): `rhm_home_doctor_ids()` + `rhm_get_home_doctors()` (MỌI hồ sơ có ảnh đại diện, sắp `menu_order ASC, date DESC`, tối đa `RHM_HOME_DOCTORS_MAX` = 8; field `rhm_doctor_home` đã bỏ — tab "Trang chủ" chỉ còn ghi chú, meta cũ không được đọc; chứng chỉ tối đa `RHM_HOME_DOCTORS_CREDENTIALS` = 5 dòng, trống thì lấy "Học vấn"), `rhm_home_doctors_text()`, Customizer "Đội ngũ bác sĩ (Trang chủ)" (eyebrow, tiêu đề, chữ + link "Xem tất cả bác sĩ", **Chuyên khoa**). Nhiều bản: `rhm_home_doctor_sections()` (tiền tố theme mod = id mục Customizer): bản 1 `rhm_home_doctors_*`, bản 2 `rhm_home_doctors_2_*` (`sections/doctors-2.php` gọi `doctors.php` với `$args['prefix']`, `require_term` → chỉ hiện khi đã chọn chuyên khoa). `rhm_home_doctor_term( $prefix )`, `rhm_get_home_doctors( $term )` (static cache theo term; term > 0 thêm `tax_query` chuyen-khoa + con), `rhm_home_doctors_text( $key, $prefix )`. Id tên bác sĩ có tiền tố id section (bản gốc không id → `doctor-<id>-name`). DOM: mỗi slider ~35 node/bác sĩ, bật bản 2 làm DOM trang chủ vượt mục tiêu 800 |
| `home-branches.php` | section "Hệ thống chi nhánh" trang chủ (`template-parts/sections/branches.php`, slider `carousel.js`, nằm sau `news`): `rhm_get_home_branches()` = chi nhánh chọn + sắp thứ tự ở Tùy biến → "Chi nhánh (Trang chủ)" (theme mod `rhm_home_branch_ids`, dùng lại `rhm_footer_branch_fields()`), chỉ lấy chi nhánh đủ tên + địa chỉ + ảnh đại diện, bỏ ID trùng; danh sách ĐỘC LẬP với footer, chi nhánh mới không tự xuất hiện; để trống → ẩn section. Tiêu đề: `rhm_home_branches_title` (mặc định `rhm_home_branches_default_title()`). CSS: `.home-branches .swiper-slide` trong `page-home.css` |
| `home-before-after.php` | section "Ảnh trước – sau" trang chủ (`template-parts/sections/before-after.php`, sau `doctors-2`, trước `news`): Tùy biến → **Trước – sau (Trang chủ)** = eyebrow `rhm_home_ba_eyebrow`, tiêu đề `rhm_home_ba_title` + danh sách `rhm_home_ba_items` (control list, "Thêm mới"/↑↓/xóa; field `rhm_home_ba_fields()`: `portrait`, `before`, `after` = ID ảnh, `name`, `location`, `service`, `product`). `rhm_get_home_before_after()` chỉ lấy mục đủ tên + 3 ảnh, tối đa `RHM_HOME_BA_MAX` = 12; trống → ẩn section. Slider native (scroll-snap + `_tiles.js`, không Swiper): ≥ 1024px mỗi trang 4 thẻ 2×2 (wrapper trang `contents lg:grid`), 768–1023px 2 thẻ/khung, < 768px 1 thẻ 88%. Thẻ: chân dung trái (cao bằng cột phải, nên dùng ảnh dọc) + Trước/Sau 3:2 bên phải (nhãn `bg-deep`), tên H3 `text-primary` + tỉnh, 2 dòng icon `menu/tooth`, `menu/implant`. DOM ~22 node/thẻ |
| `about.php` | section Giới thiệu trang chủ, nhiều bản cùng 1 template `template-parts/sections/about.php`: `rhm_about_sections()` (tiền tố theme mod = id mục Customizer => `id` section, `reverse`, nhãn). Bản 1 `rhm_about_*` (ảnh trái), bản 2 `rhm_about_2_*` (desktop ảnh phải, chữ trái; mobile vẫn ảnh trên; `sections/about-2.php` gọi `about.php` với `$args['prefix']`). `rhm_get_about( $prefix )`; Customizer `rhm_customize_about_section()` (`inc/customizer.php`). Trống Tiêu đề → ẩn bản đó. Class đảo chiều viết nguyên văn trong `about.php` |
| `news.php`, `quick-services.php`, `certificates.php`, `footer.php`, `contact.php`, `flag-links.php` | dữ liệu từng khối (đọc từ Customizer). Cột "Hệ thống đối tác" ở footer: chọn + sắp thứ tự chi nhánh (CPT) trong Tùy biến → Chân trang → "Chi nhánh hiển thị" (theme mod `rhm_footer_branch_ids`, `rhm_footer_branch_fields()` / `rhm_footer_branch_choices()`), `rhm_get_footer_branches()` trả địa chỉ theo đúng thứ tự đã chọn. `certificates.php`: Tùy biến "Chứng nhận (Landing page)" — hiện không còn hiển thị ở đâu (đã bỏ khỏi trang chủ và landing page; giữ code + theme mod để dùng lại nếu cần) |
| `customizer.php`, `customize-list.php`, `class-rhm-customize-list-control.php`, `class-rhm-customize-icon-control.php` | panel Customizer + control repeater dạng list (JSON; field `[nhãn, kiểu, choices]`, kiểu `image` / `text` / `textarea` / `url` / `select` — `select` chỉ lưu giá trị có trong `choices`, JS ở `assets/admin/customize-list.js`) + control chọn icon (kiểu field `rhm-icon`) |
| `schema.php` | bổ sung Person/bác sĩ vào graph Yoast (`wpseo_schema_graph`) |
| `svg-upload.php` | cho upload SVG, chặn SVG không an toàn |
| `disable-comments.php` | tắt hoàn toàn bình luận/pingback |
| `landing/` | Landing page theo chủ đề (Page Template + ACF Flexible Content `rhm_landing_sections`): `rhm_landing_templates()` (chủ đề → bundle CSS + layout được dùng; `rhm_page_style_bundle()` đọc từ đây), `rhm_landing_layouts()`, `rhm_landing_render()`; field 1 group/chủ đề ở `fields.php` + `layouts/<layout>.php`. Hướng dẫn: `docs/landing-pages.md` |
| `plugins.php` | require các file `inc/plugin/*` |
| `plugin/fluentform.php` | Fluent Forms: jQuery + JS "chạm mới tải", `rhm_render_fluent_form()` (form của theme), gỡ CSS Fluent khi trang chỉ có form của theme |
| `plugin/kk-star-ratings.php` | kk Star Ratings: `rhm_get_star_rating()` (luôn tồn tại, trả rỗng khi plugin tắt), chỉ giữ CSS/JS của plugin ở bài viết có sao |

## Quy ước quan trọng

### CSS
- Trang trí/icon tích của section bác sĩ trang chủ là pseudo-element trong `components/_home-doctors.css` (import vào `page-home.css` + `page-about.css`; `.home-doctor-card`, `.home-doctor-photo`, `.home-doctor-check`) thay vì phần tử HTML → giữ DOM nhỏ (~35 node/bác sĩ; DOM trang chủ hiện ~825 node, mục tiêu < 800).
- `footer.css` còn chứa form đặt lịch, popup đặt lịch và nút liên hệ nhanh (in sau `</footer>`, ngoài mọi vùng utility → viết bằng class component, không dùng utility Tailwind trong 2 template đó).
- Mọi trang nạp `base` + `header` + `footer` + **đúng 1** file theo loại trang do `rhm_page_style_bundle()` (`inc/enqueue.php`) chọn:
  `page-home`, `page-about`, `page-contact`, `single-post`, `single-doctor`, `page`, `404`, `archive`.
  Thêm loại trang mới: tạo `src/css/pages/<tên>.css` + thêm nhánh `match` trong `rhm_page_style_bundle()`.
  Landing page: bundle khai báo trong `rhm_landing_templates()` (`inc/landing/landing.php`), `rhm_page_style_bundle()` tự đọc qua `rhm_landing_style_bundle()` — không thêm nhánh `match`.
- **Tailwind chia theo vùng DOM**: `header.css` → utility chỉ trong `.site-header`; các file trang → `.site-main`; `footer.css` → `.site-footer`. Lý do: tránh utility thường ở file nạp sau đè utility responsive ở file nạp trước. Đặt class Tailwind vào đúng vùng tương ứng với file CSS sẽ biên dịch nó.
- Dùng `.site-container` (không dùng `.container` của Tailwind — đã bị loại).
- Token trong `src/css/theme.css`: màu `primary`, `dark`, `teal`, `mint`, `deep`, `muted`, `line`, `soft`, `footer-*`…; font duy nhất Roboto (tự host); breakpoint thêm `xs` = 560px; `max-w-site` = 1440px; `shadow-card`. Ưu tiên token thay vì mã màu cứng.

### PHP
- Mỗi file mở đầu `defined( 'ABSPATH' ) || exit;`, có type hint, theo WordPress Coding Standards.
- Tính năng mới → file mới trong `inc/` + `require_once` trong `functions.php`. Tích hợp plugin mới → `inc/plugin/<tên-thư-mục-plugin>.php` + require trong `inc/plugins.php`; tự kiểm tra plugin bật (`function_exists`, `shortcode_exists`…).
- Landing page: xem mục **Landing page** bên dưới và `docs/landing-pages.md` (không viết cứng section trong `page-templates/`).
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

## Landing page (Page + Page Template + ACF Flexible Content)

Chi tiết nhập liệu + cách thêm layout/chủ đề: `docs/landing-pages.md`. Code: `inc/landing/` (điểm vào `landing.php`, require trong `functions.php`).

- **Chủ đề** = Page Template, khai báo trong `rhm_landing_templates()` (filter `rhm_landing_templates`): `slug` (tiền tố key ACF — **không đổi** khi đã có dữ liệu), `label`, `bundle` CSS, `layouts` cho phép. Hiện có 1 chủ đề: `page-templates/tpl-gioi-thieu.php` (slug `about`, bundle `page-about`, dùng mọi layout). Template chỉ gọi `rhm_landing_render( get_the_ID() )` trong vòng lặp `the_post()`.
- **Layout** = thư viện section dùng chung, khai báo trong `rhm_landing_layouts()` (`label`, `boxed`). Mỗi layout: field ở `inc/landing/layouts/<key>.php` (hàm `rhm_landing_layout_<key>_fields( $prefix )`, gạch ngang trong key → gạch dưới) + hiển thị ở `template-parts/landing/<key>.php`.
  Layout hiện có: `hero`, `banner`, `highlights`, `cards`, `services`, `doctor-cards`, `feature`, `tiles`, `promo` (3 cái cuối là dạng ô) — `feature`/`promo` là lưới ảnh + chữ 2 cột CHỈ từ `sm` (640px), dưới đó xếp dọc (`sizes` có nhánh `(max-width: 639px) calc(100vw - 74px)` đặt trước `rhm_landing_box_sizes()`), `intro`, `text-image`, `image-stack`, `icon-list`, `photo-groups`, `stats`, `timeline`, `gallery`, `clinics`, `doctors`, `form`, `faq`, `editor`, `booking`.
  Layout thêm 2026-11-13, mỗi cái có mục riêng bên dưới: `banner`, `text-image`, `image-stack`, `icon-list`, `photo-groups`, `clinics`, `form`.
- **Field ACF**: `inc/landing/fields.php` đăng ký 1 field group / chủ đề (`group_rhm_landing_<slug>`, location `page_template`), 1 Flexible Content `rhm_landing_sections`. Key: `field_rhm_landing_<slug>_<layout>_<field>` (repeater con: `…_<repeater>_<field>`), name ngắn giống nhau giữa các chủ đề. Mọi layout tự có field chung `show` (Hiện/Ẩn) + `anchor` (id section); layout dạng ô có thêm `width` (`RHM_LANDING_SPANS` = 10, 5, 4, 6 phần mười hàng). Ảnh luôn trả **ID**. Helper: `rhm_landing_field()`, `rhm_landing_image_field()`, `rhm_landing_wysiwyg_field()`, `rhm_landing_button_fields()`, `rhm_landing_icon_field()`.
- **Render** (`rhm_landing_render()`): bỏ section Ẩn / layout chủ đề không cho phép; section đầu không phải `hero` → in `components/page-title` (H1); chưa có section nào → tiêu đề + nội dung editor + `booking-cta` (giao diện cũ). Layout dạng ô in `<section class="h-full …">` KHÔNG tự bọc `.site-container`: các ô liền nhau được gom vào hàng `.site-container grid lg:grid-cols-10` cho tới khi đủ 10 phần (ô không in gì thì bỏ qua). Neo trùng → thêm hậu tố `-2`, `-3`….
- **$args** của template-part: `data`, `index`, `is_first` (section đầu → ảnh là LCP: `fetchpriority="high"`, không lazy), `id` (id section; id tiêu đề = `<id>-title`), `prev` (layout liền trước, vd `highlights` sau `hero` thì đè lên mép banner), `span` (chỉ dạng ô).
- **Chỉ đúng 1 H1**: `hero` là H1 + breadcrumb CHỈ khi đứng đầu; ở vị trí khác là H2, ảnh lazy.
- Helper đọc dữ liệu (trả giá trị đã làm sạch, chưa escape): `rhm_landing_text/lines/image/gallery/rows/icon/url/html()`. Helper in: `rhm_landing_heading( $id, $title, $eyebrow, $desc, $style )` (`center` | `bar` | `box` | `plain`), `rhm_landing_button()`, `rhm_landing_picture( $id, $sizes, $class, $priority, $mobile, $mobile_sizes, $alt )` (srcset + ảnh mobile art direction; `$alt` null = alt attachment, `''` = ảnh minh họa có chữ đi kèm), `rhm_landing_box_sizes( $span, $fraction, $mobile_fraction, $offset )` (`sizes` cho ảnh trong ô), `rhm_landing_enqueue_module( 'carousel' | 'embed' )` (chỉ gọi khi section cần).
- Class Tailwind của layout phải viết nguyên văn trong `template-parts/landing/*.php` hoặc `inc/landing/landing.php` (2 chỗ được `@source` trong `page-about.css`). CSS riêng của landing (nền `.rhm-landing`, `.landing-hero` + mask ảnh, vạch `.landing-title-center`) nằm trong `@layer components` của `page-about.css`.
- Icon: bộ `assets/icons/landing/` (24×24, nét `stroke="currentColor"`, in inline bằng `rhm_icon_svg( 'landing', … )`); thêm icon = chép file `.svg` có `<title>` tiếng Việt.
- `doctors`, `booking` dùng lại `template-parts/sections/*` của trang chủ, truyền `$args` (`id`, `title`, `eyebrow`/`text`); trống thì lấy chữ của Customizer / `rhm_booking_cta_default()`.
- **Ngân sách CSS**: `page-about.css` ≈ 9,5 kB gzip (bản 1.4.13, 2026-11-13; từ 8,85 kB trước đợt thêm section: `clinics` +0,4 kB, `form` +0,1 kB, `photo-groups` +0,08 kB, `icon-list` +0,07 kB, `banner` / `text-image` / `image-stack` ~0) → tổng CSS trang Giới thiệu ≈ 21,8 kB, VƯỢT 20 kB vì chủ đề giữ cả các section slider (Swiper CSS + `_home-doctors.css`). Muốn giảm: bỏ `gallery`, `doctors` khỏi `layouts` của chủ đề và bỏ import/`@source` tương ứng trong `page-about.css`.
- **Dữ liệu mẫu**: plugin `rhm-demo-data` (≥ 1.1.0; ≥ 1.2.0 có `banner`, `clinics`, `text-image`, `image-stack`, `icon-list` (2 section: ảnh phải + ảnh dưới), `photo-groups`, `form` (form_id trống → form đặt lịch mặc định; neo `dang-ky`, banner mẫu link tới đó), ghi được gallery nằm trong repeater và caption ảnh qua option `caption` của `RHM_Demo_Images::get()`) tạo trang "Giới thiệu (dữ liệu mẫu)" `/gioi-thieu-mau/` đủ mọi section (Công cụ → Dữ liệu mẫu RHM → "Tạo trang landing mẫu", hoặc `wp rhm-demo landing`). Plugin ghi meta theo định dạng ACF với key field ở trên (`rhm-demo-data/includes/class-rhm-demo-landing.php`) → **đổi/thêm field của layout thì sửa cả file đó**.
- Sau deploy: loại ảnh banner (`hero`, ảnh LCP) khỏi Lazyload của Swift giống ảnh hero trang chủ.
- **Slider ảnh native** (`tiles`, `clinics`, section trang chủ `before-after`): scroll-snap + `src/js/_tiles.js` (nạp động từ `carousel.js` khi có `[data-rhm-tiles-controls]`). Markup: khối `[data-rhm-tiles]` chứa `[data-rhm-tiles-controls]` > nút `[data-rhm-tiles-prev]` / `[data-rhm-tiles-next]` có `aria-controls` = id track `[data-rhm-tiles-track]`. `_tiles.js` tìm track theo `aria-controls` của nút trước (không có → track đầu tiên của section) → 1 section có nhiều track được; nút ‹ › cuộn đúng 1 phần tử con đầu + gap (con đầu rộng 0 = wrapper `display: contents` → lấy con đầu của nó); khối nút `invisible` khi track không tràn. Không dùng Swiper cho slider mới trong landing (Swiper CSS đã làm `page-about` vượt ngân sách).

### Ảnh rộng hết khung (dùng chung `text-image`, `image-stack`)
- Ảnh rộng hết `.site-container`, class `block h-auto w-full` trong khung `overflow-hidden rounded-2xl bg-soft shadow-card` → **giữ nguyên tỉ lệ file, không cắt** (width/height của `<img>` = kích thước file → CLS 0). Content upload ảnh tỉ lệ nào cũng hiện đủ.
- `sizes` `(min-width: 1440px) 1408px, calc(100vw - 32px)` (khung 1440 − lề 2 × 16). Đã đo: 1440 → file 1440w, 1024 → 1024w, 768 → 768w, 360 → 330w.
- Ảnh đầu của section là LCP (`fetchpriority="high"`, không lazy) CHỈ khi section đứng đầu trang (`$args['is_first']`), còn lại lazy.
- Chữ rộng hết khung chạy tới mép phải → từ `lg` bị cột nút liên hệ nhanh (`fixed`, ~88px từ mép phải màn hình) đè lên cuối dòng. Ảnh thì không sao. `image-stack` tránh bằng `max-w-4xl` cho mô tả; `text-image` hiện CHƯA tránh (người dùng chọn full width, chưa quyết việc chừa `lg:pr-24`).

### Section `banner` (Banner ảnh desktop 1920 × 650 / mobile 828 × 620)
- **File**: field `inc/landing/layouts/banner.php` (`rhm_landing_layout_banner_fields()`), hiển thị `template-parts/landing/banner.php`. Không JS, không CSS riêng.
- **Field** (key `field_rhm_landing_about_banner_*`): `image` (desktop, `required`), `image_mobile` (tùy chọn), `link` (`rhm_landing_url()`: http(s), tương đối, `#neo`, tel/mailto; external tự `target="_blank" rel="nofollow"`), `layout` (button_group `full` mặc định | `container`). Không có ảnh desktop → không in.
- **Ảnh**: `rhm_landing_picture( $image, $sizes, 'block h-auto w-full', is_first, $mobile, $mobile_sizes )` → `<source media="(max-width: 767px)">` ảnh mobile + `<img>` ảnh desktop, mỗi nhánh có width/height của file → giữ tỉ lệ, CLS 0, mỗi màn hình tải đúng 1 file. `sizes`: `full` = `100vw` (cả 2 nhánh); `container` = `(min-width: 1440px) 1408px, calc(100vw - 32px)` / mobile `calc(100vw - 32px)`. Không ảnh mobile → nhánh `<source>` bị bỏ, ảnh desktop dùng mọi màn hình.
- **Markup**: `<div id="<id>" class="py-6 lg:py-8">` (không phải `<section>` vì không có tiêu đề) → `full`: không bọc `.site-container`, tràn mép màn hình, không bo góc; `container`: `.site-container` + khung `overflow-hidden rounded-2xl shadow-card`. Có link → `<a>` bọc cả ảnh, tên link = alt của ảnh (`rhm_landing_image_alt()`: alt → tiêu đề attachment, không rỗng).
- **LCP**: section đứng đầu trang → `fetchpriority="high"` (không lazy); trang vẫn in H1 `components/page-title` phía trên (section đầu không phải `hero`). Sau deploy: loại ảnh banner khỏi Lazyload của Swift nếu đặt đầu trang.
- **Đã đo** (2026-11-13, trang mẫu, `full`): 360 → `banner-mobile-412x309`, 412@1,75x → `mobile-768x575`, 767 → ảnh mobile, 768 → `banner-768x260` (đổi nhánh đúng mốc), 1024/1440 → file cùng cỡ, 1920 → file gốc; mỗi lần chỉ 1 request ảnh banner; khung cao = ảnh (không hở dưới `<picture>` inline); không tràn ngang, CLS 0, console sạch.

### Section `text-image` (Tiêu đề + nội dung dài + ảnh lớn bên dưới)
Thiết kế: cụm tiêu đề canh giữa → nội dung dài → 1 ảnh lớn rộng hết khung ngay dưới nội dung.

- **File**: field `inc/landing/layouts/text-image.php` (`rhm_landing_layout_text_image_fields()`), hiển thị `template-parts/landing/text-image.php`. Không JS, không CSS riêng.
- **Field** (key `field_rhm_landing_about_text-image_*`): `title` (H2, bắt buộc), `eyebrow`, `content` (`rhm_landing_wysiwyg_field()` với `toolbar` = `full`, không upload ảnh; hướng dẫn content dùng "Tiêu đề 3" cho tiêu đề phụ vì trang đã có H1, section đã có H2), `image` (ID). Hiện khi có `title` + (`content` hoặc `image`).
- **Hiển thị**: `rhm_landing_heading( …, 'center' )` → `<div class="entry-content">` (in qua `rhm_landing_html()`: `wp_kses_post` + link external) **rộng hết khung** — người dùng yêu cầu full width, đã bỏ cột đọc `max-w-[868px]` của bản đầu → ảnh (`mt-6 lg:mt-8` nếu có nội dung).

### Section `image-stack` (Ảnh lớn xếp dọc, kèm mô tả ảnh)
Thiết kế: tiêu đề → ảnh → ảnh → ảnh… (xếp dọc), mỗi ảnh rộng hết khung, mô tả ngay dưới ảnh.

- **File**: field `inc/landing/layouts/image-stack.php` (`rhm_landing_layout_image_stack_fields()`), hiển thị `template-parts/landing/image-stack.php`. Không JS, không CSS riêng.
- **Field** (key `field_rhm_landing_about_image-stack_*`): `title` (H2, bắt buộc), `eyebrow`, `images` (gallery, trả ID, tối đa 30, kéo thả sắp thứ tự). Hiện khi có `title` + ≥ 1 ảnh hợp lệ (`rhm_landing_gallery()` nạp sẵn cache attachment, bỏ ID đã xóa).
- **Mô tả ảnh = caption của attachment** (`wp_get_attachment_caption()`), KHÔNG phải field ACF: content sửa ở ô "Chú thích" khi bấm ảnh trong khung gallery ACF (hoặc thư viện Media) → sửa 1 chỗ, mọi nơi dùng ảnh đó đổi theo. Cần mô tả khác nhau cho cùng 1 ảnh ở 2 trang thì phải đổi sang repeater (ảnh + mô tả).
- **Hiển thị**: mỗi ảnh 1 `<figure>` (cách nhau `space-y-8 lg:space-y-12`); `<figcaption>` chỉ in khi caption không trống, chữ thuần (`esc_html`), xuống dòng → `<br>` (qua `rhm_landing_lines()`), canh giữa `mx-auto max-w-4xl` (dễ đọc + không chạy tới cột nút liên hệ nhanh).
- **Đã đo** (2026-11-13, trang mẫu, 3 ảnh tỉ lệ 2,29 / 1,6 / 1,78, ảnh 3 không caption): giữ đúng tỉ lệ, không tràn ngang 360–1440px, CLS 0, console sạch, ảnh không caption không có `<figcaption>`.

### Section `icon-list` (Danh sách có icon + ảnh trái / phải / trên / dưới)
- **File**: field `inc/landing/layouts/icon-list.php` (`rhm_landing_layout_icon_list_fields()`), hiển thị `template-parts/landing/icon-list.php`. Không JS, không CSS riêng.
- **Field** (key `field_rhm_landing_about_icon-list_*`): `title` (H2, bắt buộc), `eyebrow`, `description` (textarea, tùy chọn), `icon` (`rhm_landing_icon_field()`, bộ `assets/icons/landing/`, mặc định `tich`; trống → `tich`), `items` repeater (table, ≤ 30): `text` (textarea, xuống dòng → `<br>`), `icon` (icon riêng, trống → icon của danh sách); `image` (ID, tùy chọn), `image_position` (button_group `left` | `right` mặc định | `top` | `bottom`). Dòng trống bỏ qua; không còn dòng → không in.
- **Bố cục**: ảnh trái/phải → `grid items-center lg:grid-cols-2 lg:gap-8`, tiêu đề kiểu `bar` trong cột chữ, danh sách 1 cột; thứ tự DOM = thứ tự mobile (left: ảnh → chữ, right: chữ → ảnh). Trên/dưới hoặc không ảnh → tiêu đề `center`, mô tả canh giữa `max-w-4xl`, danh sách `lg:grid-cols-2`, ảnh rộng hết khung trước/sau danh sách.
- **Dòng danh sách**: icon trong vòng tròn `bg-mint text-primary` (`size-9 lg:size-10`, icon `size-5 lg:size-6`, `aria-hidden`) + chữ `pt-1.5 lg:pt-2` cho thẳng hàng với icon.
- **Cột nút liên hệ nhanh**: cột chữ chạm mép phải khung (ảnh trái, trên, dưới, không ảnh) có `lg:pr-24` → chữ dừng ~1328px ở màn 1440, không bị cột nút (từ ~1352px) đè. Ảnh phải thì ảnh nằm ở mép phải, không cần.
- **Ảnh**: `block h-auto w-full` (giữ tỉ lệ) trong khung `overflow-hidden rounded-2xl bg-soft shadow-card`; `sizes` trái/phải `(min-width: 1440px) 688px, (min-width: 1024px) calc(50vw - 32px), calc(100vw - 32px)`, trên/dưới `(min-width: 1440px) 1408px, calc(100vw - 32px)`. LCP khi section đứng đầu.
- **Đã đo** (2026-11-13, trang mẫu, đủ 4 vị trí): đúng thứ tự mobile, 2 cột desktop (ảnh 688px ở 1440), danh sách trên/dưới 2 cột từ 1024px, icon riêng từng dòng hiện đúng, không tràn ngang 360–1440px, CLS 0, console sạch.

### Section `photo-groups` (Nhóm ảnh 3 cột)
Thiết kế: tiêu đề H2 + mô tả canh giữa → lần lượt từng item: tiêu đề H3 vạch dọc trái (`border-l-4 border-primary`) → mô tả → hàng ảnh 3 cột.

- **File**: field `inc/landing/layouts/photo-groups.php` (`rhm_landing_layout_photo_groups_fields()`), hiển thị `template-parts/landing/photo-groups.php`. Không JS, không CSS riêng.
- **Field** (key `field_rhm_landing_about_photo-groups_*`): `title` (H2, bắt buộc), `description` (textarea, mỗi dòng 1 đoạn, tùy chọn), `items` repeater (≤ 12, thu gọn theo tiêu đề item): `title` (H3, bắt buộc), `text` (textarea, tùy chọn), `images` (gallery trong repeater, trả ID, ≤ 9). Item thiếu tiêu đề hoặc không có ảnh → bỏ qua; không còn item nào → không in section. Dữ liệu mẫu ghi gallery-trong-repeater được từ plugin 1.2.0.
- **Hàng ảnh** `<ul aria-labelledby="<id>-item-N">`: ảnh khung `aspect-[4/3]` + `object-cover` (cắt giữa → hàng thẳng đều). ≥ `sm` (640px) `grid-cols-3` (gap 16px, `lg` 24px; dư ảnh xuống hàng). < 640px 1 hàng vuốt ngang: `-mx-4 px-4` (tràn tới mép màn hình) + `snap-x snap-mandatory` + **`scroll-px-4`** (thiếu thì điểm snap bỏ lề → ảnh dính sát mép trái) + `tabindex="0"` (vùng cuộn phải focus được — axe), thẻ `w-[80%]` hé ảnh kế; item chỉ 1 ảnh → `w-full`, không tabindex.
- **`sizes`**: `(min-width: 1440px) 453px, (min-width: 1024px) calc(33.33vw - 27px), (min-width: 640px) calc(33.33vw - 21px), calc(80vw - 26px)` (1 ảnh: nhánh cuối `calc(100vw - 32px)`). Chỉ ảnh đầu của item đầu là LCP khi section đứng đầu trang.
- **Đã đo** (2026-11-13, trang mẫu, 2 item × 3 ảnh): 3 ảnh / 1 hàng từ 640px (453px ở 1440 → file 640w), mobile vuốt được, ảnh đầu cách mép 16px cả lúc tải lẫn sau khi snap; không tràn ngang 360–1440px, CLS 0, console sạch.

### Section `form` (Form đăng ký – Fluent Forms)
Thiết kế: khung bo `rounded-3xl` nền gradient mint → trắng → mint, viền + bóng; trái = tiêu đề kiểu `bar` (eyebrow + H2 vạch dọc) + mô tả + danh sách lợi ích (chấm tròn `bg-primary` + icon `landing/tich`) + thẻ hotline (icon điện thoại tròn + "Hotline tư vấn" + số); phải = thẻ form trắng `.rhm-booking` (H3 tùy chọn + form). Desktop 2 cột `lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)]`, mobile chữ trên form dưới. Cùng ngôn ngữ thiết kế với khối form trang Liên hệ / hồ sơ bác sĩ.

- **File**: field `inc/landing/layouts/form.php` (`rhm_landing_layout_form_fields()`), hiển thị `template-parts/landing/form.php`, ô chọn form `rhm_fluent_form_choices()` (`inc/plugin/fluentform.php`), chống trùng form `rhm_landing_booking_form_anchor()` + filter `rhm_landing_booking_url()` (`inc/landing/landing.php`). Không JS, không CSS riêng (style form: `_booking-form.css`, nạp mọi trang qua `footer.css`).
- **Field** (key `field_rhm_landing_about_form_*`): `eyebrow`, `title` (trống → "Đăng ký tư vấn"), `description` (textarea, mỗi dòng 1 đoạn), `points` (textarea, mỗi dòng 1 lợi ích; trống → không có danh sách), `hotline` (true_false, mặc định bật; số lấy theme mod `rhm_hotline`), `form_id` (select ACF, `choices` = `rhm_fluent_form_choices()`: form Fluent đã xuất bản "Tên (#ID)", CHỈ truy vấn khi `is_admin()` → ngoài site không tốn truy vấn; trống → `rhm_booking_form_id`), `form_title` (H3 trên form, tùy chọn).
- **Render form** qua `rhm_render_fluent_form( $id )` trong khối `.rhm-booking` → CSS của Fluent bị gỡ, JS Fluent (jQuery + form-submission) "chạm mới tải" qua `fluent-lazy.js`. Không có form (chưa chọn, Fluent tắt) → khách KHÔNG thấy section; người có quyền `edit_theme_options` thấy ghi chú cấu hình trong thẻ form.
- **Trùng form với popup**: Fluent sinh id HTML theo số form (`fluentform_3`, `ff_3_…`, nonce) → in 1 form 2 lần trên 1 trang = trùng id (lỗi axe, nhãn trỏ nhầm ô, JS có thể gửi nhầm form). `rhm_landing_booking_form_anchor()` tìm section `form` đầu tiên của trang landing có form (đã tính fallback) == `rhm_booking_popup_form_id()` → `rhm_booking_popup_enabled()` = false (không in popup) + `rhm_booking_url` = `#<id section>` (nút header, nút liên hệ nhanh "popup", hero… cuộn tới section) — giống trang Liên hệ. Tính từ DỮ LIỆU (`rhm_landing_sections()`), không đợi render, vì nút đặt lịch ở header in trước `<main>`; id section tính bằng `rhm_landing_part_args()` (không tính hậu tố chống trùng neo — đặt neo trùng thì link có thể lệch). Chưa xử lý: 2 section `form` cùng 1 form trên 1 trang (vẫn trùng id) — đừng làm vậy. Hồ sơ bác sĩ (`template-parts/doctor/booking.php` + popup cùng form) có thể có cùng vấn đề trùng id, chưa kiểm tra.
- **Đã đo** (2026-11-13, trang mẫu, form #3: họ tên, số điện thoại, dịch vụ): không trùng id, không tràn ngang 360/1024/1440px, CLS lúc tải + cuộn = 0, CLS khi Fluent khởi tạo sau lần chạm đầu = 0; lúc tải chỉ có `fluent-lazy.js`, jQuery + `form-submission.js` tải sau khi focus ô đầu; bấm Gửi khi trống → hiện "Trường này là bắt buộc"; console sạch. CSS `page-about` +0,1 kB gzip (9,34 kB). Từ 1440px nhãn của cột nút liên hệ nhanh ("Gọi ngay"…) đè lên mép phải các ô nhập — chưa xử lý (chờ người dùng quyết).
- Chữ "Submit Form", "- Select -" là cài đặt của form trong Fluent Forms (sửa ở trình sửa form), không phải của theme.

### Section `clinics` (Hệ thống phòng khám)
Thiết kế: tiêu đề H2 + mô tả canh giữa → hàng viên chi nhánh (tên + nút vàng "Chỉ đường") → slider 1 ảnh lớn rộng hết khung / lần, tiêu đề + chú thích riêng của ảnh ngay dưới → dải ảnh nhỏ (bấm ô nào slider chuyển tới ảnh đó).

- **File**: field `inc/landing/layouts/clinics.php`, hiển thị `template-parts/landing/clinics.php`, JS `src/js/_showcase.js` (+ `_tiles.js`), dữ liệu mẫu `rhm-demo-data/includes/class-rhm-demo-landing.php` (`clinic_slides()`).
- **Field** (key `field_rhm_landing_about_clinics_*`): `title` (H2, bắt buộc), `description` (textarea, mỗi dòng 1 đoạn), `branches` repeater (≤ 6: `name`, `map_url`), `items` repeater (≤ 12: `image` ID, `title` bắt buộc, `text`). Không có `title` section hoặc không có ảnh hợp lệ (ảnh + tiêu đề) → không in. Bản đầu dùng repeater `groups` (nhóm nhiều ảnh) đã bỏ — dữ liệu `groups` cũ không còn được đọc.
- **Chi nhánh**: nhập tay, KHÔNG lấy từ CPT `chi-nhanh`. Có `map_url` → link "Chỉ đường" qua `rhm_external_link_attrs()` (tab mới, nofollow) + chữ ẩn "tới <tên>" (nhiều link cùng chữ); không có → viên chỉ có tên. Mobile: viên rộng hết hàng, tên tự xuống dòng, nút `whitespace-nowrap shrink-0` (nút cao cố định `h-8`, chữ xuống dòng sẽ tràn).
- **Slider**: track `<div id="<id>-track">` flex + `snap-x snap-mandatory` + `[scrollbar-width:none]`, mỗi slide `w-full shrink-0 flex-col` (`role="group"`, `aria-roledescription`, nhãn "N / tổng: tiêu đề" — track là `div`, không phải `ul`, vì `role="group"` trong list là lỗi axe). Slide = ảnh (`aspect-[4/3] sm:aspect-video`, `object-cover`) + hộp chú thích `flex-1` (H3 nền `deep` chữ trắng + nội dung nền `mint`) → mọi slide cao bằng nhau, chuyển ảnh không nhảy trang. Vuốt được khi chưa có JS.
- **Nút ‹ ›**: lớp phủ `absolute inset-x-0 top-0` CÙNG khung tỉ lệ với ảnh → nút canh giữa ẢNH, không giữa cả slide. Từ `lg` lùi vào `px-24` (96px): cột nút liên hệ nhanh `fixed` cách mép phải màn hình ~88px che nút › nếu đặt sát mép. Nút đang ở đầu/cuối: `disabled:opacity-0`.
- **Dải ảnh nhỏ** `[data-rhm-showcase-thumbs]` (chỉ khi ≥ 2 ảnh): `<button aria-controls="<id>-track">`, ô đang xem `aria-current="true"` (style `aria-[current=true]:ring-3 opacity-100`), ảnh `alt=""` + chữ ẩn "Xem ảnh N: tiêu đề". Dải có `p-1` để viền ô đang chọn không bị cắt bởi `overflow-x-auto`. 6 ô / hàng từ `lg`, 4 từ `sm`, 40% trên mobile.
- **`_showcase.js`**: bấm ô → `track.scrollTo( index × clientWidth )`; track cuộn (gom bằng rAF) → `Math.round( scrollLeft / clientWidth )` → đổi `aria-current` + cuộn ô vào giữa dải bằng `strip.scrollBy` (không `scrollIntoView` → không kéo trang). Tôn trọng `prefers-reduced-motion`. `carousel.js` chỉ import khi có `[data-rhm-showcase-thumbs]`; template chỉ enqueue `carousel` khi ≥ 2 ảnh.
- **`sizes`** (ghi comment trong template, sửa bố cục thì tính lại): ảnh lớn `(min-width: 1440px) 1408px, (min-width: 640px) calc(100vw - 32px), calc(133.33vw - 43px)` — dưới 640px khung 4:3 phủ ảnh 16:9 nên cần ảnh rộng = cao khung × 16/9. Ảnh nhỏ `(min-width: 1440px) 223px, (min-width: 1024px) calc(16.67vw - 17px), (min-width: 640px) calc(25vw - 19px), calc(40vw - 16px)`. Chỉ ảnh đầu khi section đứng đầu trang mới `fetchpriority="high"`, còn lại lazy.
- **Đã đo** (2026-11-13, trang mẫu, 6 ảnh): CLS 0 cả khi chuyển ảnh, không tràn ngang 360–1440px, ảnh tải đúng cỡ (1440 → file 1440w, 360 → 640w), console sạch; JS `_showcase` ~0,5 kB gzip; CSS `page-about` +0,4 kB gzip. Kiểm tra lại nút ‹ › không bị che bằng `elementFromPoint` ở 1024/1280/1440px khi sửa vị trí nút hoặc nút liên hệ nhanh.

## CPT Chi nhánh
- Post type `chi-nhanh` (`RHM_BRANCH_POST_TYPE`), menu admin "Chi nhánh". **Không công khai**: `public`/`publicly_queryable` = false, không rewrite, không archive → `/chi-nhanh/` trả 404, không vào tìm kiếm/sitemap. Cần trang riêng thì phải bật lại các tham số này + thêm template, CSS bundle và flush rewrite.
- `supports`: `title` (tên chi nhánh) + `thumbnail` (ảnh đại diện: mặt tiền / không gian chi nhánh; `rhm_get_branches()` trả `image` = ID, khi in vẫn kiểm tra `wp_attachment_is_image()`). KHÔNG có `page-attributes` (không có khung Thuộc tính / ô Thứ tự) → chi nhánh luôn sắp theo tên A → Z, cả ngoài site lẫn danh sách admin. Danh sách admin có cột ảnh 60px.
- Taxonomy `danh-muc-chi-nhanh` (`RHM_BRANCH_TAXONOMY`, phân cấp như Chuyên mục, không công khai, có cột trong admin).
- Field ACF (`group_rhm_branch`): `rhm_branch_address` (textarea, bắt buộc), `rhm_branch_hotline` (text), `rhm_branch_map_url` (url, link chia sẻ Google Maps; `rhm_get_branches()` trả `map_url`). Đọc qua `rhm_get_branch_field( 'address' | 'hotline' | 'map_url', $id )` (fallback `get_post_meta` khi không có ACF). In hotline thành link gọi điện bằng `rhm_get_tel_href()`.
- `rhm_get_branches()` cache bằng transient (key `rhm_branches_v2_…`: đổi tiền tố khi đổi cấu trúc mảng trả về) theo số phiên bản `rhm_branches_cache_version`; `rhm_flush_branches_cache()` tăng phiên bản khi lưu / xóa / bỏ thùng rác / khôi phục chi nhánh, sửa / xóa danh mục, sau `acf/save_post`, và khi đổi/xóa ảnh đại diện (meta `_thumbnail_id`, Classic Editor lưu bằng AJAX không qua `save_post`). Ghi dữ liệu ngoài admin (script, `update_field`) thì gọi `rhm_flush_branches_cache()` sau khi ghi.
- Footer (cột "Hệ thống đối tác"): chỉ in **địa chỉ** của các chi nhánh được chọn ở Tùy biến → Chân trang → "Chi nhánh hiển thị" (`rhm_footer_branch_ids`), đúng thứ tự ↑↓ đã sắp. `rhm_get_footer_branches()` bỏ ID trùng, chi nhánh đã xóa / nháp / thùng rác, chi nhánh chưa có địa chỉ. **Không chọn chi nhánh nào → ẩn cả cột.** Danh sách nhập tay cũ `rhm_footer_branches` đã bỏ (theme mod cũ còn trong DB nhưng không được đọc).
- Footer và section chi nhánh trang chủ (xem ngay dưới) dùng chung ô chọn `rhm_footer_branch_fields()` / `rhm_footer_branch_choices()` (ID => "Tên — địa chỉ rút gọn") nhưng lưu 2 danh sách riêng; cả hai đọc dữ liệu qua `rhm_get_branches()` (có cache) → không truy vấn thêm.

### Chi nhánh trang chủ
- Tùy biến → **Chi nhánh (Trang chủ)**: tiêu đề `rhm_home_branches_title` (mặc định “Hệ thống chi nhánh”), danh sách `rhm_home_branch_ids` dùng control list chọn CPT, thêm/xóa/sắp ↑↓.
- Mặc định danh sách trống → ẩn section. Danh sách độc lập footer; chi nhánh mới không tự thêm vào home.
- `inc/home-branches.php` → `rhm_get_home_branches()` đọc qua cache `rhm_get_branches()`, giữ thứ tự chọn, bỏ ID trùng/nháp/xóa/thiếu tên, địa chỉ hoặc ảnh hợp lệ. Ảnh bị xóa được kiểm tra lại khi render.
- `template-parts/sections/branches.php` thay vị trí Chứng nhận trên home. Tiêu đề giống section bác sĩ: eyebrow `rhm_home_branches_eyebrow` (mặc định `rhm_home_branches_default_eyebrow()`, trống = ẩn) + H2 căn trái; nút ‹ › ở hàng tiêu đề (≥ 1024px) / dưới slider (< 1024px), ẩn bằng class breakpoint khi mọi thẻ đã vừa khung. Thẻ giống khối Tin tức: ảnh 4:3 (zoom nhẹ khi rê chuột) → tên H3 + vạch gradient → địa chỉ (icon `map-pin`) + hotline `tel:` (nếu có) → link "Chỉ đường" (dùng nguyên `map_url` của chi nhánh, KHÔNG tự tạo link từ địa chỉ; trống → không in nút; link external). Carousel không tự chạy, mobile hé thẻ kế; 640/768/1024px có 2/3/4 thẻ, ít mục canh giữa. Slide `pb-10` + `.swiper -mb-8` để bóng thẻ không bị cắt. CSS kéo đều chiều cao thẻ trong `page-home.css`.
- Chứng nhận đã bỏ khỏi cả trang chủ lẫn landing page (không còn layout landing `certificates`). Giữ `inc/certificates.php`, `template-parts/sections/certificates.php`, Tùy biến **Chứng nhận (Landing page)** và các theme mod cũ để dùng lại nếu cần.

## CPT Bác sĩ
- Post type `bac-si`, archive `/bac-si/`, single `/bac-si/<slug>/`.
- Taxonomy **Chuyên khoa** `chuyen-khoa` (`RHM_DOCTOR_TAXONOMY`, `inc/doctors.php`): phân cấp như Chuyên mục, **chỉ dùng trong admin** (`public`/`rewrite`/`query_var` = false → `/chuyen-khoa/…` 404, không vào sitemap). Có cột Chuyên khoa + ô lọc ở danh sách Bác sĩ (`rhm_doctor_taxonomy_filter()`, lọc bằng query var chung `taxonomy` + `term`). Chưa hiển thị ngoài site; muốn có trang/bộ lọc công khai thì bật lại các tham số trên + template, bundle CSS, tăng `rhm_doctor_rewrite_version`.
- Có `page-attributes` (ô "Thứ tự" = `menu_order`): dùng để sắp bác sĩ trong section trang chủ.
- Rewrite flush 1 lần theo option `rhm_doctor_rewrite_version` — đổi cấu trúc URL thì tăng giá trị này.
- Chi tiết nhập liệu: `docs/doctor-profiles.md`.
