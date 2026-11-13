<?php
/**
 * Landing – Nhóm ảnh 3 cột: tiêu đề + mô tả canh giữa → lần lượt từng item (tiêu đề H3 vạch dọc + mô tả + hàng ảnh 4:3).
 * Hàng ảnh: ≥ 640px lưới 3 cột (dư ảnh xuống hàng); < 640px 1 hàng vuốt ngang (scroll-snap, hé ảnh kế, CSS thuần, không JS).
 * Field: inc/landing/layouts/photo-groups.php. Thiếu tiêu đề section hoặc không có item hợp lệ (tiêu đề + ≥ 1 ảnh) → không hiện.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );

$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_name   = rhm_landing_text( $rhm_row, 'title' );
	$rhm_images = rhm_landing_gallery( $rhm_row, 'images' );
	if ( '' !== $rhm_name && $rhm_images ) {
		$rhm_items[] = array( $rhm_name, rhm_landing_lines( $rhm_row, 'text' ), $rhm_images );
	}
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_head_id = $args['id'] . '-title';
// Ảnh = 1 cột. ≥ 640px lưới 3 cột trong khung W (1408px ≥ 1440px, còn lại 100vw − 32px), cách 16px (sm) / 24px (lg):
// ≥ 1440px (1408 − 48) / 3 = 453px | 1024–1439px (100vw − 32 − 48) / 3 | 640–1023px (100vw − 32 − 32) / 3.
// < 640px thẻ 80% khung (hé ảnh kế): 0,8 × (100vw − 32) → calc(80vw − 26px); chỉ 1 ảnh → rộng hết khung.
$rhm_desktop_sizes = '(min-width: 1440px) 453px, (min-width: 1024px) calc(33.33vw - 27px), (min-width: 640px) calc(33.33vw - 21px), ';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'center' ); ?>
		<?php foreach ( rhm_landing_lines( $rhm_data, 'description' ) as $rhm_line ) : ?>
			<p class="mx-auto mb-3 max-w-4xl text-center leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
		<?php endforeach; ?>

		<div class="mt-6 space-y-10 lg:mt-8 lg:space-y-14">
			<?php foreach ( $rhm_items as $rhm_index => [ $rhm_name, $rhm_lines, $rhm_images ] ) : ?>
				<?php
				$rhm_item_id = $args['id'] . '-item-' . ( $rhm_index + 1 );
				$rhm_single  = 1 === count( $rhm_images );
				$rhm_sizes   = $rhm_desktop_sizes . ( $rhm_single ? 'calc(100vw - 32px)' : 'calc(80vw - 26px)' );
				?>
				<div>
					<h3 id="<?php echo esc_attr( $rhm_item_id ); ?>" class="border-l-4 border-primary pl-3 text-xl leading-snug font-bold text-primary lg:text-2xl"><?php echo esc_html( $rhm_name ); ?></h3>
					<?php foreach ( $rhm_lines as $rhm_line ) : ?>
						<p class="mt-3 leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
					<?php endforeach; ?>

					<?php
					// Mobile cuộn ngang → tabindex="0" để bàn phím cuộn được (axe: scrollable-region-focusable).
					// Track tràn ra mép màn hình (-mx-4 px-4); scroll-px-4 để điểm snap chừa lề 16px (không có thì ảnh dính sát mép trái).
					?>
					<ul class="-mx-4 mt-4 flex snap-x snap-mandatory scroll-px-4 gap-3 overflow-x-auto overscroll-x-contain px-4 pb-2 sm:mx-0 sm:grid sm:grid-cols-3 sm:gap-4 sm:overflow-visible sm:px-0 sm:pb-0 lg:mt-5 lg:gap-6" aria-labelledby="<?php echo esc_attr( $rhm_item_id ); ?>"<?php echo $rhm_single ? '' : ' tabindex="0"'; ?>>
						<?php foreach ( $rhm_images as $rhm_n => $rhm_image ) : ?>
							<li class="<?php echo esc_attr( $rhm_single ? 'w-full shrink-0 sm:w-auto' : 'w-[80%] shrink-0 snap-start sm:w-auto' ); ?>">
								<span class="relative block aspect-[4/3] overflow-hidden rounded-xl bg-soft shadow-card">
									<?php
									// Chỉ ảnh đầu của item đầu, khi section đứng đầu trang, mới là ảnh LCP.
									// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
									echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', $args['is_first'] && 0 === $rhm_index && 0 === $rhm_n );
									?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>
