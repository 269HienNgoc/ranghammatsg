# Landing page theo chủ đề

Mỗi chủ đề landing (hiện có: **Giới thiệu**) là 1 Page Template. Khi trang chọn template đó, editor hiện nhóm field
"Landing page – <chủ đề>": danh sách **section** để thêm, xóa, kéo sắp xếp và tạm ẩn.

## Cho content: tạo trang

1. Trang → Thêm trang mới → nhập tiêu đề.
2. Khung **Thuộc tính trang** → Giao diện → chọn **Giới thiệu**. Nhóm field "Landing page – Giới thiệu" hiện dưới tiêu đề.
3. Bấm **Thêm section** và chọn loại. Section hiện trên trang theo đúng thứ tự trong danh sách (kéo thanh tiêu đề để đổi chỗ).
4. Mỗi section có 2 ô ở cuối:
   - **Hiển thị**: tắt thì section bị ẩn tạm, dữ liệu vẫn giữ.
   - **Neo (id)**: vd `doi-ngu-bac-si`. Nút/link `#doi-ngu-bac-si` sẽ cuộn tới section này.
5. Section nào thiếu dữ liệu bắt buộc thì **không hiện** (không để khung trống): thiếu tiêu đề, không có ảnh hay mục hợp lệ nào.

### Các loại section (chủ đề Giới thiệu)

Thứ tự theo thiết kế: Banner → Dải điểm nổi bật → Thẻ giá trị → Danh sách dịch vụ → Bác sĩ tiêu biểu → (Ô thông tin 2/5 + Ô lưới ảnh 3/5) → (Ô banner 1/2 + Ô banner 1/2) → (Ô thông tin 1/2 + Ô thông tin 1/2) → Ảnh lớn + chữ + ô nổi bật.

