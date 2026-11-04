---
paths:
  - "wp-content/themes/themeranghammatsg/src/css/**"
  - "wp-content/themes/themeranghammatsg/inc/enqueue.php"
  - "wp-content/themes/themeranghammatsg/inc/cleanup.php"
  - "wp-content/themes/themeranghammatsg/header.php"
  - "wp-content/themes/themeranghammatsg/functions.php"
  - "wp-content/themes/themeranghammatsg/vite.config.js"
---

# FCP + Speed Index: CSS, font, `<head>` (FCP ≤ 1,0 s, SI ≤ 1,9 s mobile lab)

- **FCP**: lúc vẽ chữ/ảnh đầu tiên. Bị chặn bởi TTFB + mọi CSS/JS chặn render trong `<head>`.
- **Speed Index**: tốc độ màn hình đầu được lấp đầy (đo bằng khung hình). Giảm khi nội dung hiện sớm và đúng vị trí, tăng khi màn hình trắng chờ JS/ảnh.

Nguồn: web.dev/articles/fcp · developer.chrome.com/docs/lighthouse/performance/first-contentful-paint · developer.chrome.com/docs/lighthouse/performance/speed-index · web.dev/articles/critical-rendering-path · web.dev/articles/font-best-practices

## 1. CSS chặn render: ít, nhỏ, đúng trang

- Mỗi trang nạp đúng `base` + `header` + `footer` + **1** bundle theo trang (`rhm_page_style_bundle()`). Không thêm file CSS toàn cục mới. Style cho section → bundle của trang đó.
- **Ngân sách**: tổng CSS chặn render của 1 trang **≤ 20 kB gzip** (hiện ~15–19 kB). Lớn hơn thì xem lại trước khi thêm. Đo: `gzip -c dist/css/<file>.css | wc -c`.
- Utility scoped theo vùng (`.site-main`, `.site-header`, `.site-footer`) + `@source` đúng file → Tailwind chỉ sinh class đang dùng. Xóa CSS/component không còn dùng (quy tắc code sạch).
- Không `@import` URL ngoài hay file không được Vite gộp (mỗi `@import` lúc chạy = thêm 1 vòng request nối tiếp). Không nạp CSS của thư viện nguyên gói (Swiper: chỉ CSS lõi + module dùng).
- CSS của plugin không cần ở trang đó → dequeue (mẫu `inc/plugin/kk-star-ratings.php`, `inc/cleanup.php`). CSS mà WordPress tự in (global styles, block library, emoji) đã gỡ trong `inc/cleanup.php`; thứ mới phát hiện cũng gỡ ở đó.
- Không tự ý tách "critical CSS inline + nạp phần còn lại bất đồng bộ": bundle đã nhỏ, nạp bất đồng bộ dễ gây FOUC/CLS. Chỉ làm khi insight "Render-blocking requests" báo tiết kiệm đáng kể (> ~150 ms) và đã đo trước/sau.

## 2. `<head>` sạch

- Không `<script>` đồng bộ trong `<head>`. JS của theme: `defer` (main.js) hoặc `type="module"`. Script plugin chặn render thì dời xuống footer hoặc hoãn (xem `pagespeed-third-party.md`).
- Thứ tự trong `<head>`: `charset` → `viewport` → preload font → CSS → phần còn lại. `<meta name="viewport" content="width=device-width, initial-scale=1">` đã có, giữ nguyên. Không chặn zoom (`user-scalable=no`, `maximum-scale=1`): vi phạm Accessibility.
- `preconnect` chỉ cho origin thật sự dùng ở màn hình đầu (tối đa 2–3). Hiện mọi tài nguyên cùng origin → không cần. Không preconnect "cho chắc".
- Không có chuỗi phụ thuộc dài (HTML → CSS → `@import` → font → …). Insight "Network dependency tree" phải ngắn: HTML → (CSS, font preload, ảnh LCP) song song.

## 3. Font: chỉ Roboto, tự host

- 1 họ font duy nhất: Roboto variable 400–700, 2 file WOFF2 (`roboto-latin` ~43 kB, `roboto-vietnamese` ~14 kB), `unicode-range` tách subset, `font-display: swap` (`src/css/components/_fonts.css`). Không thêm font, độ đậm hay kiểu chữ nào khác (italic thật, icon font). Icon dùng SVG.
- Preload (`rhm_preload_fonts()`) phải có `crossorigin`, `type="font/woff2"`, URL **trùng khớp** với `src` trong CSS (khác URL hoặc version → tải 2 lần).
- Preload font cạnh tranh băng thông với ảnh LCP trên mạng 1,6 Mbps. Nếu LCP mobile là ảnh và insight cho thấy font làm chậm ảnh: thử chỉ preload `roboto-latin`, đo trước/sau rồi mới đổi.
- Không dùng Google Fonts CDN, không `@import url(fonts.googleapis…)`.

## 4. Speed Index

- Màn hình đầu phải dựng xong chỉ bằng HTML + CSS: không skeleton chờ JS, không ẩn nội dung đến khi JS chạy, không hiệu ứng xuất hiện ở màn hình đầu.
- Header/hero hiện ngay đúng kích thước cuối. Màu nền chỗ giữ ảnh (`bg-soft`…) giúp màn hình không trắng trong lúc ảnh tải.

## 5. Kiểm tra

- Lighthouse: FCP ≤ 1,0 s mobile; insight "Render-blocking requests" không có mục nào ước tính tiết kiệm đáng kể; "Font display" đạt; "Network dependency tree" ngắn.
- DevTools Coverage (hoặc CDP `CSS.startRuleUsageTracking`): bundle của trang không có khối lớn CSS không dùng cho template đó.
