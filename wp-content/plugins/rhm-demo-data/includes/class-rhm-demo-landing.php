<?php
/**
 * Trang landing mẫu "Giới thiệu" (Page Template page-templates/tpl-gioi-thieu.php của theme): đủ mọi section
 * của chủ đề, nội dung theo thiết kế trang Giới thiệu, ảnh sinh bằng GD.
 *
 * - Field khớp theme: themeranghammatsg/inc/landing/ (Flexible Content "rhm_landing_sections", chủ đề slug "about").
 * - Ghi meta theo đúng định dạng ACF lưu (value + "_name" => field key; flexible = mảng tên layout;
 *   repeater = số dòng + name_i_sub) → ACF PRO đọc được ngay, kể cả khi lúc tạo ACF chưa bật.
 * - Chạy lại: cập nhật đúng trang mẫu cũ (meta _rhm_demo = landing), không tạo trang trùng.
 * - Icon (highlights, cards, ô nổi bật) là tên file trong themeranghammatsg/assets/icons/landing/.
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

final class RHM_Demo_Landing {

	public const TEMPLATE = 'page-templates/tpl-gioi-thieu.php';

	/** Slug chủ đề trong rhm_landing_templates() của theme → tiền tố key field ACF. */
	private const THEME_SLUG = 'about';

	private const FIELD = 'rhm_landing_sections';

	private const MARK = 'landing';

	/** Ảnh cần sinh: slug => [rộng, cao, tùy chọn vẽ] (cùng định dạng RHM_Demo_Content::images()). */
	public static function images(): array {
		$photo = static fn( string $title, string $from = 'primary', string $to = 'deep', string $style = 'photo' ): array => array(
			'style' => $style,
			'from'  => $from,
			'to'    => $to,
			'title' => $title,
			'alt'   => $title,
		);

		$list = array(
			'landing-hero'      => array( 1600, 1000, array( 'style' => 'portrait', 'from' => 'mint', 'to' => 'primary', 'title' => 'Nụ cười khỏe đẹp', 'subtitle' => 'Răng Hàm Mặt Sài Gòn · Dữ liệu mẫu', 'alt' => 'Khách hàng mỉm cười tại Nha khoa Răng Hàm Mặt Sài Gòn' ) ),
			'landing-reception' => array( 900, 675, $photo( 'Quầy lễ tân phòng khám', 'teal' ) ),
			'landing-license'   => array( 600, 800, array( 'style' => 'cert', 'title' => 'Giấy phép hoạt động', 'subtitle' => 'Sở Y tế TP. Hồ Chí Minh', 'alt' => 'Giấy phép hoạt động khám chữa bệnh' ) ),
			'landing-promo-1'   => array( 800, 500, $photo( 'All-on-4', 'teal', 'primary' ) ),
			'landing-promo-2'   => array( 800, 500, $photo( 'Khay niềng trong suốt', 'primary', 'teal' ) ),
			'landing-logo'      => array( 360, 84, array( 'style' => 'photo', 'from' => 'deep', 'to' => 'teal', 'title' => 'invisalign', 'alt' => 'Logo Invisalign' ) ),
			'landing-sterile'   => array( 900, 600, $photo( 'Nồi hấp tiệt khuẩn EN 13060', 'deep', 'teal' ) ),
			'landing-material'  => array( 900, 600, $photo( 'Vật liệu chính hãng', 'green', 'teal' ) ),
			'landing-facility'  => array( 1200, 900, $photo( 'Phòng điều trị hiện đại', 'primary', 'teal' ) ),
		);
		foreach ( array( 'Trồng răng Implant', 'Niềng răng', 'Bọc răng sứ', 'Tẩy trắng răng', 'Nha khoa tổng quát' ) as $i => $title ) {
			$list[ 'landing-service-' . ( $i + 1 ) ] = array( 640, 400, $photo( $title, $i % 2 ? 'teal' : 'primary' ) );
		}
		foreach ( array( 'CT Cone Beam', 'iTero 5D Plus', 'Laser Pioon', 'Kính hiển vi', 'Piezotome', 'Star Pen' ) as $i => $title ) {
			$list[ 'landing-tech-' . ( $i + 1 ) ] = array( 400, 400, $photo( $title, $i % 2 ? 'teal' : 'primary' ) );
		}
		foreach ( array( 'Sảnh chờ', 'Phòng điều trị', 'Phòng X-quang', 'Khu tiệt khuẩn' ) as $i => $title ) {
			$list[ 'landing-gallery-' . ( $i + 1 ) ] = array( 1200, 900, $photo( $title, $i % 2 ? 'teal' : 'primary' ) );
		}
		return $list;
	}

	/**
	 * Các section theo thứ tự trên trang (khớp layout + field của theme). Ảnh = ID trong $img.
	 *
	 * @param array<string, int> $img     slug ảnh => attachment ID.
	 * @param int[]              $doctors ID hồ sơ bác sĩ (có ảnh đại diện) cho section "Bác sĩ tiêu biểu".
	 */
	public static function sections( array $img, array $doctors ): array {
		$rows = array(
			array(
				'acf_fc_layout' => 'hero',
				'eyebrow'       => '',
				'title'         => 'Nha khoa Răng Hàm Mặt Sài Gòn – Vì sức khỏe răng miệng bền lâu',
				'lead'          => 'Thương hiệu đặt sức khỏe lên trên hết, cam kết không chạy theo lợi nhuận mà sử dụng các phương pháp an toàn, hiệu quả cho người bệnh.',
				'description'   => 'Với định hướng nâng cao sức khỏe răng miệng và kiến tạo nụ cười khỏe đẹp cho cộng đồng, đặc biệt là người dân tại TP.HCM, Cần Thơ, Mỹ Tho, Rạch Giá và các khu vực lân cận, Nha Khoa Răng Hàm Mặt Sài Gòn đầu tư mạnh mẽ về đội ngũ bác sĩ, trang thiết bị hiện đại và quy trình kiểm soát vô khuẩn.',
				'image'         => $img['landing-hero'],
				'image_mobile'  => '',
				'booking'       => false,
				'button_text'   => '',
				'button_link'   => '',
			),
			array(
				'acf_fc_layout' => 'highlights',
				'items'         => array(
					array( 'icon' => 'doi-ngu', 'label' => 'Đội ngũ bác sĩ giỏi' ),
					array( 'icon' => 'rang', 'label' => 'Implant & chỉnh nha' ),
					array( 'icon' => 'khien', 'label' => 'Trang thiết bị hiện đại' ),
					array( 'icon' => 'tim', 'label' => 'An toàn điều trị' ),
				),
			),
			array(
				'acf_fc_layout' => 'cards',
				'title'         => 'Sứ mệnh – Tầm nhìn – Giá trị cốt lõi',
				'eyebrow'       => '',
				'description'   => '',
				'image'         => $img['landing-reception'],
				'items'         => array(
					array( 'icon' => 'muc-tieu', 'title' => 'Sứ mệnh', 'text' => 'Hướng đến chăm sóc và bảo vệ sức khỏe răng miệng cộng đồng bằng các giải pháp an toàn, chất lượng và phù hợp.' ),
					array( 'icon' => 'mat', 'title' => 'Tầm nhìn', 'text' => 'Trở thành địa chỉ nha khoa uy tín, được khách hàng tin tưởng lựa chọn.' ),
					array( 'icon' => 'kim-cuong', 'title' => 'Giá trị cốt lõi', 'text' => 'Chuyên môn cao, tận tâm, an toàn, minh bạch và trách nhiệm.' ),
				),
			),
			array(
				'acf_fc_layout' => 'services',
				'title'         => 'Đa dạng dịch vụ nha khoa',
				'description'   => 'Từ điều trị cơ bản đến chuyên sâu, đáp ứng nhu cầu đa dạng của khách hàng.',
				'items'         => array_map(
					static fn( int $n, string $title ): array => array( 'image' => $img[ 'landing-service-' . $n ], 'title' => $title, 'link' => '/' ),
					array( 1, 2, 3, 4, 5 ),
					array( 'Trồng răng Implant', 'Niềng răng', 'Bọc răng sứ', 'Tẩy trắng răng', 'Nha khoa tổng quát' )
				),
			),
			array(
				'acf_fc_layout' => 'doctor-cards',
				'title'         => 'Đội ngũ bác sĩ chuyên môn cao',
				'description'   => 'Nha Khoa Răng Hàm Mặt Sài Gòn quy tụ đội ngũ bác sĩ giàu kinh nghiệm, đặc biệt chuyên sâu về cấy ghép Implant và niềng răng – chỉnh nha.',
				'items'         => array_map(
					static fn( int $id, string $text ): array => array( 'doctor' => $id, 'text' => $text ),
					array_slice( $doctors, 0, 2 ),
					array_slice(
						array(
							'Bác sĩ có nhiều năm kinh nghiệm trong lĩnh vực Implant và chỉnh nha, từng học tập tại Harvard và đạt nhiều chứng chỉ chuyên môn quốc tế.',
							'Bác sĩ chuyên sâu Implant toàn hàm All-on-4, thực hiện thành công nhiều ca trồng răng phức tạp.',
						),
						0,
						min( 2, count( $doctors ) )
					)
				),
			),
			array(
				'acf_fc_layout' => 'feature',
				'width'         => '4',
				'title'         => 'Giấy phép hoạt động',
				'image'         => $img['landing-license'],
				'text'          => 'Phòng khám được cấp giấy phép hoạt động bởi Sở Y tế, đảm bảo tiêu chuẩn khám chữa bệnh.',
				'button_text'   => '',
				'button_link'   => '',
			),
			array(
				'acf_fc_layout' => 'tiles',
				'width'         => '6',
				'title'         => 'Công nghệ hiện đại, điều trị chính xác',
				'items'         => array_map(
					static fn( int $n, string $label ): array => array( 'image' => $img[ 'landing-tech-' . $n ], 'label' => $label ),
					array( 1, 2, 3, 4, 5, 6 ),
					array( "CT\nCone Beam", "iTero\n5D Plus", "Laser\nPioon", "Kính hiển vi\nnha khoa", 'Piezotome', 'Star Pen' )
				),
			),
			array(
				'acf_fc_layout' => 'promo',
				'width'         => '5',
				'title'         => 'Cấy ghép răng toàn hàm All-on-4',
				'subtitle'      => 'Theo tiêu chuẩn Paulo Malo',
				'image'         => $img['landing-promo-1'],
				'logo'          => '',
				'link'          => '/',
			),
			array(
				'acf_fc_layout' => 'promo',
				'width'         => '5',
				'title'         => 'Invisalign Diamond Provider',
				'subtitle'      => 'Nhà cung cấp phương pháp chỉnh nha Invisalign hạng Diamond',
				'image'         => $img['landing-promo-2'],
				'logo'          => $img['landing-logo'],
				'link'          => '',
			),
			array(
				'acf_fc_layout' => 'feature',
				'width'         => '5',
				'title'         => 'Hệ thống vô trùng chuẩn Châu Âu EN 13060',
				'image'         => $img['landing-sterile'],
				'text'          => 'Quy trình vệ sinh, khử khuẩn và tiệt khuẩn dụng cụ theo tiêu chuẩn Châu Âu, đảm bảo an toàn trong từng điều trị.',
				'button_text'   => '',
				'button_link'   => '',
			),
			array(
				'acf_fc_layout' => 'feature',
				'width'         => '5',
				'title'         => 'Vật liệu chính hãng – Bảo hành rõ ràng',
				'image'         => $img['landing-material'],
				'text'          => 'Hợp tác cùng các thương hiệu uy tín như Straumann, Osstem, Invisalign... với chính sách bảo hành minh bạch.',
				'button_text'   => '',
				'button_link'   => '',
			),
			array(
				'acf_fc_layout'       => 'intro',
				'title'               => 'Đầu tư mạnh mẽ về cơ sở vật chất',
				'eyebrow'             => '',
				'content'             => '<p>Không gian hiện đại, thiết bị tiên tiến cùng môi trường khám chẩn sạch sẽ, mang đến trải nghiệm dịch vụ tốt nhất cho khách hàng.</p>',
				'image'               => $img['landing-facility'],
				'video'               => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
				'image_position'      => 'left',
				'button_text'         => '',
				'button_link'         => '',
				'callout_icon'        => 'heo-dat',
				'callout_title'       => 'Chi phí hợp lý, chính sách rõ ràng',
				'callout_text'        => 'Mức chi phí phù hợp, minh bạch, hỗ trợ trả góp và chính sách bảo hành rõ ràng.',
				'callout_button_text' => 'Tư vấn chi tiết',
				'callout_button_link' => '#dat-lich',
			),
			// Các section không có trong thiết kế: có sẵn để content xem giao diện, tắt "Hiển thị" nếu không dùng.
			array(
				'acf_fc_layout' => 'stats',
				'title'         => 'Những con số ấn tượng',
				'items'         => array(
					array( 'number' => '15+', 'label' => 'Năm kinh nghiệm' ),
					array( 'number' => '20.000+', 'label' => 'Khách hàng tin chọn' ),
					array( 'number' => '6.000+', 'label' => 'Ca Implant thành công' ),
					array( 'number' => '4', 'label' => 'Cơ sở trên toàn quốc' ),
				),
			),
			array(
				'acf_fc_layout' => 'timeline',
				'eyebrow'       => 'Hành trình',
				'title'         => 'Chặng đường phát triển',
				'items'         => array(
					array( 'year' => '2010', 'title' => 'Thành lập phòng khám', 'text' => 'Khởi đầu với 3 ghế nha và đội ngũ 5 bác sĩ tại TP.HCM.' ),
					array( 'year' => '2015', 'title' => 'Mở rộng dịch vụ Implant', 'text' => 'Đầu tư CT Cone Beam, triển khai cấy ghép Implant và All-on-4.' ),
					array( 'year' => '2020', 'title' => 'Mở chi nhánh miền Tây', 'text' => 'Có mặt tại Cần Thơ, Mỹ Tho và Rạch Giá.' ),
					array( 'year' => '2025', 'title' => 'Invisalign Diamond Provider', 'text' => 'Đạt hạng Diamond trong hệ thống chỉnh nha Invisalign.' ),
				),
			),
			array(
				'acf_fc_layout' => 'gallery',
				'eyebrow'       => '',
				'title'         => 'Không gian phòng khám',
				'description'   => 'Không gian sạch sẽ, hiện đại, thân thiện với mọi khách hàng.',
				'images'        => array( $img['landing-gallery-1'], $img['landing-gallery-2'], $img['landing-gallery-3'], $img['landing-gallery-4'] ),
			),
			array(
				'acf_fc_layout' => 'doctors',
				'eyebrow'       => '',
				'title'         => '',
			),
			array(
				'acf_fc_layout' => 'faq',
				'title'         => '',
				'items'         => array(
					array( 'question' => 'Phòng khám làm việc vào thời gian nào?', 'answer' => '<p>Phòng khám làm việc từ <strong>8:00 đến 20:00</strong> tất cả các ngày trong tuần, kể cả Chủ nhật.</p>' ),
					array( 'question' => 'Có hỗ trợ trả góp chi phí điều trị không?', 'answer' => '<p>Có. Phòng khám hỗ trợ trả góp 0% lãi suất qua thẻ tín dụng cho các dịch vụ Implant, niềng răng và bọc sứ.</p>' ),
					array( 'question' => 'Dụng cụ được tiệt khuẩn thế nào?', 'answer' => '<p>Toàn bộ dụng cụ được tiệt khuẩn bằng nồi hấp chuẩn Châu Âu EN 13060 và đóng gói riêng cho từng khách hàng.</p>' ),
				),
			),
			array(
				'acf_fc_layout' => 'editor',
				'narrow'        => true,
			),
			array(
				'acf_fc_layout' => 'booking',
				'title'         => 'Đặt lịch khám ngay hôm nay',
				'text'          => '',
				'anchor'        => 'dat-lich',
			),
		);

		foreach ( $rows as &$row ) {
			$row['show']   = true;
			$row['anchor'] = $row['anchor'] ?? '';
		}
		unset( $row );

		return $rows;
	}

	/** Nội dung ô soạn thảo chính (in ở section "Nội dung trình soạn thảo"). */
	public static function content(): string {
		return <<<HTML
<h2>Cam kết của chúng tôi</h2>
<p>Nha khoa Răng Hàm Mặt Sài Gòn luôn đặt <strong>an toàn và hiệu quả điều trị</strong> lên hàng đầu. Mỗi kế hoạch điều trị được bác sĩ tư vấn rõ ràng về phương pháp, thời gian và chi phí trước khi thực hiện.</p>
<ul>
<li>Thăm khám và tư vấn miễn phí cùng bác sĩ chuyên khoa.</li>
<li>Báo giá minh bạch, không phát sinh chi phí ẩn.</li>
<li>Chính sách bảo hành rõ ràng cho từng dịch vụ.</li>
</ul>
<p><em>Đây là nội dung mẫu do plugin RHM Demo Data tạo.</em></p>
HTML;
	}

	/**
	 * Tạo / cập nhật trang landing mẫu. Trả về ID trang.
	 *
	 * @param callable|null $log Ghi tiến độ.
	 */
	public static function create( RHM_Demo_Images $images, int $author, ?callable $log = null ): int {
		if ( ! isset( wp_get_theme()->get_page_templates( null, 'page' )[ self::TEMPLATE ] ) ) {
			throw new RuntimeException( 'Theme đang bật chưa có template "Giới thiệu" (page-templates/tpl-gioi-thieu.php). Hãy cập nhật theme Răng Hàm Mặt SG bản có landing page.' );
		}

		$img = array();
		foreach ( self::images() as $slug => [ $w, $h, $opts ] ) {
			$img[ $slug ] = $images->get( $slug, $w, $h, $opts );
			if ( ! $img[ $slug ] ) {
				throw new RuntimeException( sprintf( 'Không tạo được ảnh "%s". Kiểm tra quyền ghi thư mục wp-content/uploads.', $slug ) );
			}
		}
		if ( $log ) {
			$log( sprintf( 'Landing: đã có %d ảnh mẫu.', count( $img ) ) );
		}

		$page = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'Giới thiệu (dữ liệu mẫu)',
			'post_name'    => 'gioi-thieu-mau',
			'post_content' => self::content(),
			'post_author'  => $author,
		);
		$id = self::existing();
		if ( $id ) {
			$page['ID'] = $id;
			unset( $page['post_name'] ); // Giữ slug hiện tại (content có thể đã đổi).
		}
		$id = wp_insert_post( wp_slash( $page ), true );
		if ( is_wp_error( $id ) ) {
			throw new RuntimeException( $id->get_error_message() );
		}

		update_post_meta( $id, '_wp_page_template', self::TEMPLATE );
		update_post_meta( $id, RHM_DEMO_META, self::MARK );
		self::write_sections( (int) $id, self::sections( $img, self::doctor_ids() ) );

		if ( $log ) {
			$log( sprintf( 'Landing: trang #%d — %s', $id, get_permalink( $id ) ) );
		}
		return (int) $id;
	}

	/** ID trang landing mẫu đã tạo trước đó (0 nếu chưa có). */
	public static function existing(): int {
		$ids = get_posts(
			array(
				'post_type'      => 'page',
				'post_status'    => 'any',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_key'       => RHM_DEMO_META, // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => self::MARK,    // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		return (int) ( $ids[0] ?? 0 );
	}

	/** 2 hồ sơ bác sĩ đã xuất bản có ảnh đại diện: ưu tiên hồ sơ mẫu, thiếu thì lấy hồ sơ thật. */
	private static function doctor_ids(): array {
		if ( ! post_type_exists( 'bac-si' ) ) {
			return array();
		}
		$query = static fn( array $extra ): array => array_map(
			'intval',
			get_posts(
				array(
					'post_type'      => 'bac-si',
					'post_status'    => 'publish',
					'posts_per_page' => 2,
					'fields'         => 'ids',
					'no_found_rows'  => true,
					'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' ),
					'meta_query'     => array_merge( array( array( 'key' => '_thumbnail_id', 'compare' => 'EXISTS' ) ), $extra ), // phpcs:ignore WordPress.DB.SlowDBQuery
				)
			)
		);
		$ids = $query( array( array( 'key' => RHM_DEMO_META, 'value' => 'master' ) ) );
		$ids = array_merge( $ids, $query( array( array( 'key' => RHM_DEMO_META, 'compare' => 'EXISTS' ) ) ) );
		$ids = array_merge( $ids, $query( array() ) );
		return array_slice( array_values( array_unique( $ids ) ), 0, 2 );
	}

	/**
	 * Ghi Flexible Content theo định dạng ACF lưu trong postmeta (xóa dữ liệu section cũ trước).
	 * Key field = field_rhm_landing_<slug chủ đề>_<layout>_<field> (themeranghammatsg/inc/landing/fields.php).
	 */
	private static function write_sections( int $id, array $rows ): void {
		global $wpdb;
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$wpdb->postmeta} WHERE post_id = %d AND (meta_key LIKE %s OR meta_key LIKE %s)",
				$id,
				$wpdb->esc_like( self::FIELD ) . '%',
				$wpdb->esc_like( '_' . self::FIELD ) . '%'
			)
		);
		wp_cache_delete( $id, 'post_meta' );

		$put = static function ( string $name, $value, string $key ) use ( $id ): void {
			update_post_meta( $id, $name, is_string( $value ) ? wp_slash( $value ) : $value );
			update_post_meta( $id, '_' . $name, $key );
		};
		$scalar = static fn( $value ): string => is_bool( $value ) ? ( $value ? '1' : '0' ) : (string) $value;

		$put( self::FIELD, array_column( $rows, 'acf_fc_layout' ), 'field_rhm_landing_' . self::THEME_SLUG . '_sections' );

		foreach ( $rows as $i => $row ) {
			$prefix = 'field_rhm_landing_' . self::THEME_SLUG . '_' . $row['acf_fc_layout'] . '_';
			foreach ( $row as $sub => $value ) {
				if ( 'acf_fc_layout' === $sub ) {
					continue;
				}
				$name = self::FIELD . "_{$i}_{$sub}";

				// Repeater: số dòng + từng field con.
				if ( is_array( $value ) && $value && is_array( reset( $value ) ) ) {
					$put( $name, (string) count( $value ), $prefix . $sub );
					foreach ( array_values( $value ) as $j => $item ) {
						foreach ( $item as $item_sub => $item_value ) {
							$put( "{$name}_{$j}_{$item_sub}", $scalar( $item_value ), $prefix . $sub . '_' . $item_sub );
						}
					}
					continue;
				}

				// Repeater / gallery rỗng → '' (ACF đọc là 0 dòng / không ảnh). Gallery: mảng ID dạng chuỗi. Còn lại: giá trị đơn.
				if ( is_array( $value ) ) {
					$put( $name, $value ? array_map( 'strval', $value ) : '', $prefix . $sub );
					continue;
				}
				$put( $name, $scalar( $value ), $prefix . $sub );
			}
		}
	}
}
