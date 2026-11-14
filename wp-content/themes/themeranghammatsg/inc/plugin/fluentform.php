<?php
/**
 * Plugin Fluent Forms: jQuery + JS của Fluent chỉ tải khi người dùng chạm vào form.
 *
 * form-submission.js cần jQuery (~50 kB gzip cả hai). Ở mọi trang có form Fluent (bất kỳ đâu: template,
 * shortcode trong nội dung, widget…), jQuery + JS của Fluent KHÔNG chạy lúc tải trang: thẻ <script> đổi thành
 * type="text/plain" + data-rhm-lazy-src; src/js/fluent-lazy.js chỉ tải lại (đúng thứ tự) khi người dùng rê chuột / chạm /
 * focus vào form, hoặc bấm gửi khi chưa tải xong (giữ lại lần gửi, gửi tiếp sau khi Fluent khởi tạo).
 * Fluent tự khởi tạo khi được nạp muộn (document đã tải xong → setTimeout(init, 100)); cấu hình từng form là
 * script thuần (window.fluent_form_*) nên chạy ngay không cần jQuery.
 * An toàn: nếu trang có script KHÁC (không phải của Fluent) phụ thuộc jQuery → không hoãn gì (in bình thường).
 * Liên quan: inc/cleanup.php đưa jQuery xuống footer + bỏ jquery-migrate. Form do theme in (rhm_render_fluent_form():
 * Liên hệ, hồ sơ bác sĩ, popup đặt lịch) dùng partial src/css/components/_booking-form.css thay cho CSS của Fluent (gỡ ở cuối file).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Handle script được hoãn trên trang hiện tại (rỗng = không hoãn). Tính ở wp_footer (trước khi in script module
 * và script chân trang), lúc mọi form Fluent trong trang đã render và đã enqueue script của nó.
 *
 * @return string[]
 */
function rhm_fluent_lazy_handles(): array {
	static $handles = null;

	// Script in trong <head> cũng qua script_loader_tag — lúc đó form chưa render → chưa tính (và không lưu cache).
	if ( ! did_action( 'wp_footer' ) ) {
		return array();
	}
	if ( null !== $handles ) {
		return $handles;
	}

	$handles = array();
	$scripts = wp_scripts();
	if ( is_admin() || ! wp_script_is( 'fluent-form-submission', 'enqueued' ) ) {
		return $handles;
	}

	// Mọi handle sẽ được in (gồm cả phụ thuộc), kèm danh sách phụ thuộc đầy đủ của từng handle.
	$closure = static function ( string $handle, array &$seen ) use ( &$closure, $scripts ): void {
		if ( isset( $seen[ $handle ] ) || ! isset( $scripts->registered[ $handle ] ) ) {
			return;
		}
		$seen[ $handle ] = true;
		foreach ( $scripts->registered[ $handle ]->deps as $dep ) {
			$closure( $dep, $seen );
		}
	};

	$all = array();
	foreach ( $scripts->queue as $handle ) {
		$closure( $handle, $all );
	}

	$jquery = array( 'jquery', 'jquery-core', 'jquery-migrate' );
	$lazy   = $jquery;
	foreach ( array_keys( $all ) as $handle ) {
		if ( in_array( $handle, $jquery, true ) ) {
			continue;
		}
		$deps = array();
		$closure( $handle, $deps );
		unset( $deps[ $handle ] );
		if ( ! array_intersect( $jquery, array_keys( $deps ) ) ) {
			continue; // Không dùng jQuery → in bình thường.
		}
		if ( ! str_starts_with( $handle, 'fluent' ) ) {
			return $handles; // Script khác cần jQuery ngay → không hoãn gì.
		}
		$lazy[] = $handle;
	}

	$handles = $lazy;
	return $handles;
}

/**
 * Trang có form Fluent và hoãn được → nạp src/js/fluent-lazy.js.
 */
function rhm_fluent_lazy_loader(): void {
	if ( rhm_fluent_lazy_handles() && file_exists( RHM_THEME_DIR . '/dist/js/fluent-lazy.js' ) ) {
		wp_enqueue_script_module( 'rhm-fluent-lazy', RHM_THEME_URI . '/dist/js/fluent-lazy.js', array(), rhm_asset_version( 'dist/js/fluent-lazy.js' ) );
	}
}
add_action( 'wp_footer', 'rhm_fluent_lazy_loader', 1 );

