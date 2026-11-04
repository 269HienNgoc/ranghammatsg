<?php
/**
 * Form tìm kiếm.
 * - Mặc định: header desktop, trang tìm kiếm, 404 (nút xanh vuông bên phải).
 * - Biến thể 'drawer': trong ngăn kéo menu mobile (bo tròn, kính lúp bên trái).
 *   Gọi: get_search_form( array( 'rhm_variant' => 'drawer' ) ).
 *
 * @package themeranghammatsg
 */

defined( 'ABSPATH' ) || exit;

$rhm_search_id = wp_unique_id( 'search-' );
$rhm_is_drawer = 'drawer' === ( $args['rhm_variant'] ?? '' );
?>
<?php if ( $rhm_is_drawer ) : ?>
<form role="search" method="get" class="flex h-12 w-full items-center gap-1 rounded-full border border-line bg-white pr-4 pl-1 transition-colors focus-within:border-green" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<button type="submit" class="inline-flex size-10 shrink-0 cursor-pointer items-center justify-center rounded-full text-muted hover:text-primary">
		<?php echo rhm_get_icon( 'search', 'size-5' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		<span class="sr-only"><?php esc_html_e( 'Tìm', 'themeranghammatsg' ); ?></span>
	</button>
	<label class="sr-only" for="<?php echo esc_attr( $rhm_search_id ); ?>"><?php esc_html_e( 'Tìm kiếm', 'themeranghammatsg' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $rhm_search_id ); ?>" class="min-w-0 flex-1 border-0 bg-transparent text-[15px] text-body placeholder:text-muted focus:outline-none" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Tìm dịch vụ, bài viết...', 'themeranghammatsg' ); ?>">
</form>
<?php else : ?>
<form role="search" method="get" class="flex h-11 w-full overflow-hidden rounded-md border border-[#e4e7ea] bg-white shadow-[0_2px_6px_rgb(16_33_45/0.06)] focus-within:border-green lg:h-[52px]" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="sr-only" for="<?php echo esc_attr( $rhm_search_id ); ?>"><?php esc_html_e( 'Tìm kiếm', 'themeranghammatsg' ); ?></label>
	<input type="search" id="<?php echo esc_attr( $rhm_search_id ); ?>" class="min-w-0 flex-1 border-0 bg-transparent px-5 text-base text-body placeholder:text-[#818181] focus:outline-none" name="s" value="<?php echo esc_attr( get_search_query() ); ?>" placeholder="<?php esc_attr_e( 'Nhập từ khóa cần tìm...', 'themeranghammatsg' ); ?>">
	<button type="submit" class="inline-flex w-[52px] shrink-0 cursor-pointer items-center justify-center bg-green text-white transition-colors hover:bg-green-dark lg:w-[62px]">
		<?php echo rhm_get_icon( 'search', 'size-[26px]' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		<span class="sr-only"><?php esc_html_e( 'Tìm', 'themeranghammatsg' ); ?></span>
	</button>
</form>
<?php endif; ?>
