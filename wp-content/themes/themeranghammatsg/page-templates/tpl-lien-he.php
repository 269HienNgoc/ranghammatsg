<?php
/**
 * Template Name: Liên hệ – Đặt lịch
 * Template Post Type: page
 *
 * Bố cục: banner (breadcrumb, H1, giới thiệu, 2 nút) → 4 thẻ liên hệ (hotline, email, địa chỉ, giờ làm việc) đè mép banner
 * → form đặt lịch (Fluent Forms, #dat-lich) + cột phải (khối hotline, chi nhánh, mạng xã hội) → nội dung trang (nếu có) → bản đồ.
 * Dữ liệu: inc/contact.php, "Thông tin phòng khám", chi nhánh / mạng xã hội của footer. CSS: page-contact.css
 *
 * Hiệu năng: trang không có ảnh lớn (LCP là tiêu đề). Bản đồ Google dạng "bấm mới tải" (src/js/embed.js) — iframe Maps ~1 MB
 * chỉ tải khi người dùng bấm. Form: CSS/JS của Fluent Forms chỉ nạp ở trang có form.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_form_id = absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
$rhm_hotline = rhm_get_option( 'rhm_hotline' );
$rhm_note    = rhm_get_option( 'rhm_hotline_note' );
$rhm_email   = rhm_get_option( 'rhm_email' );
$rhm_address = rhm_get_option( 'rhm_address' );
$rhm_hours   = rhm_get_option( 'rhm_working_hours' );
$rhm_map_src = rhm_get_contact_map_src();
$rhm_branch  = rhm_get_footer_branches();
$rhm_social  = rhm_get_footer_socials();

// 4 thẻ liên hệ: thẻ nào thiếu dữ liệu thì bỏ.
$rhm_cards = array_filter(
	array(
		array( 'phone', __( 'Hotline', 'themeranghammatsg' ), $rhm_hotline, $rhm_hotline ? rhm_get_tel_href( $rhm_hotline ) : '', __( 'Gọi ngay', 'themeranghammatsg' ) ),
		array( 'mail', __( 'Email', 'themeranghammatsg' ), $rhm_email, $rhm_email ? 'mailto:' . $rhm_email : '', __( 'Gửi email', 'themeranghammatsg' ) ),
		array( 'map-pin', __( 'Địa chỉ', 'themeranghammatsg' ), $rhm_address, $rhm_address ? rhm_get_directions_url( $rhm_address ) : '', __( 'Chỉ đường', 'themeranghammatsg' ) ),
		array( 'clock', __( 'Giờ làm việc', 'themeranghammatsg' ), $rhm_hours, '', '' ),
	),
	static fn( array $card ): bool => '' !== trim( $card[2] )
);

if ( $rhm_map_src && file_exists( RHM_THEME_DIR . '/dist/js/embed.js' ) ) {
	wp_enqueue_script_module( 'rhm-embed', RHM_THEME_URI . '/dist/js/embed.js', array(), rhm_asset_version( 'dist/js/embed.js' ) );
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="relative overflow-hidden bg-linear-to-b from-soft to-white pt-6 pb-20 lg:pt-10 lg:pb-28">
		<span class="pointer-events-none absolute -top-24 -right-24 size-72 rounded-full bg-mint-dark/50 lg:size-96" aria-hidden="true"></span>
		<span class="pointer-events-none absolute -bottom-40 -left-24 size-80 rounded-full bg-mint/70" aria-hidden="true"></span>
		<?php echo rhm_get_icon( 'tooth', 'pointer-events-none absolute top-1/2 right-[6%] hidden size-64 -translate-y-1/2 text-teal-dark/[0.06] lg:block xl:size-80' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>

		<div class="site-container relative">
			<?php rhm_breadcrumb(); ?>
			<div class="mt-6 max-w-3xl lg:mt-10">
				<p class="mb-4 inline-flex items-center gap-2 rounded-full bg-mint-dark px-3.5 py-1.5 text-[13px] font-bold text-teal-dark uppercase">
					<?php echo rhm_get_icon( 'calendar', 'size-4 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<?php esc_html_e( 'Liên hệ & đặt lịch', 'themeranghammatsg' ); ?>
				</p>
				<h1 class="text-3xl leading-tight font-bold text-teal-dark sm:text-4xl lg:text-5xl"><?php the_title(); ?></h1>
				<?php if ( rhm_contact_text( 'intro' ) ) : ?>
					<p class="mt-4 max-w-2xl text-base leading-relaxed text-muted lg:text-lg"><?php echo esc_html( rhm_contact_text( 'intro' ) ); ?></p>
				<?php endif; ?>
				<div class="mt-7 flex flex-wrap gap-3">
					<a class="btn btn-primary rounded-full border-0 bg-teal-dark px-6 hover:bg-primary" href="#dat-lich">
						<?php echo rhm_get_icon( 'calendar', 'size-5 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<?php esc_html_e( 'Đặt lịch ngay', 'themeranghammatsg' ); ?>
					</a>
					<?php if ( $rhm_hotline ) : ?>
						<a class="btn rounded-full border-teal-dark/20 bg-white px-6 text-teal-dark hover:border-teal-dark hover:text-teal-dark" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
							<?php echo rhm_get_icon( 'phone', 'size-5 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<?php echo esc_html( $rhm_hotline ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>

	<?php if ( $rhm_cards ) : ?>
		<div class="site-container relative z-10 -mt-12 lg:-mt-16">
			<ul class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:gap-5">
				<?php foreach ( $rhm_cards as [ $rhm_icon, $rhm_label, $rhm_value, $rhm_href, $rhm_action ] ) : ?>
					<li class="flex gap-4 rounded-2xl border border-line bg-white p-4 shadow-card sm:flex-col sm:gap-0 sm:p-5 lg:p-6">
						<span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-mint text-teal-dark">
							<?php echo rhm_get_icon( $rhm_icon, 'size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<div class="flex min-w-0 flex-1 flex-col">
							<p class="text-sm text-muted sm:mt-4"><?php echo esc_html( $rhm_label ); ?></p>
							<p class="mt-0.5 leading-snug font-semibold wrap-anywhere text-teal-dark sm:mt-1 <?php echo 'phone' === $rhm_icon ? 'text-xl' : ''; ?>"><?php echo nl2br( esc_html( $rhm_value ) ); ?></p>
							<?php if ( $rhm_href ) : ?>
								<a class="mt-auto inline-flex items-center gap-1.5 pt-2 text-sm font-semibold text-primary hover:text-teal-dark sm:pt-4" href="<?php echo esc_url( $rhm_href ); ?>"<?php echo rhm_external_link_attrs( $rhm_href ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
									<?php echo esc_html( $rhm_action ); ?>
									<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								</a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>

	<section class="site-container grid items-start gap-6 py-12 lg:grid-cols-[minmax(0,1.4fr)_minmax(0,1fr)] lg:gap-8 lg:py-16">
		<div id="dat-lich" class="rhm-booking rounded-3xl border border-line bg-white p-5 shadow-card sm:p-8 lg:p-10">
			<p class="mb-3 inline-flex items-center gap-2 text-[13px] font-bold tracking-wide text-primary uppercase">
				<span class="h-0.5 w-6 rounded-full bg-primary" aria-hidden="true"></span>
				<?php esc_html_e( 'Đặt lịch online', 'themeranghammatsg' ); ?>
			</p>
			<h2 class="text-2xl leading-tight font-bold text-teal-dark lg:text-3xl"><?php echo esc_html( rhm_contact_text( 'form_title' ) ); ?></h2>
			<?php if ( rhm_contact_text( 'form_note' ) ) : ?>
				<p class="mt-2 text-muted"><?php echo esc_html( rhm_contact_text( 'form_note' ) ); ?></p>
			<?php endif; ?>
			<div class="mt-6">
				<?php
				if ( $rhm_form_id && shortcode_exists( 'fluentform' ) ) {
					echo do_shortcode( sprintf( '[fluentform id="%d"]', $rhm_form_id ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fluent Forms output.
				} elseif ( current_user_can( 'edit_theme_options' ) ) {
					echo '<p class="rounded-xl border border-dashed border-primary p-4 text-sm">' . esc_html__( 'Chưa cấu hình form: tạo form trong Fluent Forms rồi nhập ID tại Giao diện → Tùy biến → Thông tin phòng khám.', 'themeranghammatsg' ) . '</p>';
				}
				?>
			</div>
		</div>

		<div class="grid gap-6">
			<?php if ( $rhm_hotline ) : ?>
				<div class="relative overflow-hidden rounded-3xl bg-teal-dark p-6 text-white sm:p-8">
					<span class="pointer-events-none absolute -top-16 -right-16 size-48 rounded-full bg-white/5" aria-hidden="true"></span>
					<span class="pointer-events-none absolute -right-6 -bottom-20 size-40 rounded-full bg-white/5" aria-hidden="true"></span>
					<h2 class="relative text-xl font-bold lg:text-2xl"><?php echo esc_html( rhm_contact_text( 'cta_title' ) ); ?></h2>
					<?php if ( rhm_contact_text( 'cta_text' ) ) : ?>
						<p class="relative mt-2 text-white/90"><?php echo esc_html( rhm_contact_text( 'cta_text' ) ); ?></p>
					<?php endif; ?>
					<a class="relative mt-5 inline-flex items-center gap-3 rounded-2xl bg-white/10 py-3 pr-5 pl-3 text-white transition-colors hover:bg-white/15 hover:text-white" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
						<span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white text-teal-dark">
							<?php echo rhm_get_icon( 'phone', 'size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<span class="leading-tight">
							<span class="block text-2xl font-bold lg:text-[28px]"><?php echo esc_html( $rhm_hotline ); ?></span>
							<?php if ( $rhm_note ) : ?>
								<span class="mt-1 block text-sm text-white"><?php echo esc_html( $rhm_note ); ?></span>
							<?php endif; ?>
						</span>
					</a>
				</div>
			<?php endif; ?>

			<?php if ( $rhm_branch || $rhm_social ) : ?>
				<div class="rounded-3xl border border-line bg-soft p-6 sm:p-8">
					<?php if ( $rhm_branch ) : ?>
						<h2 class="text-lg font-bold text-teal-dark lg:text-xl"><?php echo esc_html( rhm_footer_text( 'branch_title' ) ); ?></h2>
						<ul class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-1 xl:grid-cols-2">
							<?php foreach ( $rhm_branch as $rhm_item ) : ?>
								<li class="flex items-center gap-2.5">
									<?php echo rhm_get_icon( 'map-pin', 'size-5 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<?php if ( $rhm_item['link'] ) : ?>
										<a class="text-body hover:text-primary" href="<?php echo esc_url( $rhm_item['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_item['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo esc_html( $rhm_item['name'] ); ?></a>
									<?php else : ?>
										<span class="text-body"><?php echo esc_html( $rhm_item['name'] ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $rhm_social ) : ?>
						<h2 class="text-lg font-bold text-teal-dark lg:text-xl <?php echo $rhm_branch ? 'mt-7 border-t border-line pt-6' : ''; ?>"><?php echo esc_html( rhm_footer_text( 'social_title' ) ); ?></h2>
						<ul class="mt-4 flex flex-wrap gap-3">
							<?php foreach ( $rhm_social as $rhm_item ) : ?>
								<li>
									<a class="block rounded-lg transition-transform hover:-translate-y-0.5" href="<?php echo esc_url( $rhm_item['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_item['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
										<img class="size-11" src="<?php echo esc_url( $rhm_item['icon'] ); ?>" alt="<?php echo esc_attr( $rhm_item['name'] ); ?>" width="44" height="44" loading="lazy" decoding="async">
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</section>

	<?php if ( '' !== trim( get_the_content() ) ) : ?>
		<div class="site-container pb-12 lg:pb-16">
			<div class="entry-content max-w-4xl"><?php the_content(); ?></div>
		</div>
	<?php endif; ?>

	<?php if ( $rhm_map_src ) : ?>
		<section class="site-container pb-12 lg:pb-20" aria-label="<?php esc_attr_e( 'Bản đồ phòng khám', 'themeranghammatsg' ); ?>">
			<div class="map-facade relative h-80 overflow-hidden rounded-3xl border border-line sm:h-96 lg:h-[440px]">
				<button class="group absolute inset-0 flex cursor-pointer items-center justify-center p-4" type="button" data-embed-type="map" data-embed-src="<?php echo esc_url( $rhm_map_src ); ?>" data-embed-title="<?php esc_attr_e( 'Bản đồ phòng khám', 'themeranghammatsg' ); ?>">
					<span class="flex max-w-sm flex-col items-center rounded-2xl bg-white p-6 text-center shadow-card transition-transform duration-300 group-hover:-translate-y-1">
						<span class="flex size-14 items-center justify-center rounded-full bg-teal-dark text-white shadow-[0_0_0_8px_rgb(16_102_92/0.12)]">
							<?php echo rhm_get_icon( 'map-pin', 'size-7' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<span class="mt-4 text-lg font-bold text-teal-dark"><?php esc_html_e( 'Xem bản đồ phòng khám', 'themeranghammatsg' ); ?></span>
						<?php if ( $rhm_address ) : ?>
							<span class="mt-1 text-sm text-muted"><?php echo esc_html( $rhm_address ); ?></span>
						<?php endif; ?>
						<span class="mt-4 inline-flex items-center gap-1.5 rounded-full bg-mint px-4 py-2 text-sm font-semibold text-teal-dark group-hover:bg-mint-dark">
							<?php esc_html_e( 'Bấm để tải bản đồ', 'themeranghammatsg' ); ?>
							<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
					</span>
				</button>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