| Section | Cần nhập | Ghi chú |
|---|---|---|
| Banner đầu trang (H1) | Tiêu đề (trống → tiêu đề trang), đoạn nổi bật (chữ đậm), mô tả, ảnh desktop 1600×1000 + ảnh mobile 828×620 (tùy chọn), nút (tùy chọn) | **Đặt đầu tiên**: tiêu đề banner là H1 của trang (banner ở vị trí khác: tiêu đề thành H2, không có breadcrumb, ảnh tải lazy). Ảnh phủ nửa phải màn hình, mép trái tự mờ dần, nên để chủ thể lệch phải. Đây là ảnh tải đầu tiên (LCP): nén WebP, ≤ 150 kB |
| Banner ảnh | **Ảnh desktop 1920 × 650** (bắt buộc), **ảnh mobile 828 × 620** (tùy chọn), link khi bấm (tùy chọn), độ rộng (Tràn màn hình / Trong khung nội dung) | Dưới 768px dùng ảnh mobile, từ 768px dùng ảnh desktop; mỗi màn hình chỉ tải 1 ảnh. Không có ảnh mobile → dùng ảnh desktop thu nhỏ (chữ trong ảnh khó đọc trên điện thoại — nên có ảnh mobile). Nhập "Văn bản thay thế" cho ảnh (là tên link nếu có link). Ảnh giữ nguyên tỉ lệ, không cắt. Đặt đầu trang → ảnh tải ưu tiên (nén WebP ≤ 200 kB); trang vẫn có tiêu đề H1 ở trên banner |
| Dải điểm nổi bật | 4 ô: icon + nhãn ngắn | Đặt ngay sau Banner thì dải nổi lên đè mép dưới banner |
| Thẻ giá trị + ảnh | Tiêu đề, 3 thẻ (icon, tiêu đề, mô tả), ảnh bên phải 4:3 (tùy chọn) | |
| Danh sách dịch vụ | Tiêu đề, mô tả, **Số dịch vụ mỗi hàng** (Tự động / 3 / 4 / 5), **Hiển thị dạng slider**, dịch vụ (tối đa 12: ảnh 4:3 khuyến nghị 800 × 600, tên, link, **Vị trí ảnh trong khung** Giữa / Trên / Dưới — ảnh người dọc/vuông chọn Trên để không mất đầu) | Tự động: không để lẻ 1 dịch vụ ở hàng cuối (6 → 3 + 3, 8 → 4 + 4, 9 → 3 + 3 + 3). Slider: 1 hàng trượt ngang, số thẻ / khung = số dịch vụ mỗi hàng, có nút trái/phải. Mobile luôn vuốt ngang |
| Bác sĩ tiêu biểu | Tiêu đề, mô tả, chọn hồ sơ bác sĩ (+ mô tả riêng nếu cần) | Dưới 640px: ảnh trên, chữ dưới; từ 640px: thẻ ngang, từ 1024px: 2 thẻ/hàng. Ảnh, tên, link lấy từ hồ sơ; mô tả trống → ô Tóm tắt của hồ sơ → Chức danh |
| **Ô** thông tin: ảnh + chữ | Độ rộng, tiêu đề, ảnh (giữ tỉ lệ), nội dung, nút | Vd Giấy phép hoạt động, Hệ thống vô trùng, Vật liệu chính hãng. Từ 640px: ảnh 40% bên trái + chữ bên phải; dưới 640px: ảnh rộng hết ô ở trên, chữ ở dưới |
| **Ô** lưới ảnh nhỏ | Độ rộng, tiêu đề, ô (ảnh vuông nền trắng + nhãn 2 dòng) | Mobile: vuốt ngang, khoảng 2 ô + hé ô kế tiếp. Từ 640px: 6 ô/hàng; desktop từ 1024px: 6 ô với khối ≥ 3/5, khối hẹp 3 ô. Dư ô: cuộn ngang, có nút trái/phải trên tablet/desktop |
| **Ô** banner nổi bật | Độ rộng, tiêu đề, dòng phụ, ảnh, logo (tùy chọn), link (tùy chọn) | Vd All-on-4, Invisalign. Có link → bấm cả ô. Từ 640px: ảnh 45% bên trái; dưới 640px: ảnh trên, chữ dưới |
| Ảnh slider + chữ, nhiều item | Tiêu đề section, dòng nhỏ, mô tả; số cột (1 / 2 / 3); vị trí ảnh (Trái / **Phải** / Trên / Dưới, chung mọi item); **Item** (tối đa 12): ảnh slider (tối đa 12, khuyến nghị 1200×900 4:3), tiêu đề item, nội dung | Vd "Trang thiết bị" → item "Máy chụp X-quang Vatech". Mỗi item là 1 thẻ nền xanh mint nhạt → trắng. Ảnh cắt về khung 4:3 (để chủ thể ở giữa). ≥ 2 ảnh: nút trái/phải + vuốt, không tự chạy. Số cột áp dụng từ 1024px (dưới đó 1 item / hàng); vị trí ảnh từ 640px; **dưới 640px luôn ảnh trên, chữ dưới**. 1 cột + ảnh trái/phải: ảnh 1/3 thẻ (dải ngang); 3 cột nên chọn ảnh Trên/Dưới |
| Ảnh lớn + chữ + ô nổi bật | Tiêu đề, nội dung, ảnh 4:3, video (tùy chọn); tab "Ô nổi bật": icon, tiêu đề, mô tả, nút | Vd Cơ sở vật chất + Chi phí hợp lý |
| Ảnh trước – sau khách hàng (slider) | Tiêu đề, dòng nhỏ, mô tả, checklist (mỗi dòng 1 mục + 1 icon chung), danh sách khách hàng: ảnh chân dung (dọc ~3:4), ảnh trước + ảnh sau (ngang 3:2), tên, tỉnh/thành, dịch vụ, loại implant/vật liệu | Slider 2 thẻ / khung từ 768px, 1 thẻ trên mobile. Khách thiếu tên hoặc thiếu 1 trong 3 ảnh bị ẩn. Tối đa 12 khách. Nền xanh nhạt như mẫu: dùng "Nền section" |
| Tiêu đề + nội dung dài + ảnh lớn | Tiêu đề, dòng nhỏ trên tiêu đề (tùy chọn), nội dung (trình soạn thảo đầy đủ: đoạn, danh sách, "Tiêu đề 3"), ảnh rộng ~1600px | Nội dung và ảnh bên dưới đều rộng hết khung (tối đa 1248px), giữ nguyên tỉ lệ ảnh, không cắt. Cần tiêu đề + (nội dung hoặc ảnh). Tiêu đề phụ trong nội dung dùng "Tiêu đề 3". Nội dung dài: bật **Rút gọn nội dung (nút Xem thêm)** + nhập **Số ký tự hiện trước nút** (100–5000, mặc định 600; cắt ở hết từ, kèm "…"; nội dung chỉ dài hơn ≤ 80 ký tự thì không rút gọn) + chữ nút tùy chọn (trống = "Xem thêm" / "Thu gọn"). Ảnh luôn hiện dưới nút |
| Ảnh lớn xếp dọc (kèm mô tả ảnh) | Tiêu đề, dòng nhỏ (tùy chọn), chọn nhiều ảnh (tối đa 30, rộng ~1600px) | Ảnh hiện lần lượt từ trên xuống, mỗi ảnh rộng hết khung, giữ nguyên tỉ lệ (không cắt). Mô tả dưới ảnh = ô **"Chú thích"** của ảnh (bấm vào ảnh trong khung chọn ảnh để sửa; trống → không hiện mô tả). Xuống dòng trong Chú thích được giữ |
| Form đăng ký (Fluent Forms) | Dòng nhỏ, tiêu đề, mô tả, lợi ích (mỗi dòng 1 ý), bật/tắt hotline, **chọn form** Fluent Forms, tiêu đề trên form (tùy chọn) | Khung nền mint: trái chữ + lợi ích có dấu tích + hotline, phải thẻ form trắng (mobile: chữ trên, form dưới). Không chọn form → dùng form đặt lịch mặc định (Tùy biến → Thông tin phòng khám). Không có form nào → khách không thấy section (quản trị viên thấy ghi chú). Nếu form trùng với form của popup đặt lịch: trang không hiện popup, mọi nút "Đặt lịch" cuộn tới section này. Chữ nút gửi / ô chọn ("Submit Form", "- Select -") sửa trong Fluent Forms. Nên đặt Neo (vd `dang-ky`) để link `#dang-ky` cuộn tới |
| Nhóm ảnh 3 cột (tiêu đề + mô tả + hàng ảnh) | Tiêu đề, mô tả (tùy chọn); **Item** (tối đa 12): tiêu đề item, mô tả item (tùy chọn), ảnh (tối đa 9, nên 3/6/9, khuyến nghị 1200×900) | Các item xếp lần lượt từ trên xuống: tiêu đề có vạch dọc → mô tả → hàng ảnh. Từ 640px: 3 ảnh / hàng (dư ảnh xuống hàng mới); mobile: 1 hàng vuốt ngang. Ảnh cắt về khung 4:3 cho hàng thẳng đều. Item thiếu tiêu đề hoặc không có ảnh bị bỏ qua |
| Danh sách có icon + ảnh | Tiêu đề, dòng nhỏ, mô tả (tùy chọn), **icon của danh sách** (mặc định dấu tích), **danh sách**: mỗi dòng 1 ý (tối đa 30) + icon riêng (tùy chọn), ảnh (tùy chọn), **vị trí ảnh**: Trái / Phải / Trên / Dưới | Trái/Phải: 2 cột trên máy tính, tiêu đề có vạch dọc; mobile: ảnh "Trái" nằm trên danh sách, ảnh "Phải" nằm dưới. Trên/Dưới (hoặc không có ảnh): tiêu đề canh giữa, ảnh rộng hết khung, danh sách chia 2 cột trên máy tính. Ảnh giữ nguyên tỉ lệ. Xuống dòng trong 1 dòng danh sách được giữ |
| Ảnh lớn + slider tab | Tiêu đề, mô tả (tùy chọn), **ảnh lớn** (ảnh dọc ~800×1000, chủ thể ở giữa, không có chữ trong ảnh — trên máy tính ảnh được cắt cho cao bằng thẻ slider; tùy chọn) + **vị trí ảnh lớn** (Trái / Phải), chữ nút (trống → "Tư vấn"); **Slide** (tối đa 8): nhãn tab (trống → tiêu đề), tiêu đề, **ảnh tròn** (vuông 600×600) + **vị trí ảnh tròn** (Trái / Phải), danh sách (mỗi dòng 1 ý) | Vd "Các loại dán sứ Veneer". Máy tính: ảnh lớn 1 bên, thẻ slider bên kia; **mobile: ảnh lớn trên, slider dưới**, trong slide ảnh tròn trên chữ. Thẻ slider: hàng tab trên cùng (tab đang xem nền xanh), nút trái/phải, vuốt trên mobile, không tự chạy. Ảnh tròn bị cắt tròn → dùng ảnh vuông, chủ thể ở giữa (ảnh chữ nhật vẫn được nhưng kém nét). Nút "Tư vấn" mở popup đặt lịch; trang có section Form đăng ký trùng form popup thì nút cuộn tới form đó. Slide thiếu tiêu đề hoặc danh sách bị bỏ qua |
| Bảng giá | Tiêu đề, dòng nhỏ, mô tả (tùy chọn); **tiêu đề cột tên** (trống → "Dịch vụ"); **Cột giá** (1–6): tiêu đề + dòng phụ (vd "Đơn vị: VNĐ"); **Dòng** (tối đa 40): tên dịch vụ (trình soạn thảo) + **Giá theo cột** (ô thứ N = cột giá thứ N: giá + giá gốc gạch ngang tùy chọn); **Nền tiêu đề cột** (nhóm thu gọn, giống Nền section: màu đơn / gradient thẳng / gradient tròn + công tắc **Chữ tối** khi chọn nền sáng — áp cho hàng tiêu đề bảng và thanh tên/thanh nút của thẻ mobile); bật/tắt cột nút + chữ nút (trống → "Tư vấn"); **Mobile dạng thẻ** (bật/tắt); ghi chú dưới bảng | Ô "Tên dịch vụ" và "Giá / nội dung ô" là trình soạn thảo (đậm, nghiêng, màu, link, danh sách; Enter = đoạn mới, Shift + Enter = xuống dòng): giá (vd `15.000.000`, `Liên hệ`) hoặc nội dung dài (cấu tạo, ưu điểm…). Cột có ô dài, nhiều dòng hoặc danh sách → canh trái. Ô tự xuống dòng, bảng tự chia độ rộng cột vừa khung; cột có ô dài hơn 40 ký tự hiển thị chữ thường canh trái (cả cột), cột giá giữ số đậm canh giữa. Có giá gốc → giá gốc gạch ngang + giá ưu đãi màu đỏ. Cột không có giá: để trống ô đó nhưng vẫn giữ ô để các ô sau đúng cột. Nút mở popup đặt lịch. Bảng **từ 4 cột** (tính cột tên + cột giá + cột nút) và bật "Mobile dạng thẻ": dưới 768px mỗi dòng thành 1 thẻ (tên trên, các giá theo nhãn cột, nút dưới; ô trống ẩn). Tắt, hoặc bảng < 4 cột: mobile giữ dạng bảng, cuộn ngang nếu không vừa. Nhiều bảng → thêm nhiều section Bảng giá |
| Số liệu nổi bật | Các ô số + nhãn (tối đa 8) | Tiêu đề không bắt buộc |
| Chặng đường phát triển | Tiêu đề, dòng nhỏ, mô tả (tùy chọn); các mốc (tối đa 20): **năm** (hiện trên hàng ngang, nên ngắn: 2014, 2015 - 2022), tiêu đề, mô tả (có thể có danh sách), **ảnh** (tùy chọn, tối đa 12, khuyến nghị 4:3) | Timeline nằm ngang ở mọi màn hình: hàng năm trên 1 đường kẻ + nội dung năm đang chọn bên dưới. Bấm năm / nút trái phải / vuốt để chuyển: đường kẻ chạy tới năm mới, năm đã qua có chấm và chữ trượt xuống, năm đang chọn hiện thành viên màu. Nhiều năm → hàng năm vuốt ngang được. Mốc thiếu năm hoặc tiêu đề bị bỏ. Ảnh: 1–2 ảnh 2 cột, 3 ảnh 3 cột, **từ 4 ảnh** tự thành slider. Chú thích dưới ảnh = ô **Chú thích** của ảnh trong thư viện (trống thì không hiện). Khung 4:3, ảnh khác tỉ lệ bị cắt quanh tâm |
| Thư viện ảnh (slider) | Tiêu đề, ảnh 4:3 | Nhập "Văn bản thay thế" cho từng ảnh trong Thư viện |
| Hệ thống phòng khám | Tiêu đề, mô tả; **Chi nhánh**: tên + link Google Maps; **Ảnh** (tối đa 12): ảnh 1600×900 (16:9), tiêu đề ảnh, chú thích; **Nền tiêu đề ảnh (H3)** (tùy chọn: màu đơn / gradient, bật "Chữ tối" khi nền sáng) | Chi nhánh có link → nút "Chỉ đường" (tab mới). Slider 1 ảnh / lần (máy tính: rộng tối đa 960px, canh giữa; mobile/tablet: rộng hết khung) (mobile khung 4:3, cắt 2 bên → để chủ thể ở giữa), tiêu đề + chú thích riêng ngay dưới ảnh. ≥ 2 ảnh: nút trái/phải, vuốt trên mobile, dải ảnh nhỏ bên dưới (bấm ô nào slider chuyển tới ảnh đó). Ảnh thiếu tiêu đề bị bỏ qua |
| Đội ngũ bác sĩ (slider) | (chỉ tiêu đề, tùy chọn) | Giống trang chủ: hồ sơ bật "Hiện ở section Đội ngũ bác sĩ" |
| Câu hỏi thường gặp | Câu hỏi + trả lời | Mở/đóng không cần JS |
| Nội dung trình soạn thảo | — | In nội dung ô soạn thảo chính của trang (bảng, ảnh, shortcode…) |
| Kêu gọi đặt lịch | Tiêu đề, mô tả (trống → chữ mặc định) | Nút đặt lịch (mở popup) + hotline |

