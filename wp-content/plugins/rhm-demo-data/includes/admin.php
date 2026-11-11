<?php
/**
 * Trang quản trị: Công cụ → Dữ liệu mẫu RHM.
 *
 * @package rhm-demo-data
 */

defined( 'ABSPATH' ) || exit;

const RHM_DEMO_MAX = 500;

function rhm_demo_admin_menu(): void {
	add_management_page( 'Dữ liệu mẫu RHM', 'Dữ liệu mẫu RHM', 'manage_options', 'rhm-demo-data', 'rhm_demo_admin_page' );
}
add_action( 'admin_menu', 'rhm_demo_admin_menu' );

function rhm_demo_admin_page(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$counts = RHM_Demo_Generator::counts();
	$notice = get_transient( 'rhm_demo_notice_' . get_current_user_id() );
	delete_transient( 'rhm_demo_notice_' . get_current_user_id() );
	?>
	<div class="wrap">
		<h1>Dữ liệu mẫu RHM</h1>

		<?php if ( $notice ) : ?>
			<div class="notice notice-<?php echo esc_attr( $notice['type'] ); ?> is-dismissible"><p><?php echo esc_html( $notice['message'] ); ?></p></div>
		<?php endif; ?>

		<?php if ( ! RHM_Demo_Images::available() ) : ?>
			<div class="notice notice-error"><p>PHP chưa bật extension <code>gd</code> – không sinh được ảnh mẫu. Laragon: Menu → PHP → Extensions → gd.</p></div>
		<?php endif; ?>
		<?php if ( ! post_type_exists( 'bac-si' ) ) : ?>
			<div class="notice notice-warning"><p>Chưa có post type <code>bac-si</code>: hãy kích hoạt theme <strong>Răng Hàm Mặt SG</strong> để tạo hồ sơ bác sĩ.</p></div>
		<?php endif; ?>
		<?php if ( ! function_exists( 'get_field' ) ) : ?>
			<div class="notice notice-info"><p>ACF PRO chưa bật. Dữ liệu vẫn được lưu đúng định dạng ACF, nhưng banner, chuyên môn, video, ca điều trị, FAQ… chỉ hiển thị khi ACF PRO được kích hoạt.</p></div>
		<?php endif; ?>

		<p>Tạo <strong>1 bài viết</strong> và <strong>1 hồ sơ bác sĩ</strong> đầy đủ thông tin (ảnh đại diện, nội dung có mục lục/bảng/ảnh, FAQ, tài liệu tham khảo, bác sĩ tham vấn; banner, chuyên môn, văn bằng, chứng chỉ, video, ca điều trị), rồi nhân bản đến đủ số lượng bên dưới. Ảnh được sinh tự động và dùng chung.</p>

		<table class="widefat striped" style="max-width:480px;margin:16px 0">
			<tbody>
				<tr><td>Bài viết mẫu hiện có</td><td><strong><?php echo (int) $counts['posts']; ?></strong></td></tr>
				<tr><td>Hồ sơ bác sĩ mẫu hiện có</td><td><strong><?php echo (int) $counts['doctors']; ?></strong></td></tr>
				<tr><td>Trang landing mẫu</td><td><strong><?php echo (int) $counts['pages']; ?></strong></td></tr>
				<tr><td>Ảnh mẫu trong Media</td><td><strong><?php echo (int) $counts['images']; ?></strong></td></tr>
			</tbody>
		</table>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rhm_demo_generate">
			<?php wp_nonce_field( 'rhm_demo_generate' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="rhm-demo-posts">Số bài viết</label></th>
					<td><input id="rhm-demo-posts" name="posts" type="number" min="0" max="<?php echo (int) RHM_DEMO_MAX; ?>" value="100" class="small-text"> <span class="description">1 bản gốc + bản sao. 0 = không tạo.</span></td>
				</tr>
				<tr>
					<th scope="row"><label for="rhm-demo-doctors">Số hồ sơ bác sĩ</label></th>
					<td><input id="rhm-demo-doctors" name="doctors" type="number" min="0" max="<?php echo (int) RHM_DEMO_MAX; ?>" value="100" class="small-text"></td>
				</tr>
				<tr>
					<th scope="row">Tùy chọn</th>
					<td>
						<label><input name="fresh" type="checkbox" value="1" checked> Xóa dữ liệu mẫu cũ trước khi tạo (giữ lại ảnh)</label><br>
						<label><input name="landing" type="checkbox" value="1" checked> Tạo luôn trang landing mẫu "Giới thiệu"</label>
					</td>
				</tr>
			</table>
			<?php submit_button( 'Tạo dữ liệu mẫu' ); ?>
		</form>

		<hr>
		<h2>Trang landing mẫu</h2>
		<?php $rhm_landing = RHM_Demo_Landing::existing(); ?>
		<p>Tạo trang <strong>"Giới thiệu (dữ liệu mẫu)"</strong> dùng template <em>Giới thiệu</em> với <strong>đủ mọi section</strong> của landing page (nội dung theo thiết kế trang Giới thiệu, ảnh sinh tự động, 2 bác sĩ lấy từ hồ sơ có ảnh đại diện). Đã có trang mẫu thì nội dung được <strong>ghi đè về mặc định</strong> (giữ slug). Cần theme Răng Hàm Mặt SG bản có landing page.</p>
		<?php if ( $rhm_landing ) : ?>
			<p>Trang hiện có: <a href="<?php echo esc_url( (string) get_permalink( $rhm_landing ) ); ?>" target="_blank" rel="noopener"><?php echo esc_html( (string) get_permalink( $rhm_landing ) ); ?></a> · <a href="<?php echo esc_url( (string) get_edit_post_link( $rhm_landing ) ); ?>">Sửa trang</a></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rhm_demo_landing">
			<?php wp_nonce_field( 'rhm_demo_landing' ); ?>
			<?php submit_button( $rhm_landing ? 'Tạo lại trang landing mẫu' : 'Tạo trang landing mẫu', 'secondary' ); ?>
		</form>

		<hr>
		<h2>Banner trang chủ</h2>
		<p>Sinh <strong>2 banner desktop (2048×800)</strong> và <strong>2 banner mobile (828×1000)</strong>, gán vào <em>Giao diện → Tùy biến → Hero banner (Trang chủ)</em> slide 1 và 2.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rhm_demo_hero">
			<?php wp_nonce_field( 'rhm_demo_hero' ); ?>
			<label><input name="overwrite" type="checkbox" value="1"> Ghi đè slide 1–2 nếu đã có banner</label>
			<?php submit_button( 'Tạo banner trang chủ', 'secondary' ); ?>
		</form>

		<hr>
		<h2>Chuyên mục mẫu</h2>
		<p>Khi tạo dữ liệu, plugin tự tạo <strong><?php echo count( RHM_Demo_Content::CATEGORIES ); ?> chuyên mục</strong> (<?php echo esc_html( implode( ', ', RHM_Demo_Content::CATEGORIES ) ); ?>) và rải đều bài viết mẫu vào đó. Bấm nút dưới để tạo chuyên mục còn thiếu và rải lại các bài mẫu đang có.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="rhm_demo_distribute">
			<?php wp_nonce_field( 'rhm_demo_distribute' ); ?>
			<?php submit_button( 'Rải lại bài viết mẫu vào chuyên mục', 'secondary' ); ?>
		</form>

		<hr>
		<h2>Xóa dữ liệu mẫu</h2>
		<p>Chỉ xóa nội dung do plugin này tạo (bài viết, hồ sơ bác sĩ, trang landing mẫu — đánh dấu bằng meta <code><?php echo esc_html( RHM_DEMO_META ); ?></code>), kể cả ảnh mẫu và danh mục/thẻ mẫu không còn bài nào dùng. Danh mục đang nằm trong menu được giữ lại để không mất menu; banner thật từng bị banner mẫu ghi đè được trả lại.</p>
		<p><strong>Lưu ý:</strong> ngừng kích hoạt hoặc xóa plugin (Plugin → Xóa) <strong>không</strong> xóa dữ liệu mẫu — mọi bài viết, hồ sơ, trang, ảnh đã tạo vẫn còn nguyên. Muốn dọn thì bấm nút dưới đây trước khi xóa plugin.</p>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Xóa toàn bộ dữ liệu mẫu?');">
			<input type="hidden" name="action" value="rhm_demo_delete">
			<?php wp_nonce_field( 'rhm_demo_delete' ); ?>
			<?php submit_button( 'Xóa toàn bộ dữ liệu mẫu', 'delete' ); ?>
		</form>

		<p class="description">WP-CLI: <code>wp rhm-demo generate --posts=100 --doctors=100 --fresh</code> · <code>wp rhm-demo landing</code> · <code>wp rhm-demo hero [--overwrite]</code> · <code>wp rhm-demo distribute</code> · <code>wp rhm-demo delete</code></p>
	</div>
	<?php
}

function rhm_demo_redirect( string $type, string $message ): never {
	set_transient( 'rhm_demo_notice_' . get_current_user_id(), array( 'type' => $type, 'message' => $message ), MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'tools.php?page=rhm-demo-data' ) );
	exit;
}

