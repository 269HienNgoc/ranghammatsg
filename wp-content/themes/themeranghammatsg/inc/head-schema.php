<?php
/**
 * Schema JSON-LD tự viết, in vào <head> mọi trang (Tùy biến → "Schema tùy chỉnh (head)").
 *
 * Danh sách nhiều schema (nút "+ Thêm mới"): mỗi mục có tên + nội dung + bật/tắt → in thành 1 thẻ riêng:
 *   <!-- Schema: <tên> -->
 *   <script type="application/ld+json" class="rhm-head-schema">{…}</script>
 * đặt ngay dưới "<!-- / Yoast SEO plugin. -->". Độc lập với inc/schema.php (bổ sung graph Yoast).
 * JSON được decode rồi encode lại (JSON_HEX_TAG) trước khi in → không thể chèn HTML/thoát khỏi thẻ <script>.
 * JSON lỗi bị Customizer chặn khi lưu (validate_callback) → schema hỏng không lên site.
 *
 * Lưu: theme mod rhm_head_schemas = chuỗi JSON [{ name, code, enabled }]. Bản cũ (≤ 1.4.22) dùng 1 ô rhm_head_schema:
 * chưa có rhm_head_schemas thì tự tách ô cũ thành các mục "Schema 1, 2…" (rhm_get_head_schemas()).
 * Control: inc/class-rhm-customize-schema-list-control.php + assets/admin/customize-head-schema.{js,css}.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Tách chuỗi nhập thành các khối JSON (chưa decode).
 * Có thẻ <script …>…</script> → mỗi thẻ 1 khối; không có → cả chuỗi là 1 khối.
 *
 * @return string[]
 */
function rhm_head_schema_raw_blocks( string $raw ): array {
	$raw = trim( $raw );
	if ( '' === $raw ) {
		return array();
	}

	if ( preg_match_all( '~<script\b[^>]*>(.*?)</script\s*>~is', $raw, $m ) ) {
		return array_values( array_filter( array_map( 'trim', $m[1] ), 'strlen' ) );
	}

	return array( $raw );
}

/**
 * Các khối schema đã decode (bỏ khối lỗi). Rỗng khi ô trống.
 *
 * @return array<int, array|object>
 */
function rhm_head_schema_blocks( string $raw ): array {
	$blocks = array();
	foreach ( rhm_head_schema_raw_blocks( $raw ) as $json ) {
		$data = json_decode( $json );
		if ( JSON_ERROR_NONE === json_last_error() && ( is_array( $data ) || is_object( $data ) ) ) {
			$blocks[] = $data;
		}
	}
	return $blocks;
}

/**
 * Chuẩn hóa danh sách (chuỗi JSON hoặc mảng) → [{ name, code, enabled }]. Giữ cả mục trống (vừa bấm "Thêm mới").
 *
 * @return array<int, array{name:string, code:string, enabled:bool}>
 */
function rhm_normalize_head_schemas( mixed $value ): array {
	$items = array();
	foreach ( rhm_decode_list( $value ) as $item ) {
		$items[] = array(
			'name'    => sanitize_text_field( (string) ( $item['name'] ?? '' ) ),
			'code'    => trim( (string) ( $item['code'] ?? '' ) ),
			'enabled' => ! isset( $item['enabled'] ) || rest_sanitize_boolean( $item['enabled'] ),
		);
	}
	return $items;
}

/**
 * Danh sách schema đã lưu. Chưa có (site từ bản ≤ 1.4.22) → tách ô cũ rhm_head_schema, mỗi khối 1 mục.
 *
 * @return array<int, array{name:string, code:string, enabled:bool}>
 */
function rhm_get_head_schemas(): array {
	$value = get_theme_mod( 'rhm_head_schemas', null );
	if ( null !== $value ) {
		return rhm_normalize_head_schemas( $value );
	}

	$items = array();
	foreach ( rhm_head_schema_raw_blocks( (string) get_theme_mod( 'rhm_head_schema', '' ) ) as $i => $json ) {
		$items[] = array(
			/* translators: %d: schema number */
			'name'    => sprintf( __( 'Schema %d', 'themeranghammatsg' ), $i + 1 ),
			'code'    => $json,
			'enabled' => true,
		);
	}
	return $items;
}