### Ô xếp cạnh nhau (Độ rộng)

Các section **Ô …** có ô "Độ rộng ô": Toàn trang, 1/2, 2/5 (hẹp), 3/5 (rộng). Các ô **liền nhau** trong danh sách tự xếp chung 1 hàng
khi tổng vừa 1 hàng: 1/2 + 1/2, 2/5 + 3/5, 3/5 + 2/5. Ô không vừa phần còn lại sẽ xuống hàng mới. Mobile: mọi ô xếp dọc.

### Định dạng chữ trong mô tả / nội dung

Các ô **mô tả, nội dung, chú thích** (mô tả section, đoạn nổi bật của banner, mô tả thẻ / mốc / item, chú thích ảnh hệ thống phòng khám, nội dung ô…)
có thanh công cụ (bấm vào ô để hiện): **đậm**, *nghiêng*, gạch chân, **màu chữ** (bảng màu thương hiệu + "Tùy chỉnh…" chọn mã màu bất kỳ),
gắn / bỏ link, canh trái / giữa / phải / đều, xóa định dạng. Enter = đoạn mới, Shift + Enter = xuống dòng. Link ra trang khác tự mở tab mới.

- Tiêu đề (H2/H3), dòng nhỏ trên tiêu đề và chữ nút vẫn là chữ thường (giữ thiết kế đồng nhất).
- **Màu tiêu đề**: mỗi section có tiêu đề có ô "Màu tiêu đề" (ngay bên phải ô Tiêu đề) — bảng màu thương hiệu hoặc mã màu bất kỳ, đổi màu cả tiêu đề chính (H1/H2) của section; để trống = màu mặc định. Section có tiêu đề con (thẻ, mốc, item, ảnh phòng khám, ô nổi bật, tiêu đề trên form, tên bác sĩ) có thêm ô **"Màu tiêu đề con (H3)"** cạnh đó: 1 màu áp cho mọi tiêu đề con của section.
- **Nền section** (mọi section, nhóm thu gọn "Nền section" ở cuối section): Kiểu nền = Mặc định (nền thiết kế) · Màu đơn · Gradient thẳng · Gradient tròn. Tối đa 3 màu (ô màu có độ trong suốt), gradient chia đều các màu. Gradient thẳng: Góc (90° trái → phải, 180° trên → dưới, 135° chéo). Gradient tròn: Hình (elip/tròn) + Tâm X/Y (%, vd 100/0 = góc trên phải). Nền phủ hết bề ngang màn hình; ô dạng lưới (Ô thông tin, Ô lưới ảnh, Ô banner) phủ đúng ô. Nền tối → bật **Chữ sáng** (chữ, tiêu đề, link thành trắng; thẻ/khung nền sáng bên trong giữ chữ tối). Tự kiểm tra độ tương phản: chữ tối trên nền tối hoặc chữ trắng trên nền sáng làm giảm điểm Accessibility.
- Vẫn là chữ thường, mỗi dòng 1 ý: "Lợi ích" của Form đăng ký, nhãn của Ô lưới ảnh nhỏ; mô tả ảnh của "Ảnh lớn xếp dọc" là chú thích ảnh trong thư viện Media.
- Dán nội dung từ Word / web khác: tiêu đề, ảnh, bảng… bị bỏ khi hiển thị, chỉ giữ đoạn văn + định dạng ở trên.
- Màu chữ nhạt (vàng, trắng, xám nhạt) trên nền trắng khó đọc và làm giảm điểm Accessibility của PageSpeed. "Trắng" / "Vàng" chỉ dùng trên nền tối.

