/*
 * Gợi ý tìm kiếm tức thời cho ô tìm kiếm trang chủ.
 *
 * Nguyên tắc: ô tìm kiếm vốn đã là một form gửi được. Script này chỉ thêm phần
 * gợi ý. Tắt JavaScript thì bấm "Tìm" vẫn ra trang kết quả như thường.
 */
( function () {
	'use strict';

	if ( typeof window.dqlSearch === 'undefined' ) {
		return;
	}

	var form = document.querySelector( '.dql-search' );

	if ( ! form ) {
		return;
	}

	var input = form.querySelector( '.dql-search__field' );
	var box = document.getElementById( 'dql-search-goi-y' );

	if ( ! input || ! box ) {
		return;
	}

	var cfg = window.dqlSearch;
	var timer = null;
	var controller = null;
	var items = [];
	var chon = -1;

	function esc( s ) {
		var d = document.createElement( 'div' );
		d.textContent = s == null ? '' : String( s );
		return d.innerHTML;
	}

	function dong() {
		box.hidden = true;
		box.innerHTML = '';
		input.setAttribute( 'aria-expanded', 'false' );
		input.removeAttribute( 'aria-activedescendant' );
		items = [];
		chon = -1;
	}

	function mo() {
		box.hidden = false;
		input.setAttribute( 'aria-expanded', 'true' );
	}

	function danhDau( i ) {
		items.forEach( function ( el, n ) {
			el.classList.toggle( 'is-chon', n === i );
		} );

		if ( i >= 0 && items[ i ] ) {
			input.setAttribute( 'aria-activedescendant', items[ i ].id );
			items[ i ].scrollIntoView( { block: 'nearest' } );
		} else {
			input.removeAttribute( 'aria-activedescendant' );
		}

		chon = i;
	}

	function ve( data ) {
		var html = '';
		var n = 0;

		if ( data.tu_dien && data.tu_dien.length ) {
			html += '<p class="dql-gy__nhan">' + esc( cfg.chuoi.tuDien ) + '</p>';
			html += '<ul class="dql-gy__list">';

			data.tu_dien.forEach( function ( t ) {
				html +=
					'<li role="option" id="dql-gy-' + n + '" class="dql-gy__item dql-gy__item--tu">' +
					'<a href="' + esc( t.url ) + '">' +
					'<span class="dql-gy__tu">' + esc( t.tieu_de ) + '</span>' +
					'<span class="dql-gy__nghia">' + esc( t.nghia ) + '</span>' +
					'</a></li>';
				n++;
			} );

			html += '</ul>';
		}

		if ( data.bai && data.bai.length ) {
			html += '<p class="dql-gy__nhan">' + esc( cfg.chuoi.baiViet ) + '</p>';
			html += '<ul class="dql-gy__list">';

			data.bai.forEach( function ( b ) {
				html +=
					'<li role="option" id="dql-gy-' + n + '" class="dql-gy__item">' +
					'<a href="' + esc( b.url ) + '">' +
					( b.anh ? '<img src="' + esc( b.anh ) + '" alt="" loading="lazy" />' : '<span class="dql-gy__khonganh"></span>' ) +
					'<span class="dql-gy__chu">' +
					'<span class="dql-gy__tieude">' + esc( b.tieu_de ) + '</span>' +
					'<span class="dql-gy__meta">' +
					( b.chuyen_muc ? esc( b.chuyen_muc ) + ' · ' : '' ) + esc( b.thoi_gian ) +
					'</span></span></a></li>';
				n++;
			} );

			html += '</ul>';
		}

		if ( ! n ) {
			html = '<p class="dql-gy__trong">' + esc( cfg.chuoi.khongThay ) + '</p>';
		} else if ( data.xem_het ) {
			html += '<a class="dql-gy__xemhet" href="' + esc( data.xem_het ) + '">' + esc( cfg.chuoi.xemHet ) + '</a>';
		}

		box.innerHTML = html;
		items = Array.prototype.slice.call( box.querySelectorAll( '.dql-gy__item' ) );
		chon = -1;
		mo();
	}

	function tim( q ) {
		// Hủy yêu cầu cũ. Không hủy thì kết quả của lần gõ trước có thể về sau
		// lần gõ sau và ghi đè lên, cho ra gợi ý không khớp với chữ đang có.
		if ( controller ) {
			controller.abort();
		}

		controller = new AbortController();

		fetch( cfg.endpoint + '?q=' + encodeURIComponent( q ), {
			signal: controller.signal,
			headers: { Accept: 'application/json' },
		} )
			.then( function ( r ) {
				return r.ok ? r.json() : null;
			} )
			.then( function ( data ) {
				if ( data ) {
					ve( data );
				}
			} )
			.catch( function () {
				// Bị hủy hoặc mạng lỗi: im lặng. Người dùng vẫn gửi form được.
			} );
	}

	input.setAttribute( 'role', 'combobox' );
	input.setAttribute( 'aria-autocomplete', 'list' );
	input.setAttribute( 'aria-expanded', 'false' );
	input.setAttribute( 'aria-controls', 'dql-search-goi-y' );
	input.setAttribute( 'autocomplete', 'off' );

	input.addEventListener( 'input', function () {
		var q = input.value.trim();

		clearTimeout( timer );

		if ( q.length < cfg.min ) {
			dong();
			return;
		}

		// Chờ người dùng ngừng gõ. Không chờ thì mỗi phím là một truy vấn.
		timer = setTimeout( function () {
			tim( q );
		}, 250 );
	} );

	input.addEventListener( 'keydown', function ( e ) {
		if ( box.hidden || ! items.length ) {
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
			items[ chon ].querySelector( 'a' ).click();
		} else if ( 'Escape' === e.key ) {
			dong();
		}
	} );

	document.addEventListener( 'click', function ( e ) {
		if ( ! form.contains( e.target ) ) {
			dong();
		}
	} );

	input.addEventListener( 'focus', function () {
		if ( input.value.trim().length >= cfg.min && box.innerHTML ) {
			mo();
		}
	} );
} )();