/**
 * Thẻ <script src> (và script nội tuyến "-js-after" phải chạy sau thư viện) của handle bị hoãn →
 * type="text/plain": trình duyệt không tải / không chạy. fluent-lazy.js đọc data-rhm-lazy-src / data-rhm-lazy để chạy lại.
 * Script "-js-before" / bản dịch giữ nguyên (chạy trước thư viện, không cần jQuery).
 */
function rhm_fluent_lazy_tag( string $tag, string $handle ): string {
	if ( ! in_array( $handle, rhm_fluent_lazy_handles(), true ) ) {
		return $tag;
	}

	$processor = new WP_HTML_Tag_Processor( $tag );
	while ( $processor->next_tag( 'script' ) ) {
		$src = $processor->get_attribute( 'src' );
		if ( is_string( $src ) ) {
			$processor->set_attribute( 'data-rhm-lazy-src', $src );
			$processor->remove_attribute( 'src' );
		} elseif ( ! str_ends_with( (string) $processor->get_attribute( 'id' ), '-js-after' ) ) {
			continue;
		}
		$processor->set_attribute( 'type', 'text/plain' );
		$processor->set_attribute( 'data-rhm-lazy', '' );
		$processor->remove_attribute( 'defer' );
		$processor->remove_attribute( 'async' );
	}

	return $processor->get_updated_html();
}
add_filter( 'script_loader_tag', 'rhm_fluent_lazy_tag', 10, 2 );

/**
 * Đang render form bằng rhm_render_fluent_form() (form của theme, có style riêng trong khối .rhm-booking)?
 */
function rhm_fluent_theme_rendering( ?bool $set = null ): bool {
	static $rendering = false;
	if ( null !== $set ) {
		$rendering = $set;
	}
	return $rendering;
}

/**
 * Có form Fluent nào trên trang KHÔNG do theme render (shortcode trong nội dung, widget…)? Form đó cần CSS gốc của Fluent.
 */
function rhm_fluent_has_foreign_form( ?bool $set = null ): bool {
	static $foreign = false;
	if ( null !== $set ) {
		$foreign = $set;
	}
	return $foreign;
}

/**
 * In form Fluent của theme (đặt trong khối .rhm-booking). '' nếu không có ID hoặc Fluent không bật.
 */
function rhm_render_fluent_form( int $form_id ): string {
	if ( $form_id <= 0 || ! shortcode_exists( 'fluentform' ) ) {
		return '';
	}
	rhm_fluent_theme_rendering( true );
	$html = do_shortcode( sprintf( '[fluentform id="%d"]', $form_id ) );
	rhm_fluent_theme_rendering( false );
	return $html;
}

/**
 * Ghi nhận form Fluent render ngoài rhm_render_fluent_form().
 */
function rhm_fluent_track_render( $output, string $tag ) {
	if ( 'fluentform' === $tag && ! rhm_fluent_theme_rendering() ) {
		rhm_fluent_has_foreign_form( true );
	}
	return $output;
}
add_filter( 'do_shortcode_tag', 'rhm_fluent_track_render', 10, 2 );

/**
 * Tên + mô tả công cụ WebMCP (agent AI) của 1 form Fluent. Form đặt lịch (popup / Customizer) = "book_appointment",
 * form khác = "contact_form_<id>". Sửa từng form: filter 'rhm_webmcp_fluent_tool'.
 *
 * @return array{0:string, 1:string} [ toolname, tooldescription ].
 */
function rhm_fluent_webmcp_tool( int $form_id ): array {
	$booking = array( rhm_booking_popup_form_id(), absint( get_theme_mod( 'rhm_booking_form_id', 0 ) ) );
	$tool    = in_array( $form_id, $booking, true )
		? array( 'book_appointment', __( 'Đặt lịch khám / tư vấn tại phòng khám Răng Hàm Mặt SG: gửi họ tên, số điện thoại và dịch vụ cần tư vấn; phòng khám sẽ gọi lại để xác nhận lịch.', 'themeranghammatsg' ) )
		: array( 'contact_form_' . $form_id, __( 'Gửi yêu cầu liên hệ / đăng ký tư vấn tới phòng khám Răng Hàm Mặt SG.', 'themeranghammatsg' ) );

	return (array) apply_filters( 'rhm_webmcp_fluent_tool', $tool, $form_id );
}

