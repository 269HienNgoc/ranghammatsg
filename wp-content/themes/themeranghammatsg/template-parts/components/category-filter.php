<?php
/**
 * Thanh lọc chuyên mục con ở đầu trang danh mục. Dữ liệu: rhm_get_category_filter() (inc/category.php). CSS: archive.css
 *
 * Mỗi nút là link tới trang chuyên mục (không lọc bằng JS) → Google đọc được, không tải thêm gì.
 * - < 768px:   nút nhỏ (cao 32px), mọi mục trên 1 hàng, vuốt ngang trong khối (trang không bị kéo ngang), không có nút "…".
 * - ≥ 768px:   "Tất cả" + 2 mục con (1024px: 3, 1280px: 4) giãn đều theo độ dài chữ; mục còn lại nằm trong nút "…".
 * Nút "…" là <details> (mở/đóng không cần JS); src/js/dropdown.js chỉ thêm đóng khi bấm ra ngoài / Esc.
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_filter = rhm_get_category_filter();

if ( ! $rhm_filter ) {
	return;
}

$rhm_count = count( $rhm_filter['items'] );

// Nút "…" chỉ hiện ở breakpoint còn mục con bị ẩn.
$rhm_more = match ( true ) {
	$rhm_count <= 2  => false,
	3 === $rhm_count => 'lg:hidden',
	4 === $rhm_count => 'xl:hidden',
	default          => '',
};

if ( false !== $rhm_more && file_exists( RHM_THEME_DIR . '/dist/js/dropdown.js' ) ) {
	wp_enqueue_script_module( 'rhm-dropdown', RHM_THEME_URI . '/dist/js/dropdown.js', array(), rhm_asset_version( 'dist/js/dropdown.js' ) );
}

$rhm_tabs = array_merge( array( $rhm_filter['all'] ), $rhm_filter['items'] );
?>
<nav class="mt-4 mb-2 lg:mb-8" aria-label="<?php esc_attr_e( 'Lọc theo chuyên mục', 'themeranghammatsg' ); ?>">
	<ul class="-mx-4 flex gap-2 overflow-x-auto px-4 [scrollbar-width:none] md:mx-0 md:gap-2.5 md:overflow-visible md:px-0 [&::-webkit-scrollbar]:hidden">
		<?php foreach ( $rhm_tabs as $rhm_index => $rhm_tab ) : ?>
			<?php
			// $rhm_index: 0 = "Tất cả", 1… = mục con. Mục con thứ 3, 4, 5+ ẩn dần vào nút "…" theo breakpoint.
			$rhm_hide = match ( true ) {
				$rhm_index <= 2  => '',
				3 === $rhm_index => 'md:max-lg:hidden',
				4 === $rhm_index => 'md:max-xl:hidden',
				default          => 'md:hidden',
			};
			?>
			<li class="shrink-0 md:min-w-0 md:flex-auto <?php echo esc_attr( $rhm_hide ); ?>">
				<a class="flex h-8 min-w-0 items-center justify-center gap-1.5 rounded-lg px-3 text-[13px] whitespace-nowrap transition-colors md:h-12 md:gap-2 md:rounded-xl md:px-5 md:text-base <?php echo $rhm_tab['active'] ? 'bg-teal-dark text-white' : 'bg-mint text-teal-dark hover:bg-mint-dark'; ?>" href="<?php echo esc_url( $rhm_tab['url'] ); ?>"<?php echo $rhm_tab['active'] ? ' aria-current="page"' : ''; ?>>
					<?php echo rhm_get_icon( 'document', 'size-4 shrink-0 md:size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<span class="truncate"><?php echo esc_html( $rhm_tab['name'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>

		<?php if ( false !== $rhm_more ) : ?>
			<li class="relative shrink-0 max-md:hidden <?php echo esc_attr( $rhm_more ); ?>">
				<details class="js-dropdown group">
					<summary class="flex size-12 cursor-pointer list-none items-center justify-center rounded-xl bg-mint text-teal-dark transition-colors group-open:bg-teal-dark group-open:text-white hover:bg-mint-dark group-open:hover:bg-teal-dark [&::-webkit-details-marker]:hidden">
						<?php echo rhm_get_icon( 'more', 'size-6' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span class="sr-only"><?php esc_html_e( 'Chuyên mục khác', 'themeranghammatsg' ); ?></span>
					</summary>
					<ul class="absolute top-full right-0 z-20 mt-2 w-64 rounded-xl border border-line bg-white py-2 shadow-card">
						<?php foreach ( array_slice( $rhm_filter['items'], 2, null, true ) as $rhm_index => $rhm_item ) : ?>
							<?php
							// Chỉ hiện trong "…" ở breakpoint mà mục đó bị ẩn khỏi hàng nút ($rhm_index: 0 = mục con đầu tiên).
							$rhm_show = match ( $rhm_index ) {
								2       => 'lg:hidden',
								3       => 'xl:hidden',
								default => '',
							};
							?>
							<li class="<?php echo esc_attr( $rhm_show ); ?>">
								<a class="flex items-center gap-2 px-4 py-2.5 text-teal-dark hover:bg-mint" href="<?php echo esc_url( $rhm_item['url'] ); ?>">
									<?php echo rhm_get_icon( 'document', 'size-5 shrink-0' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
									<?php echo esc_html( $rhm_item['name'] ); ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</details>
			</li>
		<?php endif; ?>
	</ul>
</nav>
