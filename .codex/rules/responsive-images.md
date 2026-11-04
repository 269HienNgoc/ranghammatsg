---
paths:
  - "wp-content/themes/themeranghammatsg/**/*.php"
---

# Ảnh responsive: mỗi màn hình tải đúng 1 file, không hại SEO / PageSpeed

Áp dụng cho MỌI ảnh in ra từ theme (template, `inc/`, section, component). Mục tiêu: trình duyệt chỉ tải file nhỏ nhất đủ nét cho màn hình hiện tại; Google vẫn đọc và index được ảnh; CLS = 0.

## 1. Chọn cách in ảnh

| Loại ảnh | Cách làm | Ví dụ trong theme |
|---|---|---|
| **Độ rộng CSS cố định theo breakpoint** (logo, icon, huy hiệu, avatar…) | `rhm_get_picture( $id, [ media => px, …, '' => px ], $attrs )` → `<picture>` + `<source media>` mỗi khoảng, srcset `1x, 2x` | Logo header (`rhm_custom_logo()` trong `inc/setup.php`), logo footer (`footer.php`) |
| **Co giãn theo bố cục** (card, ảnh bài, ảnh section, lưới) | `wp_get_attachment_image()` / `the_post_thumbnail()` với `sizes` = độ rộng hiển thị THẬT theo bố cục (WordPress tự sinh srcset `w`) | `card-post.php`, `featured-post.php`, `sections/about.php`, `sections/news.php`, `sections/certificates.php` |
| **Ảnh khác nhau giữa desktop / mobile** (art direction: ảnh ngang ↔ ảnh dọc) | `<picture>` + `<source media="(max-width: 767px)">` trỏ attachment khác, mỗi nhánh có srcset + sizes riêng | Hero (`rhm_hero_picture()` trong `inc/hero.php`) |
| **SVG** (icon admin upload) | `<img src>` + `width`/`height` cố định; KHÔNG nhúng inline SVG người dùng upload | `sections/quick-services.php`, mạng xã hội footer |
| Ảnh raster hiển thị theo **chiều cao** cố định (huy hiệu ngang) | `rhm_footer_image( $id, $height_px, … )` → `sizes` = chiều cao × tỉ lệ ảnh | Huy hiệu footer |

Không dùng `<source media>` với density (`1x/2x`) cho ảnh co giãn theo `vw` — dùng `sizes` + srcset `w`. Không thêm `<source type="image/webp">`: plugin Converter for Media đã trả WebP/AVIF ở CÙNG URL theo header Accept (`wp-content/uploads/.htaccess`).

## 2. Số px phải khớp CSS

- `rhm_get_picture()`: px mỗi media = độ rộng của class `w-*` ở breakpoint đó (vd `w-36 lg:w-40 xl:w-44` → `'' => 144, '(min-width: 1024px)' => 160, '(min-width: 1280px)' => 176`). Media lớn đặt TRƯỚC (trình duyệt lấy `<source>` khớp đầu tiên). Viết comment nhắc khớp class cạnh lời gọi.
- `sizes`: tính theo bố cục thật (khung `.site-container` max 1440px, lề 16px, gap, padding, số cột) cho từng breakpoint, viết comment cách tính cạnh biến `$rhm_*_sizes`.
- Ảnh `object-cover` phủ khung cao hơn tỉ lệ ảnh: độ rộng tải về ≈ chiều cao khung × tỉ lệ ảnh, KHÔNG phải độ rộng cột → đo thực tế rồi đặt `sizes` (vd `featured-post.php`).
- srcset chỉ gom các size CÙNG tỉ lệ với file nguồn: dùng size `rhm-*` có sẵn (`inc/setup.php`); `rhm-card` (600×400 crop) chỉ dành cho khung 3:2. Không thêm image size mới nếu size có sẵn đủ dùng.

## 3. Tải ảnh (LCP / lazy)

- Ảnh LCP (ảnh lớn nhất màn hình đầu: hero slide 1, bài nổi bật đầu trang danh mục): `loading="eager"` + `fetchpriority="high"`, KHÔNG lazy. Mỗi trang CHỈ 1 ảnh `fetchpriority="high"` — kiểm tra `document.querySelectorAll('img[fetchpriority=high]')` (WordPress có thể tự gắn cho ảnh khác, vd `the_custom_logo()`).
- Cùng 1 ảnh in 2 chỗ (vd logo header + ngăn kéo menu) → cho 2 chỗ chọn CÙNG file ở mọi breakpoint, vì ảnh trong khối ẩn (`display:none`, `lg:hidden`) vẫn bị tải nếu không lazy.
- Ảnh hiển thị theo chiều cao/độ rộng cố định: dùng `h-*`/`w-*` cố định, không `max-h`/`max-w` (kích thước dự phòng trước khi ảnh tải sẽ khác → CLS).
- Mọi ảnh còn lại: `loading="lazy"` + `decoding="async"` (lazy native — không dùng thư viện lazy JS, không dùng `data-src` thay `src`).
- Chỉ slide 2+ của carousel được hoãn bằng `data-srcset` (JS gắn lại khi sắp hiện) — ảnh nội dung thường phải có `src`/`srcset` thật trong HTML để Google index.
- Luôn có `width` + `height` (hoặc khung `aspect-*` + `object-cover`/`object-contain`) → CLS = 0.

## 4. SEO

- `alt`: ảnh mang nội dung → alt tiếng Việt có nghĩa (thứ tự: chữ nhập tay trong Customizer → alt của attachment → tiêu đề attachment). Ảnh trang trí hoặc ảnh nằm trong link trùng nội dung tiêu đề bên cạnh → `alt=""` + link `tabindex="-1" aria-hidden="true"`.
- Ảnh nội dung KHÔNG dùng `background-image` CSS (Google không index, không responsive).
- Ảnh luôn nằm trong HTML do PHP in ra, không chèn bằng JS (trừ phần tử facade như video).

## 5. Kiểm tra trước khi báo xong

Chrome headless (xem mục "Kiểm tra" trong CLAUDE.md), so bằng TÊN FILE trong `currentSrc` (không dùng `naturalWidth`):

- Độ rộng 320 / 375 / 1024 / 1280 / 1440 với DPR 1, 1,75 (PageSpeed mobile) và 2.
- Mỗi ảnh chỉ 1 request (`performance.getEntriesByType('resource')`), file tải về ≥ độ rộng hiển thị × DPR nhưng là bản nhỏ nhất đạt điều kiện đó.
- `encodedBodySize` khớp file `.webp` (không phải PNG/JPG gốc).
- Không tràn ngang, không layout shift.
- WP Rocket bật → xóa cache trước khi đo.