/**
 * WebMCP khai báo: gắn toolname + tooldescription vào <form> của mọi form Fluent (shortcode do theme hay nội dung in ra).
 * Chỉ thêm thuộc tính, không đổi markup hiển thị → không ảnh hưởng CLS / JS "chạm mới tải". Ô nhập đã có name + <label> của Fluent.
 */
function rhm_fluent_webmcp_attrs( $output, string $tag ) {
	if ( 'fluentform' !== $tag || is_admin() || ! is_string( $output ) || '' === $output ) {
		return $output;
	}

	$processor = new WP_HTML_Tag_Processor( $output );
	while ( $processor->next_tag( 'form' ) ) {
		$form_id = absint( $processor->get_attribute( 'data-form_id' ) );
		if ( ! $form_id ) {
			continue;
		}
		if ( null === $processor->get_attribute( 'toolname' ) ) {
			[ $name, $description ] = rhm_fluent_webmcp_tool( $form_id ) + array( '', '' );
			if ( '' !== $name && '' !== $description ) {
				$processor->set_attribute( 'toolname', $name );
				$processor->set_attribute( 'tooldescription', $description );
			}
		}
		break;
	}

	return $processor->get_updated_html();
}
add_filter( 'do_shortcode_tag', 'rhm_fluent_webmcp_attrs', 10, 2 );

/**
 * Bỏ 2 file CSS của Fluent (~9 kB gzip, chặn hiển thị) khi mọi form trên trang đều do theme in — form của theme đã có đủ
 * style trong partial _booking-form.css (nạp mọi trang qua footer.css, vì popup đặt lịch có ở mọi trang).
 * Trang có form Fluent khác (shortcode trong nội dung, widget…) vẫn giữ CSS của plugin.
 * Fluent enqueue CSS lúc render shortcode (giữa trang) → gỡ cả ở wp_enqueue_scripts (khi nội dung không có shortcode) lẫn đầu
 * wp_footer (đã render hết form, kể cả popup trong footer.php; trước khi WordPress in style nạp muộn).
 */
function rhm_fluent_dequeue_styles(): void {
	if ( doing_action( 'wp_enqueue_scripts' ) && is_singular() && has_shortcode( (string) get_post_field( 'post_content' ), 'fluentform' ) ) {
		return;
	}
	if ( rhm_fluent_has_foreign_form() ) {
		return;
	}
	wp_dequeue_style( 'fluent-form-styles' );
	wp_dequeue_style( 'fluentform-public-default' );
}
add_action( 'wp_enqueue_scripts', 'rhm_fluent_dequeue_styles', 100 );
add_action( 'wp_footer', 'rhm_fluent_dequeue_styles', 1 );

/**
 * Danh sách form Fluent đã xuất bản: [ ID => "Tên (#ID)" ] cho ô chọn trong admin (vd layout landing "form").
 * Chỉ truy vấn trong admin (ngoài site trả rỗng, không tốn truy vấn); Fluent tắt → rỗng.
 *
 * @return array<int, string>
 */
function rhm_fluent_form_choices(): array {
	static $choices = null;
	if ( null !== $choices ) {
		return $choices;
	}
	$choices = array();
	if ( ! is_admin() || ! shortcode_exists( 'fluentform' ) ) {
		return $choices;
	}
	global $wpdb;
	$table = $wpdb->prefix . 'fluentform_forms';
	$rows  = $wpdb->get_results( "SELECT id, title FROM {$table} WHERE status = 'published' ORDER BY id DESC" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- bảng của Fluent, không có tham số người dùng; chỉ chạy ở màn hình admin.
	foreach ( (array) $rows as $row ) {
		// Tên mặc định của Fluent đã có "(#ID)" (vd "Blank Form (#3)") → không thêm lần nữa.
		$choices[ (int) $row->id ] = str_contains( (string) $row->title, '#' . $row->id ) ? (string) $row->title : sprintf( '%s (#%d)', $row->title, $row->id );
	}
	return $choices;
}
