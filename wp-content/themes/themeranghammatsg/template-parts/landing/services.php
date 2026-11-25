<?php
/**
 * Landing – Danh sách dịch vụ: tiêu đề canh giữa + thẻ (ảnh 4:3 + tên trên dải mint; 2026-11-25 tăng từ 16:10 để ảnh người bớt bị cắt). Field: inc/landing/layouts/services.php.
 *
 * Số dịch vụ mỗi hàng desktop (≥ 1024px) = field "columns" (3 | 4 | 5); trống = Tự động, chọn số cột không để lẻ 1 thẻ
 * ở hàng cuối (tính ngay bên dưới, biến $rhm_cols). 2 chế độ:
 * - Lưới (mặc định): < 1024px 1 hàng cuộn ngang (scroll-snap, CSS thuần, không JS); ≥ 1024px lưới N cột, dư xuống hàng.
 * - Slider (field "slider" bật VÀ số dịch vụ > N): mọi màn hình 1 hàng trượt ngang, ≥ 1024px N thẻ / khung; nút ‹ ›
 *   (rhm_landing_tiles_nav(), _tiles.js) ở 2 mép từ 1024px, dưới slider khi nhỏ hơn hoặc section đứng đầu trang.
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
		// Vị trí cắt ảnh trong khung 4:3 (class nguyên văn; '' = giữa, mặc định của object-cover).
		$rhm_pos     = match ( $rhm_row['position'] ?? '' ) {
			'top'    => ' object-top',
			'bottom' => ' object-bottom',
			default  => '',
		};
		$rhm_items[] = array( $rhm_image, $rhm_name, rhm_landing_url( $rhm_row, 'link' ), $rhm_pos );
	}
}
if ( ! $rhm_items ) {
	return;
}

$rhm_title   = rhm_landing_text( $rhm_data, 'title' ) ?: __( 'Đa dạng dịch vụ nha khoa', 'themeranghammatsg' );
$rhm_head_id = $args['id'] . '-title';
$rhm_count   = count( $rhm_items );

// Số cột desktop: chọn tay 3/4/5, còn lại Tự động — ≤ 5 thẻ: 1 hàng (tối thiểu 3 cột để thẻ không quá to);
// nhiều hơn: số cột 3–5 chia hết số thẻ (ưu tiên 4 → 3 → 5), không chia hết thì cột cho hàng cuối đầy nhất.
$rhm_cols = (int) ( $rhm_data['columns'] ?? 0 );
if ( ! in_array( $rhm_cols, array( 3, 4, 5 ), true ) ) {
	if ( $rhm_count <= 5 ) {
		$rhm_cols = max( 3, $rhm_count );
	} else {
		$rhm_cols = 4;
		$rhm_best = -1;
		foreach ( array( 4, 3, 5 ) as $rhm_try ) {
			$rhm_last = $rhm_count % $rhm_try ?: $rhm_try; // Số thẻ ở hàng cuối.
			$rhm_fill = $rhm_last / $rhm_try;
			if ( $rhm_fill > $rhm_best ) {
				$rhm_best = $rhm_fill;
				$rhm_cols = $rhm_try;
			}
		}
	}
}

$rhm_slider = ! empty( $rhm_data['slider'] ) && $rhm_count > $rhm_cols;
$rhm_track  = $args['id'] . '-track';

// Class viết nguyên văn (Tailwind quét file này). Lưới: lg:grid N cột. Slider: thẻ ≥ 1024px = (khung − (N − 1) × 16px) / N.
$rhm_grid = match ( $rhm_cols ) {
	3       => 'lg:grid lg:grid-cols-3',
	4       => 'lg:grid lg:grid-cols-4',
	default => 'lg:grid lg:grid-cols-5',
};
$rhm_slide = match ( $rhm_cols ) {
	3       => 'lg:w-[calc((100%-32px)/3)]',
	4       => 'lg:w-[calc((100%-48px)/4)]',
	default => 'lg:w-[calc((100%-64px)/5)]',
};
$rhm_list = $rhm_slider
	// Đệm 8px ngang / 12px dọc + lề âm (≥ 1024px): bóng thẻ không bị overflow của track cắt; scroll-padding giữ điểm dừng thẳng mép.
	? '-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto overscroll-x-contain px-4 pb-2 [scrollbar-width:none] lg:-mx-2 lg:-my-3 lg:scroll-px-2 lg:px-2 lg:py-3'
	// scroll-px-4: điểm dừng snap tính cả lề 16px (thiếu thì thẻ đầu dính sát mép trái màn hình trên mobile).
	: '-mx-4 flex snap-x snap-mandatory scroll-px-4 gap-4 overflow-x-auto px-4 pb-2 lg:mx-0 lg:gap-4 lg:overflow-visible lg:px-0 lg:pb-0 ' . $rhm_grid;
$rhm_item_class = 'w-[45%] shrink-0 snap-start sm:w-[30%] ' . ( $rhm_slider ? $rhm_slide : 'lg:w-auto' );

// Ảnh = độ rộng thẻ (2 chế độ cùng độ rộng). ≥ 1024px: N cột cách 16px trong khung 1248px (≥ 1280px) / 100vw − 32px;
// < 1024px: thẻ 45% (sm 30%) của 100vw − 32px. Section không đứng đầu bị chừa cột nút liên hệ nhanh ở 1024–1479px → dư nhẹ.
$rhm_desktop = match ( $rhm_cols ) {
	3       => '(min-width: 1280px) 406px, (min-width: 1024px) calc(33.33vw - 21px)',
	4       => '(min-width: 1280px) 300px, (min-width: 1024px) calc(25vw - 20px)',
	default => '(min-width: 1280px) 237px, (min-width: 1024px) calc(20vw - 19px)',
};
$rhm_sizes = $rhm_desktop . ', (min-width: 640px) calc(30vw - 10px), calc(45vw - 14px)';

if ( $rhm_slider ) {
	rhm_landing_enqueue_module( 'carousel' ); // carousel.js nạp _tiles.js khi có [data-rhm-tiles-controls].
}
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', rhm_landing_rich( $rhm_data, 'description' ), 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>
		<?php if ( $rhm_slider ) : ?>
			<div class="relative" data-rhm-tiles>
		<?php endif; ?>
		<ul<?php echo $rhm_slider ? ' id="' . esc_attr( $rhm_track ) . '" aria-labelledby="' . esc_attr( $rhm_head_id ) . '" tabindex="0" data-rhm-tiles-track' : ''; ?> class="<?php echo esc_attr( $rhm_list ); ?>">
			<?php foreach ( $rhm_items as [ $rhm_image, $rhm_name, $rhm_url, $rhm_pos ] ) : ?>
				<li class="<?php echo esc_attr( $rhm_item_class ); ?>">
					<?php if ( '' !== $rhm_url ) : ?>
						<a class="group block h-full overflow-hidden rounded-lg bg-mint shadow-card" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo rhm_external_link_attrs( $rhm_url ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
					<?php else : ?>
						<div class="h-full overflow-hidden rounded-lg bg-mint shadow-card">
					<?php endif; ?>
						<span class="relative block aspect-[4/3] overflow-hidden bg-soft">
							<?php
							// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
							echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover transition-transform duration-300 group-hover:scale-105' . $rhm_pos, false );
							?>
						</span>
						<span class="block px-2 py-3 text-center text-sm leading-snug font-bold text-primary lg:text-base"><?php echo esc_html( $rhm_name ); ?></span>
					<?php echo '' !== $rhm_url ? '</a>' : '</div>'; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( $rhm_slider ) : ?>
				<?php rhm_landing_tiles_nav( $rhm_track, empty( $args['is_first'] ), __( 'Dịch vụ trước', 'themeranghammatsg' ), __( 'Dịch vụ tiếp theo', 'themeranghammatsg' ) ); ?>
			</div>
		<?php endif; ?>
	</div>
</section>
