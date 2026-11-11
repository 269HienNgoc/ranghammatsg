# Theme Răng Hàm Mặt SG (`themeranghammatsg`)

Classic theme (không phải block theme) cho phòng khám Răng Hàm Mặt SG — tác giả Aimedis. PHP ≥ 8.1, WP ≥ 6.5.
Text domain `themeranghammatsg`; tiền tố hàm/hằng: `rhm_` / `RHM_` (`RHM_THEME_DIR`, `RHM_THEME_URI`).
Gutenberg bị tắt cho mọi post type → nội dung soạn bằng Classic Editor.

Plugin phụ thuộc: ACF PRO (field bác sĩ / bài viết / landing page), Fluent Forms (form đặt lịch), kk Star Ratings, Yoast SEO (schema).

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
│   ├── landing/              landing.php (registry + render + helper), fields.php (ACF), layouts/<layout>.php (field từng khối)
│   └── plugin/               tích hợp plugin, tên = thư mục plugin: fluentform.php, kk-star-ratings.php (nạp qua inc/plugins.php)
├── template-parts/
│   ├── sections/             trang chủ (thứ tự trong front-page.php): hero, quick-services, about, doctors, news, certificates, booking-cta
│   │                         doctors, certificates, booking-cta nhận $args để landing dùng lại
│   ├── landing/              17 layout: hero, highlights, cards, services, doctor-cards, feature, tiles, promo, intro,
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
│   │                         _fonts, _pagination, _toc, _carousel, _home-doctors
│   └── js/                   main.js (header sticky, menu mobile/submenu, mở popup đặt lịch), home (hero), carousel
│                             (+ _carousel nạp động), dropdown, embed (facade video/bản đồ), fluent-lazy
├── dist/                     kết quả build — ĐƯỢC COMMIT (server không build): css/, js/, fonts/ (Roboto latin + vietnamese)
├── assets/
│   ├── admin/                customize-list.css/js: control "list" (repeater) trong Customizer
│   ├── icons/                thư viện icon .svg (README.md) — đồng bộ nguyên thư mục lên CDN
│   │   ├── menu/             24px currentColor, inline — icon mục menu
│   │   ├── services/         48px màu vẽ sẵn, <img> — Dịch vụ nổi bật
│   │   ├── landing/          15 icon 24px nét stroke currentColor, inline — khối landing page
│   │   └── cta/              24px currentColor, inline — nút liên hệ nhanh
│   └── images/flags/         vn.svg, gb.svg (cờ ngôn ngữ ở header)
├── languages/                (trống) file dịch
├── docs/                     doctor-profiles.md (nhập hồ sơ), doctor-profiles-verification.md (kết quả đo),
│                             landing-pages.md (nhập liệu landing + thêm layout/chủ đề)
├── AGENTS.md                 hướng dẫn cho agent, cập nhật theo code hiện tại (KHÔNG có trong zip theme)
└── package.json, vite.config.js, node_modules/   build (KHÔNG có trong zip theme)
```

Đóng gói: tăng `Version` trong `style.css` → `npm run build` → zip thư mục theme (gốc zip là `themeranghammatsg/`), bỏ `src/`, `node_modules/`, `docs/`, `package*.json`, `vite.config.js`, `.gitignore`, `CLAUDE.md`, `AGENTS.md`. ZIP lưu ở `docs/` gốc repo: `docs/themeranghammatsg-<version>.zip` (phiên bản hiện tại 1.3.0). Mu-plugin `wp-content/mu-plugins/0-rhm-head-order.php` nằm ngoài theme → deploy riêng.

Sau khi upload theme lên server: xóa page cache Swift / LiteSpeed / Cloudflare ở các lớp đang dùng; loại `themeranghammatsg/dist/js/main.js` khỏi Delay JS của Swift để popup đặt lịch và menu mobile sẵn sàng ngay khi người dùng bấm.

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
| `landing/landing.php` | registry chủ đề/layout, đọc `rhm_landing_sections`, render trang và helper ảnh/chữ/link; `rhm_landing_style_bundle()` cung cấp bundle cho `inc/enqueue.php` |
| `landing/fields.php` / `landing/layouts/*.php` | đăng ký ACF ở `acf/init`: 1 field group/chủ đề, Flexible Content và field từng layout; chỉ hiện khi Page chọn đúng template |
| `plugins.php` | require các file `inc/plugin/*` |
| `plugin/fluentform.php` | Fluent Forms: jQuery + JS "chạm mới tải", `rhm_render_fluent_form()` (form của theme), gỡ CSS Fluent khi trang chỉ có form của theme |
| `plugin/kk-star-ratings.php` | kk Star Ratings: `rhm_get_star_rating()` (luôn tồn tại, trả rỗng khi plugin tắt), chỉ giữ CSS/JS của plugin ở bài viết có sao |

## Quy ước quan trọng

### CSS

- CSS slider và trang trí bác sĩ dùng chung giữa trang chủ và landing nằm ở `src/css/components/_carousel.css`, `_home-doctors.css`; được import trong `page-home.css` và `page-about.css`.
- `footer.css` còn chứa form đặt lịch, popup đặt lịch và nút liên hệ nhanh (in sau `</footer>`, ngoài mọi vùng utility → viết bằng class component, không dùng utility Tailwind trong 2 template đó).
- Mọi trang nạp `base` + `header` + `footer` + **đúng 1** file theo loại trang do `rhm_page_style_bundle()` (`inc/enqueue.php`) chọn:
  `page-home`, `page-about`, `page-contact`, `single-post`, `single-doctor`, `page`, `404`, `archive`.
  Thêm loại trang mới ngoài landing: tạo `src/css/pages/<tên>.css` + thêm nhánh `match` trong `rhm_page_style_bundle()`. Chủ đề landing mới: khai báo `bundle` trong `rhm_landing_templates()`; enqueue tự chọn, không cần thêm nhánh `match`. Trang chủ vẫn ưu tiên `page-home`.
- **Tailwind chia theo vùng DOM**: `header.css` → utility chỉ trong `.site-header`; các file trang → `.site-main`; `footer.css` → `.site-footer`. Lý do: tránh utility thường ở file nạp sau đè utility responsive ở file nạp trước. Đặt class Tailwind vào đúng vùng tương ứng với file CSS sẽ biên dịch nó.
- Dùng `.site-container` (không dùng `.container` của Tailwind — đã bị loại).
- Token trong `src/css/theme.css`: màu `primary`, `dark`, `teal`, `mint`, `deep`, `muted`, `line`, `soft`, `footer-*`…; font duy nhất Roboto (tự host); breakpoint thêm `xs` = 560px; `max-w-site` = 1440px; `shadow-card`. Ưu tiên token thay vì mã màu cứng.

### PHP

- Mỗi file mở đầu `defined( 'ABSPATH' ) || exit;`, có type hint, theo WordPress Coding Standards.
- Tính năng mới → file mới trong `inc/` + `require_once` trong `functions.php`. Tích hợp plugin mới → `inc/plugin/<tên-thư-mục-plugin>.php` + require trong `inc/plugins.php`; tự kiểm tra plugin bật (`function_exists`, `shortcode_exists`…).
- Chủ đề/layout landing mới → xem mục "Landing page" bên dưới và `docs/landing-pages.md`; Page Template chỉ gọi renderer, không viết cứng danh sách section trong template.
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
- `landing/`: 24×24, `fill="none" stroke="currentColor"`, in inline bằng `rhm_icon_svg( 'landing', … )`; `<title>` tiếng Việt là nhãn ô chọn ACF. Thêm icon vào thư mục này, không nhúng SVG mới trực tiếp vào template landing.
- CDN: đồng bộ nguyên `assets/icons/` lên Cloudflare, nhập URL thư mục ở Tùy biến → Thư viện icon (`rhm_icon_base_url`). URL luôn kèm `?ver=filemtime`.
- Icon UI trong template (home, phone, chevron…) vẫn là chuỗi SVG trong `inc/icons.php`.

### Hiệu năng (xem thêm `.claude/rules/` ở gốc repo)

- Ảnh: dùng `rhm_get_picture()` / `wp_get_attachment_image()` với `sizes` chính xác; ảnh LCP `loading="eager"` + `fetchpriority="high"`, còn lại lazy. Không dùng `the_custom_logo()`.
- Swift Performance AI (bật *Lazyload images*) ghi đè MỌI `<img>` thành `loading="lazy" fetchpriority="low"`, kể cả ảnh LCP. Ảnh LCP (hero slide 1, ảnh bài nổi bật danh mục) và logo phải nằm trong danh sách loại trừ ảnh của Swift (cấu hình trong admin, không sửa code plugin). Kiểm tra: HTML phải có đúng 1 `fetchpriority="high"`.
- Fluent Forms được tải kiểu "chạm mới tải" (`inc/plugin/fluentform.php` + `src/js/fluent-lazy.js`); CSS Fluent bị gỡ, style riêng ở `_booking-form.css`.
- Video YouTube chỉ kết nối sau khi bấm phát (`embed.js`); slider không tự chạy.
- JS vanilla, không phụ thuộc jQuery; nạp `defer` ở footer.

## Landing page

### Kiến trúc và hành vi hiện tại

- Dùng **Page + Page Template + ACF PRO Flexible Content** (`RHM_LANDING_FIELD = 'rhm_landing_sections'`). Hiện có **1 chủ đề Giới thiệu**, 17 layout; chưa có chức năng chọn mẫu để tự chèn toàn bộ section.
- `functions.php` nạp `inc/landing/landing.php`. `rhm_landing_templates()` là registry `file template => [slug, label, bundle, layouts]`, mở rộng qua filter cùng tên; `rhm_landing_layouts()` là thư viện layout với nhãn và cờ `boxed`.
- `fields.php` đăng ký `group_rhm_landing_<slug>` ở `acf/init`, location `page_template`; mỗi layout có field chung `show` (ẩn/hiện) và `anchor` (neo HTML). Content thêm/xóa/kéo sắp xếp section trong ACF, xem trang qua Preview.
- `tpl-gioi-thieu.php` gọi `rhm_landing_render( get_the_ID() )` trong vòng lặp `the_post()`. Renderer chỉ lấy section đang hiện và layout chủ đề cho phép; từng template-part bỏ qua dữ liệu không hợp lệ. Đổi template không tự chuyển đổi dữ liệu; layout ngoài danh sách của chủ đề mới không được render.
- Không có section đang hiện/hợp lệ theo registry, hoặc thiếu ACF: hiện tiêu đề trang + nội dung Classic Editor (nếu có) + CTA đặt lịch. Khi đã có section, nội dung Classic Editor chỉ được in ở layout `editor` (chỉ nên thêm một lần).
- Layout `doctors`, `certificates`, `booking` dùng lại các section tương ứng trong `template-parts/sections/`, truyền `$args` để đổi ID/tiêu đề. Bác sĩ/chứng nhận lấy dữ liệu chung như trang chủ; `doctor-cards` cho chọn riêng hồ sơ bác sĩ.

### Hợp đồng template-part và bố cục

- Mỗi template-part nhận `$args`: `data` (field ACF, ảnh = ID), `index`, `is_first`, `id`, `prev`; layout dạng ô nhận thêm `span`. ID mặc định `rhm-section-N`, tiêu đề dùng `<id>-title`; neo nhập vào được chuẩn hóa bằng `sanitize_title()`.
- Ba layout dạng ô: `feature`, `tiles`, `promo`. Độ rộng `RHM_LANDING_SPANS = [10, 5, 4, 6]` tương ứng toàn trang, 1/2, 2/5, 3/5. Renderer gom các ô liền nhau vào lưới 10 cột từ 1024px; ô không vừa phần còn lại xuống hàng mới, mobile xếp dọc. Ô không có HTML bị bỏ qua.
- Template ô tự in khối nền/viền với `h-full`, **không tự bọc `.site-container`** vì renderer đã tạo hàng. Thay bộ độ rộng phải cập nhật đồng thời `RHM_LANDING_SPANS`, `rhm_landing_span_class()` và `rhm_landing_width_field()`.
- Đọc dữ liệu qua `rhm_landing_text/lines/image/gallery/icon/rows/url/html()`. In tiêu đề/nút/ảnh bằng `rhm_landing_heading()`, `rhm_landing_button()`, `rhm_landing_picture()`; tính `sizes` ảnh trong ô bằng `rhm_landing_box_sizes()`. Giữ quy ước escape và link external của theme.
- `rhm_landing_enqueue_module()` dùng module carousel/embed sẵn có, chỉ gọi khi khối cần. FAQ dùng `<details>`; dịch vụ cuộn ngang bằng CSS; video chỉ tải sau khi bấm phát.

### Thêm layout hoặc chủ đề

1. **Layout mới**: khai báo key/nhãn/cờ `boxed` trong `rhm_landing_layouts()`; tạo `inc/landing/layouts/<key>.php` với hàm `rhm_landing_layout_<key>_fields( string $prefix ): array` (gạch ngang đổi thành gạch dưới trong tên hàm) và `template-parts/landing/<key>.php`; thêm key vào `layouts` của các chủ đề dùng nó.
2. **Field**: dùng helper trong `fields.php`; key có dạng `field_rhm_landing_<slug>_<layout>_<field>`, repeater thêm prefix riêng cho field con. Không đổi `slug`, key hoặc name đã có dữ liệu nếu chưa có phương án chuyển dữ liệu.
3. **Chủ đề mới**: thêm cấu hình vào `rhm_landing_templates()`; tạo Page Template có `Template Name`, gọi header → vòng lặp + `rhm_landing_render()` → footer; tạo entry CSS theo `bundle`. Không cần sửa `inc/enqueue.php`.
4. **CSS**: giữ utility trong `.site-main`, `@source` renderer/helper và các template-part được dùng. `page-about.css` hiện quét toàn bộ `template-parts/landing/`; nếu khối gọi section ở thư mục khác, thêm `@source` tương ứng. Chỉ import CSS Swiper/các partial khi chủ đề sử dụng chúng; sửa `src/` → build `dist/` → xóa page cache.

### Kiểm tra và hiệu năng landing

- Chỉ dùng một hero và đặt đầu danh sách section đang hiện để làm H1; nếu section đầu không phải hero, renderer in tiêu đề trang riêng. Hiện chưa có validation khóa vị trí/số lượng hero → kiểm tra HTML có đúng **1 H1** sau khi content sắp xếp.
- `is_first` dựa trên vị trí section trong danh sách trước khi template-part bỏ qua dữ liệu rỗng. Đây là cờ ưu tiên ảnh, không phải phép đo LCP thực tế. Kiểm tra phần tử LCP, `sizes`/`srcset`, ưu tiên ảnh đầu màn hình và loại trừ ảnh này khỏi Lazyload của Swift; khi ưu tiên bằng `rhm_landing_picture()`, ảnh có `fetchpriority="high"` và không lazy.
- Ngân sách tổng CSS inline **≤ 20 kB gzip**. Ngày **2026-11-11**: tổng kích thước gzip riêng từng bundle `base + header + footer + page-about` là **21.147 byte (~21,1 kB)**, trong đó `page-about` 8.809 byte; đang vượt ngân sách. Đây là mốc đo asset, chưa phải kết quả PageSpeed và không thay đổi ngưỡng yêu cầu.
- Khi mở rộng chủ đề, kiểm soát CSS của cả các layout được cho phép dù trang chưa dùng chúng. Chưa được coi landing đã đạt yêu cầu hiệu năng chỉ vì build thành công; đo mobile/desktop, CLS khi cuộn và tương tác theo `.claude/rules/pagespeed-measure.md`. Hướng dẫn content và dev chi tiết: [docs/landing-pages.md](docs/landing-pages.md).

## CPT Bác sĩ

- Post type `bac-si`, archive `/bac-si/`, single `/bac-si/<slug>/`, không có taxonomy.
- Có `page-attributes` (ô "Thứ tự" = `menu_order`): dùng để sắp bác sĩ trong section trang chủ.
- Rewrite flush 1 lần theo option `rhm_doctor_rewrite_version` — đổi cấu trúc URL thì tăng giá trị này.
- Chi tiết nhập liệu: `docs/doctor-profiles.md`.