### Icon

Icon nét màu xanh lấy từ `assets/icons/landing/` của theme (đội ngũ, răng, khiên, tim, mục tiêu, mắt, kim cương, heo đất, giấy phép,
huy chương, lấp lánh, đồng hồ, kính hiển vi, thiết bị, dấu tích). Thêm icon: chép file `.svg` 24×24 nét `stroke="currentColor"`,
có `<title>` tiếng Việt (nhãn trong ô chọn).

Chưa thêm section nào: trang hiện tiêu đề + nội dung trình soạn thảo + kêu gọi đặt lịch (giao diện cũ).

Đổi template của trang: dữ liệu section vẫn được giữ, nhưng loại section mà chủ đề mới không có thì không hiển thị.

### Trang mẫu

Plugin **RHM Demo Data** (≥ 1.1.0) tạo trang "Giới thiệu (dữ liệu mẫu)" (`/gioi-thieu-mau/`) có đủ mọi section, nội dung theo thiết kế trang Giới thiệu: Công cụ → Dữ liệu mẫu RHM → **Tạo trang landing mẫu**, hoặc `wp rhm-demo landing`. Chạy lại = ghi đè nội dung về mặc định. Nút "Xóa toàn bộ dữ liệu mẫu" (hoặc `wp rhm-demo delete`) xóa trang và ảnh mẫu; ngừng kích hoạt / xóa plugin thì dữ liệu mẫu vẫn giữ nguyên (từ 1.1.1). Nội dung + key field ACF nằm ở `rhm-demo-data/includes/class-rhm-demo-landing.php` — đổi field của layout trong theme thì sửa cả file này.

