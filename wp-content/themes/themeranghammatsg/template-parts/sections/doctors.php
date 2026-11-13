<?php
/**
 * Section: Đội ngũ bác sĩ (slider, mỗi slide 1 bác sĩ). Dữ liệu: inc/home-doctors.php. CSS: utility + components/_home-doctors.css
 * Dùng ở: trang chủ (bản 1 + bản 2 qua doctors-2.php), landing page (layout "doctors").
 * $args (tùy chọn): prefix (khóa trong rhm_home_doctor_sections(), mặc định 'rhm_home_doctors': chữ + chuyên khoa lấy từ mục Customizer đó),
 * id (id của section; trống → id của bản trong rhm_home_doctor_sections()), eyebrow, title (trống → Customizer).
 *
 * Mỗi thẻ: học vị → tên (H3) → chức vụ → 2 ô số liệu (kinh nghiệm, số ca) → chứng chỉ chuyên môn → nút Xem hồ sơ / Đặt lịch hẹn;
 * ảnh chân dung: cột phải (≥ 1024px), trên cùng (mobile). Dòng nào thiếu dữ liệu thì không in.
 * Desktop: cột chữ tối đa 640/680px + cột ảnh cố định, cả cụm canh giữa thẻ → chữ luôn sát ảnh, phần dư (khi hồ sơ ít dữ liệu
 * hoặc màn hình rộng) chia đều ra 2 mép thẻ thay vì thành khoảng trống giữa chữ và ảnh.
 *
 * Hiệu năng:
 * - Bố cục slider dựng sẵn bằng CSS (slide w-full, Swiper slidesPerView 'auto') → trước/sau khi JS chạy giống hệt, CLS = 0.
 *   Các slide cao bằng nhau (flex stretch) → đổi slide không làm section đổi chiều cao.
 * - JS: src/js/carousel.js (chỉ khi ≥ 2 bác sĩ), Swiper chỉ tải khi section sắp vào màn hình.
 * - Ảnh lazy (section nằm dưới màn hình đầu), sizes = độ rộng khung ảnh thật.
 * - Slide đệm 16px/24px, .swiper lề âm tương ứng → bóng của thẻ không bị overflow: hidden cắt, slide kế không lộ ra.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_prefix  = (string) ( $args['prefix'] ?? 'rhm_home_doctors' );
$rhm_config  = rhm_home_doctor_sections()[ $rhm_prefix ] ?? null;
if ( ! $rhm_config ) {
	return;
}
$rhm_term = rhm_home_doctor_term( $rhm_prefix );
if ( $rhm_config['require_term'] && ! $rhm_term ) {
	return; // Bản 2 chưa chọn chuyên khoa → không hiện (tránh trùng slider bản 1).
}
$rhm_doctors = rhm_get_home_doctors( $rhm_term );

if ( ! $rhm_doctors ) {
	return;
}

$rhm_count = count( $rhm_doctors );

if ( $rhm_count > 1 && file_exists( RHM_THEME_DIR . '/dist/js/carousel.js' ) ) {
	wp_enqueue_script_module( 'rhm-carousel', RHM_THEME_URI . '/dist/js/carousel.js', array(), rhm_asset_version( 'dist/js/carousel.js' ) );
}

$rhm_id       = (string) ( $args['id'] ?? '' ) ?: $rhm_config['id'];
$rhm_title_id = '' !== $rhm_id ? $rhm_id . '-title' : 'doctors-title';
$rhm_eyebrow  = (string) ( $args['eyebrow'] ?? '' ) ?: rhm_home_doctors_text( 'eyebrow', $rhm_prefix );
$rhm_title    = (string) ( $args['title'] ?? '' ) ?: rhm_home_doctors_text( 'title', $rhm_prefix );
$rhm_all_text = rhm_home_doctors_text( 'all_text', $rhm_prefix );
$rhm_all_link = rhm_home_doctors_text( 'all_link', $rhm_prefix );
// Id tên bác sĩ có tiền tố section → 1 bác sĩ nằm ở 2 slider cùng trang không trùng id (bản gốc không id: giữ "doctor-<id>-name").
$rhm_name_id  = static fn( int $doctor_id ): string => ( '' !== $rhm_id ? $rhm_id . '-' : '' ) . 'doctor-' . $doctor_id . '-name';

// Khung ảnh (trừ viền trắng 6px mỗi bên): ≥ 1280px cột 440px → 428px; 1024px cột 340px → 328px; 640–1023px tối đa 360px → 348px;
// < 640px: màn hình − lề khung 16 − đệm thẻ 20 (mỗi bên; đệm slide bù bằng lề âm của .swiper) − viền 12 = 100vw − 84px.
$rhm_sizes = '(min-width: 1280px) 428px, (min-width: 1024px) 328px, (min-width: 640px) 348px, calc(100vw - 84px)';
?>
<section<?php echo '' !== $rhm_id ? ' id="' . esc_attr( $rhm_id ) . '"' : ''; ?> class="overflow-hidden py-4 lg:py-5" aria-labelledby="<?php echo esc_attr( $rhm_title_id ); ?>">
	<?php
	// Bố cục: < 1024px xếp dọc: tiêu đề → thẻ → nút ‹ › → link "xem tất cả".
	// ≥ 1024px lưới 2 cột: hàng 1 = tiêu đề | link + nút ‹ › (không đặt nút ở mép thẻ: cột nút liên hệ nhanh cố định ở mép phải
	// màn hình sẽ đè lên), hàng 2 = thẻ rộng hết. Nút ‹ › phải nằm trong .js-carousel (carousel.js tìm nút trong khối này).
	?>
	<div class="site-container flex flex-col lg:grid lg:grid-cols-[minmax(0,1fr)_auto] lg:items-end lg:gap-x-8<?php echo $rhm_count > 1 ? ' js-carousel' : ''; ?>">
		<div class="mb-2 lg:mb-4">
			<?php if ( $rhm_eyebrow ) : ?>
				<p class="mb-3 flex items-center gap-2 text-[13px] font-semibold tracking-wide text-dark uppercase lg:text-sm">
					<span class="h-0.5 w-6 rounded-full bg-primary" aria-hidden="true"></span>
					<?php echo esc_html( $rhm_eyebrow ); ?>
				</p>
			<?php endif; ?>
			<h2 id="<?php echo esc_attr( $rhm_title_id ); ?>" class="text-[28px] leading-tight font-bold text-dark lg:text-4xl"><?php echo esc_html( $rhm_title ); ?></h2>
		</div>

		<div class="order-3 flex items-center justify-center gap-3 lg:order-none lg:mb-5">
			<?php if ( $rhm_all_text && $rhm_all_link ) : ?>
				<a class="mr-3 hidden items-center gap-2 font-semibold text-primary hover:text-primary-dark lg:inline-flex" href="<?php echo esc_url( $rhm_all_link ); ?>"<?php echo rhm_external_link_attrs( $rhm_all_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
					<?php echo esc_html( $rhm_all_text ); ?>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			<?php endif; ?>
			<?php if ( $rhm_count > 1 ) : ?>
				<button class="carousel-prev flex size-11 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-[0_4px_14px_rgb(16_42_67/0.1)] transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary" type="button" aria-label="<?php esc_attr_e( 'Bác sĩ trước', 'themeranghammatsg' ); ?>" disabled>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5 rotate-180' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
				<button class="carousel-next flex size-11 cursor-pointer items-center justify-center rounded-full border border-line bg-white text-primary shadow-[0_4px_14px_rgb(16_42_67/0.1)] transition-colors hover:border-primary hover:bg-primary hover:text-white disabled:cursor-default disabled:opacity-40 disabled:hover:border-line disabled:hover:bg-white disabled:hover:text-primary" type="button" aria-label="<?php esc_attr_e( 'Bác sĩ tiếp theo', 'themeranghammatsg' ); ?>">
					<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</button>
			<?php endif; ?>
		</div>

		<div class="relative order-2 lg:order-none lg:col-span-2">
			<div class="swiper -mx-4 -my-2">
				<div class="swiper-wrapper">
					<?php foreach ( $rhm_doctors as $rhm_doctor ) : ?>
						<div class="swiper-slide h-auto w-full px-4 pt-4 pb-8">
							<article class="home-doctor-card relative isolate grid h-full items-center overflow-hidden rounded-[28px] border border-line bg-linear-to-br from-white via-[#f7fcfa] to-mint p-5 shadow-card sm:p-8 lg:grid-cols-[minmax(0,640px)_340px] lg:justify-center lg:gap-10 lg:px-16 lg:py-6 xl:grid-cols-[minmax(0,680px)_440px] xl:gap-16 xl:px-5" aria-labelledby="<?php echo esc_attr( $rhm_name_id( $rhm_doctor['id'] ) ); ?>">
								<?php // Họa tiết (răng mờ, vầng sáng, khối tròn + khung nét đứt quanh ảnh): pseudo-element trong page-home.css, 0 node DOM. ?>
								<figure class="home-doctor-photo relative mx-auto w-full max-w-[360px] lg:order-last lg:max-w-none">
									<a class="relative block aspect-[4/3] overflow-hidden rounded-3xl border-[6px] border-white bg-white shadow-[0_16px_40px_rgb(16_42_67/0.14)] sm:aspect-[4/5]" href="<?php echo esc_url( $rhm_doctor['url'] ); ?>" tabindex="-1" aria-hidden="true">
										<?php rhm_doctor_image( $rhm_doctor['image'], $rhm_sizes, 'absolute inset-0 size-full object-cover object-top', $rhm_doctor['name'], false, 4 / 5 ); ?>
									</a>
								</figure>

								<div class="relative mt-6 min-w-0 lg:mt-0">
									<?php if ( $rhm_doctor['degree'] ) : ?>
										<p class="text-base font-semibold text-dark lg:text-lg"><?php echo esc_html( $rhm_doctor['degree'] ); ?></p>
									<?php endif; ?>
									<h3 id="<?php echo esc_attr( $rhm_name_id( $rhm_doctor['id'] ) ); ?>" class="mt-1 text-[26px] leading-tight font-bold text-primary uppercase lg:text-[40px]">
										<a class="hover:text-primary-dark" href="<?php echo esc_url( $rhm_doctor['url'] ); ?>"><?php echo esc_html( $rhm_doctor['name'] ); ?></a>
									</h3>
~
									<?php if ( $rhm_doctor['position'] ) : ?>
										<p class="mt-4 text-[15px] leading-relaxed text-body lg:text-base">
											<span class="font-semibold text-teal-dark"><?php esc_html_e( 'Chuyên khoa:', 'themeranghammatsg' ); ?></span>
											<?php echo esc_html( $rhm_doctor['position'] ); ?>
										</p>
									<?php endif; ?>

									<?php if ( $rhm_doctor['years'] || $rhm_doctor['case_count'] ) : ?>
										<dl class="mt-5 grid max-w-md grid-cols-2 gap-3 lg:mt-6 lg:max-w-none lg:gap-4">
											<?php
											foreach (
												array(
													array( $rhm_doctor['years'], __( 'Kinh nghiệm lâm sàng', 'themeranghammatsg' ) ),
													array( $rhm_doctor['case_count'], __( 'Hồ sơ điều trị', 'themeranghammatsg' ) ),
												) as [ $rhm_value, $rhm_label ]
											) :
												if ( ! $rhm_value ) {
													continue;
												}
												?>
												<div class="flex flex-col-reverse rounded-2xl border border-line bg-white/85 px-4 py-3 shadow-[0_4px_14px_rgb(16_42_67/0.06)] lg:border-l-4 lg:border-l-primary lg:px-5 lg:py-4">
													<dt class="mt-0.5 text-[13px] leading-snug text-muted lg:text-sm"><?php echo esc_html( $rhm_label ); ?></dt>
													<dd class="text-[22px] leading-tight font-bold text-primary lg:text-[26px]"><?php echo esc_html( $rhm_value ); ?></dd>
												</div>
											<?php endforeach; ?>
										</dl>
									<?php endif; ?>

									<?php if ( $rhm_doctor['credentials'] ) : ?>
										<?php // Desktop: gom vào khung trắng mờ rộng hết cột → lấp khoảng trống giữa chữ và ảnh. ?>
										<div class="mt-6 lg:rounded-2xl lg:border lg:border-line lg:bg-white/70 lg:px-6 lg:py-5 lg:shadow-[0_4px_14px_rgb(16_42_67/0.05)]">
											<p class="font-semibold text-teal-dark lg:text-lg"><?php esc_html_e( 'Chứng chỉ chuyên môn', 'themeranghammatsg' ); ?></p>
											<ul class="mt-3 space-y-2 lg:space-y-2.5">
												<?php foreach ( $rhm_doctor['credentials'] as $rhm_line ) : ?>
													<li class="home-doctor-check relative pl-[30px] text-[15px] leading-relaxed text-body lg:text-base"><?php echo esc_html( $rhm_line ); ?></li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endif; ?>

									<div class="mt-7 grid grid-cols-2 gap-3 sm:flex sm:flex-wrap lg:mt-8">
										<a class="btn btn-primary h-12 rounded-full px-5 text-[15px] font-bold sm:px-8 sm:text-base" href="<?php echo esc_url( $rhm_doctor['url'] ); ?>">
											<?php esc_html_e( 'Xem hồ sơ', 'themeranghammatsg' ); ?><span class="sr-only"> <?php echo esc_html( $rhm_doctor['name'] ); ?></span>
										</a>
										<?php rhm_booking_button( 'h-12 rounded-full border-0 bg-linear-to-b from-[#fbe38a] to-[#e9b934] px-5 text-[15px] font-bold text-dark shadow-[0_6px_16px_rgb(233_185_52/0.35)] hover:from-[#f7d863] hover:to-[#dfa91c] hover:text-dark sm:px-8 sm:text-base' ); ?>
									</div>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if ( $rhm_all_text && $rhm_all_link ) : ?>
			<p class="order-4 mt-6 text-center lg:hidden">
				<a class="inline-flex items-center gap-2 font-semibold text-primary hover:text-primary-dark" href="<?php echo esc_url( $rhm_all_link ); ?>"<?php echo rhm_external_link_attrs( $rhm_all_link ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static attributes. ?>>
					<?php echo esc_html( $rhm_all_text ); ?>
					<?php echo rhm_get_icon( 'arrow-right', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			</p>
		<?php endif; ?>
	</div>
</section>
