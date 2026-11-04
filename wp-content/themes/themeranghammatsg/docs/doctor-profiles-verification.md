# Kiểm tra CPT Bác sĩ — 04/11/2026

Môi trường: `http://bactrung.test`, WordPress 7.1.4, PHP container 8.3.35, ACF PRO 6.8.10, Vite 8.3.5, Swiper 14.4.0. Lighthouse 13.6.0, Chrome 156. WP Rocket tắt. Mỗi loại trang chạy ba lần mobile và ba lần desktop, lấy trung vị.

## Kết quả chức năng

- PHP lint 28 file và `npm run build` thành công; `git diff --check` sạch. Triage xác nhận classic theme.
- Kiểm tra đủ 10 section trên hồ sơ tạm có dữ liệu đầy đủ; hồ sơ trống và thư viện một ảnh bỏ qua các section/nút thừa. URL danh sách, chi tiết và phân trang hoạt động.
- Tạo 27 hồ sơ công khai tạm: phân trang 13 / 12 / 2, đủ 27 ID duy nhất. Hồ sơ nháp bổ sung không xuất hiện trong danh sách công khai.
- Mở Classic Editor và nhóm ACF, sửa/lưu qua form quản trị thật. Văn bằng, chứng chỉ, ảnh hoạt động, hai nhóm video và năm ca hợp lệ được giữ sau khi lưu; dòng ca thiếu ảnh bị bỏ qua. CPT hỗ trợ revisions.
- Xác minh không có taxonomy và không có meta đánh giá tự sinh cho hồ sơ bác sĩ. Rewrite version 1 được cập nhật một lần.
- Bác sĩ khác tối đa sáu hồ sơ, loại hồ sơ hiện tại; bài viết lấy ba bài post mới nhất.
- Form dùng ID 3 từ Customizer, tải jQuery/Fluent khi focus; gửi rỗng bằng bàn phím hiện lỗi tiếng Việt. Không tạo lượt đặt lịch thử.
- Video không phát sinh iframe/request ngoài lúc tải hoặc cuộn trang. Sau Enter trên nút phát, iframe youtube-nocookie có title đúng và nhận focus. Request video kiểm thử được chặn.
- Đã xóa 28 hồ sơ kiểm thử và phiên đăng nhập tạm; giữ nguyên toàn bộ media sẵn có. Không để hồ sơ minh họa công khai.

## Bố cục và tương tác

42 lượt trình duyệt: 7 trang × 320/360/375/768/1024/1440px (hồ sơ đầy đủ, danh sách, trang 2, hồ sơ trống, hồ sơ một ảnh, danh mục, Liên hệ). Không lỗi JavaScript/HTTP, không ID trùng và không tràn ngang sau khi bỏ overflow clip trên html/body. Thanh lọc danh mục và slider chỉ cuộn bên trong khối. CLS khi cuộn bằng 0.

CPU chậm 4× trên hồ sơ:

| Màn hình | CLS khi thao tác/cuộn | Event duration lớn nhất | Long task lớn nhất |
|---|---:|---:|---:|
| 375px | 0 | 80 ms | 0 ms |
| 1440px | 0 | 88 ms | 60 ms |

Menu, slider và form dùng được bằng bàn phím. Các nút đặt lịch trỏ `#dat-lich`; anchor nằm dưới header sticky. Event duration là kiểm tra tương tác trong lab, không phải INP field. Có long task vượt mục tiêu 50 ms khi khởi tạo thành phần phía dưới; chưa đạt tuyệt đối yêu cầu không có task >50 ms.

Ảnh được kiểm tra ở 320/375/1024/1280/1440px với DPR 1, 1.75 và 2. Mỗi trang có đúng một ảnh ưu tiên cao. Có width/height và khung tỷ lệ; ảnh dưới trang dùng lazy/async. `sizes` xét cả tỷ lệ ảnh nguồn khi object-cover vào khung chân dung. Khi thiếu hero, chân dung eager/high và chỉ có một request cho ảnh đầu trang. Ảnh chưa thấy trong slider giữ lazy; không xem chúng là ảnh lỗi.

