<?php
/**
 * Landing – Ảnh lớn + chữ + ô nổi bật: ảnh 4:3 (video YouTube/.mp4 "bấm mới tải" nếu có) | tiêu đề + nội dung + nút + ô nổi bật
 * (icon tròn, H3, mô tả, nút). Field: inc/landing/layouts/intro.php. Thiếu tiêu đề → không hiện. embed.js chỉ nạp khi có video.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data  = $args['data'] ?? array();
$rhm_title = rhm_landing_text( $rhm_data, 'title' );
if ( '' === $rhm_title ) {
	return;
}

$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_content = rhm_landing_html( $rhm_data, 'content' );
$rhm_video   = $rhm_image ? rhm_parse_video_url( esc_url_raw( (string) ( $rhm_data['video'] ?? '' ) ) ) : array();
$rhm_right   = 'right' === ( $rhm_data['image_position'] ?? 'left' );
$rhm_head_id = $args['id'] . '-title';
$rhm_callout = rhm_landing_text( $rhm_data, 'callout_title' );
$rhm_c_icon  = rhm_landing_icon( $rhm_data, 'callout_icon' );

if ( $rhm_video ) {
	rhm_landing_enqueue_module( 'embed' );
}

// Cột ảnh: khung 1408px, 2 cột cách 32px → 688px; 1024–1439px: (100vw − 32 − 32) / 2; dưới 1024px: 1 cột.
$rhm_sizes = '(min-width: 1440px) 688px, (min-width: 1024px) calc(50vw - 32px), calc(100vw - 32px)';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="<?php echo esc_attr( $rhm_image ? 'site-container grid items-center gap-6 lg:grid-cols-2 lg:gap-8' : 'site-container max-w-[868px]' ); ?>">
		<?php if ( $rhm_image ) : ?>
			<div class="<?php echo esc_attr( $rhm_right ? 'relative aspect-[4/3] overflow-hidden rounded-2xl bg-soft shadow-card lg:order-last' : 'relative aspect-[4/3] overflow-hidden rounded-2xl bg-soft shadow-card' ); ?>">
				<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
				echo rhm_landing_picture( $rhm_image, $rhm_sizes, 'absolute inset-0 size-full object-cover', (bool) $args['is_first'] );
				?>
				<?php if ( $rhm_video ) : ?>
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
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="min-w-0">
			<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'plain' ); ?>
			<?php if ( '' !== $rhm_content ) : ?>
				<div class="entry-content -mt-2 lg:text-lg"><?php echo $rhm_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post trong rhm_landing_html(). ?></div>
			<?php endif; ?>
			<?php rhm_landing_button( $rhm_data, 'btn btn-primary mt-6 h-11 gap-2 rounded-full px-6 text-sm font-bold' ); ?>

			<?php if ( '' !== $rhm_callout ) : ?>
				<div class="mt-6 flex items-center gap-4 rounded-2xl border border-line bg-linear-to-br from-mint to-white p-5 shadow-card lg:mt-8 lg:gap-6 lg:p-6">
					<?php if ( $rhm_c_icon ) : ?>
						<span class="flex size-16 shrink-0 items-center justify-center rounded-full bg-white text-primary shadow-card lg:size-20">
							<?php echo rhm_icon_svg( 'landing', $rhm_c_icon, 'size-9 lg:size-11' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
						</span>
					<?php endif; ?>
					<div class="min-w-0">
						<h3 class="text-base leading-snug font-bold text-primary uppercase lg:text-lg"><?php echo esc_html( $rhm_callout ); ?></h3>
						<?php foreach ( rhm_landing_lines( $rhm_data, 'callout_text' ) as $rhm_line ) : ?>
							<p class="mt-1.5 text-sm leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
						<?php endforeach; ?>
						<?php rhm_landing_button( $rhm_data, 'btn btn-primary mt-3 h-10 gap-2 rounded-full px-5 text-sm font-bold', 'callout_button' ); ?>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
