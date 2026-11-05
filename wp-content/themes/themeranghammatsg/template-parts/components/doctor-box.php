<?php
/**
 * Box thông tin bác sĩ trong nội dung bài viết (shortcode [bac_si_tham_van], inc/doctor-box.php).
 * $args = rhm_get_doctor_box(): name, url, thumbnail, label, highlights. CSS: src/css/components/_doctor-box.css.
 * Tên in bằng <p> (không dùng h2/h3) để mục lục (inc/toc.php) không lấy làm mục.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_box = $args;
?>
<aside class="rhm-doctor-box<?php echo $rhm_box['thumbnail'] ? '' : ' rhm-doctor-box--no-photo'; ?>" aria-label="<?php esc_attr_e( 'Bác sĩ tham vấn chuyên môn', 'themeranghammatsg' ); ?>">
	<?php
	if ( $rhm_box['thumbnail'] ) {
		// Ảnh tròn 88px (< 640px) / 144px — khớp .rhm-doctor-box__photo trong _doctor-box.css.
		echo rhm_get_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_get_picture() escape thuộc tính.
			$rhm_box['thumbnail'],
			array( '(min-width: 640px)' => 144, '' => 88 ),
			array( 'class' => 'rhm-doctor-box__photo', 'alt' => $rhm_box['name'], 'loading' => 'lazy', 'decoding' => 'async' )
		);
	}
	?>
	<div class="rhm-doctor-box__head">
		<p class="rhm-doctor-box__label"><?php echo esc_html( $rhm_box['label'] ); ?></p>
		<p class="rhm-doctor-box__name"><?php echo esc_html( $rhm_box['name'] ); ?></p>
	</div>
	<?php if ( $rhm_box['highlights'] ) : ?>
		<ul class="rhm-doctor-box__list">
			<?php foreach ( $rhm_box['highlights'] as $rhm_line ) : ?>
				<li><?php echo rhm_get_icon( 'check', 'rhm-doctor-box__check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?><span><?php echo esc_html( $rhm_line ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<div class="rhm-doctor-box__actions">
		<?php rhm_booking_button( 'rhm-doctor-box__btn', 'size-5 shrink-0', true ); ?>
		<a class="btn btn-outline rhm-doctor-box__btn" href="<?php echo esc_url( $rhm_box['url'] ); ?>"><span><?php esc_html_e( 'Xem hồ sơ', 'themeranghammatsg' ); ?></span><?php echo rhm_get_icon( 'arrow-right', 'size-5 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></a>
	</div>
</aside>
