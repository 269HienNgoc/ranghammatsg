<?php
/**
 * Landing – Ảnh lớn + chữ + ô nổi bật: ảnh (video YouTube/.mp4 "bấm mới tải" nếu có) | tiêu đề + nội dung + nút + ô nổi bật
 * (icon tròn, H3, mô tả, nút). Field: inc/landing/layouts/intro.php. Thiếu tiêu đề → không hiện. embed.js chỉ nạp khi có video.
 *
 * 2 kiểu (field style):
 * - '' (mặc định): ảnh khung 4:3 (object-cover) cạnh cột chữ, lưới 2 cột từ 1024px.
 * - 'card' (2026-11-26, theo thiết kế người dùng gửi): ảnh hiện TRỌN đúng tỉ lệ file (h-auto, dùng được ảnh ghép sẵn nền
 *   trong suốt, bo góc rounded-lg 8px — người dùng yêu cầu cho mềm mại), cột ~3/10 đè 80px lên mép thẻ trắng (~7/10) chứa chữ; thẻ có ảnh trang trí nền tùy chọn (card_bg, alt="",
 *   lazy). < 1024px: ảnh trên (≤ 448px, canh giữa), thẻ dưới, không đè.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_h3_attr = rhm_landing_color_attr( rhm_landing_color( $rhm_data, 'h3_color' ) ); // Màu mọi H3 của section ('' = màu thiết kế).
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
if ( '' === $rhm_title ) {
	return;
}

$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_content = rhm_landing_html( $rhm_data, 'content' );
$rhm_video   = $rhm_image ? rhm_parse_video_url( esc_url_raw( (string) ( $rhm_data['video'] ?? '' ) ) ) : array();
$rhm_right   = 'right' === ( $rhm_data['image_position'] ?? 'left' );
$rhm_card    = $rhm_image && 'card' === ( $rhm_data['style'] ?? '' );
$rhm_head_id = $args['id'] . '-title';
$rhm_callout = rhm_landing_text( $rhm_data, 'callout_title' );
$rhm_c_icon  = rhm_landing_icon( $rhm_data, 'callout_icon' );

if ( $rhm_video ) {
	rhm_landing_enqueue_module( 'embed' );
}

if ( $rhm_card ) {
	// Kiểu thẻ: cột ảnh = 3/10 khung (1248px) + 80px đè lên thẻ → ≈ 455px; 1024–1279px (100vw − 32) × 0,3 + 80;
	// < 1024px ảnh tối đa 448px (max-w-md), nhỏ hơn thì 100vw − 32px.
	$rhm_sizes = '(min-width: 1280px) 455px, (min-width: 1024px) calc(30vw + 70px), (min-width: 480px) 448px, calc(100vw - 32px)';
} else {
	// Cột ảnh: khung 1248px, 2 cột cách 32px → 608px; 1024–1279px: (100vw − 32 − 32) / 2; dưới 1024px: 1 cột.
	$rhm_sizes = '(min-width: 1280px) 608px, (min-width: 1024px) calc(50vw - 32px), calc(100vw - 32px)';
}

// Nút phát video phủ lên ảnh (dùng chung 2 kiểu).
ob_start();
if ( $rhm_video ) :
	?>
					<button class="group absolute inset-0 flex cursor-pointer items-center justify-center bg-dark/15 transition-colors hover:bg-dark/25" type="button" data-embed-type="<?php echo esc_attr( $rhm_video['type'] ); ?>" data-embed-src="<?php echo esc_url( $rhm_video['src'] ); ?>" data-embed-title="<?php echo esc_attr( $rhm_title ); ?>">
						<span class="flex size-16 items-center justify-center rounded-full bg-white/90 text-primary shadow-card transition-transform duration-300 group-hover:scale-110 lg:size-20">
							<?php echo rhm_get_icon( 'play', 'ml-1 size-7 lg:size-9' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<span class="sr-only">
							<?php
							/* translators: %s: tiêu đề section */
							echo esc_html( sprintf( __( 'Xem video: %s', 'themeranghammatsg' ), $rhm_title ) );
							?>
						</span>
					</button>
	<?php
endif;
$rhm_video_html = (string) ob_get_clean();

