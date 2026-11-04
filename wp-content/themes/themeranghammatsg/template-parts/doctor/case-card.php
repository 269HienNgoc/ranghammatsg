<?php
defined( 'ABSPATH' ) || exit;
$rhm_item = $args['item'];
// Trong khung 4:3: trái 1/2, phải 1/2 trừ gap 8px; border/padding của card thêm 18px.
$rhm_sizes = '(min-width: 1440px) 214px, (min-width: 1024px) calc((100vw - 158px) / 6), (min-width: 640px) calc(25vw - 29px), calc(42.5vw - 35px)';
?>
<article class="doctor-case">
	<div class="doctor-case__images">
		<?php rhm_doctor_image( $rhm_item['portrait'], $rhm_sizes, 'doctor-case__portrait', sprintf( __( 'Khách hàng %s', 'themeranghammatsg' ), $rhm_item['name'] ), false, 2 / 3 ); ?>
		<?php foreach ( array( 'before' => __( 'Trước điều trị', 'themeranghammatsg' ), 'after' => __( 'Sau điều trị', 'themeranghammatsg' ) ) as $rhm_key => $rhm_label ) : ?>
			<figure class="doctor-case__comparison">
				<?php rhm_doctor_image( $rhm_item[ $rhm_key ], $rhm_sizes, 'doctor-case__image', $rhm_label . ' — ' . $rhm_item['name'], false, 4 / 3 ); ?>
				<figcaption><?php echo esc_html( $rhm_label ); ?></figcaption>
			</figure>
		<?php endforeach; ?>
	</div>
	<div class="doctor-case__body">
		<div class="doctor-case__heading"><h3><?php echo esc_html( $rhm_item['name'] ); ?></h3><?php if ( $rhm_item['location'] ) : ?><p><?php echo esc_html( $rhm_item['location'] ); ?></p><?php endif; ?></div>
		<?php foreach ( array( 'method' => __( 'Phương án', 'themeranghammatsg' ), 'treatment' => __( 'Loại điều trị', 'themeranghammatsg' ) ) as $rhm_key => $rhm_label ) : ?>
			<?php if ( $rhm_item[ $rhm_key ] ) : ?><p class="doctor-case__detail"><strong><?php echo esc_html( $rhm_label ); ?>:</strong> <?php echo esc_html( $rhm_item[ $rhm_key ] ); ?></p><?php endif; ?>
		<?php endforeach; ?>
	</div>
</article>
