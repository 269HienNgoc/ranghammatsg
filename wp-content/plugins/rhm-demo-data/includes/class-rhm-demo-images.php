<?php
/**
 * Sinh ảnh mẫu bằng GD (JPEG) rồi đưa vào Thư viện Media như ảnh upload thật
 * (WordPress tự cắt đủ các size rhm-* của theme → srcset hoạt động như dữ liệu thật).
 *
 * Mỗi ảnh có "slug" riêng, lưu ở meta _rhm_demo_image: chạy lại thì dùng lại ảnh cũ, không sinh trùng.
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

final class RHM_Demo_Images {

	/** Màu thương hiệu theo src/css/theme.css của theme. */
	private const PALETTE = array(
		'primary' => array( 11, 122, 85 ),
		'deep'    => array( 0, 52, 66 ),
		'teal'    => array( 29, 92, 90 ),
		'green'   => array( 19, 181, 75 ),
		'mint'    => array( 236, 247, 243 ),
		'accent'  => array( 211, 216, 103 ),
		'cream'   => array( 252, 249, 240 ),
		'gray'    => array( 120, 132, 140 ),
		'grayd'   => array( 70, 80, 88 ),
	);

	private ?string $font      = null;
	private ?string $font_bold = null;

	public function __construct() {
		$this->font      = $this->find_font( array( 'C:/Windows/Fonts/arial.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf', '/usr/share/fonts/dejavu/DejaVuSans.ttf', '/Library/Fonts/Arial.ttf' ) );
		$this->font_bold = $this->find_font( array( 'C:/Windows/Fonts/arialbd.ttf', '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf', '/Library/Fonts/Arial Bold.ttf' ) ) ?? $this->font;
	}

	public static function available(): bool {
		return extension_loaded( 'gd' ) && function_exists( 'imagecreatetruecolor' ) && function_exists( 'imagejpeg' );
	}

	/**
	 * Lấy (hoặc tạo) 1 ảnh mẫu.
	 *
	 * @param array{title?:string, subtitle?:string, style?:string, from?:string, to?:string, alt?:string} $opts
	 *        style: banner | portrait | photo | cert | video | before | after.
	 */
	public function get( string $slug, int $width, int $height, array $opts = array() ): int {
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => '_rhm_demo_image', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => $slug,             // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( $existing && file_exists( (string) get_attached_file( (int) $existing[0] ) ) ) {
			return (int) $existing[0];
		}

		$image = $this->draw( $width, $height, $opts );
		$file  = $this->save( $image, 'rhm-demo-' . $slug . '.jpg' );
		imagedestroy( $image );

		return $file ? $this->attach( $file, $slug, $opts ) : 0;
	}

	/* -----------------------------------------------------------------
	 * Vẽ
	 * -------------------------------------------------------------- */

	private function draw( int $w, int $h, array $opts ): GdImage {
		$style = $opts['style'] ?? 'photo';
		$from  = self::PALETTE[ $opts['from'] ?? 'primary' ];
		$to    = self::PALETTE[ $opts['to'] ?? 'deep' ];

		if ( 'before' === $style ) {
			$from = self::PALETTE['gray'];
			$to   = self::PALETTE['grayd'];
		} elseif ( 'after' === $style ) {
			$from = self::PALETTE['green'];
			$to   = self::PALETTE['primary'];
		}

		$img = imagecreatetruecolor( $w, $h );
		imagealphablending( $img, true );
		imageantialias( $img, true );

		if ( 'cert' === $style ) {
			$this->draw_certificate( $img, $w, $h, $opts );
			return $img;
		}

		if ( 'hero' === $style ) {
			$this->draw_hero( $img, $w, $h, $from, $to, $opts );
			return $img;
		}

		$this->gradient( $img, $w, $h, $from, $to );
		$this->bubbles( $img, $w, $h );

		$min   = min( $w, $h );
		$white = imagecolorallocate( $img, 255, 255, 255 );

		switch ( $style ) {
			case 'portrait':
				$this->silhouette( $img, $w, $h );
				imagefilledrectangle( $img, 0, (int) ( $h * 0.76 ), $w, $h, imagecolorallocatealpha( $img, ...array_merge( self::PALETTE['deep'], array( 40 ) ) ) );
				$this->text_block( $img, $w, $h, $opts, (int) ( $h * 0.86 ), $min * 0.055 );
				break;

			case 'video':
				$this->tooth( $img, (int) ( $w * 0.2 ), (int) ( $h * 0.5 ), (int) ( $min * 0.35 ), 70 );
				$r = (int) ( $min * 0.13 );
				imagefilledellipse( $img, (int) ( $w / 2 ), (int) ( $h / 2 ), $r * 2, $r * 2, imagecolorallocatealpha( $img, 255, 255, 255, 20 ) );
				$cx = (int) ( $w / 2 + $r * 0.12 );
				$cy = (int) ( $h / 2 );
				$t  = (int) ( $r * 0.55 );
				imagefilledpolygon( $img, array( $cx - $t, $cy - $t, $cx - $t, $cy + $t, $cx + $t, $cy ), imagecolorallocate( $img, ...self::PALETTE['primary'] ) );
				$this->text_block( $img, $w, $h, $opts, (int) ( $h * 0.82 ), $min * 0.06 );
				break;

			case 'before':
			case 'after':
				$this->tooth( $img, (int) ( $w / 2 ), (int) ( $h * 0.42 ), (int) ( $min * 0.5 ), 'after' === $style ? 0 : 35 );
				$label = 'after' === $style ? 'SAU' : 'TRƯỚC';
				$this->text( $img, $label, $min * 0.1, (int) ( $w / 2 ), (int) ( $h * 0.84 ), $white, true );
				break;

			case 'banner':
				$this->tooth( $img, (int) ( $w * 0.78 ), (int) ( $h * 0.5 ), (int) ( $min * 0.7 ), 60 );
				$this->text_block( $img, $w, $h, $opts, (int) ( $h * 0.45 ), $min * 0.075, (int) ( $w * ( $w > $h ? 0.36 : 0.5 ) ) );
				break;

			default: // photo
				$this->tooth( $img, (int) ( $w / 2 ), (int) ( $h * 0.4 ), (int) ( $min * 0.42 ), 40 );
				$this->text_block( $img, $w, $h, $opts, (int) ( $h * 0.78 ), $min * 0.06 );
		}

		return $img;
	}

	private function gradient( GdImage $img, int $w, int $h, array $from, array $to ): void {
		for ( $y = 0; $y < $h; $y++ ) {
			$t = $y / max( 1, $h - 1 );
			$c = imagecolorallocate(
				$img,
				(int) ( $from[0] + ( $to[0] - $from[0] ) * $t ),
				(int) ( $from[1] + ( $to[1] - $from[1] ) * $t ),
				(int) ( $from[2] + ( $to[2] - $from[2] ) * $t )
			);
			imageline( $img, 0, $y, $w, $y, $c );
		}
	}

	private function bubbles( GdImage $img, int $w, int $h ): void {
		$min = min( $w, $h );
		$c   = imagecolorallocatealpha( $img, 255, 255, 255, 112 );
		foreach ( array( array( 0.08, 0.12, 0.5 ), array( 0.92, 0.85, 0.7 ), array( 0.7, 0.05, 0.3 ), array( 0.15, 0.95, 0.35 ) ) as [ $x, $y, $r ] ) {
			imagefilledellipse( $img, (int) ( $w * $x ), (int) ( $h * $y ), (int) ( $min * $r ), (int) ( $min * $r ), $c );
		}
	}

	/** Chiếc răng cách điệu (thân + 2 chân răng), $alpha 0 = đặc, 127 = trong suốt. */
	private function tooth( GdImage $img, int $cx, int $cy, int $size, int $alpha ): void {
		$s = $size / 2;
		// Gom vào ảnh tạm để các mảng chồng lên nhau không đậm màu hơn chỗ khác.
		$layer = imagecreatetruecolor( $size + 4, $size + 4 );
		imagealphablending( $layer, false );
		imagefill( $layer, 0, 0, imagecolorallocatealpha( $layer, 0, 0, 0, 127 ) );
		imagesavealpha( $layer, true );
		$lc = imagecolorallocatealpha( $layer, 255, 255, 255, 0 );
		$o  = 2;
		imagefilledellipse( $layer, (int) ( $o + $s * 0.62 ), (int) ( $o + $s * 0.55 ), (int) ( $s * 1.1 ), (int) ( $s * 1.0 ), $lc );
		imagefilledellipse( $layer, (int) ( $o + $s * 1.38 ), (int) ( $o + $s * 0.55 ), (int) ( $s * 1.1 ), (int) ( $s * 1.0 ), $lc );
		imagefilledrectangle( $layer, (int) ( $o + $s * 0.12 ), (int) ( $o + $s * 0.55 ), (int) ( $o + $s * 1.88 ), (int) ( $o + $s * 1.05 ), $lc );
		imagefilledpolygon( $layer, array( (int) ( $o + $s * 0.12 ), (int) ( $o + $s * 1.0 ), (int) ( $o + $s * 0.95 ), (int) ( $o + $s * 1.0 ), (int) ( $o + $s * 0.62 ), (int) ( $o + $s * 1.95 ), (int) ( $o + $s * 0.38 ), (int) ( $o + $s * 1.95 ) ), $lc );
		imagefilledpolygon( $layer, array( (int) ( $o + $s * 1.05 ), (int) ( $o + $s * 1.0 ), (int) ( $o + $s * 1.88 ), (int) ( $o + $s * 1.0 ), (int) ( $o + $s * 1.62 ), (int) ( $o + $s * 1.95 ), (int) ( $o + $s * 1.38 ), (int) ( $o + $s * 1.95 ) ), $lc );
		rhm_demo_imagecopymerge_alpha( $img, $layer, (int) ( $cx - $s - $o ), (int) ( $cy - $s - $o ), 127 - $alpha );
		imagedestroy( $layer );
	}

	/** Bóng người (đầu + vai) cho ảnh chân dung. */
	private function silhouette( GdImage $img, int $w, int $h ): void {
		$c = imagecolorallocatealpha( $img, 255, 255, 255, 60 );
		imagefilledellipse( $img, (int) ( $w / 2 ), (int) ( $h * 0.36 ), (int) ( $w * 0.38 ), (int) ( $w * 0.44 ), $c );
		imagefilledellipse( $img, (int) ( $w / 2 ), (int) ( $h * 0.8 ), (int) ( $w * 0.9 ), (int) ( $h * 0.5 ), $c );
	}

	private function draw_certificate( GdImage $img, int $w, int $h, array $opts ): void {
		imagefilledrectangle( $img, 0, 0, $w, $h, imagecolorallocate( $img, ...self::PALETTE['cream'] ) );
		$deep   = imagecolorallocate( $img, ...self::PALETTE['deep'] );
		$accent = imagecolorallocate( $img, 184, 160, 70 );
		$pad    = (int) ( min( $w, $h ) * 0.04 );
		imagesetthickness( $img, max( 4, (int) ( $pad * 0.35 ) ) );
		imagerectangle( $img, $pad, $pad, $w - $pad, $h - $pad, $deep );
		imagesetthickness( $img, 2 );
		imagerectangle( $img, $pad * 2, $pad * 2, $w - $pad * 2, $h - $pad * 2, $accent );
		imagesetthickness( $img, 1 );

		$this->text( $img, mb_strtoupper( $opts['title'] ?? 'Chứng nhận' ), $h * 0.065, (int) ( $w / 2 ), (int) ( $h * 0.3 ), $deep, true );
		$this->text( $img, $opts['subtitle'] ?? '', $h * 0.04, (int) ( $w / 2 ), (int) ( $h * 0.45 ), $deep );
		$this->text( $img, 'Trao cho: BS. Nguyễn Minh Anh', $h * 0.045, (int) ( $w / 2 ), (int) ( $h * 0.58 ), $deep, true );
		imageline( $img, (int) ( $w * 0.3 ), (int) ( $h * 0.66 ), (int) ( $w * 0.7 ), (int) ( $h * 0.66 ), $accent );
		imagefilledellipse( $img, (int) ( $w * 0.78 ), (int) ( $h * 0.78 ), (int) ( $h * 0.18 ), (int) ( $h * 0.18 ), $accent );
		$this->text( $img, 'DỮ LIỆU MẪU', $h * 0.028, (int) ( $w * 0.3 ), (int) ( $h * 0.8 ), $accent, true );
	}

	/**
	 * Banner hero trang chủ.
	 * - Ngang (desktop): chữ canh trái ở nửa trái, hình răng lớn + vòng sáng ở nửa phải.
	 * - Dọc (mobile): chữ canh giữa ở nửa trên, hình răng ở nửa dưới.
	 * opts: badge (nhãn nhỏ trên tiêu đề), title, subtitle, cta (chữ trên nút), note (dòng nhỏ cuối).
	 */
	private function draw_hero( GdImage $img, int $w, int $h, array $from, array $to, array $opts ): void {
		$wide  = $w > $h;
		$white = imagecolorallocate( $img, 255, 255, 255 );
		$mint  = imagecolorallocate( $img, ...self::PALETTE['mint'] );
		$deep  = imagecolorallocate( $img, ...self::PALETTE['deep'] );
		$acc   = imagecolorallocate( $img, ...self::PALETTE['accent'] );

		// Nền: gradient chéo (ngang: chủ yếu trái → phải; dọc: chủ yếu trên → dưới), vẽ theo ô 8px cho nhanh.
		for ( $y = 0; $y < $h; $y++ ) {
			for ( $x = 0; $x < $w; $x += 8 ) {
				$t = $wide ? ( 0.75 * $x / $w + 0.25 * $y / $h ) : ( 0.25 * $x / $w + 0.75 * $y / $h );
				$c = imagecolorallocate(
					$img,
					(int) ( $from[0] + ( $to[0] - $from[0] ) * $t ),
					(int) ( $from[1] + ( $to[1] - $from[1] ) * $t ),
					(int) ( $from[2] + ( $to[2] - $from[2] ) * $t )
				);
				imageline( $img, $x, $y, min( $w - 1, $x + 7 ), $y, $c );
			}
		}

		$min = min( $w, $h );
		$glow = static fn( int $a ) => imagecolorallocatealpha( $img, 255, 255, 255, $a );
		if ( $wide ) {
			$cx = (int) ( $w * 0.74 );
			$cy = (int) ( $h * 0.5 );
		} else {
			$cx = (int) ( $w * 0.5 );
			$cy = (int) ( $h * 0.8 );
		}
		$rings = $wide ? array( array( 1.25, 116 ), array( 0.95, 110 ), array( 0.68, 100 ) ) : array( array( 0.82, 116 ), array( 0.62, 108 ), array( 0.44, 98 ) );
		foreach ( $rings as [ $r, $a ] ) {
			imagefilledellipse( $img, $cx, $cy, (int) ( $min * $r ), (int) ( $min * $r ), $glow( $a ) );
		}
		imagefilledellipse( $img, (int) ( $w * 0.03 ), (int) ( $h * 0.05 ), (int) ( $min * 0.5 ), (int) ( $min * 0.5 ), $glow( 115 ) );
		imagefilledellipse( $img, (int) ( $w * ( $wide ? 0.45 : 0.95 ) ), (int) ( $h * ( $wide ? 1.0 : 0.42 ) ), (int) ( $min * 0.35 ), (int) ( $min * 0.35 ), $glow( 115 ) );

		$this->tooth( $img, $cx, $cy, (int) ( $min * ( $wide ? 0.58 : 0.3 ) ), 0 );
		// Lấp lánh quanh răng.
		foreach ( ( $wide ? array( array( -0.36, -0.28, 0.035 ), array( 0.34, -0.2, 0.025 ), array( 0.3, 0.3, 0.03 ) ) : array( array( -0.3, -0.12, 0.035 ), array( 0.28, -0.06, 0.025 ), array( 0.26, 0.18, 0.03 ) ) ) as [ $dx, $dy, $s ] ) {
			$this->sparkle( $img, (int) ( $cx + $min * $dx ), (int) ( $cy + $min * $dy ), (int) ( $min * $s ), $acc );
		}

		// Khối chữ.
		$left    = $wide ? (int) ( $w * 0.075 ) : 0;
		$max_w   = $wide ? (int) ( $w * 0.5 ) : (int) ( $w * 0.86 );
		$align   = static fn( int $text_w ): int => $wide ? $left : (int) ( ( $w - $text_w ) / 2 );
		$title_s = $wide ? $h * 0.085 : $w * 0.066;
		$lines   = $this->wrap( (string) ( $opts['title'] ?? '' ), $title_s, $max_w, true );
		$sub     = $this->wrap( (string) ( $opts['subtitle'] ?? '' ), $title_s * 0.42, $max_w, false );

		$y = $wide ? (int) ( $h * 0.5 - ( count( $lines ) * $title_s * 1.3 + $title_s * 2.6 ) / 2 ) : (int) ( $h * 0.06 );

		if ( ! empty( $opts['badge'] ) ) {
			$bs  = $title_s * 0.34;
			$bw  = $this->text_width( $opts['badge'], $bs, true ) + (int) ( $bs * 2 );
			$bh  = (int) ( $bs * 2.3 );
			$bx  = $align( $bw );
			$this->pill( $img, $bx, $y, $bw, $bh, $acc );
			$this->text_at( $img, $opts['badge'], $bs, $bx + (int) $bs, (int) ( $y + $bh * 0.68 ), $deep, true );
			$y += (int) ( $bh + $title_s * 1.25 );
		} else {
			$y += (int) $title_s;
		}

		foreach ( $lines as $line ) {
			$this->text_at( $img, $line, $title_s, $align( $this->text_width( $line, $title_s, true ) ), $y, $white, true );
			$y += (int) ( $title_s * 1.3 );
		}
		$y += (int) ( $title_s * 0.1 );
		foreach ( $sub as $line ) {
			$this->text_at( $img, $line, $title_s * 0.42, $align( $this->text_width( $line, $title_s * 0.42, false ) ), $y, $mint );
			$y += (int) ( $title_s * 0.62 );
		}

		if ( ! empty( $opts['cta'] ) ) {
			$cs = $title_s * 0.36;
			$cw = $this->text_width( $opts['cta'], $cs, true ) + (int) ( $cs * 3.2 );
			$ch = (int) ( $cs * 2.8 );
			$cx = $align( $cw );
			$y += (int) ( $title_s * 0.35 );
			$this->pill( $img, $cx + 4, $y + 6, $cw, $ch, imagecolorallocatealpha( $img, 0, 0, 0, 100 ) );
			$this->pill( $img, $cx, $y, $cw, $ch, $white );
			$this->text_at( $img, $opts['cta'], $cs, $cx + (int) ( $cs * 1.6 ), (int) ( $y + $ch * 0.66 ), imagecolorallocate( $img, ...self::PALETTE['primary'] ), true );
			$y += $ch;
		}

		if ( ! empty( $opts['note'] ) ) {
			$ns = $title_s * 0.27;
			$y += (int) ( $title_s * 0.75 );
			$this->text_at( $img, $opts['note'], $ns, $align( $this->text_width( $opts['note'], $ns, false ) ), $y, $mint );
		}
	}

	private function pill( GdImage $img, int $x, int $y, int $w, int $h, int $color ): void {
		$r = (int) ( $h / 2 );
		imagefilledrectangle( $img, $x + $r, $y, $x + $w - $r, $y + $h, $color );
		imagefilledellipse( $img, $x + $r, $y + $r, $h, $h, $color );
		imagefilledellipse( $img, $x + $w - $r, $y + $r, $h, $h, $color );
	}

	private function sparkle( GdImage $img, int $x, int $y, int $s, int $color ): void {
		$k = (int) ( $s * 0.22 );
		imagefilledpolygon( $img, array( $x, $y - $s, $x + $k, $y - $k, $x + $s, $y, $x + $k, $y + $k, $x, $y + $s, $x - $k, $y + $k, $x - $s, $y, $x - $k, $y - $k ), $color );
	}

	private function text_width( string $text, float $size, bool $bold ): int {
		$font = $bold ? $this->font_bold : $this->font;
		if ( ! $font ) {
			return strlen( remove_accents( $text ) ) * imagefontwidth( 5 );
		}
		$box = imagettfbbox( $size, 0, $font, $text );
		return (int) abs( $box[2] - $box[0] );
	}

	/** Chữ canh trái tại ($x, $y = đường chân chữ). */
	private function text_at( GdImage $img, string $text, float $size, int $x, int $y, int $color, bool $bold = false ): void {
		$font = $bold ? $this->font_bold : $this->font;
		if ( $font ) {
			imagettftext( $img, $size, 0, $x, $y, $color, $font, $text );
			return;
		}
		imagestring( $img, 5, $x, $y - imagefontheight( 5 ), remove_accents( $text ), $color );
	}

	/** Tiêu đề + phụ đề, tự xuống dòng trong $max_w. */
	private function text_block( GdImage $img, int $w, int $h, array $opts, int $y, float $size, int $cx = 0 ): void {
		$white = imagecolorallocate( $img, 255, 255, 255 );
		$mint  = imagecolorallocate( $img, ...self::PALETTE['mint'] );
		$cx    = $cx ?: (int) ( $w / 2 );
		$max_w = (int) ( ( $cx < $w / 2 ? $cx * 2 : $w ) * 0.86 );
		$lines = $this->wrap( (string) ( $opts['title'] ?? '' ), $size, $max_w, true );
		$y    -= (int) ( ( count( $lines ) - 1 ) * $size * 0.7 );
		foreach ( $lines as $line ) {
			$this->text( $img, $line, $size, $cx, $y, $white, true );
			$y += (int) ( $size * 1.4 );
		}
		if ( ! empty( $opts['subtitle'] ) ) {
			$this->text( $img, $opts['subtitle'], $size * 0.55, $cx, (int) ( $y + $size * 0.1 ), $mint );
		}
	}

	/** @return string[] */
	private function wrap( string $text, float $size, int $max_w, bool $bold ): array {
		if ( '' === $text || ! $this->font ) {
			return '' === $text ? array() : array( $text );
		}
		$lines = array();
		$line  = '';
		foreach ( preg_split( '/\s+/u', $text ) as $word ) {
			$try = '' === $line ? $word : $line . ' ' . $word;
			$box = imagettfbbox( $size, 0, $bold ? $this->font_bold : $this->font, $try );
			if ( '' !== $line && abs( $box[2] - $box[0] ) > $max_w ) {
				$lines[] = $line;
				$line    = $word;
			} else {
				$line = $try;
			}
		}
		$lines[] = $line;
		return $lines;
	}

	/** Chữ canh giữa tại ($cx, $cy = đường chân chữ). Không có font TTF → font GD (bỏ dấu). */
	private function text( GdImage $img, string $text, float $size, int $cx, int $cy, int $color, bool $bold = false ): void {
		if ( '' === $text ) {
			return;
		}
		$font = $bold ? $this->font_bold : $this->font;
		if ( $font ) {
			$box = imagettfbbox( $size, 0, $font, $text );
			imagettftext( $img, $size, 0, (int) ( $cx - ( $box[2] - $box[0] ) / 2 ), $cy, $color, $font, $text );
			return;
		}
		$text = remove_accents( $text );
		imagestring( $img, 5, (int) ( $cx - strlen( $text ) * imagefontwidth( 5 ) / 2 ), $cy - imagefontheight( 5 ), $text, $color );
	}

	private function find_font( array $candidates ): ?string {
		$candidates = (array) apply_filters( 'rhm_demo_font', $candidates );
		foreach ( $candidates as $path ) {
			if ( is_readable( $path ) ) {
				return $path;
			}
		}
		return null;
	}

	/* -----------------------------------------------------------------
	 * Lưu + đưa vào Media
	 * -------------------------------------------------------------- */

	private function save( GdImage $image, string $filename ): string {
		$upload = wp_upload_dir();
		if ( ! empty( $upload['error'] ) || ! wp_mkdir_p( $upload['path'] ) ) {
			return '';
		}
		$path = trailingslashit( $upload['path'] ) . wp_unique_filename( $upload['path'], $filename );
		imageinterlace( $image, true );
		return imagejpeg( $image, $path, 82 ) ? $path : '';
	}

	private function attach( string $file, string $slug, array $opts ): int {
		require_once ABSPATH . 'wp-admin/includes/image.php';

		$title = (string) ( $opts['title'] ?? $slug );
		$id    = wp_insert_attachment(
			array(
				'post_title'     => $title,
				'post_mime_type' => 'image/jpeg',
				'post_status'    => 'inherit',
			),
			$file,
			0,
			true
		);
		if ( is_wp_error( $id ) ) {
			wp_delete_file( $file );
			return 0;
		}

		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file ) );
		update_post_meta( $id, '_wp_attachment_image_alt', sanitize_text_field( $opts['alt'] ?? $title ) );
		update_post_meta( $id, '_rhm_demo_image', $slug );
		update_post_meta( $id, RHM_DEMO_META, 'image' );

		return (int) $id;
	}
}

/**
 * Ghép ảnh có kênh alpha với độ đậm $pct (0–100+): imagecopymerge của GD bỏ qua alpha nên tự làm.
 */
function rhm_demo_imagecopymerge_alpha( GdImage $dst, GdImage $src, int $x, int $y, int $opacity ): void {
	$w   = imagesx( $src );
	$h   = imagesy( $src );
	$tmp = imagecreatetruecolor( $w, $h );
	imagecopy( $tmp, $dst, 0, 0, $x, $y, $w, $h );
	imagecopy( $tmp, $src, 0, 0, 0, 0, $w, $h );
	imagecopymerge( $dst, $tmp, $x, $y, 0, 0, $w, $h, (int) round( max( 0, min( 127, $opacity ) ) / 127 * 100 ) );
	imagedestroy( $tmp );
}
