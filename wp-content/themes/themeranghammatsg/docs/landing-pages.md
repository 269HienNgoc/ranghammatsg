# Landing page theo chủ đề

Mỗi chủ đề landing (hiện có: **Giới thiệu**) là 1 Page Template. Khi trang chọn template đó, editor hiện nhóm field
"Landing page – <chủ đề>": danh sách **section** để thêm, xóa, kéo sắp xếp và tạm ẩn.

## Cho content: tạo trang

1. Trang → Thêm trang mới → nhập tiêu đề.
2. Khung **Thuộc tính trang** → Giao diện → chọn **Giới thiệu**. Nhóm field "Landing page – Giới thiệu" hiện dưới tiêu đề.
3. Bấm **Thêm section** và chọn loại. Section hiện trên trang theo đúng thứ tự trong danh sách (kéo thanh tiêu đề để đổi chỗ).
4. Mỗi section có 2 ô ở cuối:
   - **Hiển thị**: tắt thì section bị ẩn tạm, dữ liệu vẫn giữ.
   - **Neo (id)**: vd `doi-ngu-bac-si`. Nút/link `#doi-ngu-bac-si` sẽ cuộn tới section này.
5. Section nào thiếu dữ liệu bắt buộc thì **không hiện** (không để khung trống): thiếu tiêu đề, không có ảnh hay mục hợp lệ nào.

### Các loại section (chủ đề Giới thiệu)

Thứ tự theo thiết kế: Banner → Dải điểm nổi bật → Thẻ giá trị → Danh sách dịch vụ → Bác sĩ tiêu biểu → (Ô thông tin 2/5 + Ô lưới ảnh 3/5) → (Ô banner 1/2 + Ô banner 1/2) → (Ô thông tin 1/2 + Ô thông tin 1/2) → Ảnh lớn + chữ + ô nổi bật.

| Section | Cần nhập | Ghi chú |
|---|---|---|
| Banner đầu trang (H1) | Tiêu đề (trống → tiêu đề trang), đoạn nổi bật (chữ đậm), mô tả, ảnh desktop 1600×1000 + ảnh mobile 828×620 (tùy chọn), nút (tùy chọn) | **Đặt đầu tiên**: tiêu đề banner là H1 của trang. Ảnh phủ nửa phải màn hình, mép trái tự mờ dần, nên để chủ thể lệch phải. Đây là ảnh tải đầu tiên (LCP): nén WebP, ≤ 150 kB |
| Dải điểm nổi bật | 4 ô: icon + nhãn ngắn | Đặt ngay sau Banner thì dải nổi lên đè mép dưới banner |
| Thẻ giá trị + ảnh | Tiêu đề, 3 thẻ (icon, tiêu đề, mô tả), ảnh bên phải 4:3 (tùy chọn) | |
| Danh sách dịch vụ | Tiêu đề, mô tả, dịch vụ (ảnh 16:10, tên, link) | 5 dịch vụ / hàng desktop; mobile vuốt ngang |
| Bác sĩ tiêu biểu | Tiêu đề, mô tả, chọn hồ sơ bác sĩ (+ mô tả riêng nếu cần) | Ảnh, tên, link lấy từ hồ sơ; mô tả trống → ô Tóm tắt của hồ sơ → Chức danh |
| **Ô** thông tin: ảnh + chữ | Độ rộng, tiêu đề, ảnh (giữ tỉ lệ), nội dung, nút | Vd Giấy phép hoạt động, Hệ thống vô trùng, Vật liệu chính hãng |
| **Ô** lưới ảnh nhỏ | Độ rộng, tiêu đề, ô (ảnh vuông nền trắng + nhãn 2 dòng) | Vd Công nghệ hiện đại: 6 ô với độ rộng 3/5 |
| **Ô** banner nổi bật | Độ rộng, tiêu đề, dòng phụ, ảnh, logo (tùy chọn), link (tùy chọn) | Vd All-on-4, Invisalign. Có link → bấm cả ô |
| Ảnh lớn + chữ + ô nổi bật | Tiêu đề, nội dung, ảnh 4:3, video (tùy chọn); tab "Ô nổi bật": icon, tiêu đề, mô tả, nút | Vd Cơ sở vật chất + Chi phí hợp lý |
| Số liệu nổi bật | Các ô số + nhãn (tối đa 8) | Tiêu đề không bắt buộc |
| Chặng đường phát triển | Tiêu đề, các mốc (năm, tiêu đề, mô tả) | |
| Thư viện ảnh (slider) | Tiêu đề, ảnh 4:3 | Nhập "Văn bản thay thế" cho từng ảnh trong Thư viện |
| Đội ngũ bác sĩ (slider) | (chỉ tiêu đề, tùy chọn) | Giống trang chủ: hồ sơ bật "Hiện ở section Đội ngũ bác sĩ" |
| Chứng nhận (slider) | (chỉ tiêu đề, tùy chọn) | Ảnh lấy từ Tùy biến → Chứng nhận (Trang chủ) |
| Câu hỏi thường gặp | Câu hỏi + trả lời | Mở/đóng không cần JS |
| Nội dung trình soạn thảo | — | In nội dung ô soạn thảo chính của trang (bảng, ảnh, shortcode…) |
| Kêu gọi đặt lịch | Tiêu đề, mô tả (trống → chữ mặc định) | Nút đặt lịch (mở popup) + hotline |

