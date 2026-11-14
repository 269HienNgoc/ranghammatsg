<?php
/**
 * Header: hàng trên (logo, tìm kiếm, cờ, đặt lịch, hotline) + menu chính.
 * Style: utility trong file này (chỉ có hiệu lực bên trong .site-header) + src/css/pages/header.css.
 *
 * Bố cục hàng trên:
 * - < 560px:     [logo] ……… [📅 Đặt lịch hẹn] [☰]
 * - 560–1023px:  [logo] … [📞 hotline] [📅 Đặt lịch hẹn] [☰]
 * - ≥ 1024px:    [logo] [tìm kiếm] [cờ] [Đặt lịch hẹn] [📞 hotline]
 *
 * Menu chính (#primary-menu):
 * - < 1024px: ngăn kéo trượt từ trái (logo + cờ + ✕, ô tìm kiếm, danh sách menu, khối tư vấn ở đáy)
 *   kèm lớp phủ .nav-backdrop; mở/đóng bằng class html.nav-open (src/js/main.js).
 * - ≥ 1024px: thanh menu ngang dưới hàng trên; phần riêng của ngăn kéo bị ẩn (lg:hidden).
 *
 * Sticky: dính đầu trang khi cuộn; desktop thu gọn bằng transform khi cuộn xuống (class .is-stuck do src/js/main.js gắn,
 * style ở cuối src/css/pages/header.css) — không đổi kích thước bố cục nên không gây layout shift.
 * LƯU Ý: không thêm transform/filter cho .site-header — sẽ làm ngăn kéo `position: fixed` bị lệch.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_hotline      = rhm_get_option( 'rhm_hotline' );
$rhm_hotline_note = rhm_get_option( 'rhm_hotline_note', __( 'Tư vấn miễn phí 24/7', 'themeranghammatsg' ) );
$rhm_current_url = get_pagenum_link( max( 1, (int) get_query_var( 'paged' ) ), false );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<link rel="alternate" hreflang="vi-vn" href="<?php echo esc_url( $rhm_current_url ); ?>" />
	<link rel="alternate" hreflang="vi" href="<?php echo esc_url( $rhm_current_url ); ?>" />
	<link rel="alternate" hreflang="x-default" href="<?php echo esc_url( $rhm_current_url ); ?>" />
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Chuyển đến nội dung', 'themeranghammatsg' ); ?></a>

<header class="site-header" id="site-header">
	<div class="header-bar site-container flex items-center gap-2.5 py-3 xs:gap-3 lg:min-h-[113px] lg:gap-3 lg:py-3 xl:gap-6">
		<div class="site-branding order-1 min-w-0 flex-1 lg:flex-initial<?php echo has_custom_logo() ? ' has-logo' : ''; ?>">
			<?php if ( has_custom_logo() ) : ?>
				<?php rhm_custom_logo(); ?>
			<?php else : ?>
				<a class="inline-block text-base leading-tight font-bold text-dark uppercase hover:text-primary xs:text-lg xl:text-2xl" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
			<?php endif; ?>
			<?php rhm_header_locations(); // Dòng địa điểm dưới logo (inc/header-locations.php). ?>
		</div>

		<div class="header-search min-w-0 flex-1 max-lg:hidden lg:order-2 lg:ml-[clamp(8px,3vw,56px)] lg:max-w-[610px] lg:min-w-[180px]">
			<?php get_search_form(); ?>
		</div>

		<div class="shrink-0 max-lg:hidden lg:order-3 lg:ml-auto">
			<?php rhm_flag_links(); ?>
		</div>

		<div class="header-booking order-3 shrink-0 lg:order-4">
			<?php rhm_booking_button( 'h-11 gap-2 rounded-xl border-0 bg-linear-to-b from-primary-light to-primary px-3.5 text-[15px] font-semibold max-[359px]:px-3 max-[359px]:text-sm whitespace-nowrap shadow-[0_4px_12px_rgb(11_122_85/0.25)] hover:from-primary-dark hover:to-primary-dark lg:h-[52px] lg:rounded-lg lg:text-base lg:font-medium xl:px-5 xl:text-[17px] 2xl:px-7 2xl:text-xl', 'size-5 shrink-0 max-[389px]:hidden lg:hidden' ); ?>
		</div>

		<?php if ( $rhm_hotline ) : ?>
			<a class="header-hotline order-2 hidden shrink-0 items-center gap-2 text-dark hover:text-primary xs:flex lg:order-5 lg:gap-2.5" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
				<?php echo rhm_get_icon( 'phone', 'size-[26px] shrink-0 text-primary lg:size-9 lg:text-current' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				<span class="flex flex-col leading-tight whitespace-nowrap">
					<span class="text-base font-bold lg:text-xl 2xl:text-2xl"><?php echo esc_html( $rhm_hotline ); ?></span>
					<?php if ( $rhm_hotline_note ) : ?>
						<span class="header-hotline-note hidden text-[13px] text-muted xl:block"><?php echo esc_html( $rhm_hotline_note ); ?></span>
					<?php endif; ?>
				</span>
			</a>
		<?php endif; ?>

		<button class="menu-toggle order-4 inline-flex size-11 shrink-0 cursor-pointer items-center justify-center rounded-xl border border-[#d6ebe6] bg-soft transition-colors hover:bg-[#e3f4f1] lg:hidden" type="button" aria-controls="primary-menu" aria-expanded="false">
			<span class="menu-toggle__bar" aria-hidden="true"></span>
			<span class="sr-only"><?php esc_html_e( 'Mở menu', 'themeranghammatsg' ); ?></span>
		</button>
	</div>

	<?php /* Lớp phủ nội dung khi mở ngăn kéo (mobile). Bấm để đóng. */ ?>
	<div class="nav-backdrop lg:hidden" aria-hidden="true"></div>

	<nav id="primary-menu" class="site-nav lg:block lg:border-t lg:border-[#edf4f4] lg:bg-linear-to-b lg:from-[#fbfefd] lg:to-soft lg:shadow-[0_1px_0_#e6efef]" aria-label="<?php esc_attr_e( 'Menu chính', 'themeranghammatsg' ); ?>">
		<div class="flex shrink-0 items-center gap-3 px-5 pt-4 pb-3 lg:hidden">
			<div class="drawer-logo min-w-0 flex-1">
				<?php if ( has_custom_logo() ) : ?>
					<?php rhm_custom_logo(); ?>
				<?php else : ?>
					<a class="inline-block text-base leading-tight font-bold text-dark uppercase" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a>
				<?php endif; ?>
			</div>
			<?php rhm_flag_links(); ?>
			<button class="nav-close inline-flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-full text-dark transition-colors hover:bg-soft" type="button">
				<?php echo rhm_get_icon( 'close', 'size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				<span class="sr-only"><?php esc_html_e( 'Đóng menu', 'themeranghammatsg' ); ?></span>
			</button>
		</div>

		<div class="shrink-0 px-5 pb-3 lg:hidden">
			<?php get_search_form( array( 'rhm_variant' => 'drawer' ) ); ?>
		</div>

		<div class="drawer-body lg:mx-auto lg:w-full lg:max-w-site lg:px-4">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'main-nav',
					'items_wrap'     => rhm_primary_menu_items_wrap(),
					'fallback_cb'    => false,
					'depth'          => 3, // Tối đa 3 cấp: menu chính → menu con → menu cháu.
				)
			);
			?>
		</div>

		<div class="shrink-0 rounded-br-2xl bg-soft px-5 pt-4 pb-[max(1rem,env(safe-area-inset-bottom))] lg:hidden">
			<p class="mb-2 text-center text-[15px] font-semibold text-primary"><?php esc_html_e( 'Cần tư vấn nha khoa?', 'themeranghammatsg' ); ?></p>
			<?php if ( $rhm_hotline ) : ?>
				<a class="mb-3 flex items-center justify-center gap-2 text-lg font-bold text-dark hover:text-primary" href="<?php echo esc_url( rhm_get_tel_href( $rhm_hotline ) ); ?>">
					<?php echo rhm_get_icon( 'phone', 'size-6 shrink-0 text-primary' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<?php echo esc_html( $rhm_hotline ); ?>
				</a>
			<?php endif; ?>
			<?php rhm_booking_button( 'h-[52px] w-full gap-3 rounded-full border-0 bg-linear-to-b from-primary-light to-primary text-base font-bold uppercase shadow-[0_6px_16px_rgb(11_122_85/0.3)] hover:from-primary-dark hover:to-primary-dark', 'size-5 shrink-0', true ); ?>
		</div>
	</nav>
</header>

<main id="main" class="site-main">
