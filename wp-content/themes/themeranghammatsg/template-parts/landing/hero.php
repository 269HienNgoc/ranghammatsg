<?php
/**
 * Landing – Banner đầu trang. Field: inc/landing/layouts/hero.php. CSS: .landing-hero (page-about.css).
 *
 * Desktop (≥ 1024px): chữ nửa trái (canh theo .site-container), ảnh tuyệt đối phủ nửa phải màn hình, object-cover,
 * mép trái mờ dần vào nền (mask CSS). Mobile: chữ → ảnh (rộng hết màn hình) bên dưới; có ảnh mobile riêng thì dùng.
 * Luôn hiện (thiếu tiêu đề → tiêu đề trang). Chỉ banner ĐẦU TRANG mới là H1 + ảnh LCP (fetchpriority=high);
 * banner đặt ở vị trí khác dùng H2 (trang đã có H1 từ khối tiêu đề trang) và ảnh lazy.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_title   = rhm_landing_text( $rhm_data, 'title' ) ?: get_the_title();
$rhm_image   = rhm_landing_image( $rhm_data, 'image' );
$rhm_mobile  = rhm_landing_image( $rhm_data, 'image_mobile' );
$rhm_lead    = rhm_landing_lines( $rhm_data, 'lead' );
$rhm_lines   = rhm_landing_lines( $rhm_data, 'description' );
$rhm_eyebrow = rhm_landing_text( $rhm_data, 'eyebrow' );
$rhm_booking = ! empty( $rhm_data['booking'] );
$rhm_button  = rhm_landing_text( $rhm_data, 'button_text' ) && rhm_landing_url( $rhm_data, 'button_link' );
$rhm_heading = $args['is_first'] ? 'h1' : 'h2';
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="landing-hero relative overflow-hidden">
	<div class="<?php echo esc_attr( $rhm_image ? 'site-container relative z-10 py-8 lg:flex lg:min-h-[480px] lg:items-center lg:py-12 xl:min-h-[540px]' : 'site-container relative z-10 py-10 lg:py-14' ); ?>">
		<div class="<?php echo esc_attr( $rhm_image ? 'min-w-0 lg:w-1/2 lg:pr-10' : 'min-w-0 max-w-4xl' ); ?>">
			<?php
			if ( $args['is_first'] ) {
				rhm_breadcrumb();
			}
			?>
			<?php if ( '' !== $rhm_eyebrow ) : ?>
				<p class="mt-5 text-[13px] font-semibold tracking-wide text-dark uppercase lg:text-sm"><?php echo esc_html( $rhm_eyebrow ); ?></p>
			<?php endif; ?>
			<<?php echo esc_html( $rhm_heading ); ?> class="mt-4 text-[26px] leading-tight font-bold text-pretty text-primary uppercase sm:text-[32px] xl:text-[38px]"><?php echo esc_html( $rhm_title ); ?></<?php echo esc_html( $rhm_heading ); ?>>

			<?php foreach ( $rhm_lead as $rhm_line ) : ?>
				<p class="mt-5 font-semibold leading-relaxed text-dark lg:text-lg"><?php echo esc_html( $rhm_line ); ?></p>
			<?php endforeach; ?>
			<?php foreach ( $rhm_lines as $rhm_line ) : ?>
				<p class="mt-4 leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
			<?php endforeach; ?>

			<?php if ( $rhm_booking || $rhm_button ) : ?>
				<div class="mt-7 flex flex-wrap gap-3">
					<?php
					if ( $rhm_booking ) {
						rhm_booking_button( 'h-12 rounded-full px-7', 'size-5' );
					}
					rhm_landing_button( $rhm_data, 'btn btn-outline h-12 gap-2 rounded-full px-7' );
					?>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php if ( $rhm_image ) : ?>
		<div class="landing-hero__media lg:absolute lg:inset-y-0 lg:right-0 lg:w-1/2">
			<?php
			// Desktop: nửa phải màn hình (không giới hạn 1440px vì ảnh tràn mép). Mobile: rộng hết màn hình.
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- rhm_html_attrs escape từng thuộc tính.
			echo rhm_landing_picture( $rhm_image, '(min-width: 1024px) 50vw, 100vw', 'block h-auto w-full lg:h-full lg:object-cover lg:object-right', (bool) $args['is_first'], $rhm_mobile, '100vw' );
			?>
		</div>
	<?php endif; ?>
</section>
