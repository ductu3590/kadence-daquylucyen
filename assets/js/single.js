/*
 * Hai nút ở đầu bài: Lưu bài và Chia sẻ.
 *
 *   - Lưu: ghi bài vào localStorage của trình duyệt này (không tài khoản, không
 *     gửi gì về máy chủ). Bấm lần nữa để bỏ lưu.
 *   - Chia sẻ: bảng chia sẻ của hệ điều hành nếu có (điện thoại), không thì
 *     chép liên kết vào bộ nhớ tạm.
 *
 * Không có JS thì hai nút không làm gì — nên ẩn chúng đi cho khỏi đánh lừa.
 */
( function () {
	'use strict';

	var cfg = window.dqlBai || {};
	var KHOA = 'dql-bai-da-luu';
	var nutLuu = document.querySelector( '[data-dql-luu]' );
	var nutChiaSe = document.querySelector( '[data-dql-chia-se]' );
	var bao;
	var hen;

	function thongBao( chu ) {
		if ( ! bao ) {
			bao = document.createElement( 'div' );
			bao.className = 'dql-bao';
			bao.setAttribute( 'role', 'status' );
			bao.setAttribute( 'aria-live', 'polite' );
			document.body.appendChild( bao );
		}

		bao.textContent = chu;
		bao.classList.add( 'is-hien' );
		clearTimeout( hen );
		hen = setTimeout( function () {
			bao.classList.remove( 'is-hien' );
		}, 2400 );
	}

	function docDanhSach() {
		try {
			var ds = JSON.parse( localStorage.getItem( KHOA ) || '[]' );
			return Array.isArray( ds ) ? ds : [];
		} catch ( e ) {
			return null;
		}
	}

	function ghiDanhSach( ds ) {
		try {
			localStorage.setItem( KHOA, JSON.stringify( ds ) );
			return true;
		} catch ( e ) {
			return false;
		}
	}

	/* --- Lưu bài ---------------------------------------------------------- */

	if ( nutLuu ) {
		var id = nutLuu.getAttribute( 'data-dql-luu' );

		// Chế độ riêng tư chặn localStorage: không lưu được thì không hiện nút.
		if ( null === docDanhSach() ) {
			nutLuu.hidden = true;
		} else {
			var capNhat = function () {
				var da = docDanhSach().some( function ( b ) {
					return String( b.id ) === id;
				} );

				nutLuu.setAttribute( 'aria-pressed', da ? 'true' : 'false' );
				nutLuu.setAttribute( 'aria-label', da ? cfg.boLuu : cfg.luu );
				nutLuu.title = da ? cfg.boLuu : cfg.luu;
			};

			capNhat();

			nutLuu.addEventListener( 'click', function () {
				var ds = docDanhSach().filter( function ( b ) {
					return String( b.id ) !== id;
				} );
				var daLuu = nutLuu.getAttribute( 'aria-pressed' ) === 'true';

				if ( ! daLuu ) {
					ds.unshift( {
						id: id,
						url: window.location.href.split( '#' )[ 0 ],
						tieuDe: document.title,
						luc: Date.now()
					} );
				}

				if ( ghiDanhSach( ds.slice( 0, 100 ) ) ) {
					capNhat();
					thongBao( daLuu ? cfg.daBoLuu : cfg.daLuu );
				}
			} );
		}
	}

	/* --- Chia sẻ ---------------------------------------------------------- */

	if ( nutChiaSe ) {
		nutChiaSe.title = cfg.chiaSe || '';

		nutChiaSe.addEventListener( 'click', function () {
			var url = window.location.href.split( '#' )[ 0 ];
			var tieuDe = ( document.querySelector( '.dql-bai__tieu-de' ) || {} ).textContent || document.title;

			if ( navigator.share ) {
				navigator.share( { title: tieuDe.trim(), url: url } ).catch( function () {
					// Người đọc đóng bảng chia sẻ: không cần báo gì.
				} );
				return;
			}

			if ( navigator.clipboard && window.isSecureContext ) {
				navigator.clipboard.writeText( url ).then(
					function () {
						thongBao( cfg.daChep );
					},
					function () {
						window.prompt( cfg.chepTay, url );
					}
				);
				return;
			}

			window.prompt( cfg.chepTay, url );
		} );
	}
} )();
