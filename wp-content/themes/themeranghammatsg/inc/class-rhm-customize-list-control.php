<?php
/**
 * Control Customizer "danh sách động" (type rhm-list). Chỉ nạp khi mở Customizer (rhm_customize_list()).
 * Giao diện từng mục do assets/admin/customize-list.js dựng từ JSON của setting.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

class RHM_Customize_List_Control extends WP_Customize_Control {

	/** @var string */
	public $type = 'rhm-list';

	/** @var array<string, array{0:string, 1:string, 2?:array}> field => [nhãn, kiểu, choices (kiểu select)]. */
	public array $fields = array();

	/** Tên 1 mục (vd "Chứng nhận") → tiêu đề "Chứng nhận 1, 2…". */
	public string $item_label = '';

	public function enqueue(): void {
		wp_enqueue_media();
		wp_enqueue_style( 'rhm-customize-list', RHM_THEME_URI . '/assets/admin/customize-list.css', array(), rhm_asset_version( 'assets/admin/customize-list.css' ) );
		wp_enqueue_script( 'rhm-customize-list', RHM_THEME_URI . '/assets/admin/customize-list.js', array( 'customize-controls', 'media-views' ), rhm_asset_version( 'assets/admin/customize-list.js' ), true );
	}

	public function to_json(): void {
		parent::to_json();

		// Ảnh xem trước cho các mục đã lưu (ảnh chọn mới lấy URL từ thư viện Media trong JS).
		$images = array();
		foreach ( rhm_decode_list( $this->value() ) as $item ) {
			foreach ( $this->fields as $key => $field ) {
				$id  = 'image' === $field[1] ? absint( $item[ $key ] ?? 0 ) : 0;
				$src = $id ? wp_get_attachment_image_url( $id, 'medium' ) : false;
				if ( $src ) {
					$images[ $id ] = $src;
				}
			}
		}

		$fields = array();
		foreach ( $this->fields as $key => $field ) {
			$fields[ $key ] = array(
				'label' => $field[0],
				'type'  => $field[1],
			);
			if ( 'select' === $field[1] ) {
				// Mảng [[giá trị, nhãn], …] thay vì object: object có khóa số bị JS tự sắp lại, mất thứ tự.
				$fields[ $key ]['choices'] = array();
				foreach ( (array) ( $field[2] ?? array() ) as $value => $label ) {
					$fields[ $key ]['choices'][] = array( (string) $value, (string) $label );
				}
			}
		}

		$this->json['fields']    = $fields;
		$this->json['itemLabel'] = $this->item_label;
		$this->json['images']    = (object) $images;
		$this->json['l10n']      = array(
			'chooseImage'   => __( 'Chọn ảnh', 'themeranghammatsg' ),
			'choose'        => __( '— Chọn —', 'themeranghammatsg' ),
			'addImages'     => __( 'Chọn nhiều ảnh (giữ Ctrl/Shift để chọn nhiều)', 'themeranghammatsg' ),
			'select'        => __( 'Dùng ảnh này', 'themeranghammatsg' ),
			'noImage'       => __( 'Bấm để chọn ảnh', 'themeranghammatsg' ),
			'removeImage'   => __( 'Bỏ ảnh', 'themeranghammatsg' ),
			'moveUp'        => __( 'Chuyển lên', 'themeranghammatsg' ),
			'moveDown'      => __( 'Chuyển xuống', 'themeranghammatsg' ),
			'remove'        => __( 'Xóa mục', 'themeranghammatsg' ),
			'confirmRemove' => __( 'Xóa mục này?', 'themeranghammatsg' ),
			'empty'         => __( 'Chưa có mục nào. Bấm "Thêm mới" để bắt đầu.', 'themeranghammatsg' ),
		);
	}

	protected function render_content(): void {
		$has_image = in_array( 'image', array_map( static fn( array $field ): string => $field[1], $this->fields ), true );
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
		<?php endif; ?>
		<ol class="rhm-list"></ol>
		<p class="rhm-list-empty description"></p>
		<p class="rhm-list-actions">
			<button type="button" class="button button-primary rhm-list-add"><?php esc_html_e( '+ Thêm mới', 'themeranghammatsg' ); ?></button>
			<?php if ( $has_image ) : ?>
				<button type="button" class="button rhm-list-add-images"><?php esc_html_e( 'Thêm nhiều ảnh', 'themeranghammatsg' ); ?></button>
			<?php endif; ?>
		</p>
		<?php
	}
}
