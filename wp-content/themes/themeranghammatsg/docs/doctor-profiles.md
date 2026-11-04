# Hồ sơ bác sĩ

Trong quản trị, chọn **Bác sĩ → Thêm bác sĩ**. Danh sách công khai ở `/bac-si/`, hồ sơ ở `/bac-si/<slug>/`. CPT nằm trong theme và không có taxonomy riêng.

- **Tiêu đề:** tên bác sĩ, dùng làm H1.
- **Nội dung:** phần giới thiệu, soạn bằng Classic Editor.
- **Tóm tắt:** mô tả ngắn trên trang danh sách.
- **Ảnh đại diện:** chân dung, dùng lại ở đầu phần chứng chỉ và card bác sĩ khác. Nên dùng ảnh dọc tỷ lệ 4:5.

Nhóm **Thông tin hồ sơ bác sĩ** của ACF PRO có các tab:

1. **Banner & giới thiệu:** banner desktop 2048×768, mobile 828×1000, chức danh và ảnh hoạt động chuyên môn. Không có banner desktop thì bỏ section banner và ưu tiên tải chân dung đầu trang. Thiếu ảnh mobile thì dùng ảnh desktop.
2. **Chuyên môn & văn bằng:** mỗi dòng trong Chuyên môn/Học vấn/Kinh nghiệm là một mục. Thư viện văn bằng đào tạo nằm sau ba danh sách.
3. **Chứng chỉ & thành tựu:** thư viện riêng, không trộn với văn bằng.
4. **Video về bác sĩ:** tối đa hai video, mỗi video cần URL YouTube, tiêu đề và ảnh bìa trong thư viện media.
5. **Ca điều trị:** danh sách ca và tối đa hai video ca điều trị. Mỗi ca cần tên khách hàng và đủ ba ảnh; địa phương, phương án và loại điều trị có thể bỏ trống. Kéo thả để đổi thứ tự các mục.

Ảnh trong thư viện có thể kéo thả để sắp xếp. Dùng ảnh đủ độ phân giải; alt lấy từ Media, fallback về tiêu đề ảnh. Section/dòng thiếu dữ liệu hợp lệ được bỏ qua. Slider không tự chạy; video chỉ kết nối YouTube sau khi bấm phát.

Form sau nhóm ca điều trị lấy ID ở **Giao diện → Tùy biến → Thông tin phòng khám**, hiện là 3. Các nút đặt lịch dùng helper của theme trên hồ sơ dẫn về `#dat-lich`. Form dùng chung kiểu hiển thị và cơ chế chạm mới tải với trang Liên hệ.

Cuối hồ sơ tự lấy tối đa sáu bác sĩ đã xuất bản khác và ba bài viết mới nhất. Danh sách bác sĩ dùng chung component `template-parts/components/featured-grid.php` với danh mục: trang đầu 1 mục nổi bật + 12 card, trang sau 12 card; không có bộ lọc chuyên mục.

Mã chính: `inc/doctors.php`, `inc/doctor-fields.php`, `single-bac-si.php`, `archive-bac-si.php`, `template-parts/doctor/`, `src/css/pages/single-doctor.css`. Form dùng partial `src/css/components/_booking-form.css`. Rewrite được cập nhật một lần theo option `rhm_doctor_rewrite_version`; không cần lưu permalink mỗi lần sửa hồ sơ.

Sau khi sửa nguồn, chạy `npm run build`. Nếu đang chạy `npm run dev`, cần khởi động lại watcher khi thêm entry mới vì danh sách entry được lấy lúc Vite khởi động. Không sửa trực tiếp `dist/`.
