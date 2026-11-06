---
paths:
  - "wp-content/themes/themeranghammatsg/src/js/**"
  - "wp-content/themes/themeranghammatsg/inc/**"
  - "wp-content/themes/themeranghammatsg/template-parts/**"
  - "wp-content/themes/themeranghammatsg/vite.config.js"
---

# JavaScript: TBT ≤ 60 ms (lab), INP ≤ 100 ms (field)

- **TBT** (Total Blocking Time, lab, chiếm **30% điểm**): tổng phần vượt 50 ms của mọi long task trong khoảng FCP → TTI. Task 70 ms tính 20 ms vào TBT.
- **INP** (Interaction to Next Paint, field): độ trễ tương tác gần tệ nhất (≈ p98 trong 1 lượt xem) của bấm/chạm/gõ phím, tính đến lúc vẽ khung hình kế tiếp. Gồm 3 phần: **input delay** (main thread đang bận) + **processing** (handler chạy) + **presentation delay** (style/layout/paint). Cuộn và hover không tính.
- Lab không đo INP → TBT thấp + handler nhẹ là cách duy nhất để chắc INP tốt trước khi có dữ liệu field.
- Lighthouse mobile **chậm CPU 4×**: task 15 ms trên máy dev thành 60 ms → đã là long task.

Nguồn: web.dev/articles/tbt · web.dev/articles/inp · web.dev/articles/optimize-inp · web.dev/articles/optimize-long-tasks · web.dev/articles/dom-size-and-interactivity · w3c.github.io/event-timing

## 1. Ít JS nhất có thể

- Mặc định **không JS**: ưu tiên HTML/CSS thuần (`<details>`, `:has()`, `scroll-snap`, `popover`, `inert`, CSS transition). Chỉ viết JS khi CSS không làm được.
- Vanilla JS, **không jQuery** trong theme. Không thêm thư viện khi vài dòng tự viết là đủ. Thư viện lớn (Swiper) chỉ import module cần (A11y, Autoplay, Navigation, Pagination) và dùng chung chunk `dist/js/swiper.js`.
- `main.js` (mọi trang, `defer`) phải nhỏ (hiện ~3 kB): chỉ header/menu. Logic của section → file module riêng, nạp bằng `wp_enqueue_script_module()` **chỉ khi section có mặt và đủ điều kiện** (vd hero ≥ 2 slide, carousel ≥ 2 mục).
- Phần dưới màn hình đầu: file nhỏ chờ `IntersectionObserver` (rootMargin ~300px) rồi mới `import()` phần nặng (mẫu `carousel.js` → `_carousel.js`).
- Tính năng chỉ cần khi người dùng tương tác: tải khi có tín hiệu `pointerover`/`focusin`/`touchstart` (mẫu `fluent-lazy.js`).
- Không gửi JS cũ/polyfill: Vite build ES module hiện đại. Không thêm `@vitejs/plugin-legacy`, không polyfill cho API trình duyệt hiện đại đã hỗ trợ (insight "Legacy JavaScript"). Không để 2 bản của cùng 1 thư viện ("Duplicated JavaScript").

## 2. Không có long task lúc tải

- Không chạy việc nặng khi module được nạp. Việc không gấp → `requestIdleCallback` (fallback `setTimeout`) hoặc sau sự kiện `load` (mẫu nạp slide hero kế tiếp).
- Vòng lặp lớn / khởi tạo nhiều thành phần → chia nhỏ và nhường main thread giữa các phần:
  ```js
  const yieldToMain = () => ( globalThis.scheduler?.yield ? scheduler.yield() : new Promise( ( r ) => setTimeout( r, 0 ) ) );
  ```
- Không dùng `document.write`, không XHR đồng bộ, không `<script>` đồng bộ trong `<head>`.

## 3. Handler tương tác nhẹ (INP)

- Handler chỉ làm việc cập nhật giao diện tức thì (đổi class, `aria-expanded`). Việc phụ (tracking, lưu trạng thái, tải dữ liệu) để sau khi vẽ: `requestAnimationFrame(() => setTimeout(work, 0))` hoặc `scheduler.yield()`.
- Không gây **forced reflow** (insight "Forced reflow"): đọc layout (`offsetHeight`, `getBoundingClientRect`, `scrollWidth`) TRƯỚC, ghi style SAU. Không xen kẽ đọc/ghi trong vòng lặp. Hoạt ảnh chiều cao accordion: đo 1 lần, ghi trong `requestAnimationFrame`.
- Listener `touchstart`/`touchmove`/`wheel` luôn `{ passive: true }` (trừ khi bắt buộc `preventDefault`). `scroll`/`resize` thì gom bằng `requestAnimationFrame` hoặc dùng `IntersectionObserver`/`ResizeObserver` thay cho việc đo liên tục (mẫu `.is-stuck` của header).
- Dùng event delegation cho danh sách dài thay vì gắn listener cho từng mục.
- Không đổi style ảnh hưởng toàn trang khi bấm (vd đổi class trên `<html>` làm toàn bộ DOM tính lại style) nếu không cần. Trường hợp cần (`html.nav-open`) thì giữ selector đơn giản.

## 4. DOM gọn

- Mục tiêu: **< 800 node** trong `<body>`, độ sâu < 32, không phần tử nào > 60 con (insight "Optimize DOM size"). DOM lớn làm mọi tương tác và layout chậm.
- Không in sẵn markup ẩn lớn (menu lặp 2 lần, modal ẩn, SVG icon lặp nhiều lần). Theme dùng 1 menu cho desktop + mobile, giữ nguyên như vậy.
- Icon SVG inline dùng lại: giữ path ngắn. Lặp quá nhiều (> ~30 icon/trang) thì cân nhắc `<use href="#id">` sprite.
- Danh sách rất dài phía dưới: `content-visibility: auto` + `contain-intrinsic-size` (xem `pagespeed-cls.md`).

## 5. Kiểm tra

- Lighthouse: TBT ≤ 60 ms (mobile), các insight "Legacy JavaScript", "Duplicated JavaScript", "Forced reflow", "DOM size" không cảnh báo, "Reduce JavaScript execution time" < 1 s.
- CDP với CPU 4×: không `longtask` > 50 ms lúc tải; mỗi tương tác (menu, submenu, slider, dropdown lọc, chạm form) có `event` duration < 100 ms.
- Sau `npm run build`: xem dung lượng gzip của file JS mới hoặc vừa sửa. JS lúc tải trang chủ (chưa tương tác) tổng ≤ ~40 kB gzip, gồm cả Swiper.
