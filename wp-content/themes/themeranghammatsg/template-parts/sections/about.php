<?php
/**
 * Section: giới thiệu (ảnh/video + chữ + số liệu). Dữ liệu: inc/about.php. CSS: page-home.css
 * Video (nếu có) chỉ tải khi bấm ▶ — src/js/embed.js, chỉ nạp khi section có video.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_about = rhm_get_about();

if ( '' === $rhm_about['title'] ) {
	return;
}

$rhm_video = $rhm_about['image'] ? $rhm_about['video'] : array();

if ( $rhm_video && file_exists( RHM_THEME_DIR . '/dist/js/embed.js' ) ) {
	wp_enqueue_script_module( 'rhm-embed', RHM_THEME_URI . '/dist/js/embed.js', array(), rhm_asset_version( 'dist/js/embed.js' ) );
}
?>
<section class="py-12 lg:py-10" aria-labelledby="about-title">
	<div class="site-container grid items-center gap-10 lg:grid-cols-[minmax(0,0.9fr)_minmax(0,1.1fr)] lg:gap-16">
		<?php if ( $rhm_about['image'] ) : ?>
			<div class="relative lg:pr-6">
				<div class="absolute inset-y-8 right-0 hidden w-1/2 rounded-3xl bg-[#eef2f5] lg:block" aria-hidden="true"></div>

				<div class="relative aspect-[4/3] overflow-hidden rounded-2xl bg-soft shadow-card">
					<?php
					echo wp_get_attachment_image(
						$rhm_about['image'],
						'full',
						false,
						array(
							'class'    => 'absolute inset-0 size-full object-cover',
							// Độ rộng cột ảnh thật: 581px khi khung 1440px, ~39vw từ 1024px, full màn hình trừ lề trên mobile.
							'sizes'    => '(min-width: 1440px) 581px, (min-width: 1024px) 39vw, calc(100vw - 32px)',
							'loading'  => 'lazy',
							'decoding' => 'async',
						)
					);
					?>
					<?php if ( $rhm_video ) : ?>
						<button class="group absolute inset-0 flex cursor-pointer items-center justify-center bg-dark/15 transition-colors hover:bg-dark/25" type="button" data-embed-type="<?php echo esc_attr( $rhm_video['type'] ); ?>" data-embed-src="<?php echo esc_url( $rhm_video['src'] ); ?>">
							<span class="flex size-16 items-center justify-center rounded-full bg-white/90 text-primary shadow-card transition-transform duration-300 group-hover:scale-110 lg:size-20">
								<?php echo rhm_get_icon( 'play', 'ml-1 size-7 lg:size-9' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</span>
							<span class="sr-only"><?php esc_html_e( 'Xem video giới thiệu', 'themeranghammatsg' ); ?></span>
						</button>
					<?php endif; ?>
				</div>

				<?php if ( $rhm_about['badge_title'] ) : ?>
					<div class="absolute right-3 bottom-3 flex max-w-[85%] items-center gap-3 rounded-2xl bg-white p-3 shadow-[0_10px_30px_rgb(16_42_67/0.15)] sm:right-0 sm:bottom-6 sm:p-4 lg:max-w-[60%] lg:gap-4 lg:p-5">
						<span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-linear-to-b from-primary-light to-primary text-white lg:size-14">
							<?php echo rhm_get_icon( 'tooth', 'size-7 lg:size-8' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<span class="leading-snug">
							<strong class="block text-lg font-bold text-dark lg:text-xl"><?php echo esc_html( $rhm_about['badge_title'] ); ?></strong>
							<?php if ( $rhm_about['badge_text'] ) : ?>
								<span class="text-sm text-body lg:text-base"><?php echo esc_html( $rhm_about['badge_text'] ); ?></span>
							<?php endif; ?>
						</span>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div>
			<?php if ( $rhm_about['eyebrow'] ) : ?>
				<p class="mb-3 flex items-center gap-2 text-[13px] font-semibold tracking-wide text-dark uppercase lg:text-sm">
					<span class="h-0.5 w-6 rounded-full bg-primary" aria-hidden="true"></span>
					<?php echo esc_html( $rhm_about['eyebrow'] ); ?>
				</p>
			<?php endif; ?>

			<h2 id="about-title" class="mb-4 text-[28px] leading-tight font-bold text-dark lg:text-4xl">
				<?php echo esc_html( $rhm_about['title'] ); ?>
				<?php if ( $rhm_about['title_highlight'] ) : ?>
					<span class="text-green"><?php echo esc_html( $rhm_about['title_highlight'] ); ?></span>
				<?php endif; ?>
			</h2>

			<?php if ( $rhm_about['content'] ) : ?>
				<div class="space-y-2 text-base leading-relaxed text-body/85">
					<?php foreach ( preg_split( '/\R+/', $rhm_about['content'] ) as $rhm_paragraph ) : ?>
						<p><?php echo esc_html( $rhm_paragraph ); ?></p>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

			<?php if ( $rhm_about['button_text'] && $rhm_about['button_link'] ) : ?>
				<a class="btn mt-7 h-13 gap-4 rounded-full border-0 bg-linear-to-b from-primary-light to-primary pr-2 pl-7 text-[15px] font-bold text-white uppercase shadow-[0_6px_16px_rgb(11_122_85/0.3)] hover:from-primary-dark hover:to-primary-dark hover:text-white" href="<?php echo esc_url( $rhm_about['button_link'] ); ?>">
					<?php echo esc_html( $rhm_about['button_text'] ); ?>
					<span class="flex size-9 items-center justify-center rounded-full bg-white/20">
						<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</span>
				</a>
			<?php endif; ?>

			<?php if ( $rhm_about['stats'] ) : ?>
				<ul class="mt-8 grid grid-cols-2 gap-3 sm:grid-cols-4">
					<?php foreach ( $rhm_about['stats'] as $rhm_stat ) : ?>
						<li class="rounded-xl border border-[#edf1f5] bg-white px-3 py-4 text-center shadow-[0_4px_14px_rgb(16_42_67/0.06)]">
							<p class="text-2xl leading-tight font-bold text-primary lg:text-[28px]"><?php echo esc_html( $rhm_stat['number'] ); ?></p>
							<?php if ( $rhm_stat['label'] ) : ?>
								<p class="mt-1 text-sm leading-snug text-muted"><?php echo esc_html( $rhm_stat['label'] ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</section>
