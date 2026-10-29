/*
 * Giao diện Đá Thô — ba việc nhỏ, không phụ thuộc jQuery.
 *
 *   1. Nút đổi chế độ sáng/tối: lưu lựa chọn, đổi thuộc tính trên <html>.
 *      Nếu header chưa có nút (chủ site chưa đặt shortcode [dql_doi_nen] vào
 *      header builder), tự chèn một nút vào phần bên phải của header Kadence.
 *   2. Dải tiến độ đọc ở trang bài viết.
 *   3. Header dính: gắn class khi đã cuộn qua, để CSS đổi sang vật liệu mờ.
 *
 * Nguyên tắc: không có script này thì mọi thứ vẫn dùng được — chế độ tối chỉ
 * theo hệ điều hành, không đổi tay được; tiến độ đọc không hiện.
 */
( function () {
	'use strict';

	var root = document.documentElement;
	var cfg = window.dqlUi || {};

	/* --- 1. Đổi chế độ sáng/tối ------------------------------------------- */

	function capNhatNut() {
		var toi = root.getAttribute( 'data-dql-theme' ) === 'dark';

		document.querySelectorAll( '[data-dql-doi-nen]' ).forEach( function ( nut ) {
			nut.setAttribute( 'aria-pressed', toi ? 'true' : 'false' );

			// Nhãn nói việc nút sẽ làm, không phải trạng thái hiện tại.
			if ( cfg.batSang && cfg.batToi ) {
				nut.setAttribute( 'aria-label', toi ? cfg.batSang : cfg.batToi );
			}
		} );
	}

	function doiNen() {
		var toi = root.getAttribute( 'data-dql-theme' ) === 'dark';
		var moi = toi ? 'light' : 'dark';

		root.setAttribute( 'data-dql-theme', moi );
		root.removeAttribute( 'data-dql-theme-auto' );

		try {
			localStorage.setItem( 'dql-theme', moi );
		} catch ( e ) {
			// Chế độ riêng tư chặn localStorage: lựa chọn chỉ sống trong phiên này.
		}

		capNhatNut();
	}

	function chenNutVaoHeader() {
		if ( ! cfg.coCheDoToi || ! cfg.nutDoiNen ) {
			return;
		}

		// Header điện thoại luôn có nút riêng (in từ PHP), nên chỉ xét header
		// desktop.
		if ( ( document.getElementById( 'main-header' ) || document ).querySelector( '[data-dql-doi-nen]' ) ) {
			return;
		}

		// Kadence: phần bên phải của hàng header chính, bản desktop và mobile.
		var cho = document.querySelector( '.site-main-header-wrap .site-header-main-section-right' )
			|| document.querySelector( '.site-main-header-wrap .site-header-section-right' )
			|| document.querySelector( '.site-header-row-container-inner' );

		var boc = document.createElement( 'div' );
		boc.className = 'dql-doi-nen-wrap';
		boc.innerHTML = cfg.nutDoiNen;

		if ( cho ) {
			cho.appendChild( boc );
		} else {
			boc.className += ' dql-doi-nen-wrap--noi';
			document.body.appendChild( boc );
		}
	}

	function khoiDongDoiNen() {
		if ( ! cfg.coCheDoToi ) {
			return;
		}

		chenNutVaoHeader();
		capNhatNut();

		document.addEventListener( 'click', function ( e ) {
			var nut = e.target.closest( '[data-dql-doi-nen]' );

			if ( nut ) {
				e.preventDefault();
				doiNen();
			}
		} );

		// Người dùng chưa chọn tay thì đi theo hệ điều hành cả khi hệ điều hành
		// đổi giữa chừng (ví dụ máy tự chuyển tối lúc hoàng hôn).
		if ( window.matchMedia ) {
			window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', function ( ev ) {
				if ( root.hasAttribute( 'data-dql-theme-auto' ) ) {
					root.setAttribute( 'data-dql-theme', ev.matches ? 'dark' : 'light' );
					capNhatNut();
				}
			} );
		}
	}

	/* --- 2. Tiến độ đọc ---------------------------------------------------- */

	function khoiDongTienDo() {
		var bar = document.querySelector( '[data-dql-progress]' );
		var bai = document.querySelector( '.single-content, .entry-content' );

		if ( ! bar || ! bai ) {
			return;
		}

		var ticking = false;

		function capNhat() {
			ticking = false;

			var rect = bai.getBoundingClientRect();
			var dau = rect.top + window.scrollY;
			var dai = rect.height - window.innerHeight * 0.6;
			var da = window.scrollY - dau;
			var pt = dai > 0 ? Math.min( 1, Math.max( 0, da / dai ) ) : 1;

			bar.style.transform = 'scaleX(' + pt.toFixed( 4 ) + ')';
		}

		function onScroll() {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( capNhat );
			}
		}

		window.addEventListener( 'scroll', onScroll, { passive: true } );
		window.addEventListener( 'resize', onScroll );
		capNhat();
	}

	/* --- 3. Header đã cuộn ------------------------------------------------- */

	function khoiDongHeader() {
		var ticking = false;

		function capNhat() {
			ticking = false;
			root.classList.toggle( 'dql-da-cuon', window.scrollY > 8 );
		}

		window.addEventListener( 'scroll', function () {
			if ( ! ticking ) {
				ticking = true;
				window.requestAnimationFrame( capNhat );
			}
		}, { passive: true } );

		capNhat();
	}

	function batDau() {
		khoiDongDoiNen();
		khoiDongTienDo();
		khoiDongHeader();
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', batDau );
	} else {
		batDau();
	}
} )();
