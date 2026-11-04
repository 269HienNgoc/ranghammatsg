---
paths:
  - "wp-content/themes/themeranghammatsg/**/*.php"
  - "wp-content/themes/themeranghammatsg/src/**"
---

# CLS (Cumulative Layout Shift): thiết kế để = 0

CLS = tổng điểm dịch chuyển bố cục trong **cửa sổ phiên lớn nhất** (các shift cách nhau < 1 s, cả cửa sổ tối đa 5 s), tính trong **suốt vòng đời trang**, gồm cả lúc cuộn. Shift trong 500 ms sau khi người dùng bấm/gõ/chạm (`hadRecentInput`) không bị tính. Lab chỉ đo lúc tải, field đo cả lúc cuộn, nên lab = 0 chưa chắc field = 0.

Nguồn: web.dev/articles/cls · web.dev/articles/optimize-cls · wicg.github.io/layout-instability

## 1. Giữ chỗ trước cho mọi thứ tải sau

- **Ảnh/video/iframe**: luôn có `width` + `height` hoặc khung `aspect-*` + `object-cover`/`object-contain` (chi tiết trong `responsive-images.md`). Chiều cao/rộng cố định thì dùng `h-*`/`w-*`, không dùng `max-h`/`max-w`.
- **Embed (YouTube, Google Maps, video)**: luôn qua facade `[data-embed-src]` (`embed.js`). Nút facade và `.embed-player` thay vào phải có CÙNG khung tỉ lệ.
- **Slider (Swiper)**: bố cục trước JS = sau JS: độ rộng slide bằng utility `w-*` trong template, khoảng cách bằng padding, nút điều hướng giữ chỗ bằng `invisible` (không dùng `hidden`), bullet pagination có chiều cao giữ sẵn. Chi tiết `carousel.js` trong CLAUDE.md.
- **Nội dung có/không tùy dữ liệu** (sao kk-star, badge, thông báo): render bằng PHP ngay từ đầu, không chèn bằng JS/AJAX sau khi tải. Phải chèn sau thì giữ chỗ bằng `min-h-*` đúng chiều cao.
- **Form Fluent**: khung form được in đầy đủ bằng PHP. Script "chạm mới tải" (`fluent-lazy.js`) không được đổi kích thước form khi khởi tạo. Thông báo lỗi/thành công hiện ở dưới nút hoặc ở vùng đã giữ chỗ.
- Không bao giờ chèn banner/thanh thông báo/cookie bar **phía trên** nội dung đang có. Cần thì dùng `position: fixed` (đè lên, không đẩy nội dung).

## 2. Ẩn/hiện theo màn hình bằng CSS, không bằng JS

- Bố cục responsive (số cột, nút "…" lọc chuyên mục, ẩn/hiện mục) tính bằng class breakpoint trong PHP/CSS (vd `md:max-lg:hidden`), **không** đo `offsetWidth` bằng JS rồi mới ẩn.
- Không dùng class do JS gắn (`.js-ready`, `.loaded`) để đổi bố cục lúc tải.

## 3. Hoạt ảnh và header sticky

- Hoạt ảnh/transition chỉ dùng `transform` và `opacity` (không gây shift). Không animate `top/left/width/height/margin/padding`.
- Header sticky `.is-stuck`: desktop thu gọn **chỉ bằng transform** (đã làm). Không đổi `height`/`padding` của header khi cuộn, vì field CLS bắt được.
- Accordion/submenu mở rộng do người dùng bấm thì được đổi chiều cao (nằm trong cửa sổ `hadRecentInput`). Hoạt ảnh tự chạy không do bấm thì không.

## 4. Font

- `font-display: swap` → lúc font thật thay font dự phòng, chữ có thể đổi độ rộng làm khối dịch chuyển. Đã preload 2 subset Roboto để giảm rủi ro.
- Nếu đo thấy shift do đổi font (insight "Layout shift culprits" ghi *web font*): thêm `@font-face` dự phòng `local("Arial")` với `size-adjust` / `ascent-override` / `descent-override` khớp Roboto. Cách này không tải thêm font nên không vi phạm quy tắc "chỉ Roboto". Hỏi người dùng trước khi thêm.

## 5. `content-visibility` và bfcache

- Nếu dùng `content-visibility: auto` cho section dài phía dưới thì PHẢI kèm `contain-intrinsic-size` gần đúng chiều cao thật, nếu không thanh cuộn nhảy và gây shift.
- Giữ trang đủ điều kiện **bfcache** (không `unload`, không `Cache-Control: no-store` cho khách): bấm Quay lại sẽ khôi phục tức thì, CLS/LCP của lần đó gần 0. Xem `pagespeed-ttfb-server.md`.

## 6. Kiểm tra

- Lighthouse CLS = 0 (mobile + desktop) và insight "Layout shift culprits" trống.
- CDP: `layout-shift` observer, cuộn hết trang, chờ slider tự chạy, mở menu mobile → tổng shift không có `hadRecentInput` = 0. Kiểm tra ở 320/360/375/768/1024/1440px.
- Thêm thứ mới vào màn hình đầu (badge, sao, nút) → so ảnh chụp trước/sau khi font + JS tải xong: phải trùng khớp từng px.