/** Sanitize setting rhm_head_schemas → chuỗi JSON. Nội dung giữ nguyên định dạng (chỉ trim); an toàn nhờ encode lại khi in. */
function rhm_sanitize_head_schemas( $value ): string {
	return (string) wp_json_encode( rhm_normalize_head_schemas( $value ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
}

/**
 * Customizer: từ chối lưu khi 1 mục không phải đúng 1 khối JSON object/mảng hợp lệ (mục trống thì bỏ qua).
 *
 * @param WP_Error $validity
 */
function rhm_validate_head_schemas( $validity, $value ) {
	foreach ( rhm_normalize_head_schemas( $value ) as $i => $item ) {
		$blocks = rhm_head_schema_raw_blocks( $item['code'] );
		if ( ! $blocks ) {
			continue;
		}

		/* translators: 1: schema number, 2: schema name */
		$label = '' !== $item['name'] ? sprintf( __( 'Schema %1$d "%2$s"', 'themeranghammatsg' ), $i + 1, $item['name'] ) : sprintf( __( 'Schema %d', 'themeranghammatsg' ), $i + 1 );

		if ( count( $blocks ) > 1 ) {
			/* translators: %s: schema label */
			$validity->add( 'rhm_head_schema_invalid', sprintf( __( '%s có nhiều hơn 1 khối <script>. Mỗi schema chỉ 1 khối — bấm "Thêm mới" để tách.', 'themeranghammatsg' ), $label ) );
			continue;
		}

		$data = json_decode( $blocks[0] );
		if ( JSON_ERROR_NONE !== json_last_error() ) {
			/* translators: 1: schema label, 2: JSON error message */
			$validity->add( 'rhm_head_schema_invalid', sprintf( __( '%1$s không phải JSON hợp lệ: %2$s', 'themeranghammatsg' ), $label, json_last_error_msg() ) );
		} elseif ( ! is_array( $data ) && ! is_object( $data ) ) {
			/* translators: %s: schema label */
			$validity->add( 'rhm_head_schema_invalid', sprintf( __( '%s phải là object {…} hoặc mảng […].', 'themeranghammatsg' ), $label ) );
		}
	}
	return $validity;
}

/** Tên schema an toàn trong comment HTML: bỏ thẻ, "<", ">", "--", xuống dòng. */
function rhm_head_schema_comment_name( string $name ): string {
	$name = str_replace( array( '<', '>' ), '', wp_strip_all_tags( $name, true ) );
	$name = trim( (string) preg_replace( '~\s+~u', ' ', str_replace( '--', '–', $name ) ) );
	return rtrim( $name, '-' );
}

/**
 * In các schema vào <head>, NGAY DƯỚI dòng "<!-- / Yoast SEO plugin. -->".
 *
 * Yoast in cả khối (từ comment mở tới comment đóng) trong action wpseo_head priority -9999 → hook wpseo_head priority 99
 * in ngay sau comment đóng. Yoast tắt → in ở wp_head priority 2 (đầu <head>). Chỉ in 1 lần mỗi request.
 * Comment "<!-- Schema: … -->" + class="rhm-head-schema": mu-plugin 0-rhm-head-order.php dựa vào 2 dấu này để kéo các thẻ
 * theo khối Yoast khi đưa lên đầu <head>.
 */
function rhm_print_head_schema(): void {
	static $printed = false;
	if ( $printed ) {
		return;
	}
	$printed = true;

	// Nén (mặc định): 1 dòng. Không nén: xuống dòng + thụt lề (JSON_PRETTY_PRINT). Cả 2 đều encode lại + JSON_HEX_TAG.
	$flags  = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG;
	$pretty = ! get_theme_mod( 'rhm_head_schema_minify', true );
	if ( $pretty ) {
		$flags |= JSON_PRETTY_PRINT;
	}
	$nl = $pretty ? "\n" : '';

	foreach ( rhm_get_head_schemas() as $item ) {
		if ( ! $item['enabled'] ) {
			continue;
		}
		$name = rhm_head_schema_comment_name( $item['name'] );
		foreach ( rhm_head_schema_blocks( $item['code'] ) as $data ) {
			$json = wp_json_encode( $data, $flags );
			if ( ! $json ) {
				continue;
			}
			if ( '' !== $name ) {
				echo '<!-- Schema: ' . $name . " -->\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã làm sạch cho comment (rhm_head_schema_comment_name).
			}
			echo '<script type="application/ld+json" class="rhm-head-schema">' . $nl . $json . $nl . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON encode lại, JSON_HEX_TAG.
		}
	}
}
add_action( 'wpseo_head', 'rhm_print_head_schema', 99 );

/** Yoast không bật → vẫn in (đầu <head>). */
function rhm_print_head_schema_fallback(): void {
	if ( ! did_action( 'wpseo_head' ) ) {
		rhm_print_head_schema();
	}
}
add_action( 'wp_head', 'rhm_print_head_schema_fallback', 2 );

/**
 * Tùy biến → "Schema tùy chỉnh (head)".
 */
function rhm_customize_head_schema( WP_Customize_Manager $wp_customize ): void {
	require_once RHM_THEME_DIR . '/inc/class-rhm-customize-schema-list-control.php';

	$wp_customize->add_section(
		'rhm_head_schema',
		array(
			'title'       => __( 'Schema tùy chỉnh (head)', 'themeranghammatsg' ),
			'description' => esc_html__( 'Schema JSON-LD tự viết, in vào thẻ <head> của MỌI trang. Bấm "+ Thêm mới" để thêm 1 schema: đặt tên (in thành comment <!-- Schema: tên --> ngay trên thẻ để dễ tìm trong mã nguồn) và dán nội dung, có hoặc không kèm thẻ <script type="application/ld+json">. Mỗi schema = 1 thẻ <script> riêng. JSON lỗi sẽ không lưu được. Lưu ý: Yoast SEO đã in Organization / WebSite / WebPage — tránh khai báo trùng, dùng "@id" để nối vào các thực thể đó. Kiểm tra bằng search.google.com/test/rich-results sau khi lưu.', 'themeranghammatsg' ),
			'priority'    => 160,
		)
	);

	// Default = dữ liệu chuyển từ ô cũ (nếu có) → mở Tùy biến lần đầu vẫn thấy schema cũ, bấm Đăng là lưu sang dạng danh sách.
	$wp_customize->add_setting(
		'rhm_head_schemas',
		array(
			'default'           => rhm_sanitize_head_schemas( rhm_get_head_schemas() ),
			'transport'         => 'refresh',
			'sanitize_callback' => 'rhm_sanitize_head_schemas',
			'validate_callback' => 'rhm_validate_head_schemas',
		)
	);

	$wp_customize->add_control(
		new RHM_Customize_Schema_List_Control(
			$wp_customize,
			'rhm_head_schemas',
			array(
				'label'       => __( 'Danh sách schema', 'themeranghammatsg' ),
				'description' => __( 'Bấm tên để mở/thu gọn. Kéo góc dưới bên phải ô nội dung để đổi chiều cao (trình duyệt này nhớ chiều cao đã chỉnh). Bỏ chọn "Bật" để tạm ẩn schema mà không xóa.', 'themeranghammatsg' ),
				'section'     => 'rhm_head_schema',
			)
		)
	);

	$wp_customize->add_setting(
		'rhm_head_schema_minify',
		array(
			'default'           => true,
			'transport'         => 'refresh',
			'sanitize_callback' => 'rest_sanitize_boolean',
		)
	);

	$wp_customize->add_control(
		'rhm_head_schema_minify',
		array(
			'label'       => __( 'Nén schema (minify)', 'themeranghammatsg' ),
			'description' => __( 'Bật: mỗi schema in trên 1 dòng (nhẹ nhất, khuyên dùng). Tắt: in xuống dòng + thụt lề cho dễ đọc khi xem mã nguồn trang. Google đọc như nhau.', 'themeranghammatsg' ),
			'section'     => 'rhm_head_schema',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'rhm_customize_head_schema', 11 );
