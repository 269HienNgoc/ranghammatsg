<?php
/**
 * Landing – Danh sách dịch vụ: tiêu đề canh giữa + thẻ (ảnh 16:10 + tên trên dải mint). Field: inc/landing/layouts/services.php.
 * Mobile/tablet: 1 hàng cuộn ngang (scroll-snap, CSS thuần, không JS); ≥ 1024px: lưới 5 cột (tối đa), hàng sau tiếp tục.
 * Không có dịch vụ hợp lệ (ảnh + tên) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_image = rhm_landing_image( $rhm_row, 'image' );
	$rhm_name  = rhm_landing_text( $rhm_row, 'title' );
	if ( $rhm_image && '' !== $rhm_name ) {
		$rhm_items[] = array( $rhm_image, $rhm_name, rhm_landing_url( $rhm_row, 'link' ) );
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_title   = rhm_landing_text( $rhm_data, 'title' ) ?: __( 'Đa dạng dịch vụ nha khoa', 'themeranghammatsg' );
$rhm_head_id = $args['id'] . '-title';
$rhm_count   = count( $rhm_items );
$rhm_grid    = match ( true ) {
	$rhm_count <= 3 => 'lg:grid lg:grid-cols-3',
	4 === $rhm_count => 'lg:grid lg:grid-cols-4',
	default          => 'lg:grid lg:grid-cols-5',
};
// Ảnh = độ rộng thẻ. ≥ 1024px lưới N cột cách 16px trong khung 1408px; < 1024px thẻ 45% (sm 30%) của 100vw − 32px.
$rhm_desktop = match ( true ) {
	$rhm_count <= 3 => '(min-width: 1440px) 459px, (min-width: 1024px) calc(33.33vw - 21px)',
	4 === $rhm_count => '(min-width: 1440px) 340px, (min-width: 1024px) calc(25vw - 20px)',
	default          => '(min-width: 1440px) 269px, (min-width: 1024px) calc(20vw - 19px)',
};
$rhm_sizes = $rhm_desktop . ', (min-width: 640px) calc(30vw - 10px), calc(45vw - 14px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', rhm_landing_text( $rhm_data, 'description' ), 'center' ); ?>
		<ul class="<?php echo esc_attr( '-mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-2 lg:mx-0 lg:gap-4 lg:overflow-visible lg:px-0 lg:pb-0 ' . $rhm_grid ); ?>">
			<?php foreach ( $rhm_items as [ $rhm_image, $rhm_name, $rhm_url ] ) : ?>
				<li class="w-[45%] shrink-0 snap-start sm:w-[30%] lg:w-auto">
					<?php if ( '' !== $rhm_url ) : ?>
						<a class="group block h-full overflow-hidden rounded-xl bg-mint shadow-card" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
					<?php else : ?>
						<div class="h-full overflow-hidden rounded-xl bg-mint shadow-card">
					<?php endif; ?>
						<span class="relative block aspect-[16/10] overflow-hidden bg-soft">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
							echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover transition-transform duration-300 group-hover:scale-105', false );
							?>
						</span>
						<span class="block px-2 py-3 text-center text-sm leading-snug font-bold text-primary lg:text-base"><?php echo esc_html( $rhm_name ); ?></span>
					<?php echo '' !== $rhm_url ? '</a>' : '</div>'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
