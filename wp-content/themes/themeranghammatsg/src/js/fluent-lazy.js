/**
 * Fluent Forms "chạm mới tải": jQuery + JS của Fluent bị inc/plugin/fluentform.php in dạng <script type="text/plain" data-rhm-lazy>.
 * File này chạy lại chúng ĐÚNG thứ tự (jQuery → Fluent) khi người dùng rê chuột / chạm / focus vào bất kỳ form Fluent nào.
 * Bấm gửi khi chưa tải xong: giữ lại lần gửi, đợi Fluent khởi tạo (bỏ lớp ff-form-loading) rồi gửi tiếp.
 */
const forms = document.querySelectorAll( 'form.frm-fluent-form' );

let loading = null;

/** Tạo lại từng <script> thật thay cho bản text/plain, đợi file trước tải xong rồi mới chạy file sau. */
const load = () =>
	( loading ??= ( async () => {
		for ( const placeholder of document.querySelectorAll( 'script[data-rhm-lazy]' ) ) {
			const script = document.createElement( 'script' );
			script.id = placeholder.id;
			const src = placeholder.dataset.rhmLazySrc;
			if ( src ) {
				const done = new Promise( ( resolve ) => {
					script.onload = resolve;
					script.onerror = resolve;
				} );
				script.src = src;
				placeholder.replaceWith( script );
				await done;
			} else {
				script.textContent = placeholder.textContent;
				placeholder.replaceWith( script );
			}
		}
	} )() );

/** Đợi Fluent khởi tạo xong form (Fluent bỏ lớp ff-form-loading). */
const ready = ( form ) =>
	new Promise( ( resolve ) => {
		if ( ! form.classList.contains( 'ff-form-loading' ) ) {
			resolve();
			return;
		}
		const observer = new MutationObserver( () => {
			if ( ! form.classList.contains( 'ff-form-loading' ) ) {
				observer.disconnect();
				resolve();
			}
		} );
		observer.observe( form, { attributes: true, attributeFilter: [ 'class' ] } );
	} );

forms.forEach( ( form ) => {
	for ( const type of [ 'pointerover', 'focusin', 'touchstart' ] ) {
		form.addEventListener( type, load, { once: true, passive: true } );
	}

	// Gửi trước khi Fluent sẵn sàng → chặn gửi kiểu thường (tải lại trang), gửi lại bằng Fluent khi đã khởi tạo.
	form.addEventListener(
		'submit',
		( event ) => {
			if ( ! form.classList.contains( 'ff-form-loading' ) ) {
				return;
			}
			event.preventDefault();
			event.stopImmediatePropagation();
			load()
				.then( () => ready( form ) )
				.then( () => form.requestSubmit( event.submitter ?? undefined ) );
		},
		{ capture: true }
	);
} );
