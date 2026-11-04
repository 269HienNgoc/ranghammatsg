<?php
/**
 * Plugin kk-star-ratings (sao đánh giá).
 *
 * Plugin nạp CSS + JS ở MỌI trang. Theme chỉ in sao ở banner bài viết (template-parts/components/page-banner.php, chỉ single.php)
 * → chỉ giữ file của plugin ở bài viết VÀ khi plugin thực sự trả về markup (cài đặt plugin có thể tắt sao ở 1 số bài).
 * rhm_get_star_rating() luôn tồn tại (trả '' khi plugin tắt) để page-banner gọi an toàn.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

/**
 * Cỡ sao (px) + khoảng cách giữa sao trong banner bài viết — cân với icon 16px / chữ 14px của dòng thông tin.
 * Truyền thẳng vào kk_star_ratings() (ghi đè cài đặt "Size"/"Gap" của plugin, mặc định 24/5): plugin tự tính lại độ rộng
 * phần sao "đã chấm" theo 2 giá trị này, và JS của plugin gửi lại cùng payload khi người đọc bấm sao → giữ nguyên cỡ.
 */
const RHM_STAR_RATING_ATTS = array(
	'size' => 18,
	'gap'  => 3,
);

/**
 * Markup sao đánh giá của bài viết hiện tại (rỗng nếu plugin tắt / không phải trang bài viết).
 * Tính 1 lần mỗi request: wp_head dùng để quyết định nạp CSS/JS, page-banner dùng lại để in.
 */
function rhm_get_star_rating(): string {
	static $markup = null;

	if ( null === $markup ) {
		$markup = function_exists( 'kk_star_ratings' ) && is_singular( 'post' ) ? trim( (string) kk_star_ratings( array( 'id' => get_queried_object_id() ) + RHM_STAR_RATING_ATTS ) ) : '';
	}

	return $markup;
}

/**
 * Bỏ CSS/JS của kk-star-ratings ở trang không hiện sao. Chạy sau plugin (ưu tiên 10).
 */
function rhm_star_rating_assets(): void {
	if ( '' === rhm_get_star_rating() ) {
		wp_dequeue_style( 'kk-star-ratings' );
		wp_dequeue_script( 'kk-star-ratings' );
	}
}
add_action( 'wp_enqueue_scripts', 'rhm_star_rating_assets', 20 );

function update_kk_star_ratings_save($post_id) {
	if ( 'bac-si' === get_post_type( $post_id ) || ( wp_is_post_revision( $post_id ) && 'bac-si' === get_post_type( wp_get_post_parent_id( $post_id ) ) ) ) {
		return;
	}

    $ratings = get_post_meta($post_id, '_kksr_ratings_default', true);
    $cont = get_post_meta($post_id, '_kksr_count_default', true);
    $avg = get_post_meta($post_id, '_kksr_avg_default', true);

// 	if($post_id == 13889) { return; }

    if ($ratings && $cont && $avg) {
        return;
    }

    // Tạo số lượt bình luận ngẫu nhiên từ 40 đến 50
    $random_count = rand(20, 50);

    // Tạo tổng điểm đánh giá ngẫu nhiên (mỗi đánh giá từ 4 đến 5 sao)
    $total_ratings = 0;
    for ($i = 0; $i < $random_count; $i++) {
        $total_ratings += rand(4, 5);
    }

    // Tính điểm trung bình
    $new_avg = ($random_count > 0) ? $total_ratings / $random_count : 0;

    // Lưu dữ liệu mới vào database
    update_post_meta($post_id, '_kksr_ratings_default', $total_ratings);
    update_post_meta($post_id, '_kksr_ratings', $total_ratings);
    update_post_meta($post_id, '_kksr_count_default', $random_count);
    update_post_meta($post_id, '_kksr_casts', $random_count);
    update_post_meta($post_id, '_kksr_avg_default', $new_avg);
    update_post_meta($post_id, '_kksr_avg', $new_avg);
}
add_action('save_post', 'update_kk_star_ratings_save');
