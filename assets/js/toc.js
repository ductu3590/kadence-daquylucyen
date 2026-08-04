/*
 * Mục lục bài viết: đóng mở, và tô sáng mục đang đọc.
 *
 * Viết bằng JavaScript thuần, không phụ thuộc jQuery.
 * Nguyên tắc: không có JS thì mục lục vẫn mở sẵn và mọi liên kết vẫn nhảy đúng
 * chỗ — script này chỉ làm cho nó dễ chịu hơn, không phải điều kiện để dùng được.
 */
( function () {
	'use strict';

	var toc = document.querySelector( '.dql-toc' );

	if ( ! toc ) {
		return;
	}

	var toggle = toc.querySelector( '.dql-toc__toggle' );
	var nav = toc.querySelector( '.dql-toc__nav' );
	var links = Array.prototype.slice.call( toc.querySelectorAll( '.dql-toc__link' ) );

	if ( ! toggle || ! nav || ! links.length ) {
		return;
	}

	// --- Đóng mở ----------------------------------------------------------
	// Trên màn hình rộng mục lục nằm ở lề và luôn mở. Trên màn hình hẹp nó
	// chiếm chỗ ngay đầu bài, nên mặc định đóng lại.
	//
	// Chế độ hiện tại đọc từ CSS chứ không lặp lại con số ngưỡng ở đây. Nếu
	// ngưỡng trong single.css đổi, file này tự đi theo — không có cách nào để
	// hai bên lệch nhau.
	function isWide() {
		return window.getComputedStyle( toc ).position === 'absolute';
	}

	// Người đọc đã tự bấm thì tôn trọng lựa chọn của họ, không ghi đè khi
	// đổi kích thước cửa sổ.
	var touched = false;

	function applyDefault() {
		if ( touched ) {
			return;
		}

		toggle.setAttribute( 'aria-expanded', isWide() ? 'true' : 'false' );
	}

	applyDefault();

	var resizeTimer;

	window.addEventListener( 'resize', function () {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( applyDefault, 150 );
	} );

	toggle.addEventListener( 'click', function () {
		// Ở chế độ cột lề, mục lục luôn mở và nút chỉ đóng vai trò tiêu đề.
		if ( isWide() ) {
			return;
		}

		touched = true;

		var open = toggle.getAttribute( 'aria-expanded' ) === 'true';
		toggle.setAttribute( 'aria-expanded', open ? 'false' : 'true' );
	} );

	// --- Tô sáng mục đang đọc --------------------------------------------
	var targets = links
		.map( function ( link ) {
			var id = decodeURIComponent( link.getAttribute( 'href' ).slice( 1 ) );
			return document.getElementById( id );
		} )
		.filter( Boolean );

	if ( ! targets.length ) {
		return;
	}

	function setCurrent( id ) {
		links.forEach( function ( link ) {
			var match = decodeURIComponent( link.getAttribute( 'href' ).slice( 1 ) ) === id;
			link.classList.toggle( 'is-current', match );

			if ( match ) {
				link.setAttribute( 'aria-current', 'true' );
			} else {
				link.removeAttribute( 'aria-current' );
			}
		} );
	}

	/*
	 * Mục đang đọc = tiêu đề CUỐI CÙNG đã đi qua vạch 130px tính từ đỉnh màn hình.
	 *
	 * Bản đầu tiên dùng IntersectionObserver với một dải quan sát hẹp gần đỉnh.
	 * Cách đó hỏng và đã đo được: khi không tiêu đề nào nằm trong dải — chuyện
	 * xảy ra liên tục với đoạn văn dài — nó giữ nguyên mục cũ, nên mục sáng lệch
	 * hẳn so với chỗ đang đọc.
	 *
	 * Phép tính trực tiếp dưới đây luôn cho một đáp án đúng ở mọi vị trí cuộn,
	 * không có trạng thái treo. Đổi lại là chạy mỗi khung hình khi cuộn, nhưng
	 * với vài chục tiêu đề thì chi phí không đáng kể.
	 */
	var THRESHOLD = 130;
	var ticking = false;

	function update() {
		ticking = false;

		var currentId = targets[ 0 ].id;

		for ( var i = 0; i < targets.length; i++ ) {
			if ( targets[ i ].getBoundingClientRect().top > THRESHOLD ) {
				break;
			}

			currentId = targets[ i ].id;
		}

		setCurrent( currentId );
	}

	function onScroll() {
		if ( ticking ) {
			return;
		}

		ticking = true;
		window.requestAnimationFrame( update );
	}

	window.addEventListener( 'scroll', onScroll, { passive: true } );
	window.addEventListener( 'resize', onScroll );
	update();
} )();