// Cột chữ: tiêu đề + nội dung + nút + ô nổi bật (dùng chung 2 kiểu; kiểu thẻ chữ nhỏ hơn: text-base lg:text-[17px]).
ob_start();
?>
			<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'plain', rhm_landing_color( $rhm_data, 'title_color' ) ); ?>
			<?php if ( '' !== $rhm_content ) : ?>
				<div class="<?php echo esc_attr( $rhm_card ? 'entry-content -mt-2 text-base lg:text-[17px]' : 'entry-content -mt-2 lg:text-lg' ); ?>"><?php echo $rhm_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post trong rhm_landing_html(). ?></div>
			<?php endif; ?>
			<?php rhm_landing_button( $rhm_data, 'btn btn-primary mt-6 h-11 gap-2 rounded-full px-6 text-sm font-bold' ); ?>

			<?php if ( '' !== $rhm_callout ) : ?>
				<div class="mt-6 flex items-center gap-4 rounded-lg border border-line bg-linear-to-br from-mint to-white p-5 shadow-card lg:mt-8 lg:gap-6 lg:p-6">
					<?php if ( $rhm_c_icon ) : ?>
						<span class="flex size-16 shrink-0 items-center justify-center rounded-full bg-white text-primary shadow-card lg:size-20">
							<?php echo rhm_icon_svg( 'landing', $rhm_c_icon, 'size-9 lg:size-11' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
						</span>
					<?php endif; ?>
					<div class="min-w-0">
						<h3 class="text-base leading-snug font-bold text-primary uppercase lg:text-lg"<?php echo $rhm_h3_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_color_attr(). ?>><?php echo esc_html( $rhm_callout ); ?></h3>
						<?php $rhm_rich = rhm_landing_rich( $rhm_data, 'callout_text' ); ?>
						<?php if ( '' !== $rhm_rich ) : ?>
							<div class="rhm-rich mt-1.5 text-sm leading-relaxed text-body/85"><?php echo $rhm_rich; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_landing_rich(). ?></div>
						<?php endif; ?>
						<?php rhm_landing_button( $rhm_data, 'btn btn-primary mt-3 h-10 gap-2 rounded-full px-5 text-sm font-bold', 'callout_button' ); ?>
					</div>
				</div>
			<?php endif; ?>
<?php
$rhm_text_html = (string) ob_get_clean();

if ( $rhm_card ) :
	$rhm_bg = rhm_landing_image( $rhm_data, 'card_bg' );
	// Ảnh nền thẻ (trang trí): thẻ ≈ 7/10 khung → 874px (≥ 1280px), (100vw − 32) × 0,7 (1024–1279px), < 1024px rộng hết khung.
	$rhm_bg_sizes = '(min-width: 1280px) 874px, (min-width: 1024px) calc(70vw - 22px), calc(100vw - 32px)';
	?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-10" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_right ? 'site-container grid items-center gap-6 lg:grid-cols-[minmax(0,7fr)_minmax(0,3fr)] lg:gap-0' : 'site-container grid items-center gap-6 lg:grid-cols-[minmax(0,3fr)_minmax(0,7fr)] lg:gap-0' ); ?>">
		<div class="<?php echo esc_attr( $rhm_right ? 'relative z-10 mx-auto w-full max-w-md overflow-hidden rounded-lg lg:order-last lg:-ml-20 lg:w-auto lg:max-w-none' : 'relative z-10 mx-auto w-full max-w-md overflow-hidden rounded-lg lg:-mr-20 lg:w-auto lg:max-w-none' ); ?>">
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
			echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'block h-auto w-full', (bool) $args['is_first'] );
			echo $rhm_video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên.
			?>
		</div>

		<div class="<?php echo esc_attr( $rhm_right ? 'relative min-w-0 overflow-hidden rounded-xl border border-line bg-white p-5 shadow-card sm:p-8 lg:py-10 lg:pr-32 lg:pl-10' : 'relative min-w-0 overflow-hidden rounded-xl border border-line bg-white p-5 shadow-card sm:p-8 lg:py-10 lg:pr-10 lg:pl-32' ); ?>">
			<?php
			if ( $rhm_bg ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_bg, $rhm_bg_sizes, 'pointer-events-none absolute inset-0 size-full object-cover object-right-bottom', false, 0, '', '' );
			}
			?>
			<div class="relative">
<?php echo $rhm_text_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên. ?>
			</div>
		</div>
	</div>
</section>
	<?php
	return;
endif;
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_image ? 'site-container grid items-center gap-6 lg:grid-cols-2 lg:gap-8' : 'site-container max-w-[868px]' ); ?>">
		<?php if ( $rhm_image ) : ?>
			<div class="<?php echo esc_attr( $rhm_right ? 'relative aspect-[4/3] overflow-hidden rounded-lg bg-soft shadow-card lg:order-last' : 'relative aspect-[4/3] overflow-hidden rounded-lg bg-soft shadow-card' ); ?>">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', (bool) $args['is_first'] );
				?>
<?php echo $rhm_video_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên. ?>
			</div>
		<?php endif; ?>

		<div class="min-w-0">
<?php echo $rhm_text_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape ở trên. ?>
		</div>
	</div>
</section>
