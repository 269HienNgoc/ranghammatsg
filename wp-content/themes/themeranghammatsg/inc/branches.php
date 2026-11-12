<?php
/**
 * Chi nhánh: CPT chi-nhanh (chỉ dữ liệu, KHÔNG có trang công khai) + danh mục danh-muc-chi-nhanh + ảnh đại diện + field ACF Địa chỉ / Hotline / URL Google Maps.
 *
 * - Nhập ở admin → Chi nhánh. Tiêu đề = tên chi nhánh; hiển thị theo tên (A → Z).
 * - Đọc dữ liệu: rhm_get_branches() (cache transient, tự làm mới khi sửa chi nhánh / danh mục).
 * - Dùng ở: footer (inc/footer.php → rhm_get_footer_branches(): địa chỉ của chi nhánh được chọn trong Tùy biến → Chân trang).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

const RHM_BRANCH_POST_TYPE = 'chi-nhanh';
const RHM_BRANCH_TAXONOMY  = 'danh-muc-chi-nhanh';

/** Số chi nhánh tối đa đọc ra 1 lần. */
const RHM_BRANCHES_MAX = 50;

function rhm_register_branches(): void {
	register_post_type(
		RHM_BRANCH_POST_TYPE,
		array(
			'labels'              => array(
				'name'               => __( 'Chi nhánh', 'themeranghammatsg' ),
				'singular_name'      => __( 'Chi nhánh', 'themeranghammatsg' ),
				'add_new_item'       => __( 'Thêm chi nhánh', 'themeranghammatsg' ),
				'edit_item'          => __( 'Sửa chi nhánh', 'themeranghammatsg' ),
				'new_item'           => __( 'Chi nhánh mới', 'themeranghammatsg' ),
				'search_items'       => __( 'Tìm chi nhánh', 'themeranghammatsg' ),
				'not_found'          => __( 'Chưa có chi nhánh.', 'themeranghammatsg' ),
				'not_found_in_trash' => __( 'Không có chi nhánh trong thùng rác.', 'themeranghammatsg' ),
				'all_items'          => __( 'Tất cả chi nhánh', 'themeranghammatsg' ),
				'menu_name'          => __( 'Chi nhánh', 'themeranghammatsg' ),
				'featured_image'     => __( 'Ảnh đại diện chi nhánh', 'themeranghammatsg' ),
				'set_featured_image' => __( 'Chọn ảnh đại diện chi nhánh', 'themeranghammatsg' ),
				'remove_featured_image' => __( 'Xóa ảnh đại diện', 'themeranghammatsg' ),
				'use_featured_image' => __( 'Dùng làm ảnh đại diện', 'themeranghammatsg' ),
			),
			// Chỉ dữ liệu: không có /chi-nhanh/ hay trang chi tiết (tránh trang mỏng), không vào tìm kiếm / sitemap.
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => false,
			'show_in_rest'        => true,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'has_archive'         => false,
			'rewrite'             => false,
			'query_var'           => false,
			'menu_icon'           => 'dashicons-location',
			'menu_position'       => 21,
			'supports'            => array( 'title', 'thumbnail' ), // thumbnail: ảnh đại diện (mặt tiền / không gian chi nhánh). Không có khung Thuộc tính → sắp theo tên.
		)
	);

	register_taxonomy(
		RHM_BRANCH_TAXONOMY,
		RHM_BRANCH_POST_TYPE,
		array(
			'labels'            => array(
				'name'          => __( 'Danh mục chi nhánh', 'themeranghammatsg' ),
				'singular_name' => __( 'Danh mục chi nhánh', 'themeranghammatsg' ),
				'menu_name'     => __( 'Danh mục', 'themeranghammatsg' ),
				'all_items'     => __( 'Tất cả danh mục', 'themeranghammatsg' ),
				'edit_item'     => __( 'Sửa danh mục', 'themeranghammatsg' ),
				'add_new_item'  => __( 'Thêm danh mục', 'themeranghammatsg' ),
				'search_items'  => __( 'Tìm danh mục', 'themeranghammatsg' ),
				'not_found'     => __( 'Chưa có danh mục.', 'themeranghammatsg' ),
			),
			'hierarchical'      => true, // Ô tick như Chuyên mục (vd Miền Nam → TP.HCM).
			'public'            => false,
			'show_ui'           => true,
			'show_in_nav_menus' => false,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'rewrite'           => false,
			'query_var'         => false,
		)
	);
}
add_action( 'init', 'rhm_register_branches' );

