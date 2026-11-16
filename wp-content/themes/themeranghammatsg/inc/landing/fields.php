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

/** Ô soạn thảo nội dung (như rich + danh sách), không upload ảnh. */
function rhm_landing_wysiwyg_field( string $prefix, string $name, string $label, array $extra = array() ): array {
	return rhm_landing_field(
		$prefix,
		$name,
		$label,
		'wysiwyg',
		$extra + array(
			'tabs'         => 'visual',
			'toolbar'      => 'rhm_content',
			'media_upload' => 0,
			'delay'        => 1,
		)
	);
}

/**
 * Ô chữ có định dạng cho mô tả / nội dung ngắn (thay textarea): đậm, nghiêng, gạch chân, màu, link, canh lề, xóa định dạng.
 * In ra bằng rhm_landing_rich() (inc/landing/landing.php). delay: TinyMCE chỉ khởi tạo khi bấm vào ô → admin nhẹ dù nhiều ô.
 */
function rhm_landing_rich_field( string $prefix, string $name, string $label, array $extra = array() ): array {
	$note = __( 'Enter = đoạn mới, Shift + Enter = xuống dòng. Màu chữ nhạt (vàng, xám nhạt…) trên nền trắng làm giảm điểm Accessibility của PageSpeed.', 'themeranghammatsg' );
	if ( isset( $extra['instructions'] ) ) {
		$extra['instructions'] .= ' ' . $note;
	}
	return rhm_landing_field(
		$prefix,
		$name,
		$label,
		'wysiwyg',
		$extra + array(
			'instructions' => $note,
			'tabs'         => 'visual',
			'toolbar'      => 'rhm_text',
			'media_upload' => 0,
			'delay'        => 1,
		)
	);
}

/**
 * Toolbar ACF WYSIWYG của theme. Giá trị 'toolbar' của field = tên đã sanitize_title, '-' → '_' (ACF):
 * 'RHM Text' → rhm_text (mô tả ngắn), 'RHM Content' → rhm_content (thêm danh sách: intro, FAQ). Full: thêm gạch chân.
 */
function rhm_landing_wysiwyg_toolbars( array $toolbars ): array {
	$text = array( 'bold', 'italic', 'underline', 'forecolor', 'link', 'unlink', 'alignleft', 'aligncenter', 'alignright', 'alignjustify', 'removeformat' );

	$toolbars['RHM Text']    = array( 1 => $text );
	$toolbars['RHM Content'] = array( 1 => array_merge( array_slice( $text, 0, 4 ), array( 'bullist', 'numlist' ), array_slice( $text, 4 ) ) );

	if ( isset( $toolbars['Full'][1] ) && ! in_array( 'underline', $toolbars['Full'][1], true ) ) {
		$pos = array_search( 'italic', $toolbars['Full'][1], true );
		array_splice( $toolbars['Full'][1], false === $pos ? 0 : $pos + 1, 0, 'underline' );
	}
	return $toolbars;
}
add_filter( 'acf/fields/wysiwyg/toolbars', 'rhm_landing_wysiwyg_toolbars' );

/**
 * Bảng màu chữ dùng chung (hex không '#' => tên): màu thương hiệu (src/css/theme.css) + vài màu nhấn. 2 màu cuối dành cho nền tối.
 * Dùng cho nút "Màu chữ" của TinyMCE và ô "Màu tiêu đề" (ACF color picker) của landing.
 *
 * @return array<string, string>
 */
function rhm_brand_text_colors(): array {
	return array(
		'0b7a55' => __( 'Xanh thương hiệu', 'themeranghammatsg' ),
		'075c40' => __( 'Xanh đậm', 'themeranghammatsg' ),
		'003442' => __( 'Xanh than', 'themeranghammatsg' ),
		'1d5c5a' => __( 'Xanh ngọc', 'themeranghammatsg' ),
		'10212d' => __( 'Đen', 'themeranghammatsg' ),
		'1f2d36' => __( 'Chữ nội dung', 'themeranghammatsg' ),
		'696b73' => __( 'Xám', 'themeranghammatsg' ),
		'c2410c' => __( 'Cam', 'themeranghammatsg' ),
		'c62828' => __( 'Đỏ', 'themeranghammatsg' ),
		'0866ff' => __( 'Xanh dương', 'themeranghammatsg' ),
		'ffffff' => __( 'Trắng (nền tối)', 'themeranghammatsg' ),
		'd3d867' => __( 'Vàng (nền tối)', 'themeranghammatsg' ),
	);
}

/**
 * Bảng màu của nút "Màu chữ" TinyMCE (mọi trình soạn thảo). "Tùy chỉnh…" (mã màu bất kỳ) có sẵn nhờ plugin colorpicker của WP.
 */
function rhm_tinymce_text_colors( array $init ): array {
	$map = array();
	foreach ( rhm_brand_text_colors() as $hex => $name ) {
		$map[] = $hex;
		$map[] = $name;
	}
	$init['textcolor_map']  = wp_json_encode( $map );
	$init['textcolor_cols'] = '6';
	$init['textcolor_rows'] = '2';
	return $init;
}
add_filter( 'tiny_mce_before_init', 'rhm_tinymce_text_colors' );