### Ô xếp cạnh nhau (Độ rộng)

Các section **Ô …** có ô "Độ rộng ô": Toàn trang, 1/2, 2/5 (hẹp), 3/5 (rộng). Các ô **liền nhau** trong danh sách tự xếp chung 1 hàng
khi tổng vừa 1 hàng: 1/2 + 1/2, 2/5 + 3/5, 3/5 + 2/5. Ô không vừa phần còn lại sẽ xuống hàng mới. Mobile: mọi ô xếp dọc.

### Icon

Icon nét màu xanh lấy từ `assets/icons/landing/` của theme (đội ngũ, răng, khiên, tim, mục tiêu, mắt, kim cương, heo đất, giấy phép,
huy chương, lấp lánh, đồng hồ, kính hiển vi, thiết bị, dấu tích). Thêm icon: chép file `.svg` 24×24 nét `stroke="currentColor"`,
có `<title>` tiếng Việt (nhãn trong ô chọn).

Chưa thêm section nào: trang hiện tiêu đề + nội dung trình soạn thảo + kêu gọi đặt lịch (giao diện cũ).

Đổi template của trang: dữ liệu section vẫn được giữ, nhưng loại section mà chủ đề mới không có thì không hiển thị.

## Cho dev

Code: `inc/landing/` (điểm vào `landing.php`), template-part: `template-parts/landing/<layout>.php`.

| File | Vai trò |
|---|---|
| `inc/landing/landing.php` | `rhm_landing_layouts()` (thư viện layout, cờ `boxed` = layout dạng ô), hàng ô `RHM_LANDING_SPANS` / `rhm_landing_span_class()` / `rhm_landing_box_sizes()` (sizes ảnh trong ô), `rhm_landing_templates()` (chủ đề, filter `rhm_landing_templates`), `rhm_landing_sections()`, `rhm_landing_render()`, helper đọc dữ liệu (`rhm_landing_text/lines/image/gallery/url/html`) và in markup (`rhm_landing_heading()`, `rhm_landing_button()`, `rhm_landing_picture()`, `rhm_landing_enqueue_module()`) |
| `inc/landing/fields.php` | `acf/init`: 1 field group `group_rhm_landing_<slug>` / chủ đề (location `page_template`), Flexible Content `rhm_landing_sections`; field chung `show` + `anchor`; helper `rhm_landing_field()`, `rhm_landing_image_field()`, `rhm_landing_wysiwyg_field()`, `rhm_landing_button_fields()` |
| `inc/landing/layouts/<layout>.php` | `rhm_landing_layout_<layout>_fields( $prefix )`: sub field của layout (chỉ nạp ở `acf/init`) |