function rhm_demo_handle_hero(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Không có quyền.' );
	}
	check_admin_referer( 'rhm_demo_hero' );

	try {
		$result = ( new RHM_Demo_Generator() )->hero( ! empty( $_POST['overwrite'] ) );
	} catch ( Throwable $e ) {
		rhm_demo_redirect( 'error', 'Lỗi: ' . $e->getMessage() );
	}

	$message = sprintf( 'Đã tạo %d banner (%d desktop + %d mobile).', 2 * count( $result['slides'] ), count( $result['slides'] ), count( $result['slides'] ) );
	$message .= $result['assigned']
		? ' Đã gán vào slide ' . implode( ', ', $result['assigned'] ) . ' của Hero banner.'
		: ' Slide 1–2 đã có banner nên chưa gán — tick "Ghi đè" để thay.';
	rhm_demo_redirect( 'success', $message );
}
add_action( 'admin_post_rhm_demo_hero', 'rhm_demo_handle_hero' );

function rhm_demo_handle_distribute(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Không có quyền.' );
	}
	check_admin_referer( 'rhm_demo_distribute' );

	$result = ( new RHM_Demo_Generator() )->distribute();
	$parts  = array();
	foreach ( $result as $name => $count ) {
		$parts[] = $name . ': ' . $count;
	}
	rhm_demo_redirect( 'success', 'Đã rải bài viết mẫu vào ' . count( $result ) . ' chuyên mục — ' . implode( ' · ', $parts ) . '.' );
}
add_action( 'admin_post_rhm_demo_distribute', 'rhm_demo_handle_distribute' );

