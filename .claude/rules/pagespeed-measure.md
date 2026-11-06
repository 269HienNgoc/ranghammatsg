---
paths:
  - "wp-content/themes/themeranghammatsg/**"
  - "wp-content/plugins/**"
---

# Đo PageSpeed: quy trình kiểm tra trước khi báo xong

Ngưỡng cần đạt: `pagespeed-targets.md`. Dự án không có test tự động: đo = `npm run build` không lỗi + Lighthouse + Chrome headless (CDP).

Nguồn: developers.google.com/speed/docs/insights/v5/get-started · developer.chrome.com/docs/lighthouse · developer.chrome.com/docs/crux · github.com/GoogleChrome/web-vitals

## 1. Trang phải đo (theo loại template bị ảnh hưởng)

| Template | URL mẫu | LCP thường là |
|---|---|---|
| Trang chủ | `/` | Ảnh hero slide 1 (mobile: ảnh dọc) |
| Danh mục | `/category/<slug>/` | Ảnh bài nổi bật |
| Bài viết | 1 bài mẫu dài (có bảng, mục lục) | H1 / đoạn đầu (bài không có ảnh đại diện) |
| Trang thường / Giới thiệu | `/gioi-thieu/`… | Tiêu đề / ảnh đầu |
| Liên hệ | trang dùng `tpl-lien-he.php` | Banner chữ |
| 404, Tìm kiếm | `/khong-co-trang/`, `/?s=nieng` | Chữ |

Sửa phần dùng chung (header, footer, base.css, main.js, `inc/enqueue.php`, `inc/cleanup.php`) → đo ít nhất trang chủ + danh mục + bài viết.

## 2. Lighthouse local (Performance + 3 hạng mục còn lại)

```bash
# Mobile (mặc định của Lighthouse = cấu hình mobile của PSI). Lưu kết quả vào scratchpad, KHÔNG lưu vào repo
npx -y lighthouse http://bactrung.test/ --chrome-flags="--headless=new" \
  --output=json --output=html --output-path=<scratchpad>/lh-home-mobile --quiet
# Desktop
npx -y lighthouse http://bactrung.test/ --preset=desktop --chrome-flags="--headless=new" \
  --output=json --output-path=<scratchpad>/lh-home-desktop.json --quiet
```

- Chạy **3–5 lần**, lấy trung vị (điểm dao động). Ghi lại phiên bản Lighthouse (`lighthouseVersion` trong JSON), so với phiên bản PSI in ở cuối báo cáo PSI.
- Đọc từ JSON: `categories.*.score`, `audits['largest-contentful-paint'|'first-contentful-paint'|'total-blocking-time'|'cumulative-layout-shift'|'speed-index'].numericValue`, cùng các audit/insight có `score < 1`.
- Đọc phần **Insights / Diagnostics**: LCP breakdown, LCP request discovery, Render-blocking requests, Network dependency tree, Layout shift culprits, Forced reflow, Improve image delivery, Use efficient cache lifetimes, Font display, Legacy/duplicated JavaScript, 3rd parties, DOM size.
- Điểm khác giữa local và production (đừng tối ưu sai chỗ):
  - TTFB local là của Docker trên máy dev, **không có page cache** (WP Rocket đang tắt). TTFB thật chỉ đo được trên hosting.
  - Local là `http://` → audit HTTPS ở Best Practices trượt là chuyện bình thường.
  - Lighthouse chạy không có cookie nên luôn là khách chưa đăng nhập. Khi xem bằng tay thì phải đăng xuất: admin bar và header `no-store` cho người đã đăng nhập làm sai số liệu.
  - Khi WP Rocket bật: xóa cache sau mỗi lần build/sửa PHP rồi mới đo.

## 3. Chrome headless + CDP (những thứ Lighthouse không thấy)

`google-chrome --headless=new --remote-debugging-port=9333` + script Node (để trong scratchpad):

- **CLS khi cuộn và khi tương tác** (Lighthouse chỉ đo lúc tải): `PerformanceObserver({ type: 'layout-shift', buffered: true })`, cuộn hết trang, mở menu/accordion/slider. Bỏ qua shift có `hadRecentInput`. Kết quả phải bằng 0.
- **INP giả lập**: `Emulation.setCPUThrottlingRate({ rate: 4 })`, `PerformanceObserver({ type: 'event', durationThreshold: 16 })`, rồi bấm menu, nút submenu, slider, nút "…" lọc chuyên mục, chạm vào form Fluent. Mỗi tương tác phải **< 100 ms**.
- **Long task**: `PerformanceObserver({ type: 'longtask' })` (hoặc `long-animation-frame`) khi tải trang với CPU 4×. Mục tiêu: không task nào > 50 ms.
- **LCP element**: `largest-contentful-paint` entry → `element`, `url`, `startTime`. Phải đúng phần tử dự kiến ở bảng mục 1.
- **Ảnh**: `currentSrc` + số request ảnh (chi tiết ở `responsive-images.md`).
- Đo transition/hoạt ảnh thì phải `Page.bringToFront` trước.

## 4. Production (sau khi deploy)

- PSI web: https://pagespeed.web.dev/ (Mobile + Desktop) cho từng loại template ở mục 1.
- PSI API (đo hàng loạt): `https://www.googleapis.com/pagespeedonline/v5/runPagespeed?url=<URL>&strategy=mobile&category=performance&category=accessibility&category=best-practices&category=seo`
- Field: phần "Discover what your real users are experiencing" của PSI, CrUX Vis (https://cruxvis.withgoogle.com/), Search Console → Core Web Vitals. Dữ liệu field cập nhật chậm (cửa sổ 28 ngày), sửa xong phải chờ ~4 tuần mới thấy hết tác dụng.

## 5. Báo cáo kết quả

Báo cho người dùng: trang đã đo, mobile/desktop, điểm 4 hạng mục, 5 chỉ số lab (trung vị), phần tử LCP, CLS khi cuộn, các insight còn trượt. Chỉ số nào chưa đạt thì nói thẳng, không làm tròn thành "đạt".
