/**
 * Video "bấm mới tải" (facade): nút [data-video-src] che ảnh bìa; khi bấm mới tạo iframe YouTube / <video>.
 * Trước khi bấm không tải gì từ YouTube → không ảnh hưởng tốc độ trang / PageSpeed.
 * Nạp bởi template-parts/sections/about.php khi section có video.
 */
document.querySelectorAll( '[data-video-src]' ).forEach( ( button ) => {
	button.addEventListener(
		'click',
		() => {
			const { videoType, videoSrc } = button.dataset;
			const player = document.createElement( videoType === 'youtube' ? 'iframe' : 'video' );

			if ( videoType === 'youtube' ) {
				player.allow = 'autoplay; encrypted-media; picture-in-picture; fullscreen';
				player.allowFullscreen = true;
				player.title = button.textContent.trim();
			} else {
				player.controls = true;
				player.autoplay = true;
				player.playsInline = true;
			}

			player.src = videoSrc;
			player.className = 'video-player';
			button.replaceWith( player );
			player.focus();
		},
		{ once: true }
	);
} );
