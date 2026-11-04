---
paths:
  - "wp-content/themes/themeranghammatsg/**/*.php"
  - "wp-content/themes/themeranghammatsg/src/**"
---

# LCP (Largest Contentful Paint): ≤ 1,5 s mobile lab, ≤ 1,8 s field

LCP = thời điểm vẽ xong phần tử nội dung lớn nhất trong khung nhìn đầu. Phần tử có thể là `<img>`, `<image>` trong SVG, poster/khung đầu `<video>`, ảnh nền `url()` hoặc khối chữ. LCP chốt lại khi người dùng tương tác lần đầu (bấm, gõ, cuộn).

Nguồn: web.dev/articles/lcp · web.dev/articles/optimize-lcp · web.dev/articles/fetch-priority · w3c.github.io/largest-contentful-paint

## 1. 4 phần của LCP: biết mình đang chậm ở đâu

| Phần | Nghĩa | Tỉ lệ nên có | Rule xử lý |
|---|---|---|---|
| TTFB | Chờ HTML về | ~40% | `pagespeed-ttfb-server.md` |
| Resource load delay | Từ TTFB đến lúc bắt đầu tải ảnh LCP | **< 10%** | Mục 2 |
| Resource load duration | Thời gian tải file ảnh | ~40% | `responsive-images.md` (đúng size, WebP/AVIF) |
| Element render delay | Ảnh đã tải xong nhưng chưa được vẽ | **< 10%** | Mục 3 |

Đọc phần chia nhỏ này trong insight "LCP breakdown" của Lighthouse hoặc trong DevTools Performance. LCP là chữ thì chỉ có TTFB + render delay.

## 2. Ảnh LCP phải được phát hiện ngay trong HTML

- Ảnh LCP là `<img>`/`<picture>` có `src`/`srcset` **thật trong HTML server trả về**. KHÔNG dùng ảnh nền CSS, KHÔNG để JS chèn ảnh, KHÔNG dùng `data-src`/`data-srcset` (chỉ slide 2+ của carousel mới được hoãn).
- Gắn `fetchpriority="high"` + `loading="eager"` (hoặc bỏ `loading`). **Mỗi trang chỉ 1 ảnh** có `fetchpriority="high"`. Tuyệt đối không `loading="lazy"` cho ảnh trên màn hình đầu.
- `decoding="async"` không dùng cho ảnh LCP (để mặc định).
- Không cần `<link rel="preload" as="image">` khi ảnh đã nằm trong HTML với `fetchpriority="high"`. Chỉ preload khi ảnh buộc phải phát hiện muộn. Với `<picture>` art direction phải preload từng nhánh, mỗi nhánh có `media` + `imagesrcset` + `imagesizes` khớp hệt (khớp sai thì tải 2 file).
- Ảnh LCP nằm trên cùng origin với trang (không qua domain CDN ảnh khác nếu chưa `preconnect`).
- Đừng để tài nguyên khác giành băng thông với ảnh LCP: font preload chỉ preload subset thật sự dùng ở màn hình đầu; không preload ảnh/JS khác; script không cần thiết thì `defer`/module/nạp sau.

## 3. Không trì hoãn việc vẽ

- KHÔNG ẩn phần tử LCP lúc đầu: không `opacity: 0`, `visibility: hidden` hay hiệu ứng fade-in/slide-in chạy khi tải trang (Chrome không tính phần tử opacity 0 → LCP bị đẩy tới lúc hoạt ảnh xong). Hiệu ứng xuất hiện chỉ dùng cho phần dưới màn hình đầu.
- Slider hero: slide 1 phải hiện đúng vị trí cuối cùng **trước khi Swiper khởi tạo** (CSS đặt sẵn bố cục). JS không được ẩn rồi hiện lại slide 1.
- CSS chặn render càng nhỏ thì ảnh vẽ càng sớm (`pagespeed-fcp-css-fonts.md`). JS đồng bộ trong `<head>` cấm tuyệt đối.
- LCP là chữ (bài viết, trang Liên hệ, 404): font đã có `font-display: swap` + preload → chữ hiện ngay bằng font dự phòng. Không đổi sang `block`/`optional` khi chưa đo.
- Không render phía client (không dựng nội dung màn hình đầu bằng JS/AJAX/REST).

## 4. Theo từng template của theme

- **Trang chủ**: ảnh hero slide 1 (`rhm_hero_picture()`, `inc/hero.php`). Nhánh mobile (`<source media="(max-width: 767px)">`) mới là LCP trên PSI mobile → dung lượng file mobile quan trọng nhất (mục tiêu ≤ ~60 kB WebP/AVIF ở độ rộng tải về).
- **Danh mục**: ảnh `featured-post.php` trang 1 phải là ảnh `fetchpriority="high"`. Ảnh card trong lưới thì lazy.
- **Bài viết**: `single.php` không có ảnh đại diện → LCP là H1/đoạn đầu → TTFB + CSS quyết định. Ảnh đầu tiên trong nội dung nếu nằm trên màn hình đầu: không lazy (WordPress tự bỏ lazy cho vài ảnh đầu qua `wp_get_loading_optimization_attributes`, kiểm tra lại kết quả).
- Section mới đặt ở đầu trang → xác định trước phần tử nào sẽ là LCP và áp dụng mục 2–3.

## 5. Kiểm tra

- `PerformanceObserver` `largest-contentful-paint` → `entry.element` đúng phần tử dự kiến, `entry.url` đúng file nhỏ nhất đủ dùng.
- Insight "LCP request discovery" phải qua hết: phát hiện được từ HTML, có `fetchpriority=high`, không lazy.
- `document.querySelectorAll('img[fetchpriority=high]').length === 1` (trang có ảnh LCP).