## Cho dev

Code: `inc/landing/` (điểm vào `landing.php`), template-part: `template-parts/landing/<layout>.php`.

"Ô lưới ảnh nhỏ" dùng native scroll + CSS scroll-snap ở mọi kích thước. Khi cần nút trái/phải, section nạp module `carousel.js`, module này tải động `src/js/_tiles.js` để điều khiển nút và cập nhật trạng thái khi cuộn/resize; không tự chạy. `_tiles.js` tìm track theo `aria-controls` của nút trước (`[data-rhm-tiles-prev]`), không có thì lấy `[data-rhm-tiles-track]` đầu tiên của section → 1 section có thể có nhiều track.

"Hệ thống phòng khám" (`clinics`) không dùng Swiper: slider là 1 track `_tiles.js` (scroll-snap, slide `w-full` = ảnh + chú thích, các slide cao bằng nhau nên chuyển ảnh không nhảy trang). Nút ‹ › nằm trên lớp phủ cùng khung tỉ lệ với ảnh (canh giữa ảnh), ≥ 1024px lùi vào 96px để cột nút liên hệ nhanh không che. `carousel.js` tải động `src/js/_showcase.js` khi có `[data-rhm-showcase-thumbs]`: bấm ảnh nhỏ → cuộn track tới slide; track cuộn → ảnh nhỏ tương ứng có `aria-current="true"`. Ảnh nhỏ `alt=""` + chữ ẩn "Xem ảnh N: tiêu đề" (tham số `$alt` của `rhm_landing_picture()`).

