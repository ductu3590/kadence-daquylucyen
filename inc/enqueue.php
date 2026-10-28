<?php
/**
 * Nạp CSS và JS của child theme.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Lấy số phiên bản cho một file tài nguyên: thời điểm file được sửa lần cuối.
 *
 * Dùng filemtime ở mọi môi trường: số chỉ đổi khi file thật sự đổi, nên bộ nhớ
 * đệm của trình duyệt vẫn hoạt động tối đa, mà sửa CSS là thấy ngay — không
 * còn cảnh sửa xong tưởng không có tác dụng vì trình duyệt giữ bản cũ.
 *
 * @param string $relative_path Đường dẫn tính từ thư mục theme, ví dụ 'assets/css/main.css'.
 * @return string
 */
function dql_asset_version( $relative_path ) {
	$file = DQL_DIR . '/' . ltrim( $relative_path, '/' );

	if ( file_exists( $file ) ) {
		return (string) filemtime( $file );
	}

	return DQL_VERSION;
}

/**
 * Nạp CSS ngoài trang.
 *
 * Phụ thuộc vào 'kadence-global' để CSS của ta luôn nằm sau CSS của theme cha
 * và sau cả CSS động mà Kadence sinh ra từ Customizer. Nhờ vậy ta ghi đè được
 * mà không cần dùng !important.
 */
function dql_enqueue_styles() {
	wp_enqueue_style(
		'dql-main',
		DQL_URI . '/assets/css/main.css',
		array( 'kadence-global' ),
		dql_asset_version( 'assets/css/main.css' )
	);

	// Chế độ tối. Nạp ở mọi trang khi tính năng bật: file nhỏ, và chế độ có thể
	// đổi giữa chừng bằng nút bấm — không thể nạp theo điều kiện phía máy chủ.
	if ( dql_dark_mode_on() ) {
		wp_enqueue_style(
			'dql-dark',
			DQL_URI . '/assets/css/dark.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/dark.css' )
		);
	}

	/*
	 * Cột bên.
	 *
	 * Hỏi thẳng Kadence xem trang này có cột bên không, thay vì liệt kê loại
	 * trang. Trang chủ phải kiểm riêng (cột bên do theme dựng). Trang chuyên mục
	 * và trang danh sách bài phải loại trừ riêng: category.php và home.php tự
	 * dựng toàn bộ trang và không in cột bên nào.
	 */
	$dql_co_cot = function_exists( '\Kadence\kadence' ) && \Kadence\kadence()->has_sidebar();

	if ( is_front_page() || ( $dql_co_cot && ! is_category() && ! is_home() ) ) {
		wp_enqueue_style(
			'dql-sidebar',
			DQL_URI . '/assets/css/sidebar.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/sidebar.css' )
		);
	}

	// Kiểu dáng các mẫu khối. Nạp ở mọi trang vì mẫu khối có thể được chèn vào
	// bất kỳ bài hay trang nào.
	wp_enqueue_style(
		'dql-blocks',
		DQL_URI . '/assets/css/blocks.css',
		array( 'dql-main' ),
		dql_asset_version( 'assets/css/blocks.css' )
	);

	// CSS trang chủ chỉ nạp ở trang chủ.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'dql-home',
			DQL_URI . '/assets/css/home.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/home.css' )
		);

		// Nạp ở đầu trang, không phải cuối: script gắn một class lên thẻ html
		// để CSS biết có nên ẩn các khối chờ hiệu ứng hay không.
		wp_enqueue_script(
			'dql-reveal',
			DQL_URI . '/assets/js/reveal.js',
			array(),
			dql_asset_version( 'assets/js/reveal.js' ),
			false
		);
	}

	// Trang chuyên mục, trang danh sách bài, trang tìm kiếm, trang thẻ.
	if ( is_home() || is_archive() || is_search() ) {
		wp_enqueue_style(
			'dql-archive',
			DQL_URI . '/assets/css/archive.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/archive.css' )
		);
	}

	if ( is_singular( 'post' ) ) {
		wp_enqueue_style(
			'dql-single',
			DQL_URI . '/assets/css/single.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/single.css' )
		);

		// Nạp cuối trang. Mục lục đã dùng được ngay khi chưa có JS.
		wp_enqueue_script(
			'dql-toc',
			DQL_URI . '/assets/js/toc.js',
			array(),
			dql_asset_version( 'assets/js/toc.js' ),
			true
		);
	}

	// Giao diện chung: nút đổi chế độ, tiến độ đọc, header đã cuộn. Nạp cuối
	// trang — không có nó thì mọi thứ vẫn dùng được.
	wp_enqueue_script(
		'dql-ui',
		DQL_URI . '/assets/js/ui.js',
		array(),
		dql_asset_version( 'assets/js/ui.js' ),
		true
	);
	wp_localize_script( 'dql-ui', 'dqlUi', dql_ui_strings() );
}
add_action( 'wp_enqueue_scripts', 'dql_enqueue_styles', 20 );
