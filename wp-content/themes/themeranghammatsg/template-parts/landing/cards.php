<?php
/**
 * Landing – Thẻ giá trị: khối nền trắng (tiêu đề vạch dọc + lưới thẻ icon nét / H3 / mô tả, canh giữa) + ảnh bên phải (tùy chọn).
 * Field: inc/landing/layouts/cards.php. Thiếu tiêu đề hoặc không có thẻ hợp lệ → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_name = rhm_landing_text( $rhm_row, 'title' );
	if ( '' !== $rhm_name ) {
		$rhm_items[] = array( rhm_landing_icon( $rhm_row, 'icon' ), $rhm_name, rhm_landing_lines( $rhm_row, 'text' ) );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_head_id = $args['id'] . '-title';
$rhm_count   = count( $rhm_items );
// Có ảnh: khối thẻ 2/3, tối đa 3 thẻ/hàng. Không ảnh: 4 thẻ/hàng khi số thẻ chia hết cho 4.
$rhm_cols = ( ! $rhm_image && 0 === $rhm_count % 4 ) ? 'grid gap-6 sm:grid-cols-2 lg:grid-cols-4' : 'grid gap-6 sm:grid-cols-3';
// Ảnh: cột 1/3 của khung 1408px, cách 24px → (1408 − 24) / 3 ≈ 461px; 1024–1439px: (100vw − 32 − 24) / 3; mobile: 100vw − 32px.
$rhm_sizes = '(min-width: 1440px) 461px, (min-width: 1024px) calc(33.33vw - 19px), calc(100vw - 32px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_image ? 'site-container grid gap-6 lg:grid-cols-3' : 'site-container' ); ?>">
		<div class="rounded-2xl border border-line bg-white/80 p-5 shadow-card lg:col-span-2 lg:p-8">
			<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), rhm_landing_text( $rhm_data, 'description' ), 'bar' ); ?>
			<ul class="<?php echo esc_attr( $rhm_cols ); ?>">
				<?php foreach ( $rhm_items as [ $rhm_icon, $rhm_name, $rhm_lines ] ) : ?>
					<li class="text-center">
						<?php if ( $rhm_icon ) : ?>
							<?php echo rhm_icon_svg( 'landing', $rhm_icon, 'mx-auto mb-3 size-12 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
						<?php endif; ?>
						<h3 class="text-lg leading-snug font-bold text-primary"><?php echo esc_html( $rhm_name ); ?></h3>
						<?php foreach ( $rhm_lines as $rhm_line ) : ?>
							<p class="mt-2 text-sm leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
						<?php endforeach; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

		<?php if ( $rhm_image ) : ?>
			<div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-soft shadow-card lg:aspect-auto">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', (bool) $args['is_first'] );
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