| File | Vai trò |
|---|---|
| `inc/landing/landing.php` | `rhm_landing_layouts()` (thư viện layout, cờ `boxed` = layout dạng ô), hàng ô `RHM_LANDING_SPANS` / `rhm_landing_span_class()` / `rhm_landing_box_sizes()` (sizes ảnh trong ô), `rhm_landing_templates()` (chủ đề, filter `rhm_landing_templates`), `rhm_landing_sections()`, `rhm_landing_render()`, helper đọc dữ liệu (`rhm_landing_text/lines/image/gallery/url/html`) và in markup (`rhm_landing_heading()`, `rhm_landing_button()`, `rhm_landing_picture()`, `rhm_landing_enqueue_module()`) |
| `inc/landing/fields.php` | `acf/init`: 1 field group `group_rhm_landing_<slug>` / chủ đề (location `page_template`), Flexible Content `rhm_landing_sections`; field chung nhóm nền (`rhm_landing_background_fields()`: `bg_type`, `bg_color_1..3`, `bg_angle`, `bg_shape`, `bg_pos_x/y`, `bg_light_text`) + `show` + `anchor`; helper `rhm_landing_field()`, `rhm_landing_image_field()`, `rhm_landing_wysiwyg_field()`, `rhm_landing_button_fields()` |
| `inc/landing/layouts/<layout>.php` | `rhm_landing_layout_<layout>_fields( $prefix )`: sub field của layout (chỉ nạp ở `acf/init`) |

