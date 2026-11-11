<?php
/**
 * Thư viện icon SVG riêng của theme: assets/icons/<bộ>/<tên>.svg
 *
 * - menu/      24×24, fill="currentColor" → in INLINE (đổi màu theo CSS, 0 request). Chọn ở Giao diện → Menu.
 * - services/  48×48, màu vẽ sẵn → in bằng <img src> (tải được qua CDN). Chọn ở Tùy biến → Dịch vụ nổi bật.
 * - cta/       24×24, fill="currentColor" → in INLINE. Chọn ở Tùy biến → Nút liên hệ nhanh (inc/floating-cta.php).
 *
 * Thêm icon: chép file .svg vào đúng thư mục (tên file a-z, 0-9, "-"), đặt <title> tiếng Việt = nhãn hiển thị
 * trong ô chọn. Không cần sửa PHP.
 *
 * CDN (Cloudflare…): đồng bộ NGUYÊN thư mục assets/icons/ lên CDN (giữ cấu trúc menu/, services/),
 * rồi nhập URL thư mục đó ở Tùy biến → Thư viện icon. Để trống = phục vụ từ theme.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Các bộ icon: thư mục => mô tả. */
const RHM_ICON_SETS = array(
	'menu'     => 'Icon menu',
	'services' => 'Icon dịch vụ nổi bật',
	'cta'      => 'Icon nút liên hệ nhanh',
	'landing'  => 'Icon landing page',
);

function rhm_icon_dir( string $set ): string {
	return RHM_THEME_DIR . '/assets/icons/' . $set;
}

/**
 * Icon có trong 1 bộ: tên file (không .svg) => nhãn (<title> trong file, không có thì lấy tên file).
 *
 * @return array<string, string>
 */
function rhm_icon_list( string $set ): array {
	static $cache = array();
	if ( isset( $cache[ $set ] ) ) {
		return $cache[ $set ];
	}

	$icons = array();
	if ( isset( RHM_ICON_SETS[ $set ] ) ) {
		foreach ( glob( rhm_icon_dir( $set ) . '/*.svg' ) ?: array() as $file ) {
			$name = basename( $file, '.svg' );
			if ( ! preg_match( '/^[a-z0-9-]+$/', $name ) ) {
				continue;
			}
			$head           = (string) file_get_contents( $file, false, null, 0, 512 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file trong theme.
			$icons[ $name ] = preg_match( '~<title>(.*?)</title>~s', $head, $m ) ? trim( html_entity_decode( $m[1] ) ) : $name;
		}
	}
	natcasesort( $icons );

	return $cache[ $set ] = $icons;
}

function rhm_icon_exists( string $set, string $name ): bool {
	return '' !== $name && preg_match( '/^[a-z0-9-]+$/', $name ) && is_file( rhm_icon_dir( $set ) . '/' . $name . '.svg' );
}

/** Sanitize giá trị chọn icon (Customizer / menu): tên icon hợp lệ của bộ, hoặc ''. */
function rhm_sanitize_icon_name( string $set, $value ): string {
	$value = sanitize_key( (string) $value );
	return rhm_icon_exists( $set, $value ) ? $value : '';
}

/** URL thư mục chứa các bộ icon: CDN (Tùy biến → Thư viện icon) hoặc thư mục trong theme. */
function rhm_icon_base_url(): string {
	$cdn = untrailingslashit( trim( (string) get_theme_mod( 'rhm_icon_base_url', '' ) ) );
	$url = '' !== $cdn ? $cdn : RHM_THEME_URI . '/assets/icons';
	return untrailingslashit( (string) apply_filters( 'rhm_icon_base_url', $url ) );
}

/**
 * URL file icon (kèm ?ver=thời gian sửa file: CDN/trình duyệt cache lâu vẫn nhận bản mới). '' nếu không tồn tại.
 */
function rhm_icon_url( string $set, string $name ): string {
	if ( ! rhm_icon_exists( $set, $name ) ) {
		return '';
	}
	$file = rhm_icon_dir( $set ) . '/' . $name . '.svg';
	return rhm_icon_base_url() . '/' . $set . '/' . $name . '.svg?ver=' . filemtime( $file );
}

/**
 * SVG inline của icon (trang trí: aria-hidden, bỏ <title>). '' nếu không tồn tại.
 * File thuộc theme (không phải người dùng upload) nên tin cậy; vẫn bỏ <script> và thuộc tính on* cho chắc.
 */
function rhm_icon_svg( string $set, string $name, string $class = '' ): string {
	static $cache = array();
	if ( ! rhm_icon_exists( $set, $name ) ) {
		return '';
	}

	$key = $set . '/' . $name;
	if ( ! isset( $cache[ $key ] ) ) {
		$svg = (string) file_get_contents( rhm_icon_dir( $set ) . '/' . $name . '.svg' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- file trong theme.
		$svg = preg_replace( array( '~<\?xml.*?\?>~s', '~<!--.*?-->~s', '~<title>.*?</title>~s', '~<script\b.*?</script>~is', '~\son\w+="[^"]*"~i' ), '', $svg );
		$cache[ $key ] = trim( (string) $svg );
	}

	$tags = new WP_HTML_Tag_Processor( $cache[ $key ] );
	if ( ! $tags->next_tag( 'svg' ) ) {
		return '';
	}
	$tags->set_attribute( 'class', trim( 'icon icon-' . $name . ' ' . $class ) );
	$tags->set_attribute( 'aria-hidden', 'true' );
	$tags->set_attribute( 'focusable', 'false' );
	$tags->remove_attribute( 'xmlns' );

	return $tags->get_updated_html();
}
