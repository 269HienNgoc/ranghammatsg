<?php
/**
 * Tạo / xóa dữ liệu mẫu.
 *
 * - Bản gốc: 1 hồ sơ bác sĩ + 1 bài viết đầy đủ (ảnh đại diện, nội dung, field ACF).
 * - Bản sao: chép nguyên post + toàn bộ post meta (gồm meta ACF) + term của bản gốc bằng SQL gộp → nhanh,
 *   100 bản mất vài giây. Ảnh dùng chung, không nhân bản file.
 * - Meta ACF ghi theo đúng định dạng ACF lưu (value + "_name" => field key; repeater = số dòng + name_i_sub)
 *   nên ACF PRO đọc được ngay, kể cả khi lúc tạo ACF chưa bật.
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

final class RHM_Demo_Generator {

	/** Meta không chép sang bản sao. */
	private const SKIP_META = array( '_edit_lock', '_edit_last', '_wp_old_slug', '_wp_old_date', RHM_DEMO_META );

	/** Option lưu ID các term do plugin tạo (để chỉ xóa đúng term của mình). */
	private const TERMS_OPTION = 'rhm_demo_terms';

	/** @var callable|null */
	private $log;

	public function __construct( ?callable $log = null ) {
		$this->log = $log;
	}

	/**
	 * @return array{images:int, doctors:int, posts:int, seconds:float}
	 */
	public function generate( int $posts = 100, int $doctors = 100 ): array {
		$start = microtime( true );
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}
		wp_raise_memory_limit( 'admin' );

		if ( ! RHM_Demo_Images::available() ) {
			throw new RuntimeException( 'PHP chưa bật extension GD (cần để sinh ảnh mẫu).' );
		}
		if ( $doctors > 0 && ! post_type_exists( 'bac-si' ) ) {
			throw new RuntimeException( 'Chưa có post type "bac-si". Hãy kích hoạt theme Răng Hàm Mặt SG trước.' );
		}

		// 1. Ảnh.
		$this->log( 'Đang sinh ảnh mẫu (lần đầu mất khoảng 1 phút vì phải cắt đủ các size của theme)…' );
		$images = new RHM_Demo_Images();
		$img    = array();
		foreach ( RHM_Demo_Content::images() as $slug => [ $w, $h, $opts ] ) {
			$img[ $slug ] = $images->get( $slug, $w, $h, $opts );
			if ( ! $img[ $slug ] ) {
				throw new RuntimeException( sprintf( 'Không tạo được ảnh "%s". Kiểm tra quyền ghi thư mục wp-content/uploads.', $slug ) );
			}
		}

		wp_defer_term_counting( true );
		wp_suspend_cache_invalidation( true );

		try {
			// 2. Bác sĩ.
			$doctor_id = 0;
			if ( $doctors > 0 ) {
				$doctor_id = $this->create_master( 'bac-si', RHM_Demo_Content::doctor( $img ), 'rhm_doctor_', array(
					'videos'      => 'field_rhm_doctor_videos_',
					'case_videos' => 'field_rhm_doctor_case_videos_',
					'cases'       => 'field_rhm_doctor_case_',
				) );
				$this->log( sprintf( 'Đã tạo hồ sơ bác sĩ gốc #%d.', $doctor_id ) );
				$this->clone_many( $doctor_id, $doctors - 1 );
			} else {
				$doctor_id = (int) ( get_posts( array( 'post_type' => 'bac-si', 'posts_per_page' => 1, 'fields' => 'ids', 'no_found_rows' => true ) )[0] ?? 0 );
			}

			// 3. Bài viết.
			if ( $posts > 0 ) {
				$cats    = $this->ensure_terms( 'category', RHM_Demo_Content::CATEGORIES );
				$tags    = $this->ensure_terms( 'post_tag', array_combine( array_map( 'sanitize_title', RHM_Demo_Content::TAGS ), RHM_Demo_Content::TAGS ) );
				$post_id = $this->create_master( 'post', RHM_Demo_Content::post( $img, $doctor_id ), 'rhm_post_', array(
					'faq'        => 'field_rhm_post_faq_',
					'references' => 'field_rhm_post_references_',
				) );
				wp_set_object_terms( $post_id, array( $cats[0] ), 'category' );
				wp_set_object_terms( $post_id, $tags, 'post_tag' );
				$this->log( sprintf( 'Đã tạo bài viết gốc #%d.', $post_id ) );

				$this->clone_many( $post_id, $posts - 1 );
			}
		} finally {
			wp_suspend_cache_invalidation( false );
			wp_defer_term_counting( false );
		}

		wp_cache_flush();

		// Rải đều MỌI bài viết mẫu (kể cả bài tạo từ lần chạy trước) vào 10 chuyên mục.
		if ( $posts > 0 ) {
			$this->distribute();
		}

		// Banner trang chủ: chỉ điền slide còn trống, không đè banner thật.
		$hero = $this->hero( false );

		return array(
			'images'  => count( $img ) + 2 * count( $hero['slides'] ),
			'doctors' => max( 0, $doctors ),
			'posts'   => max( 0, $posts ),
			'seconds' => round( microtime( true ) - $start, 1 ),
		);
	}

	/**
	 * Xóa mọi bài viết / bác sĩ / ảnh do plugin tạo, và term do plugin tạo nếu không còn bài nào dùng.
	 *
	 * @return array{posts:int, images:int, terms:int}
	 */
	public function delete(): array {
		global $wpdb;
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_type FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID WHERE m.meta_key = %s",
				RHM_DEMO_META
			)
		);

		$this->unset_demo_hero();

		$result = array( 'posts' => 0, 'images' => 0, 'terms' => 0 );
		wp_defer_term_counting( true );
		foreach ( $rows as $row ) {
			if ( 'attachment' === $row->post_type ) {
				$result['images'] += wp_delete_attachment( (int) $row->ID, true ) ? 1 : 0;
			} else {
				$result['posts'] += wp_delete_post( (int) $row->ID, true ) ? 1 : 0;
			}
		}
		wp_defer_term_counting( false );

		$terms = (array) get_option( self::TERMS_OPTION, array() );
		foreach ( $terms as $taxonomy => $ids ) {
			foreach ( (array) $ids as $term_id ) {
				wp_update_term_count_now( array( (int) $term_id ), $taxonomy );
				$term = get_term( (int) $term_id, $taxonomy );
				if ( $term instanceof WP_Term && 0 === (int) $term->count && (int) get_option( 'default_category' ) !== $term->term_id ) {
					$result['terms'] += wp_delete_term( $term->term_id, $taxonomy ) ? 1 : 0;
				}
			}
		}
		delete_option( self::TERMS_OPTION );

		return $result;
	}

	/**
	 * Sinh banner hero trang chủ (mỗi slide: ảnh desktop 2048×800 + ảnh mobile 828×1000) và gán vào
	 * Customizer → Hero banner (theme mod rhm_hero_{n}_desktop / _mobile / _link).
	 *
	 * @param bool $overwrite true = ghi đè slide đang có ảnh; false = chỉ điền slide còn trống.
	 * @return array{slides: array<int, array{desktop:int, mobile:int}>, assigned: int[]}
	 */
	public function hero( bool $overwrite = false ): array {
		if ( ! RHM_Demo_Images::available() ) {
			throw new RuntimeException( 'PHP chưa bật extension GD (cần để sinh ảnh banner).' );
		}
		if ( function_exists( 'set_time_limit' ) ) {
			set_time_limit( 0 );
		}

		$images = new RHM_Demo_Images();
		$result = array( 'slides' => array(), 'assigned' => array() );

		foreach ( RHM_Demo_Content::hero_banners() as $n => $slide ) {
			$ids = array();
			foreach ( array( 'desktop', 'mobile' ) as $device ) {
				[ $slug, $w, $h, $opts ] = $slide[ $device ];
				$ids[ $device ]          = $images->get( $slug, $w, $h, $opts );
				if ( ! $ids[ $device ] ) {
					throw new RuntimeException( sprintf( 'Không tạo được ảnh "%s". Kiểm tra quyền ghi thư mục wp-content/uploads.', $slug ) );
				}
			}
			$result['slides'][ $n ] = $ids;
			$this->log( sprintf( 'Banner %d: desktop #%d, mobile #%d.', $n, $ids['desktop'], $ids['mobile'] ) );

			$current = absint( get_theme_mod( "rhm_hero_{$n}_desktop" ) );
			if ( ! $overwrite && $current && wp_attachment_is_image( $current ) && $current !== $ids['desktop'] ) {
				continue; // Slide đã có banner thật.
			}

			$term = get_term_by( 'name', $slide['link'], 'category' );
			set_theme_mod( "rhm_hero_{$n}_desktop", $ids['desktop'] );
			set_theme_mod( "rhm_hero_{$n}_mobile", $ids['mobile'] );
			set_theme_mod( "rhm_hero_{$n}_link", $term ? get_term_link( $term ) : '' );
			$result['assigned'][] = $n;
		}

		return $result;
	}

	/** Gỡ banner mẫu khỏi Customizer (slide đang dùng ảnh do plugin tạo). */
	private function unset_demo_hero(): void {
		for ( $n = 1; $n <= 5; $n++ ) {
			$id = absint( get_theme_mod( "rhm_hero_{$n}_desktop" ) );
			if ( $id && get_post_meta( $id, RHM_DEMO_META, true ) ) {
				remove_theme_mod( "rhm_hero_{$n}_desktop" );
				remove_theme_mod( "rhm_hero_{$n}_mobile" );
				remove_theme_mod( "rhm_hero_{$n}_link" );
			}
		}
	}

	/**
	 * Tạo (nếu thiếu) 10 chuyên mục mẫu rồi rải đều các bài viết mẫu hiện có vào đó, lần lượt theo thứ tự mới → cũ
	 * (bài 1 → mục 1, bài 2 → mục 2, …, bài 11 → mục 1). Mỗi bài thuộc đúng 1 chuyên mục mẫu.
	 *
	 * @return array<string, int> tên chuyên mục => số bài.
	 */
	public function distribute(): array {
		global $wpdb;

		$cats = $this->ensure_terms( 'category', RHM_Demo_Content::CATEGORIES );
		if ( ! $cats ) {
			return array();
		}

		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID
				WHERE m.meta_key = %s AND p.post_type = 'post' ORDER BY p.post_date DESC, p.ID DESC",
				RHM_DEMO_META
			)
		);

		wp_defer_term_counting( true );
		foreach ( $ids as $i => $id ) {
			wp_set_object_terms( (int) $id, array( $cats[ $i % count( $cats ) ] ), 'category' );
		}
		wp_defer_term_counting( false );
		wp_update_term_count_now( $cats, 'category' );

		$result = array();
		foreach ( $cats as $term_id ) {
			$term = get_term( $term_id, 'category' );
			if ( $term instanceof WP_Term ) {
				$result[ $term->name ] = (int) $term->count;
			}
		}
		$this->log( sprintf( 'Đã rải %d bài viết mẫu vào %d chuyên mục.', count( $ids ), count( $cats ) ) );

		return $result;
	}

	/** Xóa bài viết + bác sĩ mẫu nhưng giữ ảnh (tạo lại nhanh, không phải sinh ảnh lần nữa). */
	public function delete_content(): int {
		global $wpdb;
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT p.ID FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID WHERE m.meta_key = %s AND p.post_type <> 'attachment'",
				RHM_DEMO_META
			)
		);
		wp_defer_term_counting( true );
		foreach ( $ids as $id ) {
			wp_delete_post( (int) $id, true );
		}
		wp_defer_term_counting( false );
		return count( $ids );
	}

	/** @return array{posts:int, doctors:int, images:int} */
	public static function counts(): array {
		global $wpdb;
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.post_type, COUNT(*) AS total FROM {$wpdb->posts} p INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID WHERE m.meta_key = %s GROUP BY p.post_type",
				RHM_DEMO_META
			),
			OBJECT_K
		);
		return array(
			'posts'   => (int) ( $rows['post']->total ?? 0 ),
			'doctors' => (int) ( $rows['bac-si']->total ?? 0 ),
			'images'  => (int) ( $rows['attachment']->total ?? 0 ),
		);
	}

	/* -----------------------------------------------------------------
	 * Nội bộ
	 * -------------------------------------------------------------- */

	/**
	 * @param array  $data       ['post' => postarr, 'thumb' => id, 'acf' => [name => value]]
	 * @param string $prefix     tiền tố name field ACF (rhm_doctor_ / rhm_post_).
	 * @param array  $repeaters  name repeater => tiền tố key field con.
	 */
	private function create_master( string $post_type, array $data, string $prefix, array $repeaters ): int {
		$id = wp_insert_post(
			wp_slash(
				$data['post'] + array(
					'post_type'   => $post_type,
					'post_status' => 'publish',
					'post_author' => $this->author_id(),
				)
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			throw new RuntimeException( $id->get_error_message() );
		}

		set_post_thumbnail( $id, (int) $data['thumb'] );
		update_post_meta( $id, RHM_DEMO_META, 'master' );

		foreach ( $data['acf'] as $name => $value ) {
			$meta = $prefix . $name;
			$key  = 'field_' . $meta;

			if ( isset( $repeaters[ $name ] ) ) {
				update_post_meta( $id, $meta, count( $value ) );
				update_post_meta( $id, '_' . $meta, $key );
				foreach ( array_values( $value ) as $i => $row ) {
					foreach ( $row as $sub => $sub_value ) {
						update_post_meta( $id, "{$meta}_{$i}_{$sub}", wp_slash( (string) $sub_value ) );
						update_post_meta( $id, "_{$meta}_{$i}_{$sub}", $repeaters[ $name ] . $sub );
					}
				}
				continue;
			}

			// Gallery: ACF lưu mảng ID dạng chuỗi.
			$value = is_array( $value ) ? array_map( 'strval', $value ) : wp_slash( (string) $value );
			update_post_meta( $id, $meta, $value );
			update_post_meta( $id, '_' . $meta, $key );
		}

		return (int) $id;
	}

	/**
	 * Tạo $count bản sao của $source_id (tiêu đề thêm " #n", ngày lùi dần mỗi bản 6 giờ).
	 */
	private function clone_many( int $source_id, int $count, ?callable $after = null ): void {
		global $wpdb;
		if ( $count < 1 ) {
			return;
		}

		$source = get_post( $source_id );
		$meta   = $wpdb->get_results( $wpdb->prepare( "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d", $source_id ), ARRAY_A );
		$meta   = array_values( array_filter( $meta, static fn( array $row ): bool => ! in_array( $row['meta_key'], self::SKIP_META, true ) ) );
		$meta[] = array( 'meta_key' => RHM_DEMO_META, 'meta_value' => 'clone' );

		$terms = array();
		foreach ( get_object_taxonomies( $source->post_type ) as $taxonomy ) {
			$terms[ $taxonomy ] = wp_get_object_terms( $source_id, $taxonomy, array( 'fields' => 'ids' ) );
		}

		$base = strtotime( $source->post_date_gmt . ' UTC' );

		for ( $n = 2; $n <= $count + 1; $n++ ) {
			$time = $base - ( $n - 1 ) * 6 * HOUR_IN_SECONDS;
			$id   = wp_insert_post(
				wp_slash(
					array(
						'post_type'     => $source->post_type,
						'post_status'   => 'publish',
						'post_author'   => $source->post_author,
						'post_title'    => $source->post_title . ' #' . $n,
						'post_content'  => $source->post_content,
						'post_excerpt'  => $source->post_excerpt,
						'post_date'     => get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $time ) ),
						'post_date_gmt' => gmdate( 'Y-m-d H:i:s', $time ),
					)
				),
				true
			);
			if ( is_wp_error( $id ) ) {
				throw new RuntimeException( $id->get_error_message() );
			}

			// Bỏ meta do hook save_post vừa thêm (vd sao đánh giá ngẫu nhiên) → bản sao giống hệt bản gốc.
			$wpdb->delete( $wpdb->postmeta, array( 'post_id' => $id ), array( '%d' ) );
			foreach ( array_chunk( $meta, 200 ) as $chunk ) {
				$values = array();
				foreach ( $chunk as $row ) {
					$values[] = $wpdb->prepare( '(%d, %s, %s)', $id, $row['meta_key'], $row['meta_value'] );
				}
				$wpdb->query( "INSERT INTO {$wpdb->postmeta} (post_id, meta_key, meta_value) VALUES " . implode( ', ', $values ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- từng dòng đã prepare.
			}
			wp_cache_delete( $id, 'post_meta' );

			foreach ( $terms as $taxonomy => $ids ) {
				if ( $ids ) {
					wp_set_object_terms( $id, $ids, $taxonomy );
				}
			}
			if ( $after ) {
				$after( $id, $n );
			}

			if ( 0 === $n % 25 ) {
				$this->log( sprintf( '%s: đã nhân bản %d/%d.', $source->post_type, $n, $count + 1 ) );
			}
		}
	}

	/** @return int[] term ID theo đúng thứ tự $terms. */
	private function ensure_terms( string $taxonomy, array $terms ): array {
		$created = (array) get_option( self::TERMS_OPTION, array() );
		$ids     = array();
		foreach ( $terms as $slug => $name ) {
			// Dùng lại term có sẵn (theo slug, rồi theo tên) để không lỗi trùng tên / không tạo bản trùng.
			$term = get_term_by( 'slug', $slug, $taxonomy ) ?: get_term_by( 'name', $name, $taxonomy );
			if ( $term ) {
				$ids[] = (int) $term->term_id;
				continue;
			}
			$new = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
			if ( is_wp_error( $new ) ) {
				if ( 'term_exists' === $new->get_error_code() ) {
					$ids[] = (int) $new->get_error_data();
				}
				continue;
			}
			$ids[]                  = (int) $new['term_id'];
			$created[ $taxonomy ][] = (int) $new['term_id'];
		}
		update_option( self::TERMS_OPTION, $created, false );
		return $ids;
	}

	private function author_id(): int {
		$id = get_current_user_id();
		if ( $id ) {
			return $id;
		}
		$admins = get_users( array( 'role' => 'administrator', 'number' => 1, 'fields' => 'ID' ) );
		return (int) ( $admins[0] ?? 1 );
	}

	private function log( string $message ): void {
		if ( $this->log ) {
			( $this->log )( $message );
		}
	}
}