Template-part nhận `$args`: `data` (dữ liệu ACF của section, ảnh = ID), `index`, `is_first` (section đầu → ảnh là LCP: `fetchpriority="high"`, không lazy), `id` (id của `<section>`; id tiêu đề = `<id>-title`), `prev` (layout liền trước), `span` (chỉ layout dạng ô: độ rộng, phần mười hàng).

Layout dạng ô in 1 `<section class="h-full …">` có nền/viền riêng, KHÔNG tự bọc `.site-container`: `rhm_landing_render()` gom các ô liền nhau vào hàng `.site-container grid lg:grid-cols-10`. Ô không in gì (thiếu dữ liệu) bị bỏ qua.

Tiêu đề section: `rhm_landing_heading( $id, $title, $eyebrow, $description, $style )`, `$style` = `center` (canh giữa, 2 vạch) | `bar` (vạch dọc trái) | `box` (vạch dọc, cỡ nhỏ trong ô) | `plain`.

### Thêm layout

1. Thêm key + nhãn (+ `boxed` nếu là dạng ô) vào `rhm_landing_layouts()`.
2. `inc/landing/layouts/<key>.php` với `rhm_landing_layout_<key>_fields( string $prefix ): array` (gạch ngang trong key → gạch dưới trong tên hàm) (key field tạo bằng `rhm_landing_field( $prefix, … )`; repeater dùng prefix `$prefix . '_<tên repeater>'`).
3. `template-parts/landing/<key>.php`: thiếu dữ liệu → `return`; tiêu đề H2 qua `rhm_landing_heading()`; ảnh qua `rhm_landing_picture()` với `sizes` tính theo bố cục (ghi comment); JS module chỉ enqueue khi cần.
4. Thêm key vào `layouts` của các chủ đề dùng nó. Bundle CSS của chủ đề đã `@source` cả thư mục `template-parts/landing/`; nếu layout gọi template-part ở chỗ khác thì thêm `@source` file đó.

### Thêm chủ đề

1. Thêm phần tử vào `rhm_landing_templates()`: `'page-templates/tpl-<x>.php' => [ 'slug', 'label', 'bundle', 'layouts' ]`. `slug` không được đổi sau khi đã có dữ liệu (nó là key ACF).
2. `page-templates/tpl-<x>.php`: header `Template Name`, rồi `get_header()` → vòng lặp `the_post()` + `rhm_landing_render( get_the_ID() )` → `get_footer()` (chép từ `tpl-gioi-thieu.php`).
3. `src/css/pages/<bundle>.css`: chép `page-about.css`, giữ `@source` các file cần → `npm run build`. `rhm_page_style_bundle()` tự chọn bundle theo cấu hình, không cần sửa `inc/enqueue.php`.

### Hiệu năng

- Ngân sách CSS inline ≤ 20 kB gzip: `base + header + footer + page-about` ≈ 21,1 kB (2026-11-11, page-about 8,8 kB sau khi bỏ layout Chứng nhận) — VƯỢT ~1 kB vì chủ đề Giới thiệu giữ cả các section slider (Swiper CSS + CSS bác sĩ trang chủ). Muốn về dưới ngân sách: bỏ `gallery`, `doctors` khỏi `layouts` của chủ đề và bỏ `swiper/css`, `_carousel.css`, `_home-doctors.css`, `@source` sections/doctors khỏi `page-about.css`.
- Ảnh banner (LCP) phải được loại trừ khỏi Lazyload của Swift Performance AI (giống ảnh hero trang chủ). Nếu không, Swift ghi đè thành `loading="lazy" fetchpriority="low"`.
- Kiểm tra: đúng 1 `<h1>`, đúng 1 `img[fetchpriority=high]`, CLS khi cuộn = 0, đo PageSpeed theo `.claude/rules/pagespeed-measure.md`.
