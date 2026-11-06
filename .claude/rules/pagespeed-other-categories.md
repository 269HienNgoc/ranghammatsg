---
paths:
  - "wp-content/themes/themeranghammatsg/**/*.php"
  - "wp-content/themes/themeranghammatsg/src/**"
---

# PSI Accessibility / Best Practices / SEO: 100 điểm

Ba hạng mục này tính đạt/trượt theo từng audit (có trọng số). Chỉ cần 1 audit nặng trượt là mất 100. Phần lớn lỗi đến từ markup PHP nên phải đúng ngay khi viết template.

Nguồn: developer.chrome.com/docs/lighthouse/accessibility/scoring · developer.chrome.com/docs/lighthouse/best-practices · developer.chrome.com/docs/lighthouse/seo · web.dev/learn/accessibility

## 1. Accessibility (axe-core)

- **Tương phản**: chữ thường ≥ 4.5:1, chữ lớn (≥ 24px, hoặc ≥ 18,66px đậm) ≥ 3:1. Gồm cả chữ trên ảnh/nền màu (nút, nhãn chuyên mục, footer `footer-muted` trên `footer`, placeholder). Thêm màu mới vào token → kiểm tra tỉ lệ trước.
- **Tên truy cập được**: link/nút chỉ có icon (☰, ✕, mũi tên, mạng xã hội, cờ VN/EN, nút slider) phải có `aria-label` hoặc `.screen-reader-text`. Link ảnh: `alt` của ảnh là tên link. Nhiều link cùng chữ ("Xem thêm") mà đích khác nhau → thêm ngữ cảnh ẩn.
- **Form**: mọi input có `<label>` (hoặc `aria-label`). Form Fluent: kiểm tra trường `type=tel`, select dịch vụ.
- **Cấu trúc**: `<html lang="vi">` (language_attributes), mỗi trang 1 `<h1>`, tiêu đề không nhảy cấp (H2 → H4), `<ul>/<ol>` chỉ chứa `<li>`, id không trùng (mục lục tự gắn id đã chống trùng), landmark `header/nav/main/footer`, skip link.
- **ARIA đúng**: `aria-expanded`/`aria-controls` trỏ id có thật; không `aria-hidden="true"` trên phần tử focus được (trừ khi kèm `tabindex="-1"`); vùng `inert` khi ngăn kéo đóng; không `tabindex` > 0.
- **Vùng chạm** ≥ 24×24px và không sát nhau (audit `target-size`); nên ≥ 44×44px cho nút chính trên mobile.
- **Zoom**: không `maximum-scale`/`user-scalable=no` trong viewport.
- `<iframe>` (sau facade) có `title` (`data-embed-title`). Vùng cuộn ngang (`.rhm-table`) có `tabindex="0"` + `role="region"` + tên.

## 2. Best Practices

- HTTPS toàn site, không mixed content (ảnh/CSS/JS `http://` trong nội dung cũ → `search-replace`).
- **Console sạch**: không lỗi JS, không 404 tài nguyên (favicon, source map, font). Audit "Browser errors were logged to the console" trượt là mất điểm.
- Không dùng API đã khóa (`document.write`, `unload`, sync XHR, AppCache…), không cookie bên thứ ba lúc tải.
- **Ảnh đúng tỉ lệ** (`image-aspect-ratio`): `width`/`height` in ra khớp tỉ lệ file, hoặc dùng `object-cover`. **Ảnh đủ nét** (`image-size-responsive`): file tải về ≥ kích thước hiển thị × DPR, nhưng không lớn thừa (cân bằng với `responsive-images.md`).
- `<!doctype html>`, `<meta charset>` sớm trong `<head>`.
- Phần "Trust and Safety" (CSP, HSTS, COOP, XFO) không tính điểm nhưng nên bật HSTS + `X-Frame-Options`/`frame-ancestors` ở server production.

## 3. SEO

- `<title>` và `<meta name="description">` có trên MỌI loại trang (dùng plugin SEO hoặc theme. Nếu chưa có, báo người dùng chọn cách, không tự làm cả 2 → trùng thẻ).
- HTTP 200 cho trang hợp lệ, 404 thật cho trang không tồn tại. Không chặn index: tắt "Ngăn cản công cụ tìm kiếm" ở production, `robots.txt` hợp lệ.
- **Link bò được**: điều hướng là `<a href="URL thật">`, không `href="#"`/`javascript:` cho link đi trang khác (menu footer đang có 3 link `#`, phải thay trước production). Chữ link mô tả được đích đến.
- `rel=canonical` hợp lệ, trỏ đúng URL cuối cùng (không redirect). `hreflang` hợp lệ (header đang in `vi-vn`, `vi`, `x-default` cùng URL, đúng cú pháp).
- Ảnh nội dung có `alt` tiếng Việt có nghĩa (`responsive-images.md` mục 4).
- Structured data (JSON-LD) không tính điểm Lighthouse, nhưng phải đúng sự thật (không đánh giá giả, xem ghi chú kk-star-ratings trong CLAUDE.md).

## 4. Kiểm tra

- Lighthouse 4 hạng mục (`pagespeed-measure.md`): mỗi hạng mục 100. Đọc "Passed audits" lẫn "Manual checks" (axe không tự kiểm được thứ tự focus, bẫy bàn phím).
- Bàn phím: Tab qua header → ngăn kéo mobile → nội dung → footer, focus luôn nhìn thấy, Esc đóng ngăn kéo/dropdown.