/** Field ACF: Địa chỉ, Hotline, URL Google Maps (tiền tố rhm_branch_). */
function rhm_branch_acf(): void {
	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}
	acf_add_local_field_group(
		array(
			'key'      => 'group_rhm_branch',
			'title'    => __( 'Thông tin chi nhánh', 'themeranghammatsg' ),
			'fields'   => array(
				array(
					'key'          => 'field_rhm_branch_address',
					'name'         => 'rhm_branch_address',
					'label'        => __( 'Địa chỉ', 'themeranghammatsg' ),
					'type'         => 'textarea',
					'rows'         => 3,
					'new_lines'    => '',
					'required'     => 1,
					'placeholder'  => __( 'Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành', 'themeranghammatsg' ),
				),
				array(
					'key'          => 'field_rhm_branch_hotline',
					'name'         => 'rhm_branch_hotline',
					'label'        => __( 'Hotline', 'themeranghammatsg' ),
					'type'         => 'text',
					'placeholder'  => '1900 xxxx',
					'instructions' => __( 'Nhập như muốn hiển thị (vd 0909 123 456); link gọi điện tự bỏ khoảng trắng.', 'themeranghammatsg' ),
				),
				array(
					'key'          => 'field_rhm_branch_map_url',
					'name'         => 'rhm_branch_map_url',
					'label'        => __( 'URL Google Maps', 'themeranghammatsg' ),
					'type'         => 'url',
					'placeholder'  => 'https://maps.app.goo.gl/...',
					'instructions' => __( 'Mở chi nhánh trên Google Maps → Chia sẻ → Sao chép đường liên kết. Dùng cho nút "Chỉ đường" ở trang chủ; để trống thì không hiện nút.', 'themeranghammatsg' ),
				),
			),
			'location' => array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => RHM_BRANCH_POST_TYPE ) ) ),
			'position' => 'acf_after_title',
		)
	);
}
add_action( 'acf/init', 'rhm_branch_acf' );

/** Giá trị field chi nhánh ('address' | 'hotline' | 'map_url'); không có ACF thì đọc post meta. */
function rhm_get_branch_field( string $name, int $post_id ): string {
	$value = function_exists( 'get_field' ) ? get_field( 'rhm_branch_' . $name, $post_id ) : get_post_meta( $post_id, 'rhm_branch_' . $name, true );
	return is_scalar( $value ) ? (string) $value : '';
}

/**
 * Chi nhánh đã xuất bản, theo tên (A → Z).
 *
 * @param array{term?:string} $args term: slug danh mục để lọc (gồm cả danh mục con).
 * @return array<int, array{id:int, name:string, address:string, hotline:string, map_url:string, image:int, terms:string[]}>
 *         address/hotline/map_url đã làm sạch, chưa escape; image = ID ảnh đại diện (0 nếu không có — khi in vẫn kiểm tra
 *         wp_attachment_is_image() vì cache có thể giữ ID ảnh vừa bị xóa khỏi Thư viện); terms = tên danh mục.
 */
function rhm_get_branches( array $args = array() ): array {
	static $cache = array();

	$term = sanitize_title( (string) ( $args['term'] ?? '' ) );
	// v2: thêm map_url — đổi tiền tố khi đổi cấu trúc mảng để bỏ transient cũ ngay.
	$key  = 'rhm_branches_v2_' . (int) get_option( 'rhm_branches_cache_version', 1 ) . '_' . md5( $term );
	if ( isset( $cache[ $key ] ) ) {
		return $cache[ $key ];
	}

	$branches = get_transient( $key );
	if ( is_array( $branches ) ) {
		return $cache[ $key ] = $branches;
	}

	$query = array(
		'post_type'           => RHM_BRANCH_POST_TYPE,
		'post_status'         => 'publish',
		'posts_per_page'      => RHM_BRANCHES_MAX,
		'orderby'             => 'title',
		'order'               => 'ASC',
		'no_found_rows'       => true,
		'ignore_sticky_posts' => true,
	);
	if ( '' !== $term ) {
		$query['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- vài chục chi nhánh, có cache.
			array(
				'taxonomy' => RHM_BRANCH_TAXONOMY,
				'field'    => 'slug',
				'terms'    => $term,
			),
		);
	}

	$branches = array();
	foreach ( get_posts( $query ) as $post ) {
		$terms      = get_the_terms( $post, RHM_BRANCH_TAXONOMY );
		$branches[] = array(
			'id'      => (int) $post->ID,
			'name'    => trim( get_the_title( $post ) ),
			'address' => trim( sanitize_textarea_field( rhm_get_branch_field( 'address', $post->ID ) ) ),
			'hotline' => trim( sanitize_text_field( rhm_get_branch_field( 'hotline', $post->ID ) ) ),
			'map_url' => esc_url_raw( trim( rhm_get_branch_field( 'map_url', $post->ID ) ), array( 'http', 'https' ) ),
			'image'   => (int) get_post_thumbnail_id( $post ),
			'terms'   => is_array( $terms ) ? wp_list_pluck( $terms, 'name' ) : array(),
		);
	}

	set_transient( $key, $branches, DAY_IN_SECONDS );
	return $cache[ $key ] = $branches;
}

