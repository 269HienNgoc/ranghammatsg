<?php
/** Nhóm nhập liệu ACF PRO cho từng hồ sơ bác sĩ. */
defined( 'ABSPATH' ) || exit;

function rhm_register_doctor_fields(): void {
	$field = static fn( string $name, string $label, string $type, array $extra = array() ): array => array_merge(
		array( 'key' => 'field_rhm_doctor_' . $name, 'name' => 'rhm_doctor_' . $name, 'label' => $label, 'type' => $type ),
		$extra
	);
	$image = static fn( string $name, string $label, array $extra = array() ): array => $field( $name, $label, 'image', $extra + array( 'return_format' => 'id', 'preview_size' => 'medium', 'mime_types' => 'jpg,jpeg,png,webp,avif', 'library' => 'all' ) );
	$gallery = static fn( string $name, string $label ): array => $field( $name, $label, 'gallery', array( 'return_format' => 'id', 'preview_size' => 'thumbnail', 'mime_types' => 'jpg,jpeg,png,webp,avif', 'instructions' => __( 'Kéo thả để sắp xếp; ảnh thiếu sẽ được bỏ qua khi hiển thị.', 'themeranghammatsg' ) ) );
	$videos = static function ( string $name, string $label ) use ( $field, $image ): array {
		return $field(
			$name,
			$label,
			'repeater',
			array(
				'max'          => 2,
				'layout'       => 'block',
				'button_label' => __( 'Thêm video', 'themeranghammatsg' ),
				'instructions' => __( 'Tối đa hai video YouTube; mỗi video cần URL, tiêu đề và ảnh bìa tự host.', 'themeranghammatsg' ),
				'sub_fields'   => array(
					array_merge( $field( $name . '_url', __( 'URL YouTube', 'themeranghammatsg' ), 'url' ), array( 'name' => 'url' ) ),
					array_merge( $field( $name . '_title', __( 'Tiêu đề video', 'themeranghammatsg' ), 'text' ), array( 'name' => 'title' ) ),
					array_merge( $image( $name . '_image', __( 'Ảnh bìa video', 'themeranghammatsg' ) ), array( 'name' => 'image' ) ),
				),
			)
		);
	};
	$case_fields = array();
	foreach ( array( 'name' => 'Tên khách hàng', 'location' => 'Địa phương', 'method' => 'Phương án', 'treatment' => 'Loại điều trị' ) as $name => $label ) {
		$case_fields[] = array_merge( $field( 'case_' . $name, $label, 'text' ), array( 'name' => $name ) );
	}
	foreach ( array( 'portrait' => 'Ảnh khách hàng', 'before' => 'Ảnh trước điều trị', 'after' => 'Ảnh sau điều trị' ) as $name => $label ) {
		$case_fields[] = array_merge( $image( 'case_' . $name, $label ), array( 'name' => $name ) );
	}
	$fields = array(
		$field( 'tab_intro', __( 'Banner & giới thiệu', 'themeranghammatsg' ), 'tab' ),
		$image( 'banner_desktop', __( 'Banner desktop — 2048 × 768', 'themeranghammatsg' ), array( 'instructions' => __( 'Ảnh tĩnh, giữ tỷ lệ gốc. Để trống để bỏ banner.', 'themeranghammatsg' ) ) ),
		$image( 'banner_mobile', __( 'Banner mobile — 828 × 1000', 'themeranghammatsg' ), array( 'instructions' => __( 'Dùng dưới 768px; để trống sẽ dùng banner desktop.', 'themeranghammatsg' ) ) ),
		$field( 'position', __( 'Chức danh', 'themeranghammatsg' ), 'text', array( 'instructions' => __( 'Tên, giới thiệu, mô tả ngắn và chân dung dùng các ô Tiêu đề, Nội dung, Tóm tắt và Ảnh đại diện của hồ sơ.', 'themeranghammatsg' ) ) ),
		$field( 'box_label', __( 'Nhãn box bài viết', 'themeranghammatsg' ), 'text', array( 'placeholder' => rhm_doctor_box_default_label(), 'instructions' => __( 'Dòng trên tên bác sĩ trong box [bac_si_tham_van] ở bài viết, vd "Cố vấn và đào tạo chuyên môn:". Để trống dùng chữ mặc định.', 'themeranghammatsg' ) ) ),
		$field( 'highlights', __( 'Điểm nổi bật (box bài viết)', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '', 'instructions' => sprintf( /* translators: %d: max lines */ __( 'Mỗi dòng 1 ý ngắn (nên ≤ 60 ký tự), hiện tối đa %d dòng có dấu tích. Để trống: tự lấy chức danh + dòng đầu Học vấn + dòng đầu Kinh nghiệm.', 'themeranghammatsg' ), RHM_DOCTOR_BOX_MAX ) ) ),
		$gallery( 'gallery', __( 'Ảnh hoạt động chuyên môn', 'themeranghammatsg' ) ),
		$field( 'tab_qualifications', __( 'Chuyên môn & văn bằng', 'themeranghammatsg' ), 'tab' ),
	);
	foreach ( array( 'specialties' => 'Chuyên môn', 'education' => 'Học vấn', 'experience' => 'Kinh nghiệm' ) as $name => $label ) {
		$fields[] = $field( $name, $label, 'textarea', array( 'rows' => 5, 'new_lines' => '', 'instructions' => __( 'Mỗi dòng là một mục trong danh sách.', 'themeranghammatsg' ) ) );
	}
	$fields = array_merge(
		$fields,
		array(
			$gallery( 'diplomas', __( 'Văn bằng đào tạo', 'themeranghammatsg' ) ),
			$field( 'tab_certificates', __( 'Chứng chỉ & thành tựu', 'themeranghammatsg' ), 'tab' ),
			$gallery( 'certificates', __( 'Chứng chỉ chuyên sâu, chứng nhận và thành tựu', 'themeranghammatsg' ) ),
			$field( 'tab_videos', __( 'Video về bác sĩ', 'themeranghammatsg' ), 'tab' ),
			$videos( 'videos', __( 'Video về bác sĩ', 'themeranghammatsg' ) ),
			$field( 'tab_cases', __( 'Ca điều trị', 'themeranghammatsg' ), 'tab' ),
			$field( 'cases', __( 'Những ca điều trị', 'themeranghammatsg' ), 'repeater', array( 'layout' => 'block', 'collapsed' => 'field_rhm_doctor_case_name', 'button_label' => __( 'Thêm ca điều trị', 'themeranghammatsg' ), 'instructions' => __( 'Mỗi ca cần tên khách hàng và đủ ba ảnh. Các dòng nhập dở không hiển thị.', 'themeranghammatsg' ), 'sub_fields' => $case_fields ) ),
			$videos( 'case_videos', __( 'Video ca điều trị', 'themeranghammatsg' ) ),
			// Section "Đội ngũ bác sĩ" trang chủ (inc/home-doctors.php, template-parts/sections/doctors.php).
			$field( 'tab_home', __( 'Trang chủ', 'themeranghammatsg' ), 'tab' ),
			$field( 'home', __( 'Hiện ở section Đội ngũ bác sĩ (trang chủ)', 'themeranghammatsg' ), 'true_false', array( 'ui' => 1, 'instructions' => __( 'Thứ tự hiển thị: ô "Thứ tự" ở khung Thuộc tính bên phải (số nhỏ đứng trước). Chưa bác sĩ nào được chọn thì trang chủ hiện các hồ sơ mới nhất. Cần có Ảnh đại diện.', 'themeranghammatsg' ) ) ),
			$field( 'degree', __( 'Học hàm / học vị', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( 'Bác sĩ CKI', 'themeranghammatsg' ), 'instructions' => __( 'Dòng nhỏ phía trên tên bác sĩ.', 'themeranghammatsg' ) ) ),
			$field( 'years', __( 'Kinh nghiệm lâm sàng', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( '15+ năm', 'themeranghammatsg' ) ) ),
			$field( 'case_count', __( 'Hồ sơ điều trị', 'themeranghammatsg' ), 'text', array( 'placeholder' => __( '6.000+ ca', 'themeranghammatsg' ) ) ),
			$field( 'credentials', __( 'Chứng chỉ chuyên môn', 'themeranghammatsg' ), 'textarea', array( 'rows' => 5, 'new_lines' => '', 'instructions' => sprintf( /* translators: %d: max lines */ __( 'Mỗi dòng 1 mục, hiện tối đa %d dòng. Để trống: dùng danh sách Học vấn.', 'themeranghammatsg' ), 5 ) ) ),
		)
	);
	acf_add_local_field_group(
		array(
			'key'      => 'group_rhm_doctor',
			'title'    => __( 'Thông tin hồ sơ bác sĩ', 'themeranghammatsg' ),
			'fields'   => $fields,
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'bac-si' ) ) ),
			'position' => 'normal',
		)
	);
}
