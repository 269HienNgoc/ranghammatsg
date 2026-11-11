<?php
/**
 * Landing – Số liệu nổi bật: lưới ô số + nhãn (2 cột mobile, tối đa 4 cột desktop). Field: inc/landing/layouts/stats.php.
 * Không có ô hợp lệ (có số) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_items = array();
foreach ( (array) ( $rhm_data['items'] ?? array() ) as $rhm_row ) {
	$rhm_number = rhm_landing_text( (array) $rhm_row, 'number' );
	if ( '' !== $rhm_number ) {
		$rhm_items[] = array( $rhm_number, rhm_landing_text( (array) $rhm_row, 'label' ) );
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_title   = rhm_landing_text( $rhm_data, 'title' );
$rhm_head_id = $args['id'] . '-title';
// 3 ô → 3 cột (không để 1 ô lẻ hàng dưới); còn lại tối đa 4 cột.
$rhm_cols = 3 === count( $rhm_items ) ? 'grid grid-cols-2 gap-3 sm:grid-cols-3 lg:gap-6' : 'grid grid-cols-2 gap-3 sm:grid-cols-4 lg:gap-6';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8"<?php echo '' !== $rhm_title ? ' aria-labelledby="' . esc_attr( $rhm_head_id ) . '"' : ''; ?>>
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'center' ); ?>
		<ul class="<?php echo esc_attr( $rhm_cols ); ?>">
			<?php foreach ( $rhm_items as [ $rhm_number, $rhm_label ] ) : ?>
				<li class="rounded-2xl border border-line bg-white px-3 py-5 text-center shadow-card lg:py-7">
					<p class="text-[28px] leading-tight font-bold text-primary lg:text-4xl"><?php echo esc_html( $rhm_number ); ?></p>
					<?php if ( '' !== $rhm_label ) : ?>
						<p class="mt-1 text-sm leading-snug text-muted lg:text-base"><?php echo esc_html( $rhm_label ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
