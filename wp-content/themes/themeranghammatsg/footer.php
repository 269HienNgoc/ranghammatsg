<?php
/**
 * Footer. Dữ liệu: inc/footer.php (Customizer → "Chân trang (Footer)" + "Thông tin phòng khám"). Style: utility trong file này + src/css/pages/footer.css.
 *
 * Bố cục:
 * - < 640px: 1 cột (thương hiệu → chi nhánh → chính sách/mạng xã hội).
 * - 640px: 2 cột (thương hiệu | chính sách/mạng xã hội), chi nhánh xuống hàng dưới, rộng hết khung.
 * - 1024px: 3 cột (thương hiệu | chi nhánh rộng gấp đôi | chính sách/mạng xã hội), 2 cột sau có vạch ngăn.
 * Danh sách chi nhánh tự chia 2 cột khi đủ chỗ cho mỗi cột ≥ 20rem (địa chỉ nằm trên 1 dòng), không đủ thì 1 cột.
 * Dưới cùng: dòng bản quyền + huy hiệu (Bộ Công Thương, DMCA…). Cột / khối nào không có dữ liệu thì không in.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_logo        = rhm_get_footer_logo_id();
$rhm_slogan      = rhm_footer_text( 'slogan' );
$rhm_contacts    = rhm_get_footer_contacts();
$rhm_branches    = rhm_get_footer_branches();
$rhm_branch_text = rhm_footer_text( 'branch_button_text' );
$rhm_branch_link = rhm_get_option( 'rhm_footer_branch_button_link' ) ?: rhm_get_booking_url();
$rhm_socials     = rhm_get_footer_socials();
$rhm_badges      = rhm_get_footer_badges();
$rhm_policy_menu = has_nav_menu( 'footer' );
?>
</main>

<footer class="site-footer">
	<div class="site-container grid gap-10 pb-10 sm:grid-cols-2 sm:gap-x-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,2.2fr)_minmax(0,0.8fr)] lg:gap-x-0 lg:pb-12">
		<div class="lg:pr-8">
			<?php if ( $rhm_logo || $rhm_slogan ) : ?>
				<div class="mb-6 flex items-center gap-4">
					<?php if ( $rhm_logo ) : ?>
						<a class="block shrink-0" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
							<?php
							// Độ rộng phải khớp class w-20 / xl:w-24 của ảnh.
							echo rhm_get_picture( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- attributes escaped in rhm_html_attrs().
								$rhm_logo,
								array(
									'(min-width: 1280px)' => 96,
									''                    => 80,
								),
								array(
									'class'    => 'block h-auto w-20 xl:w-24',
									'alt'      => get_bloginfo( 'name' ),
									'loading'  => 'lazy',
									'decoding' => 'async',
								)
							);
							?>
						</a>
					<?php endif; ?>
					<?php if ( $rhm_slogan ) : ?>
						<p class="text-lg leading-snug font-light text-white italic"><?php echo esc_html( $rhm_slogan ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<h2 class="footer-heading"><?php echo esc_html( rhm_footer_text( 'title' ) ); ?></h2>
			<?php if ( $rhm_contacts ) : ?>
				<ul class="space-y-3">
					<?php foreach ( $rhm_contacts as $rhm_icon => $rhm_html ) : ?>
						<li class="flex items-start gap-3">
							<?php echo rhm_get_icon( $rhm_icon, 'mt-0.5 size-5 shrink-0 text-white' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<span class="min-w-0"><?php echo $rhm_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in rhm_get_footer_contacts(). ?></span>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( $rhm_branches ) : ?>
			<div class="sm:order-last sm:col-span-2 lg:order-none lg:col-span-1 lg:border-l lg:border-white/10 lg:px-6 xl:px-10">
				<h2 class="footer-heading"><?php echo esc_html( rhm_footer_text( 'branch_title' ) ); ?></h2>
				<ul class="grid grid-cols-[repeat(auto-fill,minmax(min(20rem,100%),1fr))] gap-x-6 gap-y-3 xl:gap-x-8">
					<?php foreach ( $rhm_branches as $rhm_branch ) : ?>
						<li class="flex items-start gap-2.5">
							<?php echo rhm_get_icon( 'map-pin', 'mt-0.5 size-5 shrink-0 text-footer-accent' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<?php if ( $rhm_branch['link'] ) : ?>
								<a class="text-footer-text hover:text-footer-accent" href="<?php echo esc_url( $rhm_branch['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_branch['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>><?php echo esc_html( $rhm_branch['name'] ); ?></a>
							<?php else : ?>
								<span><?php echo esc_html( $rhm_branch['name'] ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $rhm_branch_text ) : ?>
					<a class="mt-6 inline-flex h-11 items-center gap-2.5 rounded-full whitespace-nowrap border border-[#2c8c73] bg-[#00503f] px-6 text-sm font-medium text-white transition-colors hover:border-primary-light hover:bg-primary hover:text-white" href="<?php echo esc_url( $rhm_branch_link ); ?>">
						<?php echo esc_html( $rhm_branch_text ); ?>
						<?php echo rhm_get_icon( 'arrow-right', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $rhm_policy_menu || $rhm_socials ) : ?>
			<div class="space-y-7 lg:border-l lg:border-white/10 lg:pl-6 xl:pl-10">
				<?php if ( $rhm_policy_menu ) : ?>
					<div>
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

				<?php if ( $rhm_socials ) : ?>
					<div>
						<h2 class="footer-heading"><?php echo esc_html( rhm_footer_text( 'social_title' ) ); ?></h2>
						<ul class="flex flex-wrap gap-3">
							<?php foreach ( $rhm_socials as $rhm_social ) : ?>
								<li>
									<a class="block rounded-lg transition-transform hover:-translate-y-0.5" href="<?php echo esc_url( $rhm_social['link'] ); ?>"<?php echo rhm_external_link_attrs( $rhm_social['link'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
										<img class="size-10" src="<?php echo esc_url( $rhm_social['icon'] ); ?>" alt="<?php echo esc_attr( $rhm_social['name'] ); ?>" width="40" height="40" loading="lazy" decoding="async">
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>
	</div>

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

<?php wp_footer(); ?>
</body>
</html>
