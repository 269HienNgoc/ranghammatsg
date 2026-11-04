<?php
/** Banner tĩnh: dùng cùng picture art direction và kích thước của homepage. */
defined( 'ABSPATH' ) || exit;
$rhm_doctor = $args['doctor'];
if ( ! $rhm_doctor['banner_desktop'] ) {
	return;
}
?>
<div class="doctor-hero">
	<?php echo rhm_hero_picture( array( 'desktop' => $rhm_doctor['banner_desktop'], 'mobile' => $rhm_doctor['banner_mobile'] ), true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped helper. ?>
</div>
