---
paths:
  - "wp-content/themes/themeranghammatsg/inc/**"
  - "wp-content/themes/themeranghammatsg/*.php"
  - "wp-content/themes/themeranghammatsg/template-parts/**"
  - "wp-content/plugins/**"
  - ".htaccess"
  - "wp-config.php"
  - "docker-compose.yml"
---

# TTFB + server/cache: TTFB ≤ 0,5 s field (HTML có cache ≤ 0,2 s)

TTFB = từ lúc bắt đầu điều hướng đến byte đầu tiên của HTML, gồm redirect + DNS + kết nối/TLS + thời gian server xử lý. TTFB là phần nền của cả FCP lẫn LCP: chậm ở đây thì mọi chỉ số đều chậm. Lighthouse cảnh báo khi phản hồi HTML > 600 ms (insight "Document request latency": phản hồi chậm, có redirect, chưa nén).

Nguồn: web.dev/articles/ttfb · web.dev/articles/optimize-ttfb · web.dev/articles/bfcache · web.dev/articles/http-cache · developer.chrome.com/docs/lighthouse/performance/uses-long-cache-ttl

## 1. Code PHP của theme: truy vấn gọn

- `WP_Query` phụ: `no_found_rows => true` (khi không phân trang), `posts_per_page` cụ thể, `ignore_sticky_posts`, chỉ lấy `fields => 'ids'` khi chỉ cần ID. Lặp có ảnh → `update_post_thumbnail_cache( $query )`. Có meta/term → để cache meta/term bật (mặc định).
- Không truy vấn trong vòng lặp (N+1). Gom 1 truy vấn rồi tra mảng.
- Kết quả tính toán đắt và ít đổi (danh sách chuyên mục lọc, số liệu…) → `wp_cache_*` / transient, xóa cache theo hook `save_post`/`edited_term`. Hàm gọi nhiều lần trong 1 request → `static` cache (mẫu `rhm_get_star_rating()`).
- **Không gọi HTTP ra ngoài** (`wp_remote_get`, API mạng xã hội, oEmbed) khi render trang. Bắt buộc phải gọi thì cache bằng transient + cron làm mới.
- `get_theme_mod` đọc từ 1 option đã autoload, gọi nhiều không sao. Option tự tạo: `autoload` = `false` nếu không cần ở mọi trang. Tổng autoload nên < ~800 kB (Site Health cảnh báo).
- Đo server: `wp profile` / Query Monitor (skill `wp-performance`). Trang không cache: PHP < 200 ms, < ~40 query.

## 2. Page cache: bắt buộc ở production

- Khách chưa đăng nhập phải nhận HTML từ page cache (WP Rocket hoặc cache của hosting/LiteSpeed/Nginx/Cloudflare APO). Không có page cache thì TTFB field gần như không thể ≤ 0,5 s.
- WP Rocket đang TẮT ở local (2026-11-04). Khi bật: xóa cache sau mỗi lần build/sửa PHP; cache được phải gồm cả mobile (theme responsive, không cần "Separate cache for mobile").
- Nội dung động theo người xem (giỏ hàng, tên người dùng) không in vào HTML được cache. Theme hiện không có, giữ nguyên như vậy.
- Server: PHP ≥ 8.2 + **OPcache** bật; nên có persistent object cache (Redis/Memcached) nếu hosting hỗ trợ.

## 3. Mạng và HTTP

- **Không redirect** trên đường vào: URL trong menu, link nội bộ, canonical, sitemap đều dùng đúng dạng cuối cùng (https, đúng www hoặc không www, có `/` cuối theo permalink). Mỗi redirect trên mobile tốn ~1 RTT (150 ms lab).
- HTTP/2 hoặc HTTP/3, TLS 1.3. Nén **Brotli** (hoặc gzip) cho HTML, CSS, JS, SVG, JSON. Font WOFF2 và ảnh WebP/AVIF vốn đã nén, không nén thêm.
- Hosting/CDN gần người dùng Việt Nam (máy chủ VN hoặc Singapore, CDN có PoP tại VN).

## 4. Header cache cho tài nguyên tĩnh

- `dist/css`, `dist/js`, `dist/fonts`: URL có `?ver=filemtime` → đặt `Cache-Control: public, max-age=31536000, immutable`.
- Ảnh trong `wp-content/uploads` (+ `uploads-webpc`): ≥ 1 năm. Ảnh thay thế luôn có URL mới khi upload lại.
- Đặt trong `.htaccess` (mod_expires/mod_headers) hoặc cấu hình server/CDN, **ngoài** các khối tự sinh `# BEGIN WordPress` / `# BEGIN WP Rocket` (bị ghi đè). Insight "Use efficient cache lifetimes" phải sạch với tài nguyên của site.
- Tài nguyên thay đổi nội dung mà KHÔNG đổi URL thì không được cache lâu. Luôn version bằng `rhm_asset_version()`.

## 5. bfcache (quay lại/tiến tới tức thì)

- Không dùng `unload`/`beforeunload` (trừ khi form đang gõ dở), không `Cache-Control: no-store` cho HTML của khách, không giữ `BroadcastChannel`/WebSocket mở.
- Kiểm tra: DevTools → Application → Back/forward cache → "Test back/forward cache" phải "Restored from back/forward cache". Lighthouse có audit "Page prevented back/forward cache restoration".

## 6. Kiểm tra

- Production: `curl -o /dev/null -s -w 'ttfb=%{time_starttransfer}\n' -H 'Accept-Encoding: br,gzip' https://<site>/` từ VN, chạy nhiều lần: HTML có cache ≤ 0,2 s. `curl -sI` thấy `content-encoding: br|gzip`, header cache của trang (HIT) và `cache-control` của tài nguyên tĩnh đúng mục 4.
- Local: TTFB chỉ dùng để so trước/sau khi sửa PHP (không có page cache, Docker). Đột ngột tăng > 50 ms sau khi sửa → có truy vấn/HTTP thừa.