## Lighthouse sau triển khai

P/A/BP/SEO = Performance / Accessibility / Best Practices / SEO. FCP/LCP/SI tính bằng giây; TBT tính bằng ms.

| Trang | Thiết bị | P/A/BP/SEO | FCP | LCP | TBT | CLS | SI |
|---|---|---|---:|---:|---:|---:|---:|
| Hồ sơ bác sĩ | mobile | 99/100/78/58 | 1.052 | 2.027 | 0 | 0.000 | 1.052 |
| Hồ sơ bác sĩ | desktop | 100/100/78/58 | 0.290 | 0.462 | 0 | 0.000 | 0.290 |
| Danh sách bác sĩ | mobile | 99/100/78/58 | 0.931 | 2.105 | 0 | 0.000 | 0.931 |
| Danh sách bác sĩ | desktop | 100/100/78/58 | 0.248 | 0.443 | 0 | 0.000 | 0.248 |
| Trang chủ | mobile | 99/100/78/58 | 0.904 | 1.955 | 0 | 0.000 | 0.904 |
| Trang chủ | desktop | 100/100/78/58 | 0.243 | 0.426 | 0 | 0.000 | 0.254 |
| Danh mục | mobile | 100/100/78/58 | 0.902 | 1.802 | 0 | 0.000 | 0.902 |
| Danh mục | desktop | 100/100/78/58 | 0.251 | 0.402 | 0 | 0.000 | 0.251 |
| Bài viết | mobile | 100/98/78/58 | 0.902 | 0.902 | 0 | 0.000 | 0.902 |
| Bài viết | desktop | 100/99/78/58 | 0.247 | 0.247 | 0 | 0.000 | 0.247 |
| Liên hệ | mobile | 100/100/78/58 | 0.902 | 0.902 | 0 | 0.000 | 0.902 |
| Liên hệ | desktop | 100/100/78/58 | 0.249 | 0.249 | 0 | 0.000 | 0.249 |

LCP hồ sơ: ảnh hero (mobile dùng ảnh dọc, desktop dùng ảnh ngang). LCP danh sách: ảnh mục nổi bật. Trang chủ: hero; danh mục: ảnh nổi bật; bài viết: tiêu đề/nội dung đầu; Liên hệ: tiêu đề banner.

So với trước thay đổi, trung vị Performance của Trang chủ vẫn 99/100, Danh mục 100/100, Bài viết 100/100, Liên hệ 100/100 (mobile/desktop). Accessibility bài viết 98 mobile/99 desktop đã có từ trước; các trang bị ảnh hưởng khác đạt 100.

## Ngưỡng chưa đạt và giới hạn môi trường

- **Chưa đạt mục tiêu Performance 100 mobile**: hai trang bác sĩ đạt trung vị 99. Hồ sơ có FCP khoảng 1.052 s và LCP 2.027 s; danh sách LCP khoảng 2.105 s, vượt ngưỡng nội bộ FCP ≤1.0 s/LCP ≤1.5 s. Các chỉ số này vẫn ở vùng xanh của Lighthouse. Desktop đạt các ngưỡng nội bộ.
- Best Practices 78 do local HTTP và không redirect HTTPS. SEO 58 do noindex của site local và chưa có meta description; không thay cấu hình lập chỉ mục hoặc metadata toàn site trong tác vụ này.
- Insights còn báo HTTP/1, chuỗi request font/CSS chặn render và một số file ảnh mẫu có thể tối ưu thêm (khoảng 6 kB ở hồ sơ desktop). Không bật tối ưu tự động WP Rocket.
- Đây là kết quả Lighthouse local với media minh họa sẵn có, không phải Google PSI production hoặc CrUX. Cần đo lại sau khi nhập ảnh/nội dung bác sĩ thật và triển khai HTTPS/cache/HTTP2 trên hosting. Không có dữ liệu INP field trong kiểm tra này.