function rhm_demo_handle_generate(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Không có quyền.' );
	}
	check_admin_referer( 'rhm_demo_generate' );

	$posts   = min( RHM_DEMO_MAX, absint( $_POST['posts'] ?? 0 ) );
	$doctors = min( RHM_DEMO_MAX, absint( $_POST['doctors'] ?? 0 ) );
	$fresh   = ! empty( $_POST['fresh'] );
	$landing = ! empty( $_POST['landing'] );

	try {
		$generator = new RHM_Demo_Generator();
		if ( $fresh ) {
			$generator->delete_content();
		}
		$result = $generator->generate( $posts, $doctors );
		$page   = $landing ? $generator->landing() : null;
	} catch ( Throwable $e ) {
		rhm_demo_redirect( 'error', 'Lỗi: ' . $e->getMessage() );
	}

	$message = sprintf( 'Đã tạo %d bài viết và %d hồ sơ bác sĩ (dùng %d ảnh mẫu) trong %s giây.', $result['posts'], $result['doctors'], $result['images'], $result['seconds'] );
	if ( $page ) {
		$message .= ' Trang landing mẫu: ' . $page['url'];
	}
	rhm_demo_redirect( 'success', $message );
}
add_action( 'admin_post_rhm_demo_generate', 'rhm_demo_handle_generate' );

function rhm_demo_handle_landing(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Không có quyền.' );
	}
	check_admin_referer( 'rhm_demo_landing' );

	try {
		$result = ( new RHM_Demo_Generator() )->landing();
	} catch ( Throwable $e ) {
		rhm_demo_redirect( 'error', 'Lỗi: ' . $e->getMessage() );
	}
	rhm_demo_redirect( 'success', sprintf( 'Đã tạo trang landing mẫu #%d (%s giây): %s', $result['id'], $result['seconds'], $result['url'] ) );
}
add_action( 'admin_post_rhm_demo_landing', 'rhm_demo_handle_landing' );

function rhm_demo_handle_delete(): void {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( 'Không có quyền.' );
	}
	check_admin_referer( 'rhm_demo_delete' );

	$result = ( new RHM_Demo_Generator() )->delete();
	$message = sprintf( 'Đã xóa %d bài viết/hồ sơ/trang, %d ảnh và %d danh mục/thẻ mẫu.', $result['posts'], $result['images'], $result['terms'] );
	if ( $result['kept_terms'] ) {
		$message .= ' Giữ lại (đang dùng trong menu hoặc còn bài thật): ' . implode( ', ', $result['kept_terms'] ) . '. Gỡ khỏi menu rồi bấm xóa lần nữa để dọn hết.';
	}
	rhm_demo_redirect( 'success', $message );
}
add_action( 'admin_post_rhm_demo_delete', 'rhm_demo_handle_delete' );
