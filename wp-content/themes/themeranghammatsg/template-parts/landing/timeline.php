<?php
/**
 * Landing – Chặng đường phát triển: danh sách mốc dọc (năm + H3 + mô tả) trên đường kẻ trái. Field: inc/landing/layouts/timeline.php.
 * Thiếu tiêu đề section hoặc không có mốc hợp lệ (năm + tiêu đề) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_items = array();
foreach ( (array) ( $rhm_data['items'] ?? array() ) as $rhm_row ) {
	$rhm_row  = (array) $rhm_row;
	$rhm_year = rhm_landing_text( $rhm_row, 'year' );
	$rhm_name = rhm_landing_text( $rhm_row, 'title' );
	if ( '' !== $rhm_year && '' !== $rhm_name ) {
		$rhm_items[] = array( $rhm_year, $rhm_name, rhm_landing_lines( $rhm_row, 'text' ) );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container max-w-[868px]">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'center' ); ?>
		<ol class="ml-2 space-y-8 border-l-2 border-primary/20 pl-6 lg:pl-8">
			<?php foreach ( $rhm_items as [ $rhm_year, $rhm_name, $rhm_lines ] ) : ?>
				<li class="relative">
					<span class="absolute top-1 -left-[33px] size-4 rounded-full bg-primary ring-4 ring-mint lg:-left-[41px]" aria-hidden="true"></span>
					<p class="text-lg font-bold text-primary"><?php echo esc_html( $rhm_year ); ?></p>
					<h3 class="mt-1 text-xl leading-snug font-bold text-dark"><?php echo esc_html( $rhm_name ); ?></h3>
					<?php foreach ( $rhm_lines as $rhm_line ) : ?>
						<p class="mt-2 leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
					<?php endforeach; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
