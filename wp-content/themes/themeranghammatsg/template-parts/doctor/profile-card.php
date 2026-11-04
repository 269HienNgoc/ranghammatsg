<?php
defined( 'ABSPATH' ) || exit;
$rhm_id       = $args['id'];
$rhm_position = sanitize_text_field( (string) rhm_get_doctor_field( 'position', $rhm_id ) );
?>
<article class="doctor-profile-card">
	<?php if ( has_post_thumbnail( $rhm_id ) ) : ?>
		<a class="doctor-profile-card__image" href="<?php echo esc_url( get_permalink( $rhm_id ) ); ?>" tabindex="-1" aria-hidden="true"><?php rhm_doctor_image( (int) get_post_thumbnail_id( $rhm_id ), $args['sizes'], 'doctor-portrait', get_the_title( $rhm_id ), false, 4 / 5 ); ?></a>
	<?php endif; ?>
	<div class="doctor-profile-card__body">
		<h3><a href="<?php echo esc_url( get_permalink( $rhm_id ) ); ?>"><?php echo esc_html( get_the_title( $rhm_id ) ); ?></a></h3>
		<?php if ( $rhm_position ) : ?><p><?php echo esc_html( $rhm_position ); ?></p><?php endif; ?>
	</div>
</article>
