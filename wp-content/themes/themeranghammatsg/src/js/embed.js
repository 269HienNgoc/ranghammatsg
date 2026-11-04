/**
 * Nội dung nhúng "bấm mới tải" (facade): nút [data-embed-src] che chỗ; khi bấm mới tạo iframe / <video>.
 * Trước khi bấm không tải gì từ YouTube / Google Maps → không ảnh hưởng tốc độ trang / PageSpeed.
 * data-embed-type: youtube (iframe youtube-nocookie, tự phát) | map (iframe Google Maps) | file (<video> .mp4/.webm).
 * Nạp bởi: template-parts/sections/about.php (video), page-templates/tpl-lien-he.php (bản đồ).
 */
document.querySelectorAll( '[data-embed-src]' ).forEach( ( button ) => {
	button.addEventListener(
		'click',
		() => {
			const { embedType, embedSrc } = button.dataset;
			const player = document.createElement( embedType === 'file' ? 'video' : 'iframe' );

			if ( embedType === 'file' ) {
				player.controls = true;
				player.autoplay = true;
				player.playsInline = true;
			} else {
				player.allow = embedType === 'youtube' ? 'autoplay; encrypted-media; picture-in-picture; fullscreen' : 'fullscreen';
				player.allowFullscreen = true;
				player.referrerPolicy = 'no-referrer-when-downgrade';
				player.title = button.dataset.embedTitle || button.textContent.trim();
			}

			player.src = embedSrc;
			player.className = 'embed-player';
			button.replaceWith( player );
			player.focus();
		},
		{ once: true }
	);
} );
