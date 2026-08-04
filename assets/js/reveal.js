/*
 * Hiện dần các khối khi cuộn tới.
 *
 * Quan trọng: CSS chỉ ẩn phần tử KHI script này đã chạy. Class 'dql-reveal-ready'
 * được gắn ngay lập tức, trước khi trang vẽ xong. Nếu JavaScript bị chặn hoặc
 * lỗi, class đó không bao giờ xuất hiện và mọi thứ hiện bình thường — không có
 * chuyện cả trang trắng xóa vì hiệu ứng hỏng.
 */
( function () {
	'use strict';

	var root = document.documentElement;

	// Người dùng đã tắt hiệu ứng chuyển động thì không làm gì cả.
	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	if ( ! ( 'IntersectionObserver' in window ) ) {
		return;
	}

	root.classList.add( 'dql-reveal-ready' );

	function batDau() {
		var items = document.querySelectorAll( '[data-reveal]' );

		if ( ! items.length ) {
			root.classList.remove( 'dql-reveal-ready' );
			return;
		}

		var observer = new IntersectionObserver(
			function ( entries ) {
				entries.forEach( function ( entry ) {
					if ( ! entry.isIntersecting ) {
						return;
					}

					entry.target.classList.add( 'is-hien' );
					observer.unobserve( entry.target );
				} );
			},
			{
				// Kích hoạt khi khối vừa nhô lên khỏi mép dưới màn hình một chút,
				// để hiệu ứng chạy xong đúng lúc người đọc nhìn tới.
				rootMargin: '0px 0px -12% 0px',
				threshold: 0,
			}
		);

		items.forEach( function ( el, i ) {
			// Các phần tử cùng một hàng lệch nhau một nhịp nhỏ, tạo cảm giác
			// chúng thuộc về nhau thay vì bật lên cùng lúc như một khối cứng.
			el.style.transitionDelay = ( i % 4 ) * 70 + 'ms';
			observer.observe( el );
		} );
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', batDau );
	} else {
		batDau();
	}
} )();
