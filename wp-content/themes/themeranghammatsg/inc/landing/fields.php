<?php
/**
 * Field ACF của landing page: mỗi chủ đề (rhm_landing_templates) 1 field group, chỉ hiện khi trang chọn đúng Page Template.
 * Field group chứa 1 Flexible Content (RHM_LANDING_FIELD) gồm các layout chủ đề cho phép; field của từng layout
 * nằm ở inc/landing/layouts/<layout>.php (hàm rhm_landing_layout_<layout>_fields( $prefix )).
 *
 * Key: field_rhm_landing_<slug>_<layout>_<field> → không trùng giữa các chủ đề. name giống nhau → render dùng chung.
 * Đổi template của trang: dữ liệu cũ vẫn nằm trong meta, layout chủ đề mới không cho phép thì không hiển thị.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * 1 field (hoặc sub field) ACF. $prefix = <slug>_<layout>[_<repeater>].
 */
function rhm_landing_field( string $prefix, string $name, string $label, string $type, array $extra = array() ): array {
	return array_merge(
		array(
			'key'   => 'field_rhm_landing_' . $prefix . '_' . $name,
			'name'  => $name,
			'label' => $label,
			'type'  => $type,
		),
		$extra
	);
}

/** Field ảnh: luôn trả ID (theme tự in srcset/sizes). */
function rhm_landing_image_field( string $prefix, string $name, string $label, array $extra = array() ): array {
	return rhm_landing_field(
		$prefix,
		$name,
		$label,
		'image',
		$extra + array(
			'return_format' => 'id',
			'preview_size'  => 'medium',
			'mime_types'    => 'jpg,jpeg,png,webp,avif',
			'library'       => 'all',
		)
	);
}

/** Ô soạn thảo rút gọn (đậm, nghiêng, link, danh sách), không upload ảnh. */
function rhm_landing_wysiwyg_field( string $prefix, string $name, string $label, array $extra = array() ): array {
	return rhm_landing_field(
		$prefix,
		$name,
		$label,
		'wysiwyg',
		$extra + array(
			'tabs'         => 'visual',
			'toolbar'      => 'basic',
			'media_upload' => 0,
			'delay'        => 1,
		)
	);
}

/** Cặp "Chữ nút + Link nút" (2 cột). */
function rhm_landing_button_fields( string $prefix, string $label = '' ): array {
	return array(
		rhm_landing_field( $prefix, 'button_text', '' !== $label ? $label : __( 'Chữ nút', 'themeranghammatsg' ), 'text', array( 'wrapper' => array( 'width' => 50 ) ) ),
		rhm_landing_field(
			$prefix,
			'button_link',
			__( 'Link nút', 'themeranghammatsg' ),
			'text',
			array(
				'wrapper'      => array( 'width' => 50 ),
				'placeholder'  => 'https://… hoặc #neo-section',
				'instructions' => __( 'Thiếu chữ hoặc link thì không hiện nút.', 'themeranghammatsg' ),
			)
		),
	);
}

/** Ô Độ rộng của layout dạng ô (đầu layout): các ô liền nhau xếp chung 1 hàng nếu tổng ≤ toàn trang. */
function rhm_landing_width_field( string $prefix ): array {
	return rhm_landing_field(
		$prefix,
		'width',
		__( 'Độ rộng ô (desktop)', 'themeranghammatsg' ),
		'button_group',
		array(
			// Khóa = RHM_LANDING_SPANS (inc/landing/landing.php).
			'choices'       => array(
				'10' => __( 'Toàn trang', 'themeranghammatsg' ),
				'5'  => __( '1/2 trang', 'themeranghammatsg' ),
				'4'  => __( '2/5 trang (hẹp)', 'themeranghammatsg' ),
				'6'  => __( '3/5 trang (rộng)', 'themeranghammatsg' ),
			),
			'default_value' => '5',
			'instructions'  => __( 'Các ô liền nhau tự xếp cạnh nhau khi vừa 1 hàng (vd 1/2 + 1/2, 2/5 + 3/5). Mobile: xếp dọc.', 'themeranghammatsg' ),
		)
	);
}