/** Làm mới cache rhm_get_branches(): tăng số phiên bản → mọi transient cũ bị bỏ qua (tự hết hạn sau 1 ngày). */
function rhm_flush_branches_cache(): void {
	update_option( 'rhm_branches_cache_version', (int) get_option( 'rhm_branches_cache_version', 1 ) + 1, false );
}
add_action( 'save_post_' . RHM_BRANCH_POST_TYPE, 'rhm_flush_branches_cache' );

/** Xóa / bỏ vào thùng rác / khôi phục chi nhánh. */
function rhm_branch_post_changed( int $post_id ): void {
	if ( RHM_BRANCH_POST_TYPE === get_post_type( $post_id ) ) {
		rhm_flush_branches_cache();
	}
}
add_action( 'deleted_post', 'rhm_branch_post_changed' );
add_action( 'trashed_post', 'rhm_branch_post_changed' );
add_action( 'untrashed_post', 'rhm_branch_post_changed' );

/** Sửa / xóa danh mục chi nhánh (tên danh mục nằm trong cache). */
add_action( 'edited_' . RHM_BRANCH_TAXONOMY, 'rhm_flush_branches_cache' );
add_action( 'delete_' . RHM_BRANCH_TAXONOMY, 'rhm_flush_branches_cache' );

/** Lưu field ACF xong (meta ghi sau save_post) → làm mới lần nữa cho chắc. */
function rhm_branch_acf_saved( $post_id ): void {
	if ( is_numeric( $post_id ) ) {
		rhm_branch_post_changed( (int) $post_id );
	}
}
add_action( 'acf/save_post', 'rhm_branch_acf_saved', 20 );

/**
 * Đổi / xóa ảnh đại diện: Classic Editor lưu _thumbnail_id ngay bằng AJAX (chưa bấm Cập nhật → không có save_post).
 *
 * @param int|int[] $meta_ids
 */
function rhm_branch_thumbnail_changed( $meta_ids, int $post_id, string $meta_key ): void {
	if ( '_thumbnail_id' === $meta_key ) {
		rhm_branch_post_changed( $post_id );
	}
}
add_action( 'added_post_meta', 'rhm_branch_thumbnail_changed', 10, 3 );
add_action( 'updated_post_meta', 'rhm_branch_thumbnail_changed', 10, 3 );
add_action( 'deleted_post_meta', 'rhm_branch_thumbnail_changed', 10, 3 );

/* ---------------------------------------------------------------------------
 * Admin: cột Ảnh / Địa chỉ / Hotline ở danh sách Chi nhánh.
 * ------------------------------------------------------------------------- */

function rhm_branch_admin_columns( array $columns ): array {
	$result = array();
	foreach ( $columns as $key => $label ) {
		if ( 'title' === $key ) {
			$result['rhm_branch_image'] = '<span class="screen-reader-text">' . esc_html__( 'Ảnh đại diện', 'themeranghammatsg' ) . '</span>';
		}
		$result[ $key ] = $label;
		if ( 'title' === $key ) {
			$result['rhm_branch_address'] = __( 'Địa chỉ', 'themeranghammatsg' );
			$result['rhm_branch_hotline'] = __( 'Hotline', 'themeranghammatsg' );
		}
	}
	return $result;
}
add_filter( 'manage_' . RHM_BRANCH_POST_TYPE . '_posts_columns', 'rhm_branch_admin_columns' );

function rhm_branch_admin_column( string $column, int $post_id ): void {
	if ( 'rhm_branch_image' === $column ) {
		echo get_the_post_thumbnail( $post_id, array( 60, 60 ), array( 'style' => 'width:60px;height:60px;object-fit:cover;border-radius:6px', 'alt' => '' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- WordPress escape thuộc tính.
	} elseif ( 'rhm_branch_address' === $column ) {
		echo nl2br( esc_html( rhm_get_branch_field( 'address', $post_id ) ) );
	} elseif ( 'rhm_branch_hotline' === $column ) {
		echo esc_html( rhm_get_branch_field( 'hotline', $post_id ) );
	}
}
add_action( 'manage_' . RHM_BRANCH_POST_TYPE . '_posts_custom_column', 'rhm_branch_admin_column', 10, 2 );

/** Cột ảnh hẹp, vừa ảnh 60px. */
function rhm_branch_admin_column_style(): void {
	$screen = get_current_screen();
	if ( $screen && 'edit-' . RHM_BRANCH_POST_TYPE === $screen->id ) {
		echo '<style>.column-rhm_branch_image{width:72px}</style>';
	}
}
add_action( 'admin_head', 'rhm_branch_admin_column_style' );

/** Danh sách admin mặc định sắp theo tên (giống thứ tự hiển thị ngoài site). */
function rhm_branch_admin_order( WP_Query $query ): void {
	if ( is_admin() && $query->is_main_query() && RHM_BRANCH_POST_TYPE === $query->get( 'post_type' ) && ! $query->get( 'orderby' ) ) {
		$query->set( 'orderby', 'title' );
		$query->set( 'order', 'ASC' );
	}
}
add_action( 'pre_get_posts', 'rhm_branch_admin_order' );
