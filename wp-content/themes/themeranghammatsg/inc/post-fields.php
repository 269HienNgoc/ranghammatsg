<?php
/** Nhóm nhập liệu ACF PRO cho bài viết: bác sĩ tham vấn, câu hỏi thường gặp, tài liệu tham khảo (chỉ nạp ở acf/init). */
defined( 'ABSPATH' ) || exit;

function rhm_register_post_fields(): void {
	$field = static fn( string $name, string $label, string $type, array $extra = array() ): array => array_merge(
		array( 'key' => 'field_rhm_post_' . $name, 'name' => 'rhm_post_' . $name, 'label' => $label, 'type' => $type ),
		$extra
	);
	// Field con: key duy nhất theo nhóm, name ngắn (question, answer…).
	$sub = static fn( string $group, string $name, string $label, string $type, array $extra = array() ): array => array_merge(
		$field( $group . '_' . $name, $label, $type, $extra ),
		array( 'name' => $name )
	);

	acf_add_local_field_group(
		array(
			'key'      => 'group_rhm_post',
			'title'    => __( 'Thông tin chuyên môn bài viết', 'themeranghammatsg' ),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'post' ) ) ),
			'position' => 'normal',
			'fields'   => array(
				$field( 'tab_reviewer', __( 'Bác sĩ tham vấn', 'themeranghammatsg' ), 'tab' ),
				$field(
					'reviewer',
					__( 'Bác sĩ tham vấn chuyên môn', 'themeranghammatsg' ),
					'post_object',
					array(
						'post_type'     => array( 'bac-si' ),
						'post_status'   => array( 'publish' ),
						'return_format' => 'id',
						'allow_null'    => 1,
						'ui'            => 1,
						'instructions'  => __( 'Bác sĩ đã đọc và duyệt nội dung chuyên môn. Hiện dưới tiêu đề bài (link tới hồ sơ) và khai báo reviewedBy trong schema. Để trống nếu chưa có bác sĩ duyệt.', 'themeranghammatsg' ),
					)
				),
				$field( 'tab_faq', __( 'Câu hỏi thường gặp', 'themeranghammatsg' ), 'tab' ),
				$field(
					'faq',
					__( 'Câu hỏi thường gặp', 'themeranghammatsg' ),
					'repeater',
					array(
						'layout'       => 'block',
						'collapsed'    => 'field_rhm_post_faq_question',
						'button_label' => __( 'Thêm câu hỏi', 'themeranghammatsg' ),
						'instructions' => __( 'Hiện thành mục "Câu hỏi thường gặp" cuối bài và khai báo FAQPage. Nhập ở đây thì KHÔNG viết lại các câu này trong nội dung bài (sẽ hiện 2 lần). Câu thiếu câu hỏi hoặc trả lời sẽ bị bỏ qua. Trả lời: xuống dòng 2 lần = đoạn mới; cho phép link, in đậm, nghiêng, danh sách.', 'themeranghammatsg' ),
						'sub_fields'   => array(
							$sub( 'faq', 'question', __( 'Câu hỏi', 'themeranghammatsg' ), 'text' ),
							$sub( 'faq', 'answer', __( 'Trả lời', 'themeranghammatsg' ), 'textarea', array( 'rows' => 4, 'new_lines' => '' ) ),
						),
					)
				),
				$field( 'tab_references', __( 'Tài liệu tham khảo', 'themeranghammatsg' ), 'tab' ),
				$field(
					'references',
					__( 'Tài liệu tham khảo', 'themeranghammatsg' ),
					'repeater',
					array(
						'layout'       => 'table',
						'button_label' => __( 'Thêm tài liệu', 'themeranghammatsg' ),
						'instructions' => __( 'Nguồn y khoa đã dùng khi viết bài. Hiện thành danh sách cuối bài và khai báo citation. Link có thể để trống.', 'themeranghammatsg' ),
						'sub_fields'   => array(
							$sub( 'references', 'title', __( 'Tên tài liệu', 'themeranghammatsg' ), 'text' ),
							$sub( 'references', 'url', __( 'Link', 'themeranghammatsg' ), 'url' ),
						),
					)
				),
			),
		)
	);
}