/** Icon nét (bộ assets/icons/landing/): ô chọn có tìm kiếm. */
function rhm_landing_icon_field( string $prefix, string $name = 'icon', array $extra = array() ): array {
	return rhm_landing_field(
		$prefix,
		$name,
		__( 'Icon', 'themeranghammatsg' ),
		'select',
		$extra + array(
			'choices'       => rhm_icon_list( 'landing' ),
			'allow_null'    => 1,
			'ui'            => 1,
			'return_format' => 'value',
			'instructions'  => __( 'Thêm icon: chép file .svg vào assets/icons/landing/ của theme.', 'themeranghammatsg' ),
		)
	);
}

/** Field chung cuối mọi layout: công tắc Hiện/Ẩn + neo (id) của section. */
function rhm_landing_common_fields( string $prefix ): array {
	return array(
		rhm_landing_field(
			$prefix,
			'show',
			__( 'Hiển thị', 'themeranghammatsg' ),
			'true_false',
			array(
				'ui'            => 1,
				'ui_on_text'    => __( 'Hiện', 'themeranghammatsg' ),
				'ui_off_text'   => __( 'Ẩn', 'themeranghammatsg' ),
				'default_value' => 1,
				'wrapper'       => array( 'width' => 30 ),
				'instructions'  => __( 'Tắt: tạm ẩn section, dữ liệu vẫn giữ.', 'themeranghammatsg' ),
			)
		),
		rhm_landing_field(
			$prefix,
			'anchor',
			__( 'Neo (id) của section', 'themeranghammatsg' ),
			'text',
			array(
				'wrapper'      => array( 'width' => 70 ),
				'placeholder'  => 'vd: doi-ngu-bac-si',
				'instructions' => __( 'Tùy chọn. Link "#doi-ngu-bac-si" sẽ cuộn tới section này. Chỉ dùng chữ thường không dấu, số, gạch ngang.', 'themeranghammatsg' ),
			)
		),
	);
}

function rhm_landing_register_fields(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$labels = rhm_landing_layouts();

	foreach ( rhm_landing_templates() as $file => $template ) {
		$slug    = $template['slug'];
		$layouts = array();

		foreach ( $template['layouts'] as $layout ) {
			$path = __DIR__ . '/layouts/' . $layout . '.php';
			if ( ! isset( $labels[ $layout ] ) || ! is_file( $path ) ) {
				continue;
			}
			require_once $path;

			$callback = 'rhm_landing_layout_' . str_replace( '-', '_', $layout ) . '_fields';
			if ( ! function_exists( $callback ) ) {
				continue;
			}

			$prefix                                  = $slug . '_' . $layout;
			$layouts[ 'layout_rhm_landing_' . $prefix ] = array(
				'key'        => 'layout_rhm_landing_' . $prefix,
				'name'       => $layout,
				'label'      => $labels[ $layout ]['label'],
				'display'    => 'block',
				'sub_fields' => array_merge(
					$labels[ $layout ]['boxed'] ? array( rhm_landing_width_field( $prefix ) ) : array(),
					$callback( $prefix ),
					rhm_landing_common_fields( $prefix )
				),
			);
		}

		if ( ! $layouts ) {
			continue;
		}

		acf_add_local_field_group(
			array(
				'key'            => 'group_rhm_landing_' . $slug,
				/* translators: %s: tên chủ đề landing */
				'title'          => sprintf( __( 'Landing page – %s', 'themeranghammatsg' ), $template['label'] ),
				'fields'         => array(
					array(
						'key'          => 'field_rhm_landing_' . $slug . '_sections',
						'name'         => RHM_LANDING_FIELD,
						'label'        => __( 'Các section của trang', 'themeranghammatsg' ),
						'type'         => 'flexible_content',
						'button_label' => __( 'Thêm section', 'themeranghammatsg' ),
						'layouts'      => $layouts,
						'instructions' => __( 'Section hiện theo đúng thứ tự bên dưới (kéo để sắp xếp). Section đầu là "Banner đầu trang" thì trang dùng tiêu đề của banner làm H1. Chưa thêm section nào: trang hiện tiêu đề + nội dung trình soạn thảo. Hướng dẫn: docs/landing-pages.md của theme.', 'themeranghammatsg' ),
					),
				),
				'location'       => array(
					array(
						array(
							'param'    => 'page_template',
							'operator' => '==',
							'value'    => $file,
						),
					),
				),
				'position'       => 'acf_after_title',
				'hide_on_screen' => array( 'discussion', 'comments', 'send-trackbacks' ),
			)
		);
	}
}
add_action( 'acf/init', 'rhm_landing_register_fields' );
