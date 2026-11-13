<?php
/**
 * Landing – Form đăng ký (Fluent Forms): khung bo góc nền mint; trái = tiêu đề vạch dọc + mô tả + lợi ích (dấu tích) + hotline,
 * phải = thẻ form trắng (.rhm-booking → style chung _booking-form.css, CSS của Fluent được gỡ, JS Fluent "chạm mới tải").
 * Mobile: chữ trên, form dưới. Field: inc/landing/layouts/form.php.
 *
 * Form: field form_id, trống → form đặt lịch mặc định (theme mod rhm_booking_form_id). Không có form (chưa chọn / Fluent tắt) →
 * khách không thấy section; người có quyền sửa giao diện thấy ghi chú cấu hình.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_data    = $args['data'] ?? array();
$rhm_form_id = absint( $rhm_data['form_id'] ?? 0 ) ?: absint( get_theme_mod( 'rhm_booking_form_id', 0 ) );
$rhm_form    = rhm_render_fluent_form( $rhm_form_id );
$rhm_editor  = current_user_can( 'edit_theme_options' );
if ( '' === trim( $rhm_form ) && ! $rhm_editor ) {
	return;
}

$rhm_title   = rhm_landing_text( $rhm_data, 'title' ) ?: __( 'Đăng ký tư vấn', 'themeranghammatsg' );
$rhm_head_id = $args['id'] . '-title';
$rhm_points  = rhm_landing_lines( $rhm_data, 'points' );
$rhm_hotline = ( ! isset( $rhm_data['hotline'] ) || $rhm_data['hotline'] ) ? rhm_get_option( 'rhm_hotline' ) : '';
$rhm_ftitle  = rhm_landing_text( $rhm_data, 'form_title' );
?>
<section id="<?php echo esc_attr( $args['id'] ); ?>" class="py-6 lg:py-8" aria-labelledby="<?php echo esc_attr( $rhm_head_id ); ?>">
	<div class="site-container">
		<div class="grid items-center gap-6 rounded-3xl border border-line bg-linear-to-br from-mint via-white to-mint p-5 shadow-card sm:p-8 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)] lg:gap-12 lg:p-12">
			<div class="min-w-0">
				<?php rhm_landing_heading( $rhm_head_id, $rhm_title, rhm_landing_text( $rhm_data, 'eyebrow' ), '', 'bar' ); ?>

				<?php foreach ( rhm_landing_lines( $rhm_data, 'description' ) as $rhm_line ) : ?>
					<p class="mb-3 leading-relaxed text-body/85"><?php echo esc_html( $rhm_line ); ?></p>
				<?php endforeach; ?>

				<?php if ( $rhm_points ) : ?>
					<ul class="mt-5 space-y-3">
						<?php foreach ( $rhm_points as $rhm_point ) : ?>
							<li class="flex items-start gap-3 leading-snug text-dark">
								<span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-primary text-white" aria-hidden="true">
									<?php echo rhm_icon_svg( 'landing', 'tich', 'size-4' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG trong theme. ?>
								</span>
								<span class="min-w-0"><?php echo esc_html( $rhm_point ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( $rhm_hotline ) : ?>
					<a class="group mt-6 inline-flex items-center gap-3 rounded-2xl border border-line bg-white py-3 pr-6 pl-3 shadow-card transition-colors hover:border-primary" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
						<span class="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary text-white transition-colors group-hover:bg-primary-dark">
							<?php echo rhm_get_icon( 'phone', 'size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</span>
						<span class="leading-tight">
							<span class="block text-sm text-muted"><?php esc_html_e( 'Hotline tư vấn', 'themeranghammatsg' ); ?></span>
							<strong class="block text-xl font-bold text-primary lg:text-2xl"><?php echo esc_html( $rhm_hotline ); ?></strong>
						</span>
					</a>
				<?php endif; ?>
			</div>

			<div class="rhm-booking min-w-0 rounded-2xl border border-line bg-white p-5 shadow-card sm:p-8">
				<?php if ( '' !== $rhm_ftitle ) : ?>
					<h3 class="mb-5 text-lg leading-snug font-bold text-teal-dark lg:text-xl"><?php echo esc_html( $rhm_ftitle ); ?></h3>
				<?php endif; ?>
				<?php if ( '' !== trim( $rhm_form ) ) : ?>
					<?php echo $rhm_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- HTML form do Fluent Forms in. ?>
				<?php else : ?>
					<p class="rounded-xl border border-dashed border-primary p-4 text-sm"><?php esc_html_e( 'Chưa có form: chọn form ở ô "Form Fluent Forms" của section này, hoặc nhập ID form đặt lịch mặc định tại Giao diện → Tùy biến → Thông tin phòng khám (Fluent Forms phải đang bật). Ghi chú này chỉ hiện với quản trị viên.', 'themeranghammatsg' ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
