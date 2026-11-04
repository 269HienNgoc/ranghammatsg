import { readdirSync } from 'node:fs';
import { basename, resolve } from 'node:path';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

const root = import.meta.dirname;

/**
 * Mỗi file (không bắt đầu bằng "_") trong thư mục là 1 entry riêng.
 * - src/css/pages/<tên>.css → dist/css/<tên>.css
 * - src/js/<tên>.js         → dist/js/<tên>.js
 * PHP quyết định file nào được nạp ở trang nào (inc/enqueue.php, template-parts/sections/hero.php).
 */
const entriesIn = ( dir, ext ) =>
	Object.fromEntries(
		readdirSync( resolve( root, dir ) )
			.filter( ( file ) => file.endsWith( ext ) && ! file.startsWith( '_' ) )
			.map( ( file ) => [ basename( file, ext ), resolve( root, dir, file ) ] )
	);

export default defineConfig( {
	plugins: [ tailwindcss() ],
	base: './',
	publicDir: false,
	build: {
		outDir: 'dist',
		emptyOutDir: true,
		cssCodeSplit: true,
		cssMinify: true,
		sourcemap: false,
		rollupOptions: {
			input: {
				...entriesIn( 'src/css/pages', '.css' ),
				...entriesIn( 'src/js', '.js' ),
			},
			output: {
				entryFileNames: 'js/[name].js',
				// Chunk dùng chung chứa Swiper (hero + slider .js-carousel) → js/swiper.js, trình duyệt tải 1 lần dùng cho cả hai.
				chunkFileNames: ( chunk ) => ( chunk.moduleIds.some( ( id ) => id.includes( '/node_modules/swiper/' ) ) && ! chunk.facadeModuleId ? 'js/swiper.js' : 'js/[name].js' ),
				assetFileNames: ( asset ) => {
					const name = asset.names?.[ 0 ] ?? '';
					if ( name.endsWith( '.css' ) ) {
						return 'css/[name][extname]';
					}
					if ( /\.(woff2?|ttf|otf)$/.test( name ) ) {
						return 'fonts/[name][extname]';
					}
					return 'images/[name][extname]';
				},
			},
		},
	},
} );
