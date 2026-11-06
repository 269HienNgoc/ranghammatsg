---
paths:
  - "wp-content/themes/themeranghammatsg/inc/plugin/**"
  - "wp-content/themes/themeranghammatsg/inc/plugins.php"
  - "wp-content/themes/themeranghammatsg/inc/enqueue.php"
  - "wp-content/themes/themeranghammatsg/inc/cleanup.php"
  - "wp-content/themes/themeranghammatsg/header.php"
  - "wp-content/themes/themeranghammatsg/footer.php"
  - "wp-content/plugins/**"
---

# Plugin và bên thứ ba: không được phá điểm

Phần lớn website WordPress tụt điểm là do plugin, mã tracking và widget gắn thêm, ít khi do theme. Mọi thứ thêm vào phải qua các quy tắc dưới đây. Insight "3rd parties" của Lighthouse liệt kê dung lượng và thời gian main thread theo từng bên.

Nguồn: web.dev/articles/optimizing-content-efficiency-loading-third-party-javascript · web.dev/articles/third-party-facades · web.dev/articles/embed-best-practices · developer.chrome.com/docs/lighthouse/performance/third-party-facades

## 1. Plugin WordPress

- Mỗi plugin có tích hợp với theme → 1 file `inc/plugin/<tên-thư-mục-plugin>.php` (nạp qua `inc/plugins.php`).
- Plugin in CSS/JS ở frontend → **chỉ giữ ở trang thật sự dùng**, dequeue ở mọi trang khác (mẫu `inc/plugin/kk-star-ratings.php`: chỉ bài viết có sao). Kiểm tra bằng cách xem nguồn trang chủ/danh mục/bài viết sau khi bật plugin.
- Script plugin cần jQuery: không để jQuery chặn render. Dời xuống footer (`inc/cleanup.php` đã bỏ jquery-migrate + dời jQuery) hoặc hoãn đến khi tương tác (mẫu Fluent Forms: `inc/plugin/fluentform.php` + `fluent-lazy.js`).
- Trước khi đề xuất cài plugin mới: kiểm tra plugin in gì ở frontend. Plugin chỉ phục vụ admin là tốt nhất. Có thể làm bằng vài dòng PHP trong theme thì không cài plugin.

## 2. WP Rocket / plugin tối ưu: không tối ưu chồng lên theme

Theme đã tự làm: CSS/JS minify (Vite), lazy ảnh native + `fetchpriority`, srcset/sizes, facade video/bản đồ, font tự host + preload, hoãn Fluent. Khi bật WP Rocket (hỏi người dùng trước):

- **Nên bật**: page cache, cache trình duyệt, nén, preload cache (sitemap).
- **Tắt hoặc loại trừ asset theme**: LazyLoad ảnh/iframe (đè lên `fetchpriority`/eager của ảnh LCP, trùng facade), Minify/Combine CSS và JS (đã minify; combine phá bundle theo trang), "Remove Unused CSS" / "Optimize CSS delivery" (có thể bỏ nhầm CSS của class do JS gắn → vỡ giao diện, CLS), "Delay JavaScript execution" (xung đột module + `fluent-lazy.js`; nếu bật thì loại trừ `dist/js/` và script Fluent), "Preload fonts" (theme đã preload).
- Sau khi đổi cấu hình: xóa cache rồi đo lại toàn bộ trang ở `pagespeed-measure.md` mục 1.

## 3. Nhúng nội dung nặng → facade "bấm mới tải"

- YouTube, Google Maps, video, Facebook page plugin, TikTok embed: KHÔNG in iframe/script lúc tải. Dùng nút `[data-embed-src]` + `embed.js` (ảnh xem trước tự host + nút play / nút "Xem bản đồ"), giữ khung tỉ lệ (CLS 0).
- Widget chat (Zalo, Messenger, Tawk, live chat): ưu tiên **link thường** (`https://zalo.me/<số>`, `tel:`, `m.me/…`) trong nút nổi tự vẽ bằng HTML/CSS. Phải dùng SDK chat thì chỉ tải khi người dùng bấm nút.
- reCAPTCHA/Turnstile của form: chỉ tải khi người dùng tương tác với form (cùng cơ chế Fluent lazy). Không tải toàn site.

## 4. Tracking (GA4, GTM, Facebook Pixel, TikTok Pixel, Hotjar…)

- Hỏi người dùng cần bên nào. Mỗi pixel thêm vào thường tốn 20–100 ms main thread trên mobile 4×.
- Bắt buộc: `async`/`defer`, in ở footer (không in trong `<head>` dạng đồng bộ). GTM: không để container chứa thẻ chặn render; heatmap/session replay chỉ bật có thời hạn.
- Cân nhắc hoãn tracking không quan trọng đến sau `load` + idle hoặc tương tác đầu tiên. Phải nói rõ với người dùng điểm đánh đổi: có thể mất một phần lượt thoát sớm.
- Không mã tracking nào được: chèn DOM phía trên nội dung (CLS), dùng `document.write`, tải font riêng, hoặc gắn listener `unload` (mất bfcache).

## 5. Kiểm tra

- Insight "3rd parties": tổng thời gian main thread của bên thứ ba lúc tải < 100 ms (mobile). Không có request bên thứ ba nào trước khi người dùng tương tác, trừ tracking đã được người dùng duyệt.
- So TBT/LCP trước và sau khi bật plugin/mã mới. Tệ đi là phải xử lý theo mục 1–4 hoặc báo người dùng.
