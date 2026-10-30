/*
 * Màn tìm kiếm — mở từ nút kính lúp trên header (inc/tim.php dựng khung).
 *
 *   1. Chặn cú bấm vào nút kính lúp của Kadence ở pha capture, trước khi tới
 *      script của Kadence, rồi mở bảng của theme thay cho ngăn kéo cũ.
 *   2. Gõ từ 2 ký tự: hỏi endpoint dql/v1/search, vẽ thuật ngữ và bài khớp,
 *      tô màu từ khóa (không phân biệt dấu: gõ "da quy" vẫn tô "Đá quý").
 *   3. Bàn phím: ↑↓ chọn, Enter mở, Esc đóng, Tab không thoát ra khỏi bảng.
 *      Đóng xong trả tiêu điểm về đúng nút đã mở.
 *
 * Form vẫn là form thật: bấm Tìm hay Enter khi chưa chọn gợi ý nào là sang
 * trang kết quả /?s= như cũ.
 */
( function () {
	'use strict';

	var hop = document.getElementById( 'dql-tim' );
	var cfg = window.dqlTim;

	if ( ! hop || ! cfg ) {
		return;
	}

	var root = document.documentElement;
	var form = hop.querySelector( '.dql-tim__form' );
	var input = hop.querySelector( '.dql-tim__nhap' );
	var xoa = hop.querySelector( '.dql-tim__xoa' );
	var goi = hop.querySelector( '[data-dql-tim-goi]' );
	var kq = hop.querySelector( '.dql-tim__kq' );
	var chuoi = cfg.chuoi || {};
	var icon = cfg.icon || {};

	var nutMo = null;
	var timer = null;
	var controller = null;
	var hoiCuoi = '';
	var items = [];
	var chon = -1;
	var hanDong = null;

	/* --- Tiện ích --------------------------------------------------------- */

	function esc( s ) {
		var d = document.createElement( 'div' );
		d.textContent = s == null ? '' : String( s );
		return d.innerHTML;
	}

	// Tiêu đề từ WordPress đã mã hóa thực thể (&#8211;…): giải về chữ thường
	// trước, rồi mới thoát lại khi đưa vào HTML.
	function giai( s ) {
		var t = document.createElement( 'textarea' );
		t.innerHTML = s == null ? '' : String( s );
		return t.value;
	}

	function thay( mau, a, b ) {
		return String( mau ).replace( '%1$d', a ).replace( '%d', a ).replace( '%2$s', b ).replace( '%s', b );
	}

	// Bỏ dấu tiếng Việt từng ký tự. Mỗi ký tự dựng sẵn (ế, ự…) tách ra thành
	// chữ gốc + dấu, bỏ dấu còn đúng một ký tự, nên vị trí trong chuỗi đã bỏ
	// dấu khớp 1–1 với chuỗi gốc.
	function boDau( c ) {
		return c.normalize( 'NFD' ).replace( /[̀-ͯ]/g, '' ).replace( /đ/g, 'd' ).replace( /Đ/g, 'D' ).toLowerCase();
	}

	function toMau( text, q ) {
		var chu = Array.from( text );
		var phang = chu.map( boDau );

		if ( phang.some( function ( c ) { return c.length !== 1; } ) ) {
			return esc( text );
		}

		var nguon = phang.join( '' );
		var tu = boDau( q ).replace( /\s+/g, ' ' ).trim();

		if ( ! tu ) {
			return esc( text );
		}

		var html = '';
		var tu_i = 0;
		var i = nguon.indexOf( tu );

		while ( i !== -1 ) {
			html += esc( chu.slice( tu_i, i ).join( '' ) ) + '<mark>' + esc( chu.slice( i, i + tu.length ).join( '' ) ) + '</mark>';
			tu_i = i + tu.length;
			i = nguon.indexOf( tu, tu_i );
		}

		return html + esc( chu.slice( tu_i ).join( '' ) );
	}

	/* --- Mở, đóng --------------------------------------------------------- */

	function datNut( mo ) {
		document.querySelectorAll( '.search-toggle-open, [data-dql-tim-mo]' ).forEach( function ( nut ) {
			nut.setAttribute( 'aria-controls', 'dql-tim' );
			nut.setAttribute( 'aria-expanded', mo ? 'true' : 'false' );
		} );
	}

	function mo( nguon ) {
		if ( ! hop.hidden && ! hanDong ) {
			return;
		}

		clearTimeout( hanDong );
		hanDong = null;
		nutMo = nguon || document.activeElement;

		hop.hidden = false;
		root.classList.add( 'dql-tim-mo' );
		datNut( true );

		// Gọi focus ngay trong cú bấm: Safari iOS chỉ bật bàn phím khi tiêu
		// điểm được đặt đồng bộ trong thao tác của người dùng.
		input.focus( { preventScroll: true } );

		window.requestAnimationFrame( function () {
			hop.classList.add( 'is-mo' );
		} );
	}

	function dong() {
		if ( hop.hidden ) {
			return;
		}

		hop.classList.remove( 'is-mo' );
		datNut( false );

		hanDong = setTimeout( function () {
			hop.hidden = true;
			hanDong = null;
			root.classList.remove( 'dql-tim-mo' );
		}, window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ? 0 : 200 );

		if ( nutMo && document.contains( nutMo ) ) {
			nutMo.focus( { preventScroll: true } );
		}
	}

	/* --- Gợi ý ------------------------------------------------------------ */

	function hienGoi() {
		goi.hidden = false;
		kq.hidden = true;
		kq.innerHTML = '';
		items = [];
		chon = -1;
		input.setAttribute( 'aria-expanded', 'false' );
		input.removeAttribute( 'aria-activedescendant' );
	}

	function danhDau( i ) {
		items.forEach( function ( el, n ) {
			el.classList.toggle( 'is-chon', n === i );
			el.setAttribute( 'aria-selected', n === i ? 'true' : 'false' );
		} );

		chon = i;

		if ( i >= 0 && items[ i ] ) {
			input.setAttribute( 'aria-activedescendant', items[ i ].id );
			items[ i ].scrollIntoView( { block: 'nearest' } );
		} else {
			input.removeAttribute( 'aria-activedescendant' );
		}
	}

	function dongBai( b, q, n ) {
		var anh = b.anh
			? '<span class="dql-tim__anh" aria-hidden="true"><img src="' + esc( b.anh ) + '" alt="" loading="lazy" /></span>'
			: '<span class="dql-tim__anh dql-tim__anh--trong" aria-hidden="true">' + ( icon.da || '' ) + '</span>';

		return '<a class="dql-tim__bai" id="dql-tim-g' + n + '" role="option" href="' + esc( b.url ) + '" style="--dql-gem: var(--dql-da-' + esc( b.mau || 'ruby' ) + ')">' +
			anh +
			'<span class="dql-tim__chu">' +
			( b.chuyen_muc ? '<span class="dql-tim__muc">' + esc( giai( b.chuyen_muc ) ) + '</span>' : '' ) +
			'<span class="dql-tim__tieu-de">' + toMau( giai( b.tieu_de ), q ) + '</span>' +
			'<span class="dql-tim__meta">' + esc( b.ngay ) + ( b.phut ? ' · ' + esc( thay( chuoi.phutDoc, b.phut, '' ) ) : '' ) + '</span>' +
			'</span></a>';
	}

	function ve( data, q ) {
		var html = '';
		var n = 0;
		// Thuật ngữ có chữ đang gõ ngay trong tên thì hiện (tối đa hai). Không
		// có thì chỉ một — thuật ngữ chỉ nhắc tới từ khóa trong phần giải nghĩa
		// ít khi là thứ người đọc tìm, không đáng chiếm chỗ của bài viết.
		var tuKhoa = boDau( q ).replace( /\s+/g, ' ' ).trim();
		var tatCa = data.tu_dien || [];
		var trungTen = tatCa.filter( function ( t ) {
			return Array.from( giai( t.tieu_de ) ).map( boDau ).join( '' ).indexOf( tuKhoa ) !== -1;
		} );
		var tuDien = trungTen.length ? trungTen.slice( 0, 2 ) : tatCa.slice( 0, 1 );
		var bai = data.bai || [];

		if ( tuDien.length ) {
			html += '<section class="dql-tim__khoi"><h2 class="dql-tim__nhan">' + esc( chuoi.thuatNgu ) + '</h2>';

			tuDien.forEach( function ( t ) {
				html += '<a class="dql-tim__tu" id="dql-tim-g' + n + '" role="option" href="' + esc( t.url ) + '">' +
					'<span class="dql-tim__tu-da" aria-hidden="true">' + ( icon.sach || '' ) + '</span>' +
					'<span class="dql-tim__tu-chu">' +
					'<span class="dql-tim__tu-ten">' + toMau( giai( t.tieu_de ), q ) + '</span>' +
					'<span class="dql-tim__tu-nghia">' + esc( giai( t.nghia ) ) + '</span>' +
					'</span>' +
					'<span class="dql-tim__tu-di">' + esc( chuoi.tuDien ) + ( icon.muiTen || '' ) + '</span>' +
					'</a>';
				n++;
			} );

			html += '</section>';
		}

		if ( bai.length ) {
			html += '<section class="dql-tim__khoi"><h2 class="dql-tim__nhan">' + esc( chuoi.baiViet ) +
				'<small>' + esc( thay( chuoi.soKq, data.so_bai || bai.length, '' ) ) + '</small></h2>';

			bai.forEach( function ( b ) {
				html += dongBai( b, q, n );
				n++;
			} );

			html += '</section>';
		}

		if ( ! n ) {
			html = '<p class="dql-tim__trong">' + esc( thay( chuoi.khongThay, 0, q ) ) + '</p>';
		} else if ( bai.length && data.xem_het ) {
			html += '<a class="dql-tim__het" id="dql-tim-g' + n + '" role="option" href="' + esc( data.xem_het ) + '">' +
				esc( thay( chuoi.xemHet, data.so_bai || bai.length, q ) ) + ( icon.muiTen || '' ) + '</a>';
		}

		kq.innerHTML = html;
		kq.hidden = false;
		goi.hidden = true;
		items = Array.prototype.slice.call( kq.querySelectorAll( '[role="option"]' ) );
		chon = -1;
		input.setAttribute( 'aria-expanded', items.length ? 'true' : 'false' );
		input.removeAttribute( 'aria-activedescendant' );
	}

	function tim( q ) {
		// Hủy yêu cầu cũ, không thì kết quả lần gõ trước về muộn sẽ đè lên.
		if ( controller ) {
			controller.abort();
		}

		controller = new AbortController();
		hoiCuoi = q;
		kq.setAttribute( 'aria-busy', 'true' );

		fetch( cfg.endpoint + '?q=' + encodeURIComponent( q ), {
			signal: controller.signal,
			headers: { Accept: 'application/json' },
		} )
			.then( function ( r ) {
				return r.ok ? r.json() : null;
			} )
			.then( function ( data ) {
				kq.removeAttribute( 'aria-busy' );

				// Người đọc đã xóa chữ trong lúc chờ: giữ trạng thái vừa mở.
				if ( data && q === hoiCuoi && input.value.trim().length >= cfg.min ) {
					ve( data, q );
				}
			} )
			.catch( function () {
				// Bị hủy hoặc mạng lỗi: im lặng. Bấm Tìm vẫn sang trang kết quả.
				kq.removeAttribute( 'aria-busy' );
			} );
	}

	function khiGo() {
		var q = input.value.trim();

		xoa.hidden = ! input.value;
		clearTimeout( timer );

		if ( q.length < cfg.min ) {
			hoiCuoi = '';

			if ( controller ) {
				controller.abort();
			}

			hienGoi();
			return;
		}

		// Chờ người đọc ngừng gõ, không thì mỗi phím là một truy vấn.
		timer = setTimeout( function () {
			tim( q );
		}, 220 );
	}

	/* --- Sự kiện ---------------------------------------------------------- */

	// Pha capture: chạy trước trình xử lý Kadence gắn thẳng lên nút.
	document.addEventListener(
		'click',
		function ( e ) {
			var nut = e.target.closest( '.search-toggle-open, [data-dql-tim-mo]' );

			if ( nut ) {
				e.preventDefault();
				e.stopImmediatePropagation();
				mo( nut );
			}
		},
		true
	);

	hop.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-dql-tim-dong]' ) ) {
			e.preventDefault();
			dong();
			return;
		}

		var cum = e.target.closest( '[data-dql-tim-cum]' );

		// Chip "hay tìm": điền vào ô và hiện gợi ý ngay, thay vì rời trang.
		if ( cum ) {
			e.preventDefault();
			input.value = cum.getAttribute( 'data-dql-tim-cum' );
			input.focus();
			clearTimeout( timer );
			xoa.hidden = false;
			tim( input.value.trim() );
		}
	} );

	xoa.addEventListener( 'click', function () {
		input.value = '';
		khiGo();
		input.focus();
	} );

	input.addEventListener( 'input', khiGo );

	input.addEventListener( 'keydown', function ( e ) {
		if ( ! items.length || kq.hidden ) {
			return;
		}

		if ( 'ArrowDown' === e.key ) {
			e.preventDefault();
			danhDau( ( chon + 1 ) % items.length );
		} else if ( 'ArrowUp' === e.key ) {
			e.preventDefault();
			danhDau( chon <= 0 ? items.length - 1 : chon - 1 );
		} else if ( 'Enter' === e.key && chon >= 0 ) {
			e.preventDefault();
			items[ chon ].click();
		}
	} );

	form.addEventListener( 'submit', function ( e ) {
		if ( ! input.value.trim() ) {
			e.preventDefault();
			input.focus();
		}
	} );

	hop.addEventListener( 'keydown', function ( e ) {
		if ( 'Escape' === e.key ) {
			e.preventDefault();
			dong();
			return;
		}

		if ( 'Tab' !== e.key ) {
			return;
		}

		// Giữ Tab trong bảng: hộp thoại modal không để tiêu điểm lọt ra trang
		// phía sau, nơi người đọc không nhìn thấy.
		var duoc = Array.prototype.filter.call(
			hop.querySelectorAll( 'a[href], button:not([disabled]), input' ),
			function ( el ) {
				return el.offsetParent !== null;
			}
		);

		if ( ! duoc.length ) {
			return;
		}

		var dau = duoc[ 0 ];
		var cuoi = duoc[ duoc.length - 1 ];

		if ( e.shiftKey && document.activeElement === dau ) {
			e.preventDefault();
			cuoi.focus();
		} else if ( ! e.shiftKey && document.activeElement === cuoi ) {
			e.preventDefault();
			dau.focus();
		}
	} );

	// Điện thoại: chữ gợi ý bản dài bị cắt giữa chừng, đổi sang bản ngắn.
	if ( input.dataset.ngan && window.matchMedia( '(max-width: 767px)' ).matches ) {
		input.placeholder = input.dataset.ngan;
	}

	datNut( false );
} )();
