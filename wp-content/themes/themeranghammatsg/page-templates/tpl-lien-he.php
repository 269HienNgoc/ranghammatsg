<?php
/**
 * Template Name: Liên hệ – Đặt lịch
 * Template Post Type: page
 *
 * Bố cục: banner chữ (dòng nhỏ, H1 = tiêu đề trang, giới thiệu, breadcrumb) → khối thông tin (dòng nhỏ + H2 + mô tả canh giữa)
 * → 2 thẻ: thông tin liên hệ (địa chỉ, hotline, email, giờ làm việc + ô ghi chú) | form đặt lịch Fluent Forms (#dat-lich)
 * → nội dung trang (nếu có).
 * Dữ liệu: inc/contact.php (chữ: Tùy biến → "Trang Liên hệ"), "Thông tin phòng khám" (liên hệ, ID form). CSS: page-contact.css
 *
 * Hiệu năng: trang không có ảnh (LCP là H1), không JS riêng. Form: CSS Fluent bị gỡ (style ở _booking-form.css + ghi đè
 * .contact-form trong page-contact.css), JS Fluent "chạm mới tải" (fluent-lazy.js).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_form_id = absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
$rhm_hotline = rhm_get_option( 'rhm_hotline' );
$rhm_note    = rhm_get_option( 'rhm_hotline_note' );
$rhm_email   = rhm_get_option( 'rhm_email' );
$rhm_address = trim( rhm_get_option( 'rhm_address' ) );
$rhm_hours   = trim( rhm_get_option( 'rhm_working_hours' ) );

// Dòng thông tin liên hệ: [ icon, nhãn, giá trị HTML đã escape ]; dòng thiếu dữ liệu thì bỏ.
$rhm_rows = array();
if ( '' !== $rhm_address ) {
	$rhm_rows[] = array( 'map-pin', __( 'Địa chỉ phòng khám', 'themeranghammatsg' ), nl2br( esc_html( $rhm_address ) ) );
}
if ( '' !== $rhm_hotline ) {
	$rhm_rows[] = array(
		'phone',
		__( 'Hotline tư vấn', 'themeranghammatsg' ),
		'<a class="font-semibold text-teal-dark hover:text-primary" href="' . esc_url( rhm_get_tel_href( $rhm_hotline ) ) . '">' . esc_html( $rhm_hotline ) . '</a>'
			. ( '' !== $rhm_note ? ' <span>(' . esc_html( $rhm_note ) . ')</span>' : '' ),
	);
}
if ( '' !== $rhm_email ) {
	$rhm_rows[] = array(
		'mail',
		__( 'Email hỗ trợ', 'themeranghammatsg' ),
		'<a class="text-teal-dark hover:text-primary" href="' . esc_url( 'mailto:' . $rhm_email ) . '">' . esc_html( $rhm_email ) . '</a>',
	);
}
if ( '' !== $rhm_hours ) {
	$rhm_rows[] = array( 'clock', __( 'Giờ làm việc', 'themeranghammatsg' ), nl2br( esc_html( $rhm_hours ) ) );
}

get_header();

while ( have_posts() ) :
	the_post();
	?>
	<section class="bg-linear-to-br from-mint via-soft to-white py-10 lg:py-16">
		<div class="site-container">
			<?php if ( rhm_contact_text( 'hero_eyebrow' ) ) : ?>
				<p class="mb-3 text-xs font-bold tracking-wider text-primary uppercase lg:text-[13px]"><?php echo esc_html( rhm_contact_text( 'hero_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h1 class="max-w-md text-4xl leading-tight font-bold text-teal-dark lg:max-w-lg lg:text-5xl"><?php the_title(); ?></h1>
			<?php if ( rhm_contact_text( 'intro' ) ) : ?>
				<p class="mt-4 max-w-xl leading-relaxed text-muted lg:mt-5"><?php echo esc_html( rhm_contact_text( 'intro' ) ); ?></p>
			<?php endif; ?>
			<div class="mt-5 lg:mt-6">
				<?php rhm_breadcrumb(); ?>
			</div>
		</div>
	</section>

	<?php
	// 1024–1479px: cột nút liên hệ nhanh (fixed, ~90px từ mép phải) đè lên thẻ form → lùi đều 2 bên 90px + lề 16px
	// (giống "Chừa chỗ cột nút liên hệ nhanh" của landing, tiêu đề vẫn đúng tâm); từ 1480px khung 1280px không chạm cột nút.
	?>
	<section class="site-container py-12 lg:py-16 lg:max-[92.5rem]:px-[6.625rem]" aria-labelledby="contact-info-title">
		<div class="mx-auto max-w-2xl text-center">
			<?php if ( rhm_contact_text( 'info_eyebrow' ) ) : ?>
				<p class="mb-2 text-xs font-bold tracking-wider text-primary uppercase lg:text-[13px]"><?php echo esc_html( rhm_contact_text( 'info_eyebrow' ) ); ?></p>
			<?php endif; ?>
			<h2 id="contact-info-title" class="text-2xl leading-tight font-bold text-teal-dark lg:text-3xl"><?php echo esc_html( rhm_contact_text( 'info_title' ) ?: get_the_title() ); ?></h2>
			<?php if ( rhm_contact_text( 'info_text' ) ) : ?>
				<p class="mt-3 leading-relaxed text-muted"><?php echo esc_html( rhm_contact_text( 'info_text' ) ); ?></p>
			<?php endif; ?>
		</div>

		<div class="mt-8 grid items-start gap-6 lg:mt-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.4fr)] lg:gap-8">
			<?php if ( $rhm_rows || rhm_contact_text( 'card_note' ) ) : ?>
				<div class="rounded-xl border border-line bg-white p-5 shadow-card sm:p-8">
					<?php if ( rhm_contact_text( 'card_title' ) ) : ?>
						<h3 class="text-xl font-bold text-teal-dark lg:text-2xl"><?php echo esc_html( rhm_contact_text( 'card_title' ) ); ?></h3>
					<?php endif; ?>
					<?php if ( rhm_contact_text( 'card_text' ) ) : ?>
						<p class="mt-2 text-sm text-muted"><?php echo esc_html( rhm_contact_text( 'card_text' ) ); ?></p>
					<?php endif; ?>

					<?php if ( $rhm_rows ) : ?>
						<ul class="mt-5 divide-y divide-line border-y border-line">
							<?php foreach ( $rhm_rows as [ $rhm_icon, $rhm_label, $rhm_value ] ) : ?>
								<li class="flex gap-4 py-4 lg:py-5">
									<span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-mint text-teal-dark">
										<?php echo rhm_get_icon( $rhm_icon, 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									</span>
									<div class="min-w-0 flex-1">
										<p class="font-semibold text-dark"><?php echo esc_html( $rhm_label ); ?></p>
										<p class="mt-1 text-sm leading-relaxed wrap-anywhere text-muted"><?php echo $rhm_value; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- đã escape khi dựng $rhm_rows. ?></p>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( rhm_contact_text( 'card_note' ) ) : ?>
						<p class="mt-6 flex gap-2.5 rounded-lg border border-mint-dark bg-mint/60 p-4 text-sm leading-relaxed text-teal-dark">
							<?php echo rhm_icon_svg( 'landing', 'lap-lanh', 'mt-0.5 size-4 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG thư viện icon. ?>
							<span><?php echo esc_html( rhm_contact_text( 'card_note' ) ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div id="dat-lich" class="contact-form rhm-booking rounded-xl border border-line bg-white p-5 shadow-card sm:p-8 <?php echo ( $rhm_rows || rhm_contact_text( 'card_note' ) ) ? '' : 'lg:col-span-2'; ?>">
				<h3 class="text-xl font-bold text-teal-dark lg:text-2xl"><?php echo esc_html( rhm_contact_text( 'form_title' ) ?: __( 'Gửi yêu cầu tư vấn', 'themeranghammatsg' ) ); ?></h3>
				<?php if ( rhm_contact_text( 'form_note' ) ) : ?>
					<p class="mt-2 text-sm text-muted"><?php echo esc_html( rhm_contact_text( 'form_note' ) ); ?></p>
				<?php endif; ?>
				<div class="mt-6">
					<?php
					if ( $rhm_form_id && shortcode_exists( 'fluentform' ) ) {
						echo rhm_render_fluent_form( $rhm_form_id ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fluent Forms output.
					} elseif ( current_user_can( 'edit_theme_options' ) ) {
						echo '<p class="rounded-lg border border-dashed border-primary p-4 text-sm">' . esc_html__( 'Chưa cấu hình form: tạo form trong Fluent Forms rồi nhập ID tại Giao diện → Tùy biến → Thông tin phòng khám.', 'themeranghammatsg' ) . '</p>';
					}
					?>
				</div>
				<?php if ( rhm_contact_text( 'form_privacy' ) ) : ?>
					<p class="mt-1 text-xs leading-relaxed text-muted"><?php echo esc_html( rhm_contact_text( 'form_privacy' ) ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<?php if ( '' !== trim( get_the_content() ) ) : ?>
		<div class="site-container pb-12 lg:pb-16">
			<div class="entry-content max-w-4xl"><?php the_content(); ?></div>
		</div>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
