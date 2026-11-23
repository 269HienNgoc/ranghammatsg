<?php
/**
 * Footer. Dữ liệu: inc/footer.php (Customizer → "Chân trang (Footer)" + "Thông tin phòng khám" + CPT Chi nhánh).
 * Style: utility trong file này + src/css/pages/footer.css.
 *
 * Bố cục:
 * - Hàng trên: công ty (logo + tên, địa chỉ, MST) | "Kết nối với chúng tôi" (hotline, email, giờ + mạng xã hội) | điều khoản chính sách.
 *   < 640px 1 cột; 640px 2 cột (chính sách xuống hàng); 1024px 3 cột có vạch ngăn.
 * - Hàng chi nhánh (viền trên): mỗi chi nhánh = viên "Chi nhánh N: tên" + địa chỉ, link bản đồ, GPHĐ, hotline, email, giờ.
 *   1 cột, 2 cột từ 768px.
 * - Dưới cùng: dòng bản quyền + huy hiệu (Bộ Công Thương, DMCA…). Cột / khối / dòng nào không có dữ liệu thì không in.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_logo        = rhm_get_footer_logo_id();
$rhm_company     = rhm_footer_text( 'title' );
$rhm_company_ln  = rhm_get_footer_company_lines();
$rhm_contacts    = rhm_get_footer_contacts();
$rhm_branches    = rhm_get_footer_branches();
$rhm_socials     = rhm_get_footer_socials();
$rhm_badges      = rhm_get_footer_badges();
$rhm_policy_menu = has_nav_menu( 'footer' );
?>
</main>

<footer class="site-footer">
	<div class="site-container grid gap-10 pb-10 sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-[minmax(0,1.15fr)_minmax(0,1fr)_minmax(0,0.85fr)] lg:gap-x-0 lg:pb-12">
		<div class="lg:pr-4 xl:pr-5">
			<?php if ( $rhm_logo || $rhm_company ) : ?>
				<div class="mb-6 flex items-center gap-4">
					<?php if ( $rhm_logo ) : ?>
						<a class="block shrink-0" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php
							// Độ rộng phải khớp class w-24 / sm:w-20 / xl:w-24 của ảnh.
							echo rhm_get_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped in rhm_html_attrs().
								$rhm_logo,
								array(
									'(min-width: 1280px)' => 96,
									'(min-width: 640px)'  => 80,
									''                    => 96,
								),
								array(
									'class'    => 'block h-auto w-24 sm:w-20 xl:w-24',
									'alt'      => get_bloginfo( 'name' ),
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						</a>
					<?php endif; ?>
					<?php if ( $rhm_company ) : ?>
						<p class="text-base leading-snug font-bold text-white uppercase xl:text-[17px]"><?php echo esc_html( $rhm_company ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( $rhm_company_ln ) : ?>
				<ul class="space-y-3">
					<?php foreach ( $rhm_company_ln as $rhm_icon => $rhm_html ) : ?>
						<li class="flex items-start gap-3">
							<?php echo rhm_get_icon( $rhm_icon, 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<span class="min-w-0"><?php echo $rhm_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rhm_get_footer_company_lines(). ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $rhm_contacts || $rhm_socials ) : ?>
			<div class="lg:border-l lg:border-white/10 lg:px-8 xl:px-12">
				<h2 class="footer-heading"><?php echo esc_html( rhm_footer_text( 'social_title' ) ); ?></h2>
				<?php if ( $rhm_contacts ) : ?>
					<ul class="space-y-4">
						<?php foreach ( $rhm_contacts as $rhm_icon => $rhm_contact ) : ?>
							<li class="flex items-start gap-3.5">
								<span class="flex size-10 shrink-0 items-center justify-center rounded-full bg-white/10 text-footer-accent">
									<?php echo rhm_get_icon( $rhm_icon, 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								</span>
								<span class="min-w-0">
									<span class="block text-[13px] leading-tight font-medium tracking-wide text-footer-muted uppercase"><?php echo esc_html( $rhm_contact['label'] ); ?></span>
									<?php echo $rhm_contact['html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rhm_get_footer_contacts(). ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $rhm_socials ) : ?>
					<ul class="flex flex-wrap gap-3<?php echo $rhm_contacts ? ' mt-6 border-t border-white/10 pt-6' : ''; ?>" aria-label="<?php esc_attr_e( 'Mạng xã hội', 'themeranghammatsg' ); ?>">
						<?php foreach ( $rhm_socials as $rhm_social ) : ?>
							<li>
								<a class="block rounded-lg transition-transform hover:-translate-y-0.5" href="<?php echo esc_url( $rhm_social['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_social['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
									<img class="size-10" src="<?php echo esc_url( $rhm_social['icon'] ); ?>" alt="<?php echo esc_attr( $rhm_social['name'] ); ?>" width="40" height="40" loading="lazy" decoding="async">
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $rhm_policy_menu ) : ?>
			<div class="lg:border-l lg:border-white/10 lg:pl-8 xl:pl-12">
				<h2 class="footer-heading"><?php echo esc_html( rhm_footer_text( 'policy_title' ) ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location'       => 'footer',
						'container'            => 'nav',
						'container_aria_label' => rhm_footer_text( 'policy_title' ),
						'menu_class'           => 'footer-nav',
						'fallback_cb'          => false,
						'depth'                => 1,
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $rhm_branches ) : ?>
		<section class="border-t border-white/10" aria-labelledby="footer-branches-title">
			<h2 id="footer-branches-title" class="sr-only"><?php echo esc_html( rhm_footer_text( 'branch_title' ) ); ?></h2>
			<div class="site-container grid gap-8 py-8 md:grid-cols-2 md:gap-x-10 lg:py-10 xl:gap-x-16">
				<?php foreach ( $rhm_branches as $rhm_i => $rhm_branch ) : ?>
					<div class="min-w-0">
						<h3 class="footer-branch-title">
							<?php
							/* translators: 1: số thứ tự chi nhánh, 2: tên chi nhánh */
							echo esc_html( sprintf( __( 'Chi nhánh %1$d: %2$s', 'themeranghammatsg' ), $rhm_i + 1, $rhm_branch['name'] ) );
							?>
						</h3>
						<ul class="mt-4 space-y-3">
							<li class="flex items-start gap-3">
								<?php echo rhm_get_icon( 'map-pin', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<span class="min-w-0">
									<?php echo nl2br( esc_html( $rhm_branch['address'] ) ); ?>
									<?php if ( $rhm_branch['map_url'] ) : ?>
										<a class="mt-1 block w-fit text-footer-accent italic underline underline-offset-4 hover:text-white" href="<?php echo esc_url( $rhm_branch['map_url'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_branch['map_url'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
											<?php esc_html_e( 'Xem bản đồ', 'themeranghammatsg' ); ?><span class="sr-only"> – <?php echo esc_html( $rhm_branch['name'] ); ?></span>
										</a>
									<?php endif; ?>
								</span>
							</li>
							<?php if ( $rhm_branch['license'] ) : ?>
								<li class="flex items-start gap-3">
									<?php echo rhm_get_icon( 'document', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<span class="min-w-0"><strong class="font-bold text-white"><?php esc_html_e( 'GPHĐ:', 'themeranghammatsg' ); ?></strong> <?php echo esc_html( $rhm_branch['license'] ); ?></span>
								</li>
							<?php endif; ?>
							<?php if ( $rhm_branch['hotline'] ) : ?>
								<li class="flex items-start gap-3">
									<?php echo rhm_get_icon( 'phone', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<a class="font-bold text-white hover:text-footer-accent" href="<?php echo esc_url( rhm_get_tel_href( $rhm_branch['hotline'] ) ); ?>"><?php echo esc_html( $rhm_branch['hotline'] ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( $rhm_branch['email'] ) : ?>
								<li class="flex items-start gap-3">
									<?php echo rhm_get_icon( 'mail', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<a class="min-w-0 wrap-anywhere text-footer-text hover:text-footer-accent" href="<?php echo esc_url( 'mailto:' . $rhm_branch['email'] ); ?>"><?php echo esc_html( $rhm_branch['email'] ); ?></a>
								</li>
							<?php endif; ?>
							<?php if ( $rhm_branch['hours'] ) : ?>
								<li class="flex items-start gap-3">
									<?php echo rhm_get_icon( 'clock', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<span class="min-w-0"><?php echo nl2br( esc_html( $rhm_branch['hours'] ) ); ?></span>
								</li>
							<?php endif; ?>
						</ul>
					</div>
				<?php endforeach; ?>
			</div>
		</section>
	<?php endif; ?>

	<div class="border-t border-white/10">
		<div class="site-container flex flex-col gap-4 py-5 sm:flex-row sm:items-center sm:justify-between sm:gap-6">
			<p class="text-sm text-footer-muted"><?php echo esc_html( rhm_footer_text( 'copyright' ) ); ?></p>
			<?php if ( $rhm_badges ) : ?>
				<ul class="flex shrink-0 flex-wrap items-center gap-4 sm:gap-6">
					<?php foreach ( $rhm_badges as $rhm_badge ) : ?>
						<?php $rhm_img = rhm_footer_image( $rhm_badge['image'], 40, 'h-10 w-auto', $rhm_badge['name'] ); ?>
						<li>
							<?php if ( $rhm_badge['link'] ) : ?>
								<a class="block" href="<?php echo esc_url( $rhm_badge['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_badge['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo $rhm_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?></a>
							<?php else : ?>
								<?php echo $rhm_img; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image(). ?>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
	</div>
</footer>

<?php
// Nút liên hệ nhanh + popup đặt lịch: style trong footer.css (component, không dùng utility vì nằm ngoài .site-footer).
get_template_part( 'template-parts/components/floating-cta' );
get_template_part( 'template-parts/components/booking-popup' );
?>

<?php wp_footer(); ?>
</body>
</html>