## Bằng chứng

JSON, ảnh chụp 375px/1440px và log kiểm tra nằm trong `/tmp/bactrung-doctors-audit/`: `before/`, `after/`, `final/`, `browser.json`, `pagination.json`, `admin.json`, `server.json`, `media.json`, `focus.json`, `video.json`. Hồ sơ tạm phục vụ đo đã được xóa nên URL chi tiết kiểm thử không còn công khai.

[Hướng dẫn nhập hồ sơ](doctor-profiles.md).

## Bộ dữ liệu bác sĩ mẫu theo yêu cầu người dùng

Đã tạo hồ sơ nguồn ID 520 (**Bác sĩ Nguyễn Minh An – Mẫu 01**) bằng RHM Sample Data v1.1.0, sau đó nhân thêm 19 hồ sơ từ nguồn này, tổng cộng **20 hồ sơ đã xuất bản**. Đây là dữ liệu mẫu người dùng yêu cầu giữ để xem giao diện, tách với fixtures kiểm thử tạm đã xóa ở trên.

- Dữ liệu ACF, nội dung và ảnh đại diện của 19 bản sao khớp hồ sơ nguồn; không có meta đánh giá tự sinh.
- Dùng chung 23 ảnh WebP minh họa tự vẽ; bản sao không tạo thêm media. Mỗi hồ sơ có 4 ảnh hoạt động, 3 văn bằng, 4 chứng chỉ, 3 ca điều trị và 2 + 2 video mẫu.
- Phân trang 13 / 7, đủ 20 ID duy nhất. Giữ nguyên 100 bài mẫu và 10 ảnh mẫu theo chuyên mục, cùng các hồ sơ nháp sẵn có.
- Kiểm tra hồ sơ trên 320/360/375/768/1024/1440px: HTTP 200, không lỗi JavaScript/ảnh, không tràn ngang, đúng một ảnh ưu tiên cao, CLS khi cuộn bằng 0; không có iframe/request ngoài trước khi bấm.
- PHP lint 8 file plugin thành công. Từ chối count = 0, count > 100 hoặc source không phải bác sĩ mẫu mà không tạo dữ liệu.

Lighthouse 13.6.0, ba lượt mỗi thiết bị, trung vị sau khi nhập bộ mẫu mới:

| Trang | Thiết bị | P/A/BP/SEO | FCP (s) | LCP (s) | TBT (ms) | CLS | SI (s) |
|---|---|---|---:|---:|---:|---:|---:|
| Hồ sơ mẫu 01 | mobile | 99/100/78/58 | 1.052 | 2.027 | 0 | 0.000 | 1.052 |
| Hồ sơ mẫu 01 | desktop | 100/100/78/58 | 0.293 | 0.463 | 0 | 0.000 | 0.293 |
| Danh sách 20 bác sĩ | mobile | 100/100/78/58 | 0.931 | 1.803 | 0 | 0.000 | 0.931 |
| Danh sách 20 bác sĩ | desktop | 100/100/78/58 | 0.247 | 0.402 | 0 | 0.000 | 0.247 |

Hồ sơ mẫu vẫn chưa đạt mục tiêu 100 mobile / LCP ≤1.5 s; danh sách đạt điểm Performance 100 nhưng LCP mobile khoảng 1.803 s vẫn vượt ngưỡng nội bộ. Giới hạn Best Practices/SEO do HTTP, noindex và thiếu meta description vẫn như phần trên. LCP hồ sơ là ảnh banner riêng, LCP danh sách là chân dung mục nổi bật.

Bằng chứng bộ mẫu mới: `/tmp/bactrung-sample-doctors/` (`server.json`, `seed-browser.json`, `final-browser.json`, `pagination.json`, `lighthouse/final/`, ảnh chụp 375px/1440px).
