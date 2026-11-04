---
paths:
  - "wp-content/themes/themeranghammatsg/**"
  - "wp-content/plugins/**"
---

# PageSpeed Insights: mục tiêu 100 điểm (mobile + desktop)

Mục tiêu dự án: PSI **100/100 Performance ở cả Mobile lẫn Desktop**, mọi chỉ số trong vùng xanh với biên an toàn rộng. Accessibility / Best Practices / SEO cũng 100 (xem `pagespeed-other-categories.md`). Rule chi tiết theo chỉ số: `pagespeed-lcp.md`, `pagespeed-cls.md`, `pagespeed-inp-tbt.md`, `pagespeed-fcp-css-fonts.md`, `pagespeed-ttfb-server.md`, `pagespeed-third-party.md`. Cách đo: `pagespeed-measure.md`. Ảnh: `responsive-images.md`.

Nguồn: developers.google.com/speed/docs/insights/v5/about · developer.chrome.com/docs/lighthouse/performance/performance-scoring · web.dev/articles/vitals · web.dev/articles/defining-core-web-vitals-thresholds

## 1. PSI có 2 phần, đừng lẫn

| Phần | Nguồn | Quyết định | Chỉ số |
|---|---|---|---|
| **Field**, ô "Core Web Vitals Assessment" | CrUX: người dùng Chrome thật, 28 ngày, phân vị **p75** | Đạt/không đạt CWV và xếp hạng tìm kiếm (Google Search) | LCP, INP, CLS (đánh giá); FCP, TTFB (tham khảo) |
| **Lab**, ô điểm 0–100 | Lighthouse chạy 1 lần trên máy Google | **Chỉ phần này tạo ra con số điểm** | FCP, LCP, TBT, CLS, Speed Index |

- Site chưa đủ traffic thì không có dữ liệu field → chỉ tối ưu được bằng lab. Lab **không đo INP**: dùng TBT làm chỉ báo thay.
- INP đã thay FID từ 03/2024. Tài liệu còn nói tới FID là tài liệu cũ.

## 2. Cách Lighthouse tính điểm

- Trọng số: **TBT 30% · LCP 25% · CLS 25% · FCP 10% · Speed Index 10%**.
- Mỗi chỉ số được quy ra điểm 0–1 theo đường cong log-normal (giá trị tại p10 của dữ liệu HTTP Archive ứng với 0.9, giá trị trung vị ứng với 0.5). Ngưỡng xanh của Lighthouse ứng với điểm 0.9, **chưa đủ để được 100**: muốn tổng ~100 thì từng chỉ số phải đạt khoảng ≥ 0.99.
- Mobile mô phỏng máy tầm trung (Moto G Power): **CPU chậm 4×, RTT 150 ms, ~1,6 Mbps**, màn hình 412×823, DPR 1.75. Desktop: CPU 1×, RTT 40 ms, ~10 Mbps. → Mobile khó hơn nhiều, luôn tối ưu theo mobile trước.
- Kiểm tra lại con số bằng máy tính điểm: https://googlechrome.github.io/lighthouse/scorecalc/

## 3. Ngưỡng cần đạt

**Lab (Lighthouse). Con số ước lượng từ đường cong điểm, để mỗi chỉ số đạt ≥ 0.99:**

| Chỉ số | Mobile | Desktop | Ngưỡng xanh Lighthouse (điểm 0.9, mobile) |
|---|---|---|---|
| FCP | **≤ 1,0 s** | ≤ 0,5 s | 1,8 s |
| LCP | **≤ 1,5 s** | ≤ 0,6 s | 2,5 s |
| TBT | **≤ 60 ms** (lý tưởng 0) | ≤ 60 ms | 200 ms |
| CLS | **≤ 0,02** (lý tưởng 0) | ≤ 0,02 | 0,1 |
| Speed Index | **≤ 1,9 s** | ≤ 0,7 s | 3,4 s |

**Field (CrUX, p75). Ngưỡng "tốt" của Google và mục tiêu chặt hơn của dự án:**

| Chỉ số | Google "tốt" | Google "kém" | **Mục tiêu dự án** |
|---|---|---|---|
| LCP | ≤ 2,5 s | > 4 s | **≤ 1,8 s** |
| INP | ≤ 200 ms | > 500 ms | **≤ 100 ms** |
| CLS | ≤ 0,1 | > 0,25 | **≤ 0,05** (thiết kế cho 0) |
| FCP | ≤ 1,8 s | > 3 s | **≤ 1,2 s** |
| TTFB | ≤ 0,8 s | > 1,8 s | **≤ 0,5 s** (HTML đã cache ≤ 0,2 s) |

## 4. Nguyên tắc khi viết code

- Mọi thay đổi ở frontend (template, CSS, JS, plugin mới, cấu hình cache) đều có thể làm tụt điểm. Trước khi báo xong phải đo như `pagespeed-measure.md`, và không được để chỉ số nào xấu đi.
- Thiếu dữ liệu chứ không thiếu hiệu năng: mặc định chọn cách **không tải gì** (không JS, không request, không font mới), rồi mới thêm khi đã chứng minh cần.
- Không tự ý bật các tính năng "tối ưu tự động" của plugin (WP Rocket: Delay JS, Remove Unused CSS, LazyLoad, Combine). Theme đã tự tối ưu, các tính năng này dễ xung đột (xem `pagespeed-third-party.md`). Hỏi người dùng trước.
- Điểm lab dao động ±3–5 giữa các lần chạy. Đánh giá theo trung vị của nhiều lần chạy, không theo 1 lần.
