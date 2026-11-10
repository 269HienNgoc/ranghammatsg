# Thư viện icon của theme

| Thư mục | Kích thước | Màu | Cách in | Chọn ở |
|---|---|---|---|---|
| `menu/` | 24×24 | `fill="currentColor"` (màu theo CSS) | inline `<svg>` (`rhm_icon_svg()`) | Giao diện → Menu → mở mục menu → "Icon (menu chính)" |
| `cta/` | 24×24 | `fill="currentColor"` | inline `<svg>` (`rhm_icon_svg()`) | Giao diện → Tùy biến → Nút liên hệ nhanh |
| `services/` | 48×48 | màu thương hiệu vẽ sẵn (#0b7a55, #13b54b, nền #cdeee1) | `<img src>` (`rhm_icon_url()`) | Giao diện → Tùy biến → Dịch vụ nổi bật (Trang chủ) |

- Thêm icon: chép file `.svg` vào đúng thư mục. Tên file chỉ gồm `a-z`, `0-9`, `-`. Đặt `<title>` tiếng Việt trong file — đó là nhãn hiện khi rê chuột trong ô chọn.
- Không đổi tên/xóa file đang được dùng (menu/dịch vụ đang chọn icon đó sẽ mất icon).

## Đồng bộ lên Cloudflare

1. Đồng bộ NGUYÊN thư mục `assets/icons/` (giữ cấu trúc `menu/`, `services/`) lên Cloudflare (R2 + custom domain, Pages…).
2. Giao diện → Tùy biến → **Thư viện icon** → nhập URL thư mục, vd `https://cdn.example.com/icons`.
3. Để trống = tải icon từ theme. Icon menu luôn in inline trong HTML nên không phụ thuộc CDN.

URL icon luôn có `?ver=<thời gian sửa file>` → có thể cache lâu (immutable) trên CDN; sửa file thì nhớ đồng bộ lại.
