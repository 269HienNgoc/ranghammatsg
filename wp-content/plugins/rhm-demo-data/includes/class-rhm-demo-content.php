<?php
/**
 * Nội dung mẫu: 1 bài viết + 1 hồ sơ bác sĩ, khớp đúng field ACF của theme
 * (themeranghammatsg/inc/post-fields.php, inc/doctor-fields.php).
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

final class RHM_Demo_Content {

	/** 10 chuyên mục mẫu: bài viết được rải đều lần lượt vào từng mục (100 bài → mỗi mục 10 bài). */
	public const CATEGORIES = array(
		'nieng-rang'          => 'Niềng răng',
		'trong-rang-implant'  => 'Trồng răng Implant',
		'rang-su-tham-my'     => 'Răng sứ thẩm mỹ',
		'tay-trang-rang'      => 'Tẩy trắng răng',
		'nho-rang-khon'       => 'Nhổ răng khôn',
		'dieu-tri-tuy'        => 'Điều trị tủy',
		'nha-chu'             => 'Nha chu',
		'nha-khoa-tre-em'     => 'Nha khoa trẻ em',
		'phau-thuat-ham-mat'  => 'Phẫu thuật hàm mặt',
		'cham-soc-rang-mieng' => 'Chăm sóc răng miệng',
	);

	public const TAGS = array( 'Nha khoa', 'Chăm sóc răng miệng', 'Niềng răng trong suốt' );

	/** Danh sách ảnh cần sinh: slug => [rộng, cao, tùy chọn vẽ]. */
	public static function images(): array {
		$list = array(
			// Bài viết.
			'post-featured'   => array( 1600, 1000, array( 'style' => 'photo', 'title' => 'Niềng răng trong suốt Invisalign', 'subtitle' => 'Răng Hàm Mặt SG · Dữ liệu mẫu', 'alt' => 'Niềng răng trong suốt Invisalign tại Răng Hàm Mặt SG' ) ),
			'post-inline-1'   => array( 1200, 800, array( 'style' => 'photo', 'from' => 'teal', 'title' => 'Quy trình niềng răng 5 bước', 'alt' => 'Quy trình niềng răng trong suốt 5 bước' ) ),
			'post-inline-2'   => array( 1200, 800, array( 'style' => 'photo', 'from' => 'green', 'to' => 'teal', 'title' => 'Vệ sinh khay niềng đúng cách', 'alt' => 'Hướng dẫn vệ sinh khay niềng' ) ),
			// Bác sĩ.
			'doctor-portrait' => array( 1200, 1500, array( 'style' => 'portrait', 'title' => 'BS. Nguyễn Minh Anh', 'subtitle' => 'Chuyên khoa Chỉnh nha', 'alt' => 'Chân dung BS. Nguyễn Minh Anh' ) ),
			'doctor-banner-d' => array( 2048, 768, array( 'style' => 'banner', 'title' => 'Nụ cười chuẩn, tự tin mỗi ngày', 'subtitle' => 'BS. Nguyễn Minh Anh · 15 năm kinh nghiệm', 'alt' => 'Banner BS. Nguyễn Minh Anh' ) ),
			'doctor-banner-m' => array( 828, 1000, array( 'style' => 'banner', 'title' => 'Nụ cười chuẩn, tự tin mỗi ngày', 'subtitle' => 'BS. Nguyễn Minh Anh', 'alt' => 'Banner BS. Nguyễn Minh Anh' ) ),
		);
		foreach ( array( 'Thăm khám tổng quát', 'Lấy dấu kỹ thuật số', 'Hội chẩn ca khó', 'Đào tạo chuyên môn' ) as $i => $title ) {
			$list[ 'doctor-gallery-' . ( $i + 1 ) ] = array( 1200, 800, array( 'style' => 'photo', 'from' => $i % 2 ? 'teal' : 'primary', 'title' => $title, 'alt' => $title ) );
		}
		foreach ( array( array( 'Bằng Bác sĩ Răng Hàm Mặt', 'Đại học Y Dược TP. Hồ Chí Minh' ), array( 'Bằng Chuyên khoa I', 'Chuyên ngành Chỉnh nha' ), array( 'Thạc sĩ Y học', 'Răng Hàm Mặt' ) ) as $i => [ $title, $sub ] ) {
			$list[ 'doctor-diploma-' . ( $i + 1 ) ] = array( 1200, 850, array( 'style' => 'cert', 'title' => $title, 'subtitle' => $sub, 'alt' => $title ) );
		}
		foreach ( array( array( 'Chứng nhận Invisalign', 'Invisalign Certified Provider' ), array( 'Chứng chỉ Implant', 'Cấy ghép Implant nâng cao' ), array( 'Chứng nhận Hội nghị', 'Hội nghị Chỉnh nha Châu Á' ), array( 'Giải thưởng', 'Bác sĩ được yêu thích' ) ) as $i => [ $title, $sub ] ) {
			$list[ 'doctor-cert-' . ( $i + 1 ) ] = array( 1200, 850, array( 'style' => 'cert', 'title' => $title, 'subtitle' => $sub, 'alt' => $title ) );
		}
		foreach ( array( 'Gặp gỡ BS. Minh Anh', 'Một ngày làm việc của bác sĩ', 'Ca niềng răng hô', 'Ca trồng Implant toàn hàm' ) as $i => $title ) {
			$list[ 'video-' . ( $i + 1 ) ] = array( 1280, 720, array( 'style' => 'video', 'from' => $i < 2 ? 'primary' : 'teal', 'title' => $title, 'alt' => $title ) );
		}
		foreach ( array( 'Chị Lan', 'Anh Tuấn', 'Chị Hoa' ) as $i => $name ) {
			$n = $i + 1;
			$list[ 'case-portrait-' . $n ] = array( 600, 600, array( 'style' => 'portrait', 'from' => 'teal', 'title' => $name, 'alt' => 'Khách hàng ' . $name ) );
			$list[ 'case-before-' . $n ]   = array( 1000, 750, array( 'style' => 'before', 'alt' => $name . ' trước điều trị' ) );
			$list[ 'case-after-' . $n ]    = array( 1000, 750, array( 'style' => 'after', 'alt' => $name . ' sau điều trị' ) );
		}
		return $list;
	}

	/**
	 * Banner hero trang chủ (Customizer → Hero banner): mỗi slide 1 ảnh desktop 2048×800 + 1 ảnh mobile dọc 828×1000.
	 *
	 * @return array<int, array{desktop:array, mobile:array, link:string}> slide => [slug, rộng, cao, opts].
	 */
	public static function hero_banners(): array {
		$slides = array(
			array(
				'from'  => 'primary',
				'to'    => 'deep',
				'badge' => 'ƯU ĐÃI THÁNG NÀY',
				'title' => 'Niềng răng trong suốt Invisalign',
				'sub'   => 'Giảm đến 30% chi phí · Trả góp 0% lãi suất',
				'cta'   => 'Đặt lịch tư vấn miễn phí',
				'note'  => 'Hotline: 1900 xxxx · Dữ liệu mẫu',
				'link'  => 'Niềng răng',
			),
			array(
				'from'  => 'teal',
				'to'    => 'primary',
				'badge' => 'CÔNG NGHỆ ĐỨC – THỤY SĨ',
				'title' => 'Trồng răng Implant không đau',
				'sub'   => 'Phục hồi răng mất vĩnh viễn · Bảo hành trọn đời',
				'cta'   => 'Nhận báo giá ngay',
				'note'  => 'Bác sĩ hơn 15 năm kinh nghiệm · Dữ liệu mẫu',
				'link'  => 'Trồng răng Implant',
			),
		);

		$result = array();
		foreach ( $slides as $i => $s ) {
			$n    = $i + 1;
			$opts = array(
				'style'    => 'hero',
				'from'     => $s['from'],
				'to'       => $s['to'],
				'badge'    => $s['badge'],
				'title'    => $s['title'],
				'subtitle' => $s['sub'],
				'cta'      => $s['cta'],
				'note'     => $s['note'],
				'alt'      => $s['title'] . ' – ' . $s['sub'],
			);
			$result[ $n ] = array(
				'desktop' => array( 'hero-' . $n . '-desktop', 2048, 800, $opts ),
				'mobile'  => array( 'hero-' . $n . '-mobile', 828, 1000, $opts ),
				'link'    => $s['link'], // Tên chuyên mục: link tới trang chuyên mục (nếu có).
			);
		}
		return $result;
	}

	/* -----------------------------------------------------------------
	 * Bác sĩ
	 * -------------------------------------------------------------- */

	public static function doctor( array $img ): array {
		$content = <<<HTML
<p><strong>BS. Nguyễn Minh Anh</strong> có hơn 15 năm kinh nghiệm trong lĩnh vực chỉnh nha và phục hình răng. Bác sĩ trực tiếp điều trị hơn 3.000 ca niềng răng mắc cài và niềng răng trong suốt, từ ca đơn giản đến các ca sai khớp cắn phức tạp.</p>
<p>Với triết lý <em>"điều trị đúng – đủ – an toàn"</em>, bác sĩ luôn dành thời gian giải thích kỹ phác đồ, ưu nhược điểm của từng phương án để khách hàng chủ động lựa chọn. Mỗi kế hoạch điều trị đều được mô phỏng 3D trước khi bắt đầu.</p>
<p>Ngoài công việc lâm sàng, bác sĩ còn tham gia giảng dạy và là báo cáo viên tại nhiều hội nghị chỉnh nha trong nước và khu vực.</p>
HTML;

		$videos = static fn( int $a, int $b, string $t1, string $t2 ): array => array(
			array( 'url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'title' => $t1, 'image' => $img[ 'video-' . $a ] ),
			array( 'url' => 'https://youtu.be/ScMzIvxBSi4', 'title' => $t2, 'image' => $img[ 'video-' . $b ] ),
		);

		$cases = array();
		foreach ( array(
			array( 'Chị Lan', 'TP. Hồ Chí Minh', 'Niềng răng trong suốt', 'Răng hô, thưa' ),
			array( 'Anh Tuấn', 'Bình Dương', 'Trồng Implant', 'Mất răng hàm' ),
			array( 'Chị Hoa', 'Đồng Nai', 'Mắc cài sứ', 'Răng khấp khểnh' ),
		) as $i => [ $name, $location, $method, $treatment ] ) {
			$n       = $i + 1;
			$cases[] = array(
				'name'      => $name,
				'location'  => $location,
				'method'    => $method,
				'treatment' => $treatment,
				'portrait'  => $img[ 'case-portrait-' . $n ],
				'before'    => $img[ 'case-before-' . $n ],
				'after'     => $img[ 'case-after-' . $n ],
			);
		}

		return array(
			'post'  => array(
				'post_title'   => 'BS. Nguyễn Minh Anh',
				'post_content' => $content,
				'post_excerpt' => 'Bác sĩ chuyên khoa Chỉnh nha với hơn 15 năm kinh nghiệm, hơn 3.000 ca niềng răng thành công.',
			),
			'thumb' => $img['doctor-portrait'],
			'acf'   => array(
				'banner_desktop' => $img['doctor-banner-d'],
				'banner_mobile'  => $img['doctor-banner-m'],
				'position'       => 'Bác sĩ Chuyên khoa I – Trưởng khoa Chỉnh nha',
				'box_label'      => 'Cố vấn và đào tạo chuyên môn:',
				'highlights'     => "Hơn 15 năm kinh nghiệm chỉnh nha\nHơn 3.000 ca niềng răng thành công\nInvisalign Certified Provider\nBáo cáo viên hội nghị chỉnh nha",
				'gallery'        => array( $img['doctor-gallery-1'], $img['doctor-gallery-2'], $img['doctor-gallery-3'], $img['doctor-gallery-4'] ),
				'specialties'    => "Niềng răng mắc cài kim loại, mắc cài sứ\nNiềng răng trong suốt Invisalign\nĐiều trị sai khớp cắn hô, móm, lệch\nPhục hình răng sứ thẩm mỹ",
				'education'      => "Bác sĩ Răng Hàm Mặt – Đại học Y Dược TP. Hồ Chí Minh\nChuyên khoa I Chỉnh nha\nThạc sĩ Y học chuyên ngành Răng Hàm Mặt\nTu nghiệp chỉnh nha tại Singapore",
				'experience'     => "2010 – 2015: Bác sĩ điều trị Bệnh viện Răng Hàm Mặt TP. HCM\n2015 – 2020: Phó khoa Chỉnh nha\n2020 – nay: Trưởng khoa Chỉnh nha Răng Hàm Mặt SG",
				'diplomas'       => array( $img['doctor-diploma-1'], $img['doctor-diploma-2'], $img['doctor-diploma-3'] ),
				'certificates'   => array( $img['doctor-cert-1'], $img['doctor-cert-2'], $img['doctor-cert-3'], $img['doctor-cert-4'] ),
				'videos'         => $videos( 1, 2, 'Gặp gỡ BS. Minh Anh', 'Một ngày làm việc của bác sĩ' ),
				'cases'          => $cases,
				'case_videos'    => $videos( 3, 4, 'Ca niềng răng hô', 'Ca trồng Implant toàn hàm' ),
			),
		);
	}

	/* -----------------------------------------------------------------
	 * Bài viết
	 * -------------------------------------------------------------- */

	public static function post( array $img, int $doctor_id ): array {
		$fig = static function ( int $id, string $caption ): string {
			$src = wp_get_attachment_image_src( $id, 'large' );
			if ( ! $src ) {
				return '';
			}
			$alt = esc_attr( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) );
			return sprintf(
				'[caption id="attachment_%1$d" align="aligncenter" width="%3$d"]<img class="size-large wp-image-%1$d" src="%2$s" alt="%5$s" width="%3$d" height="%4$d" /> %6$s[/caption]',
				$id,
				esc_url( $src[0] ),
				$src[1],
				$src[2],
				$alt,
				esc_html( $caption )
			);
		};
		$img1 = $fig( $img['post-inline-1'], 'Quy trình niềng răng trong suốt gồm 5 bước chính.' );
		$img2 = $fig( $img['post-inline-2'], 'Vệ sinh khay niềng mỗi ngày giúp khay luôn trong và sạch.' );

		$content = <<<HTML
<p><strong>Niềng răng trong suốt</strong> là phương pháp chỉnh nha sử dụng chuỗi khay nhựa trong suốt được thiết kế riêng, giúp răng dịch chuyển dần về vị trí mong muốn mà gần như không ai nhận ra bạn đang niềng. Bài viết này tổng hợp quy trình, chi phí và những lưu ý quan trọng trước khi bắt đầu.</p>

<h2>Niềng răng trong suốt là gì?</h2>
<p>Khác với mắc cài truyền thống, khay niềng trong suốt có thể tháo lắp. Bạn đeo khay 20–22 giờ mỗi ngày và thay khay mới sau mỗi 1–2 tuần theo chỉ định của bác sĩ.</p>
<h3>Ưu điểm</h3>
<ul>
<li>Thẩm mỹ cao, gần như vô hình khi giao tiếp.</li>
<li>Tháo lắp dễ dàng khi ăn uống và vệ sinh.</li>
<li>Ít gây tổn thương niêm mạc má, môi.</li>
<li>Xem trước kết quả bằng mô phỏng 3D.</li>
</ul>
<h3>Hạn chế</h3>
<ul>
<li>Chi phí cao hơn mắc cài kim loại.</li>
<li>Đòi hỏi tính kỷ luật: đeo đủ thời gian mỗi ngày.</li>
</ul>

<h2>Ai nên niềng răng trong suốt?</h2>
<p>Phương pháp phù hợp với người trưởng thành và thanh thiếu niên có các tình trạng: răng thưa, răng khấp khểnh, hô nhẹ đến trung bình, khớp cắn sâu, khớp cắn chéo.</p>

[bac_si_tham_van]

<h2>Quy trình niềng răng trong suốt</h2>
{$img1}
<ol>
<li><strong>Thăm khám và chụp phim:</strong> bác sĩ kiểm tra tổng quát, chụp X-quang toàn cảnh và cephalometric.</li>
<li><strong>Quét dấu răng 3D:</strong> máy quét iTero thay cho lấy dấu bằng thạch cao.</li>
<li><strong>Lập kế hoạch và mô phỏng:</strong> xem trước từng giai đoạn dịch chuyển của răng.</li>
<li><strong>Đeo khay và tái khám:</strong> thay khay định kỳ, tái khám mỗi 6–8 tuần.</li>
<li><strong>Duy trì kết quả:</strong> đeo hàm duy trì sau khi kết thúc điều trị.</li>
</ol>

<h2>Chi phí niềng răng trong suốt</h2>
<p>Chi phí phụ thuộc vào mức độ phức tạp của ca và số lượng khay cần dùng. Bảng dưới đây mang tính tham khảo:</p>
<table>
<thead><tr><th>Gói điều trị</th><th>Số khay</th><th>Thời gian</th><th>Chi phí tham khảo</th></tr></thead>
<tbody>
<tr><td>Express</td><td>Tối đa 7 khay</td><td>3–6 tháng</td><td>35.000.000đ</td></tr>
<tr><td>Lite</td><td>Tối đa 14 khay</td><td>6–9 tháng</td><td>60.000.000đ</td></tr>
<tr><td>Moderate</td><td>Tối đa 26 khay</td><td>9–15 tháng</td><td>85.000.000đ</td></tr>
<tr><td>Comprehensive</td><td>Không giới hạn</td><td>15–24 tháng</td><td>120.000.000đ</td></tr>
</tbody>
</table>

<h2>Lưu ý khi niềng răng trong suốt</h2>
{$img2}
<h3>Vệ sinh khay niềng</h3>
<p>Rửa khay bằng nước mát và bàn chải lông mềm mỗi sáng và tối. Không dùng nước nóng vì có thể làm biến dạng khay.</p>
<h3>Ăn uống</h3>
<p>Luôn tháo khay khi ăn và uống đồ có màu, có đường. Đánh răng trước khi đeo lại khay để tránh sâu răng.</p>
<blockquote><p>Hiệu quả niềng răng trong suốt phụ thuộc tới 50% vào việc bạn đeo khay đủ thời gian mỗi ngày.</p></blockquote>

<h2>Kết luận</h2>
<p>Niềng răng trong suốt là lựa chọn thẩm mỹ và tiện lợi cho nhiều trường hợp sai lệch răng. Hãy đặt lịch thăm khám để bác sĩ đánh giá và tư vấn phương án phù hợp nhất với bạn.</p>
HTML;

		return array(
			'post'  => array(
				'post_title'   => 'Niềng răng trong suốt: quy trình, chi phí và những lưu ý quan trọng',
				'post_content' => $content,
				'post_excerpt' => 'Tổng hợp quy trình 5 bước, bảng chi phí tham khảo và các lưu ý khi niềng răng trong suốt để đạt kết quả tốt nhất.',
			),
			'thumb' => $img['post-featured'],
			'acf'   => array(
				'reviewer'   => $doctor_id,
				'faq'        => array(
					array( 'question' => 'Niềng răng trong suốt có đau không?', 'answer' => 'Bạn có thể cảm thấy hơi căng tức 1–2 ngày đầu khi thay khay mới. Cảm giác này nhẹ hơn nhiều so với niềng mắc cài và sẽ tự hết.' ),
					array( 'question' => 'Mỗi ngày cần đeo khay bao lâu?', 'answer' => "Tối thiểu <strong>20–22 giờ</strong> mỗi ngày. Chỉ tháo khi ăn uống và vệ sinh răng miệng.\n\nĐeo không đủ thời gian sẽ kéo dài tiến trình điều trị." ),
					array( 'question' => 'Niềng răng trong suốt mất bao lâu?', 'answer' => 'Trung bình từ 6 đến 24 tháng tùy mức độ sai lệch của răng.' ),
					array( 'question' => 'Có cần đeo hàm duy trì sau khi niềng không?', 'answer' => 'Có. Hàm duy trì giúp răng giữ ổn định ở vị trí mới, tránh tình trạng răng di chuyển trở lại.' ),
				),
				'references' => array(
					array( 'title' => 'American Association of Orthodontists – Clear Aligners', 'url' => 'https://aaoinfo.org/' ),
					array( 'title' => 'Hiệp hội Răng Hàm Mặt Việt Nam', 'url' => 'https://www.vos.org.vn/' ),
					array( 'title' => 'Rossini G. et al. Efficacy of clear aligners in controlling orthodontic tooth movement. Angle Orthodontist, 2015.', 'url' => '' ),
				),
			),
		);
	}
}
