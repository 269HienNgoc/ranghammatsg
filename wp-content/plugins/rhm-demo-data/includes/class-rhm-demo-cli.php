<?php
/**
 * WP-CLI: wp rhm-demo generate | delete | status
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

final class RHM_Demo_CLI {

	/**
	 * Tạo dữ liệu mẫu (1 bản gốc + bản sao).
	 *
	 * ## OPTIONS
	 *
	 * [--posts=<n>]
	 * : Tổng số bài viết. Mặc định 100.
	 *
	 * [--doctors=<n>]
	 * : Tổng số hồ sơ bác sĩ. Mặc định 100.
	 *
	 * [--fresh]
	 * : Xóa bài viết/bác sĩ mẫu cũ trước (giữ ảnh).
	 *
	 * ## EXAMPLES
	 *
	 *     wp rhm-demo generate --posts=100 --doctors=100 --fresh
	 */
	public function generate( array $args, array $assoc ): void {
		$generator = new RHM_Demo_Generator( static fn( string $m ) => WP_CLI::log( $m ) );
		try {
			if ( WP_CLI\Utils\get_flag_value( $assoc, 'fresh', false ) ) {
				WP_CLI::log( sprintf( 'Đã xóa %d bài/hồ sơ mẫu cũ.', $generator->delete_content() ) );
			}
			$r = $generator->generate( absint( $assoc['posts'] ?? 100 ), absint( $assoc['doctors'] ?? 100 ) );
		} catch ( Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
		WP_CLI::success( sprintf( '%d bài viết, %d hồ sơ bác sĩ, %d ảnh — %s giây.', $r['posts'], $r['doctors'], $r['images'], $r['seconds'] ) );
	}

	/**
	 * Xóa toàn bộ dữ liệu mẫu (bài viết, bác sĩ, ảnh, term mẫu không còn dùng).
	 *
	 * [--yes]
	 * : Không hỏi lại.
	 */
	public function delete( array $args, array $assoc ): void {
		WP_CLI::confirm( 'Xóa toàn bộ dữ liệu mẫu?', $assoc );
		$r = ( new RHM_Demo_Generator() )->delete();
		WP_CLI::success( sprintf( 'Đã xóa %d bài/hồ sơ, %d ảnh, %d term.', $r['posts'], $r['images'], $r['terms'] ) );
		if ( $r['kept_terms'] ) {
			WP_CLI::warning( 'Giữ lại term đang dùng trong menu/bài thật: ' . implode( ', ', $r['kept_terms'] ) . '. Gỡ khỏi menu rồi chạy lại để dọn hết.' );
		}
	}

	/**
	 * Tạo 2 banner desktop (2048×800) + 2 banner mobile (828×1000) và gán vào Hero banner slide 1–2.
	 *
	 * [--overwrite]
	 * : Ghi đè slide đang có banner.
	 */
	public function hero( array $args, array $assoc ): void {
		try {
			$r = ( new RHM_Demo_Generator( static fn( string $m ) => WP_CLI::log( $m ) ) )->hero( (bool) WP_CLI\Utils\get_flag_value( $assoc, 'overwrite', false ) );
		} catch ( Throwable $e ) {
			WP_CLI::error( $e->getMessage() );
		}
		WP_CLI::success( $r['assigned'] ? 'Đã gán slide ' . implode( ', ', $r['assigned'] ) . '.' : 'Đã tạo ảnh; slide đã có banner nên chưa gán (dùng --overwrite).' );
	}

	/**
	 * Tạo 10 chuyên mục mẫu (nếu thiếu) và rải đều bài viết mẫu hiện có vào đó.
	 */
	public function distribute(): void {
		$r = ( new RHM_Demo_Generator() )->distribute();
		foreach ( $r as $name => $count ) {
			WP_CLI::log( sprintf( '%-22s %d bài', $name, $count ) );
		}
		WP_CLI::success( sprintf( 'Đã rải bài viết mẫu vào %d chuyên mục.', count( $r ) ) );
	}

	/** Đếm dữ liệu mẫu hiện có. */
	public function status(): void {
		$c = RHM_Demo_Generator::counts();
		WP_CLI::log( sprintf( 'Bài viết: %d · Bác sĩ: %d · Ảnh: %d', $c['posts'], $c['doctors'], $c['images'] ) );
	}
}