/** Ô chọn màu ACF (Iris): hàng màu nhanh = bảng màu thương hiệu, vẫn chọn được mã màu bất kỳ. */
function rhm_acf_color_palette(): void {
	$palette = array_map( static fn( $hex ): string => '#' . $hex, array_keys( rhm_brand_text_colors() ) );
	wp_add_inline_script(
		'acf-input',
		'acf.add_filter("color_picker_args",function(a){a.palettes=' . wp_json_encode( $palette ) . ';return a;});'
	);
}
add_action( 'acf/input/admin_enqueue_scripts', 'rhm_acf_color_palette' );

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

/**
 * Chèn ô màu NGAY SAU field 'title' cấp đầu của layout, cùng hàng với tiêu đề (mỗi ô rộng 20%):
 * - "Màu tiêu đề" (title_color) — mọi layout có 'title'.
 * - "Màu tiêu đề con (H3)" (h3_color) — thêm khi $h3 (rhm_landing_layouts()['h3']): 1 màu cho mọi H3 của section.
 * Tiêu đề đứng 1 mình → tiêu đề = 100 − ô màu. Tiêu đề chung hàng với field kế (vd tiêu đề 60 + dòng nhỏ 40) → tiêu đề − 20,
 * field kế thu phần còn thiếu (tối thiểu 20) để cả hàng vẫn = 100. Layout không có 'title' → giữ nguyên.
 * Đọc bằng rhm_landing_color( $data, 'title_color' | 'h3_color' ).
 */
function rhm_landing_add_title_color( string $prefix, array $fields, bool $h3 = false ): array {
	$fields = array_values( $fields );
	$index  = array_search( 'title', array_column( $fields, 'name' ), true );
	if ( false === $index ) {
		return $fields;
	}

	$color = static fn( string $name, string $label ): array => rhm_landing_field(
		$prefix,
		$name,
		$label,
		'color_picker',
		array(
			'wrapper'        => array( 'width' => 20 ),
			'return_format'  => 'string',
			'enable_opacity' => 0,
			'instructions'   => __( 'Trống: màu mặc định. Màu nhạt trên nền sáng làm giảm điểm Accessibility.', 'themeranghammatsg' ),
		)
	);
	$colors = array( $color( 'title_color', __( 'Màu tiêu đề', 'themeranghammatsg' ) ) );
	if ( $h3 ) {
		$colors[] = $color( 'h3_color', __( 'Màu tiêu đề con (H3)', 'themeranghammatsg' ) );
	}
	$used = 20 * count( $colors );

	$w = static fn( int $i ): int => isset( $fields[ $i ] ) ? ( (int) ( $fields[ $i ]['wrapper']['width'] ?? 0 ) ?: 100 ) : 100;

	// Phần hàng đã có trước tiêu đề (ACF xếp field nối nhau, tràn 100 thì xuống hàng): > 0 = tiêu đề chung hàng với field trước.
	$before = 0;
	for ( $i = 0; $i < $index; $i++ ) {
		$before = $before + $w( $i ) > 100 ? $w( $i ) : $before + $w( $i );
	}
	$before = $before + $w( $index ) > 100 ? 0 : $before;
	$width  = $w( $index );

	if ( $before > 0 ) {
		// Field trước (vd dòng nhỏ 40) + tiêu đề → field trước − 20, tiêu đề lấp phần còn lại cạnh ô màu.
		$fields[ $index - 1 ]['wrapper']['width'] = max( 20, $w( $index - 1 ) - 20 );
		$fields[ $index ]['wrapper']['width']     = max( 20, 100 - ( $before - $w( $index - 1 ) + $fields[ $index - 1 ]['wrapper']['width'] ) - $used );
	} elseif ( $width + $w( $index + 1 ) <= 100 && isset( $fields[ $index + 1 ] ) ) {
		$fields[ $index ]['wrapper']['width']     = max( 20, $width - 20 );
		$fields[ $index + 1 ]['wrapper']['width'] = max( 20, 100 - $fields[ $index ]['wrapper']['width'] - $used );
	} else {
		$fields[ $index ]['wrapper']['width'] = 100 - $used;
	}
	array_splice( $fields, $index + 1, 0, $colors );
	return $fields;
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
			$fields                                  = rhm_landing_add_title_color( $prefix, $callback( $prefix ), ! empty( $labels[ $layout ]['h3'] ) );
			$layouts[ 'layout_rhm_landing_' . $prefix ] = array(
				'key'        => 'layout_rhm_landing_' . $prefix,
				'name'       => $layout,
				'label'      => $labels[ $layout ]['label'],
				'display'    => 'block',
				'sub_fields' => array_merge(
					$labels[ $layout ]['boxed'] ? array( rhm_landing_width_field( $prefix ) ) : array(),
					$fields,
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
