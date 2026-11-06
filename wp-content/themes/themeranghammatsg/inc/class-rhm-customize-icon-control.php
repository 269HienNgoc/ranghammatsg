<?php
/**
 * Control Customizer "chọn icon" (type rhm-icon): lưới ảnh xem trước từ thư viện assets/icons/<bộ>/.
 * Chỉ nạp khi mở Customizer (rhm_customize_fields() với kiểu 'rhm-icon').
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

class RHM_Customize_Icon_Control extends WP_Customize_Control {

	/** @var string */
	public $type = 'rhm-icon';

	/** Bộ icon (khóa của RHM_ICON_SETS). */
	public string $icon_set = 'services';

	/** CSS dùng chung cho mọi control loại này: in 1 lần. */
	private static bool $printed_css = false;

	public function render_content(): void {
		$icons = rhm_icon_list( $this->icon_set );
		$name  = '_customize-radio-' . $this->id;
		$value = (string) $this->value();

		if ( ! self::$printed_css ) {
			self::$printed_css = true;
			?>
			<style>
				.rhm-icon-picker { display: grid; grid-template-columns: repeat(auto-fill, minmax(44px, 1fr)); gap: 6px; margin-top: 6px; }
				.rhm-icon-picker label { position: relative; display: flex; height: 44px; align-items: center; justify-content: center; border: 1px solid #dcdcde; border-radius: 6px; background: #fff; cursor: pointer; }
				.rhm-icon-picker input { position: absolute; opacity: 0; pointer-events: none; }
				.rhm-icon-picker img { width: 30px; height: 30px; }
				.rhm-icon-picker .rhm-icon-none { font-size: 11px; line-height: 1.1; text-align: center; color: #646970; }
				.rhm-icon-picker label:hover { border-color: #0b7a55; }
				.rhm-icon-picker label:has(input:checked) { border-color: #0b7a55; background: #ecf7f3; box-shadow: 0 0 0 1px #0b7a55; }
				.rhm-icon-picker label:has(input:focus-visible) { outline: 2px solid #2271b1; outline-offset: 1px; }
			</style>
			<?php
		}
		?>
		<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php if ( $this->description ) : ?>
			<span class="description customize-control-description"><?php echo wp_kses_post( $this->description ); ?></span>
		<?php endif; ?>
		<div class="rhm-icon-picker">
			<label title="<?php esc_attr_e( 'Không chọn (dùng ảnh tải lên bên dưới)', 'themeranghammatsg' ); ?>">
				<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="" <?php $this->link(); ?> <?php checked( '', $value ); ?>>
				<span class="rhm-icon-none"><?php esc_html_e( 'Không', 'themeranghammatsg' ); ?></span>
			</label>
			<?php foreach ( $icons as $icon => $label ) : ?>
				<label title="<?php echo esc_attr( $label ); ?>">
					<input type="radio" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $icon ); ?>" <?php $this->link(); ?> <?php checked( $icon, $value ); ?>>
					<img src="<?php echo esc_url( rhm_icon_url( $this->icon_set, $icon ) ); ?>" alt="<?php echo esc_attr( $label ); ?>" width="30" height="30" loading="lazy">
				</label>
			<?php endforeach; ?>
		</div>
		<?php
	}
}
