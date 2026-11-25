<?php
/**
 * Landing – Ảnh trước – sau khách hàng (slider). Field: inc/landing/layouts/before-after.php.
 *
 * Tiêu đề canh giữa (+ dòng nhỏ, mô tả) → dải checklist (icon chung, tự xuống hàng, canh giữa) → slider thẻ khách hàng
 * (thẻ dùng chung với trang chủ: template-parts/components/before-after-card.php).
 * Slider native (scroll-snap, không Swiper) + nút ‹ › do src/js/_tiles.js (nạp động từ carousel.js):
 * - ≥ 768px: 2 thẻ / khung, cuộn 1 thẻ / lần. ≥ 1024px: nút tròn ở 2 mép slider (giữa chiều cao thẻ, đè nửa lên mép thẻ);
 *   768–1023px: nút dưới slider.
 *   Section đứng đầu trang (tràn màn hình, không chừa cột nút liên hệ nhanh): nút luôn nằm dưới slider.
 * - < 768px: 1 thẻ rộng 88% (hé thẻ kế), nút nằm dưới slider (không che ảnh).
 * Bố cục dựng sẵn bằng CSS → chưa có JS vẫn vuốt được, CLS = 0. Nút giữ chỗ bằng invisible (_tiles.js bật khi track tràn).
 * Thiếu tiêu đề hoặc không còn khách hợp lệ (tên + 3 ảnh) → không in.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
$rhm_rows  = rhm_landing_rows( $rhm_data, 'items' );
if ( '' === $rhm_title || ! $rhm_rows ) {
	return;
}

// Nạp sẵn cache mọi ảnh của section (1 truy vấn) trước khi kiểm tra từng ảnh.
$rhm_ids = array();
foreach ( $rhm_rows as $rhm_row ) {
	foreach ( array( 'portrait', 'before', 'after' ) as $rhm_key ) {
		$rhm_ids[] = absint( $rhm_row[ $rhm_key ] ?? 0 );
	}
}
_prime_post_caches( array_filter( $rhm_ids ), false, true );

$rhm_items = array();
foreach ( $rhm_rows as $rhm_row ) {
	$rhm_item = array(
		'portrait' => rhm_landing_image( $rhm_row, 'portrait' ),
		'before'   => rhm_landing_image( $rhm_row, 'before' ),
		'after'    => rhm_landing_image( $rhm_row, 'after' ),
		'name'     => rhm_landing_text( $rhm_row, 'name' ),
		'location' => rhm_landing_text( $rhm_row, 'location' ),
		'service'  => rhm_landing_text( $rhm_row, 'service' ),
		'product'  => rhm_landing_text( $rhm_row, 'product' ),
	);
	if ( '' !== $rhm_item['name'] && $rhm_item['portrait'] && $rhm_item['before'] && $rhm_item['after'] ) {
		$rhm_items[] = $rhm_item;
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_count    = count( $rhm_items );
$rhm_head_id  = $args['id'] . '-title';
$rhm_track_id = $args['id'] . '-track';
$rhm_h3_attr  = rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'h3_color' ) );
$rhm_points   = rhm_landing_lines( $rhm_data, 'points' );
$rhm_icon     = rhm_landing_icon( $rhm_data, 'points_icon' ) ?: 'tich';

if ( $rhm_count > 1 ) {
	rhm_landing_enqueue_module( 'carousel' ); // carousel.js nạp _tiles.js khi có [data-rhm-tiles-controls].
}

// Độ rộng 1 cột ảnh (chân dung / trước / sau cùng cột) — bố cục thẻ giống trang chủ (2 thẻ / hàng từ 768px, cùng đệm).
// Khung nội dung W = min(100vw, 1280px) − 32px.
// ≥ 1280px: thẻ (1248 − 24) / 2 = 612 → trừ đệm 2×16 = 580 → (580 − 8) / 2 = 286px.
// 1024–1279px: thẻ 50vw − 28 → trừ đệm 32 → (50vw − 60 − 8) / 2 = 25vw − 34px (section không đứng đầu bị chừa cột nút
// liên hệ nhanh 2 × 90px → ảnh thật nhỏ hơn, file dư nhẹ: chấp nhận như các section landing khác).
// 768–1023px: thẻ W/2 − 8 = 50vw − 24 → trừ đệm 2×12 → (50vw − 48 − 8) / 2 = 25vw − 28px.
// < 768px: thẻ 88% W = 88vw − 28 → trừ đệm 24 → (88vw − 52 − 8) / 2 = 44vw − 30px.
$rhm_sizes = '(min-width: 1280px) 286px, (min-width: 1024px) calc(25vw - 34px), (min-width: 768px) calc(25vw - 28px), calc(44vw - 30px)';

?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-10 lg:py-14" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), rhm_landing_rich( $rhm_data, 'description' ), 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>

		<?php if ( $rhm_points ) : ?>
			<ul class="mx-auto mb-6 flex max-w-5xl flex-wrap justify-center gap-x-8 gap-y-3 lg:mb-8">
				<?php foreach ( $rhm_points as $rhm_point ) : ?>
					<li class="flex items-center gap-2 text-dark lg:text-lg">
						<?php echo rhm_icon_svg( 'landing', $rhm_icon, 'size-6 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
						<?php echo esc_html( $rhm_point ); ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

		<div class="relative" data-rhm-tiles>
			<?php
			// Đệm 8px ngang / 12px dọc + lề âm tương ứng: bóng thẻ không bị overflow của track cắt; scroll-padding giữ điểm dừng thẳng mép khung.
			?>
			<div id="<?php echo esc_attr( $rhm_track_id ); ?>" class="-mx-2 -my-3 flex snap-x snap-mandatory scroll-px-2 gap-4 overflow-x-auto overscroll-x-contain px-2 py-3 [scrollbar-width:none] lg:gap-6" role="region" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>" data-rhm-tiles-track<?php echo $rhm_count > 1 ? ' tabindex="0"' : ''; ?>>
				<?php
				foreach ( $rhm_items as $rhm_index => $rhm_item ) {
					get_template_part(
						'template-parts/components/before-after-card',
						null,
						array(
							'item'     => $rhm_item,
							'sizes'    => $rhm_sizes,
							'priority' => ! empty( $args['is_first'] ) && 0 === $rhm_index,
							'h3_attr'  => $rhm_h3_attr,
							'card'     => 'w-[88%] shrink-0 snap-start rounded-lg bg-white p-3 shadow-[0_6px_20px_rgb(16_42_67/0.1)] md:w-[calc(50%-8px)] lg:w-[calc(50%-12px)] lg:p-4',
							'media'    => 'rounded-md',
							'label'    => 'rounded-tr-md',
						)
					);
				}
				?>
			</div>

			<?php
			if ( $rhm_count > 1 ) {
				// ≥ 1024px nút ở 2 mép; section đứng đầu (tràn màn hình) → nút dưới slider (rhm_landing_tiles_nav()).
				rhm_landing_tiles_nav( $rhm_track_id, empty( $args['is_first'] ), __( 'Khách hàng trước', 'themeranghammatsg' ), __( 'Khách hàng tiếp theo', 'themeranghammatsg' ) );
			}
			?>
		</div>
	</div>
</section>
