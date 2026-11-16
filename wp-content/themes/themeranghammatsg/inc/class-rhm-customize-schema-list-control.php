<?php
/**
 * Control Customizer "danh sách schema" (type rhm-schema-list) — Tùy biến → "Schema tùy chỉnh (head)".
 * Chỉ nạp khi mở Customizer (rhm_customize_head_schema()). Giao diện do assets/admin/customize-head-schema.js dựng
 * từ JSON của setting rhm_head_schemas: mỗi mục = tên + ô code (CodeMirror JSON) + bật/tắt, thêm/sắp xếp/xóa.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

class RHM_Customize_Schema_List_Control extends WP_Customize_Control {

	/** @var string */
	public $type = 'rhm-schema-list';

	/** @var array|false Settings code editor (false = người dùng tắt tô màu cú pháp trong Hồ sơ → textarea thường). */
	private $editor_settings = false;

	public function enqueue(): void {
		$this->editor_settings = wp_enqueue_code_editor( array( 'type' => 'application/json' ) );
		wp_enqueue_style( 'rhm-customize-head-schema', RHM_THEME_URI . '/assets/admin/customize-head-schema.css', array( 'customize-controls' ), rhm_asset_version( 'assets/admin/customize-head-schema.css' ) );
		wp_enqueue_script( 'rhm-customize-head-schema', RHM_THEME_URI . '/assets/admin/customize-head-schema.js', array( 'customize-controls' ), rhm_asset_version( 'assets/admin/customize-head-schema.js' ), true );
	}

	public function to_json(): void {
		parent::to_json();

		$this->json['editorSettings'] = $this->editor_settings;
		$this->json['l10n']           = array(
			/* translators: %d: schema number */
			'untitled'      => __( 'Schema %d – chưa đặt tên', 'themeranghammatsg' ),
			'name'          => __( 'Tên schema', 'themeranghammatsg' ),
			'namePlaceholder' => __( 'vd Phòng khám chính, FAQ trang chủ…', 'themeranghammatsg' ),
			'code'          => __( 'Nội dung (JSON-LD, có hoặc không kèm thẻ <script>)', 'themeranghammatsg' ),
			'enabled'       => __( 'Bật', 'themeranghammatsg' ),
			'disabled'      => __( 'Đang tắt', 'themeranghammatsg' ),
			'hasError'      => __( 'Lỗi JSON', 'themeranghammatsg' ),
			'moveUp'        => __( 'Chuyển lên', 'themeranghammatsg' ),
			'moveDown'      => __( 'Chuyển xuống', 'themeranghammatsg' ),
			'remove'        => __( 'Xóa schema', 'themeranghammatsg' ),
			'confirmRemove' => __( 'Xóa schema này?', 'themeranghammatsg' ),
			'empty'         => __( 'Chưa có schema nào. Bấm "+ Thêm mới" để bắt đầu.', 'themeranghammatsg' ),
			/* translators: %s: schema names */
			'lintError'     => __( 'Sửa lỗi JSON trước khi lưu: %s', 'themeranghammatsg' ),
			'multiBlock'    => __( 'Mỗi schema chỉ 1 khối <script> — bấm "+ Thêm mới" để tách.', 'themeranghammatsg' ),
		);
	}

	protected function render_content(): void {
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<ol class="rhm-schema-list"></ol>
		<p class="rhm-schema-empty description"></p>
		<p><button type="button" class="button button-primary rhm-schema-add"><?php esc_html_e( '+ Thêm mới', 'themeranghammatsg' ); ?></button></p>
		<?php
	}
}