Template-part nhận `$args`: `data` (dữ liệu ACF của section, ảnh = ID), `index`, `is_first` (section đầu → ảnh là LCP: `fetchpriority="high"`, không lazy), `id` (id của `<section>`; id tiêu đề = `<id>-title`), `prev` (layout liền trước), `span` (chỉ layout dạng ô: độ rộng, phần mười hàng).

Layout dạng ô in 1 `<section class="h-full …">` có nền/viền riêng, KHÔNG tự bọc `.site-container`: `rhm_landing_render()` gom các ô liền nhau vào hàng `.site-container grid lg:grid-cols-10`. Ô không in gì (thiếu dữ liệu) bị bỏ qua.

Tiêu đề section: `rhm_landing_heading( $id, $title, $eyebrow, $description, $style )`, `$style` = `center` (canh giữa, 2 vạch) | `bar` (vạch dọc trái) | `box` (vạch dọc, cỡ nhỏ trong ô) | `plain`.

### Thêm layout

1. Thêm key + nhãn (+ `boxed` nếu là dạng ô) vào `rhm_landing_layouts()`.
2. `inc/landing/layouts/<key>.php` với `rhm_landing_layout_<key>_fields( string $prefix ): array` (gạch ngang trong key → gạch dưới trong tên hàm) (key field tạo bằng `rhm_landing_field( $prefix, … )`; repeater dùng prefix `$prefix . '_<tên repeater>'`).
3. `template-parts/landing/<key>.php`: thiếu dữ liệu → `return`; tiêu đề H2 qua `rhm_landing_heading()`; ảnh qua `rhm_landing_picture()` với `sizes` tính theo bố cục (ghi comment); JS module chỉ enqueue khi cần.
4. Thêm key vào `layouts` của các chủ đề dùng nó. Bundle CSS của chủ đề đã `@source` cả thư mục `template-parts/landing/`; nếu layout gọi template-part ở chỗ khác thì thêm `@source` file đó.

### Thêm chủ đề

1. Thêm phần tử vào `rhm_landing_templates()`: `'page-templates/tpl-<x>.php' => [ 'slug', 'label', 'bundle', 'layouts' ]`. `slug` không được đổi sau khi đã có dữ liệu (nó là key ACF).
2. `page-templates/tpl-<x>.php`: header `Template Name`, rồi `get_header()` → vòng lặp `the_post()` + `rhm_landing_render( get_the_ID() )` → `get_footer()` (chép từ `tpl-gioi-thieu.php`).
3. `src/css/pages/<bundle>.css`: chép `page-about.css`, giữ `@source` các file cần → `npm run build`. `rhm_page_style_bundle()` tự chọn bundle theo cấu hình, không cần sửa `inc/enqueue.php`.

### Hiệu năng

- Ngân sách CSS inline ≤ 20 kB gzip: `base + header + footer + page-about` ≈ 21,1 kB (2026-11-11, page-about 8,8 kB) — VƯỢT ~1 kB vì chủ đề Giới thiệu giữ cả các section slider (Swiper CSS + CSS bác sĩ trang chủ). Muốn về dưới ngân sách: bỏ `gallery`, `doctors`, `certificates` khỏi `layouts` của chủ đề và bỏ `swiper/css`, `_carousel.css`, `_home-doctors.css`, `@source` sections/doctors + certificates khỏi `page-about.css`.
- Ảnh banner (LCP) phải được loại trừ khỏi Lazyload của Swift Performance AI (giống ảnh hero trang chủ). Nếu không, Swift ghi đè thành `loading="lazy" fetchpriority="low"`.
- Kiểm tra: đúng 1 `<h1>`, đúng 1 `img[fetchpriority=high]`, CLS khi cuộn = 0, đo PageSpeed theo `.claude/rules/pagespeed-measure.md`.
