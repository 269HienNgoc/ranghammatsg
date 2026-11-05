<?php
/**
 * Schema JSON-LD bổ sung, chèn vào graph của Yoast SEO (filter wpseo_schema_graph) — KHÔNG in <script> riêng
 * để không tạo thực thể trùng với Article / WebPage / Organization mà Yoast đã khai báo. Tắt Yoast → không có schema.
 *
 * - Bài viết: WebPage thêm FAQPage + mainEntity → các Question; reviewedBy → Person (bác sĩ tham vấn); Article.citation.
 * - Hồ sơ bác sĩ: WebPage → ProfilePage, mainEntity → Person.
 * Chỉ khai báo nội dung ĐANG hiển thị trên trang (đọc chung hàm với template: inc/post-extras.php, inc/doctors.php).
 * Không thêm đánh giá/điểm sao tự đặt (vi phạm chính sách review của Google).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/** Person cho 1 hồ sơ bác sĩ; @id = permalink#person (dùng chung cho bài viết và trang hồ sơ). */
function rhm_schema_doctor_person( int $doctor_id, string $org_id, bool $full = false ): array {
	$url    = (string) get_permalink( $doctor_id );
	$person = array(
		'@type' => 'Person',
		'@id'   => $url . '#person',
		'name'  => get_the_title( $doctor_id ),
		'url'   => $url,
	);
	$position = sanitize_text_field( (string) rhm_get_doctor_field( 'position', $doctor_id ) );
	if ( $position ) {
		$person['jobTitle'] = $position;
	}
	$image = has_post_thumbnail( $doctor_id ) ? wp_get_attachment_image_url( (int) get_post_thumbnail_id( $doctor_id ), 'full' ) : '';
	if ( $image ) {
		$person['image'] = $image;
	}
	if ( $org_id ) {
		$person['worksFor'] = array( '@id' => $org_id );
	}
	if ( $full ) {
		$description = has_excerpt( $doctor_id ) ? wp_strip_all_tags( get_the_excerpt( $doctor_id ) ) : '';
		$specialties = rhm_get_doctor( $doctor_id )['lists']['specialties'];
		if ( $description ) {
			$person['description'] = $description;
		}
		if ( $specialties ) {
			$person['knowsAbout'] = $specialties;
		}
	}
	return $person;
}

/** Bỏ piece $id (vd Person tài khoản đăng bài của Yoast) khi không còn piece nào khác tham chiếu tới nó. */
function rhm_schema_drop_orphan( array $graph, string $id ): array {
	if ( '' === $id ) {
		return $graph;
	}
	$needle = wp_json_encode( array( '@id' => $id ) );
	foreach ( $graph as $piece ) {
		if ( ( $piece['@id'] ?? '' ) !== $id && false !== strpos( (string) wp_json_encode( $piece ), $needle ) ) {
			return $graph;
		}
	}
	return array_values( array_filter( $graph, static fn( array $piece ): bool => ( $piece['@id'] ?? '' ) !== $id ) );
}

function rhm_schema_graph( $graph, $context ) {
	$is_post   = is_singular( 'post' );
	$is_doctor = is_singular( 'bac-si' );
	if ( ( ! $is_post && ! $is_doctor ) || ! is_array( $graph ) || empty( $context->main_schema_id ) ) {
		return $graph;
	}

	$post_id = get_queried_object_id();
	$page_id = (string) $context->main_schema_id; // @id WebPage của Yoast; Article = $page_id . '#article'.
	$page    = null;
	$article = null;
	$org_id  = '';
	foreach ( $graph as $index => $piece ) {
		$id = $piece['@id'] ?? '';
		if ( $page_id === $id ) {
			$page = $index;
		} elseif ( $page_id . '#article' === $id ) {
			$article = $index;
		} elseif ( in_array( 'Organization', (array) ( $piece['@type'] ?? array() ), true ) ) {
			$org_id = (string) $id;
		}
	}
	if ( null === $page ) {
		return $graph;
	}

	if ( $is_doctor ) {
		$person                        = rhm_schema_doctor_person( $post_id, $org_id, true );
		$graph[ $page ]['@type']       = 'ProfilePage';
		$graph[ $page ]['mainEntity']  = array( '@id' => $person['@id'] );
		$graph[]                       = $person;
		return $graph;
	}

	$faq = rhm_get_post_faq( $post_id );
	if ( $faq ) {
		$types = (array) $graph[ $page ]['@type'];
		if ( ! in_array( 'FAQPage', $types, true ) ) {
			$types[] = 'FAQPage';
		}
		$graph[ $page ]['@type']      = $types;
		$graph[ $page ]['mainEntity'] = array();
		foreach ( $faq as $index => $item ) {
			$url                            = $page_id . '#rhm-faq-' . ( $index + 1 ); // Khớp id <details> trong post-faq.php.
			$graph[ $page ]['mainEntity'][] = array( '@id' => $url );
			$graph[]                        = array(
				'@type'          => 'Question',
				'@id'            => $url,
				'url'            => $url,
				'position'       => $index + 1,
				'name'           => $item['question'],
				'answerCount'    => 1,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => trim( $item['answer'] ), 'inLanguage' => 'vi' ),
				'inLanguage'     => 'vi',
			);
		}
	}

	$reviewer = rhm_get_post_reviewer( $post_id );
	if ( $reviewer ) {
		$person                       = rhm_schema_doctor_person( $reviewer, $org_id );
		$graph[ $page ]['reviewedBy'] = array( '@id' => $person['@id'] );
		$graph[]                      = $person;
		// Banner hiện bác sĩ ở vị trí tác giả (page-banner.php) → Article.author cũng là bác sĩ.
		if ( null !== $article ) {
			$old_author                  = (string) ( $graph[ $article ]['author']['@id'] ?? '' );
			$graph[ $article ]['author'] = array( 'name' => $person['name'], '@id' => $person['@id'] );
		}
	}

	$references = rhm_get_post_references( $post_id );
	if ( $references && null !== $article ) {
		$graph[ $article ]['citation'] = array_map(
			static fn( array $item ): array => array_filter( array( '@type' => 'CreativeWork', 'name' => $item['title'], 'url' => $item['url'] ) ),
			$references
		);
	}

	// Cuối hàm: bỏ piece làm đánh lại chỉ số mảng ($page / $article không còn đúng).
	return isset( $old_author ) ? rhm_schema_drop_orphan( $graph, $old_author ) : $graph;
}
add_filter( 'wpseo_schema_graph', 'rhm_schema_graph', 10, 2 );
