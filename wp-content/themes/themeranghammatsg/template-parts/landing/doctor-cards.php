<?php
/**
 * Landing – Bác sĩ tiêu biểu: mobile ảnh vuông trên, tên H3 + mô tả dưới; từ 640px thẻ ngang, 2 thẻ / hàng ≥ 1024px.
 * Field: inc/landing/layouts/doctor-cards.php. Dữ liệu từ hồ sơ bac-si (đã xuất bản, có ảnh đại diện); không có ai → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data = $args['data'] ?? array();
$rhm_rows = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_id = absint( $rhm_row['doctor'] ?? 0 );
	if ( $rhm_id && 'bac-si' === get_post_type( $rhm_id ) && 'publish' === get_post_status( $rhm_id ) && ! isset( $rhm_rows[ $rhm_id ] ) ) {
		$rhm_rows[ $rhm_id ] = rhm_landing_lines( $rhm_row, 'text' );
	}
}
if ( ! $rhm_rows ) {
	return;
}

// Nạp sẵn meta hồ sơ + ảnh đại diện 1 lần (tránh N+1).
update_postmeta_cache( array_keys( $rhm_rows ) );
$rhm_thumbs = array_filter( array_map( 'get_post_thumbnail_id', array_keys( $rhm_rows ) ) );
if ( $rhm_thumbs ) {
	_prime_post_caches( $rhm_thumbs, false, true );
}

$rhm_doctors = array();
foreach ( $rhm_rows as $rhm_id => $rhm_lines ) {
	$rhm_image = (int) get_post_thumbnail_id( $rhm_id );
	if ( ! $rhm_image ) {
		continue;
	}
	if ( ! $rhm_lines ) {
		$rhm_excerpt = (string) get_post_field( 'post_excerpt', $rhm_id );
		$rhm_lines   = rhm_landing_lines( array( 'x' => '' !== trim( $rhm_excerpt ) ? $rhm_excerpt : (string) rhm_get_doctor_field( 'position', $rhm_id ) ), 'x' );
	}
	$rhm_doctors[] = array(
		'id'    => $rhm_id,
		'name'  => get_the_title( $rhm_id ),
		'url'   => (string) get_permalink( $rhm_id ),
		'image' => $rhm_image,
		'lines' => $rhm_lines,
	);
}
if ( ! $rhm_doctors ) {
	return;
}

$rhm_title   = rhm_landing_text( $rhm_data, 'title' ) ?: __( 'Đội ngũ bác sĩ chuyên môn cao', 'themeranghammatsg' );
$rhm_head_id = $args['id'] . '-title';
$rhm_single  = 1 === count( $rhm_doctors );
// Mobile: ảnh rộng cả thẻ, trừ lề khung 32px + viền 2px. Từ 640px: ảnh = 40% phần trong thẻ.
// Một thẻ: tối đa 692px từ viewport 724px → ảnh (692 − 2) × 40% = 276px.
// Nhiều thẻ: từ 1024px chia 2 cột, gap 24px → ảnh ((100vw − 32 − 24) / 2 − 2) × 40%; khung tối đa 1440px.
$rhm_sizes = $rhm_single
	? '(min-width: 724px) 276px, (min-width: 640px) calc(40vw - 14px), calc(100vw - 34px)'
	: '(min-width: 1440px) 276px, (min-width: 1024px) calc(20vw - 12px), (min-width: 640px) calc(40vw - 14px), calc(100vw - 34px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', rhm_landing_text( $rhm_data, 'description' ), 'center' ); ?>
		<div class="<?php echo esc_attr( $rhm_single ? 'mx-auto grid max-w-[692px] gap-6' : 'grid gap-6 lg:grid-cols-2' ); ?>">
			<?php foreach ( $rhm_doctors as $rhm_doctor ) : ?>
				<article class="grid grid-cols-1 items-center overflow-hidden rounded-2xl border border-line bg-white shadow-card sm:grid-cols-[40%_minmax(0,1fr)]" aria-labelledby="<?php echo esc_attr( $args['id'] . '-doctor-' . $rhm_doctor['id'] ); ?>">
					<a class="relative block aspect-square w-full min-w-0 self-start overflow-hidden bg-soft" href="<?php echo esc_url( $rhm_doctor['url'] ); ?>" tabindex="-1" aria-hidden="true">
						<?php rhm_doctor_image( $rhm_doctor['image'], $rhm_sizes, 'absolute inset-0 size-full object-cover object-top', $rhm_doctor['name'], false, 1 ); ?>
					</a>
					<div class="min-w-0 p-4 wrap-break-word sm:p-6 lg:p-8">
						<h3 id="<?php echo esc_attr( $args['id'] . '-doctor-' . $rhm_doctor['id'] ); ?>" class="text-base leading-snug font-bold text-primary uppercase sm:text-lg">
							<a class="hover:text-primary-dark" href="<?php echo esc_url( $rhm_doctor['url'] ); ?>"><?php echo esc_html( $rhm_doctor['name'] ); ?></a>
						</h3>
						<?php foreach ( $rhm_doctor['lines'] as $rhm_line ) : ?>
							<p class="mt-2 text-sm leading-relaxed text-body/85 sm:mt-3"><?php echo esc_html( $rhm_line ); ?></p>
						<?php endforeach; ?>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
