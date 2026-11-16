<?php
/**
 * Landing – Ảnh lớn + slider tab: tiêu đề H2 canh giữa → 1 bên ảnh lớn (trái / phải, chọn ở section), 1 bên thẻ slider:
 * hàng tab (tên slide) → slide = ảnh tròn (trái / phải, chọn từng slide) + tiêu đề H3 + danh sách tích + nút Tư vấn.
 * Mobile: ảnh lớn trên, thẻ slider dưới; trong slide ảnh tròn trên chữ.
 * Field: inc/landing/layouts/tab-slider.php. Thiếu tiêu đề section hoặc không có slide hợp lệ (tiêu đề + danh sách) → không hiện.
 *
 * Hiệu năng / truy cập:
 * - Không Swiper: track native scroll + scroll-snap (vuốt được khi chưa có JS, bố cục dựng bằng CSS → CLS = 0);
 *   slide cao bằng nhau (flex stretch) → đổi slide không làm trang nhảy.
 * - JS nạp qua carousel.js (chỉ khi ≥ 2 slide): _tiles.js (nút ‹ ›) + _showcase.js (tab ↔ slide đang xem, aria-current).
 * - Nút Tư vấn: href = rhm_get_booking_url() (trang có section form trùng form popup → #<id form>), có popup → data-rhm-popup.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_h3_attr = rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'h3_color' ) ); // Màu mọi H3 của section ('' = màu thiết kế).
$rhm_title   = rhm_landing_text( $rhm_data, 'title' );

$rhm_items = array();
foreach ( rhm_landing_rows( $rhm_data, 'items' ) as $rhm_row ) {
	$rhm_name   = rhm_landing_text( $rhm_row, 'title' );
	$rhm_points = rhm_landing_lines( $rhm_row, 'points' );
	if ( '' === $rhm_name || ! $rhm_points ) {
		continue;
	}
	$rhm_tab     = rhm_landing_text( $rhm_row, 'tab' );
	$rhm_items[] = array(
		'tab'    => '' !== $rhm_tab ? $rhm_tab : $rhm_name,
		'title'  => $rhm_name,
		'image'  => rhm_landing_image( $rhm_row, 'image' ),
		'right'  => 'right' === ( $rhm_row['image_position'] ?? 'left' ),
		'points' => $rhm_points,
	);
}
if ( '' === $rhm_title || ! $rhm_items ) {
	return;
}

$rhm_id      = $args['id'];
$rhm_head_id = $rhm_id . '-title';
$rhm_track   = $rhm_id . '-track';
$rhm_count   = count( $rhm_items );
$rhm_multi   = $rhm_count > 1;
$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_right   = 'right' === ( $rhm_data['image_position'] ?? 'left' );
$rhm_button  = rhm_landing_text( $rhm_data, 'button_text' );
$rhm_button  = '' !== $rhm_button ? $rhm_button : __( 'Tư vấn', 'themeranghammatsg' );
$rhm_url     = rhm_get_booking_url();
$rhm_popup   = rhm_booking_popup_enabled() ? ' data-rhm-popup="booking"' : '';
if ( $rhm_multi ) {
	rhm_landing_enqueue_module( 'carousel' );
}

// Ảnh lớn: khung W = 1408px (≥ 1440px) | 100vw − 32px, 2 cột cách 32px.
// ≥ 1536px cột ảnh 2/5: (1408 − 32) × 0,4 = 550px.
// 1024–1535px cột ảnh 1/3 (260–400px) nhưng khung cao = thẻ slider (~360px) + object-cover: ảnh ngang 4:3 cần rộng
// ≈ 360 × 4/3 = 480px > cột → lấy 480px (ảnh dọc thì dư, chấp nhận).
// < 1024px ảnh trên slider, giữ tỉ lệ, tối đa max-w-md 448px.
$rhm_sizes = '(min-width: 1536px) 550px, (min-width: 1024px) 480px, (min-width: 480px) 448px, calc(100vw - 32px)';
// Ảnh tròn cố định theo breakpoint, khớp class size-40 sm:size-48 lg:size-40 xl:size-48 2xl:size-56 bên dưới.
$rhm_round_sizes = '(min-width: 1536px) 224px, (min-width: 1280px) 192px, (min-width: 1024px) 160px, (min-width: 640px) 192px, 160px';

$rhm_arrow = 'pointer-events-auto flex size-9 cursor-pointer items-center justify-center rounded-full border border-primary/40 bg-white/90 text-primary shadow-card transition-colors hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-0';
$rhm_desc  = rhm_landing_rich( $rhm_data, 'description' );
?>
<section id="<?php echo esc_attr( $rhm_id ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<?php rhm_landing_heading( $rhm_head_id, $rhm_title, '', '', 'center', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>
		<?php if ( '' !== $rhm_desc ) : ?>
			<div class="rhm-rich mx-auto -mt-2 mb-6 max-w-4xl text-center leading-relaxed text-body/85 lg:mb-8"><?php echo $rhm_desc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
		<?php endif; ?>

		<?php
		// Ảnh lớn luôn đứng trước trong DOM → mobile ảnh trên; desktop ảnh phải = lg:order-last + đảo tỉ lệ cột.
		// ≥ 1024px hàng stretch: khung ảnh cao ĐÚNG bằng thẻ slider (ảnh absolute + object-cover, không góp chiều cao)
		// → mép trên/dưới 2 khối thẳng hàng, cân đối (người dùng yêu cầu 2026-11-16). < 1024px ảnh giữ nguyên tỉ lệ file.
		?>
		<div class="<?php echo esc_attr( ! $rhm_image ? 'mx-auto max-w-5xl' : ( $rhm_right ? 'grid gap-6 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)] lg:gap-8 2xl:grid-cols-[minmax(0,3fr)_minmax(0,2fr)]' : 'grid gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,2fr)] lg:gap-8 2xl:grid-cols-[minmax(0,2fr)_minmax(0,3fr)]' ) ); ?>">
			<?php if ( $rhm_image ) : ?>
				<div class="<?php echo esc_attr( $rhm_right ? 'relative mx-auto w-full max-w-md overflow-hidden rounded-xl bg-soft shadow-card lg:order-last lg:min-h-80 lg:max-w-none' : 'relative mx-auto w-full max-w-md overflow-hidden rounded-xl bg-soft shadow-card lg:min-h-80 lg:max-w-none' ); ?>">
					<?php
					// Section đứng đầu trang → ảnh lớn là LCP (fetchpriority=high), còn lại lazy. rhm_html_attrs escape từng thuộc tính.
					echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full lg:absolute lg:inset-0 lg:size-full lg:object-cover', (bool) $args['is_first'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
			<?php endif; ?>

			<?php // Thẻ flex-col: khi khung ảnh (lg:min-h-80) cao hơn nội dung, vùng slide giãn ra, nội dung canh giữa dọc. ?>
			<div class="flex min-w-0 flex-col overflow-hidden rounded-xl border border-line bg-white shadow-card">
				<?php if ( $rhm_multi ) : ?>
					<div class="flex overflow-x-auto overscroll-x-contain bg-soft [scrollbar-width:none]" role="group" aria-label="<?php echo esc_attr( $rhm_title ); ?>" data-rhm-showcase-thumbs>
						<?php foreach ( $rhm_items as $rhm_index => $rhm_item ) : ?>
							<button type="button" class="min-h-12 min-w-36 flex-1 shrink-0 cursor-pointer px-4 py-2.5 text-center text-[15px] leading-snug font-medium text-body transition-colors hover:text-primary aria-[current=true]:bg-primary aria-[current=true]:text-white lg:text-base" aria-controls="<?php echo esc_attr( $rhm_track ); ?>"<?php echo $rhm_index ? '' : ' aria-current="true"'; ?>><?php echo esc_html( $rhm_item['tab'] ); ?></button>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>

				<div class="relative flex flex-1 flex-col" data-rhm-tiles>
					<div id="<?php echo esc_attr( $rhm_track ); ?>" class="flex flex-1 snap-x snap-mandatory overflow-x-auto overscroll-x-contain [scrollbar-width:none]" role="region" aria-label="<?php echo esc_attr( $rhm_title ); ?>" data-rhm-tiles-track<?php echo $rhm_multi ? ' tabindex="0"' : ''; ?>>
						<?php foreach ( $rhm_items as $rhm_index => $rhm_item ) : ?>
							<?php
							// Ảnh tròn phải: đảo thứ tự + tỉ lệ cột từ sm (mobile luôn ảnh trên). lg (cột thẻ hẹp nhất) ảnh nhỏ lại size-40.
							$rhm_slide_class = $rhm_item['image']
								? ( $rhm_item['right'] ? 'grid w-full shrink-0 snap-start content-center items-center gap-5 px-12 py-6 sm:grid-cols-[minmax(0,1fr)_auto] sm:gap-6 lg:px-14 lg:py-8' : 'grid w-full shrink-0 snap-start content-center items-center gap-5 px-12 py-6 sm:grid-cols-[auto_minmax(0,1fr)] sm:gap-6 lg:px-14 lg:py-8' )
								: 'w-full shrink-0 snap-start content-center px-12 py-6 lg:px-14 lg:py-8';
							?>
							<div class="<?php echo esc_attr( $rhm_slide_class ); ?>" role="group" aria-roledescription="<?php esc_attr_e( 'slide', 'themeranghammatsg' ); ?>" aria-label="<?php echo esc_attr( ( $rhm_index + 1 ) . ' / ' . $rhm_count . ': ' . $rhm_item['title'] ); ?>">
								<?php if ( $rhm_item['image'] ) : ?>
									<span class="<?php echo esc_attr( $rhm_item['right'] ? 'relative block size-40 justify-self-center overflow-hidden rounded-full bg-soft sm:order-last sm:size-48 lg:size-40 xl:size-48 2xl:size-56' : 'relative block size-40 justify-self-center overflow-hidden rounded-full bg-soft sm:size-48 lg:size-40 xl:size-48 2xl:size-56' ); ?>">
										<?php
										// Ảnh minh họa có tiêu đề H3 đi kèm → alt rỗng (không đọc lặp). Luôn lazy (ảnh lớn mới là LCP).
										echo rhm_landing_picture( $rhm_item['image'], $rhm_round_sizes, 'absolute inset-0 size-full object-cover', false, 0, '', '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</span>
								<?php endif; ?>
								<div class="min-w-0">
									<h3 class="mb-3 text-xl leading-snug font-bold text-primary lg:text-[22px]"<?php echo $rhm_h3_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_color_attr(). ?>><?php echo esc_html( $rhm_item['title'] ); ?></h3>
									<ul class="space-y-2.5">
										<?php foreach ( $rhm_item['points'] as $rhm_point ) : ?>
											<li class="flex items-start gap-2.5 leading-relaxed text-body">
												<?php echo rhm_icon_svg( 'landing', 'tich', 'mt-0.5 size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
												<span class="min-w-0"><?php echo esc_html( $rhm_point ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
									<a class="mt-5 inline-flex h-10 items-center rounded-full bg-footer-accent px-7 font-bold text-dark shadow-card transition hover:brightness-95" href="<?php echo esc_url( $rhm_url ); ?>"<?php echo $rhm_popup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attribute. ?>>
										<?php echo esc_html( $rhm_button ); ?>
										<span class="sr-only">
											<?php
											/* translators: %s: tiêu đề slide */
											echo esc_html( sprintf( __( 'về %s', 'themeranghammatsg' ), $rhm_item['title'] ) );
											?>
										</span>
									</a>
								</div>
							</div>
						<?php endforeach; ?>
					</div>

					<?php if ( $rhm_multi ) : ?>
						<?php // Nút ‹ › canh giữa theo chiều cao slide, nằm trong phần đệm ngang px-12 / lg:px-14 của slide (không đè chữ). ?>
						<div class="pointer-events-none invisible absolute inset-0 flex items-center justify-between px-1.5 lg:px-2.5" data-rhm-tiles-controls>
							<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Slide trước', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-prev disabled>
								<?php echo rhm_get_icon( 'chevron', 'size-5 rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</button>
							<button type="button" class="<?php echo esc_attr( $rhm_arrow ); ?>" aria-label="<?php esc_attr_e( 'Slide tiếp theo', 'themeranghammatsg' ); ?>" aria-controls="<?php echo esc_attr( $rhm_track ); ?>" data-rhm-tiles-next disabled>
								<?php echo rhm_get_icon( 'chevron', 'size-5 -rotate-90' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>
