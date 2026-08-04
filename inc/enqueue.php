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
 * Trước đây hàm này chỉ dùng filemtime khi WP_DEBUG bật, còn lại trả về
 * DQL_VERSION. Cách đó hỏng trong thực tế: WP_DEBUG mặc định tắt, nên mỗi lần
 * sửa CSS là trình duyệt vẫn giữ bản cũ cho tới khi xóa bộ nhớ đệm thủ công —
 * đủ để tưởng nhầm rằng thay đổi không có tác dụng.
 *
 * Dùng filemtime ở mọi môi trường là đúng: số chỉ đổi khi file thật sự đổi,
 * nên bộ nhớ đệm của trình duyệt vẫn hoạt động tối đa. Chi phí là một lệnh
 * stat cho mỗi file, và realpath cache của PHP đã lo phần lớn.
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

	/*
	 * Cột bên.
	 *
	 * Trước đây chỉ nạp khi is_active_sidebar( 'sidebar-primary' ) — đúng khi cột
	 * bên chỉ gồm widget. Giờ phần lớn nội dung cột bên do theme dựng (xem
	 * inc/sidebar.php) và vẫn hiện dù không có widget nào, nên điều kiện đó sai.
	 *
	 * Hỏi thẳng Kadence xem trang này có cột bên không, thay vì liệt kê loại
	 * trang. Danh sách liệt kê sẽ sai ngay: trang tìm kiếm và trang từ điển đều
	 * là is_search()/is_archive() nhưng dùng template tự dựng, không có cột bên.
	 *
	 * Trang chủ phải kiểm riêng: cột bên ở đó do theme dựng nên Kadence không
	 * biết tới nó.
	 *
	 * Trang chuyên mục phải loại trừ riêng: category.php tự dựng toàn bộ trang và
	 * không in cột bên nào, nhưng Kadence vẫn trả về true vì tùy chọn bố cục của
	 * nó cho trang lưu trữ vẫn là "có cột bên". Không loại ra thì mỗi lượt xem
	 * chuyên mục tải thừa 913 dòng CSS không dùng tới.
	 */
	$dql_co_cot = function_exists( '\Kadence\kadence' ) && \Kadence\kadence()->has_sidebar();

	if ( is_front_page() || ( $dql_co_cot && ! is_category() ) ) {
		wp_enqueue_style(
			'dql-sidebar',
			DQL_URI . '/assets/css/sidebar.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/sidebar.css' )
		);
	}

	// Kiểu dáng các mẫu khối. Nạp ở mọi trang vì mẫu khối có thể được chèn vào
	// bất kỳ bài hay trang nào — kể cả trang giới thiệu hay trang liên hệ.
	wp_enqueue_style(
		'dql-blocks',
		DQL_URI . '/assets/css/blocks.css',
		array( 'dql-main' ),
		dql_asset_version( 'assets/css/blocks.css' )
	);

	// CSS trang chủ chỉ nạp ở trang chủ. Các trang khác không phải tải phần
	// bố cục mà chúng không dùng tới.
	if ( is_front_page() ) {
		wp_enqueue_style(
			'dql-home',
			DQL_URI . '/assets/css/home.css',
			array( 'dql-main' ),
			dql_asset_version( 'assets/css/home.css' )
		);

		// Nạp ở đầu trang, không phải cuối: script gắn một class lên thẻ html
		// để CSS biết có nên ẩn các khối chờ hiệu ứng hay không. Nạp cuối trang
		// sẽ khiến các khối hiện ra rồi mới biến mất, nhấp nháy một nhịp.
		wp_enqueue_script(
			'dql-reveal',
			DQL_URI . '/assets/js/reveal.js',
			array(),
			dql_asset_version( 'assets/js/reveal.js' ),
			false
		);
	}

	// Trang chuyên mục, trang danh sách bài, trang tìm kiếm.
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

		// Nạp cuối trang. Mục lục đã dùng được ngay khi chưa có JS, script này
		// chỉ bổ sung việc đóng mở và tô sáng mục đang đọc.
		wp_enqueue_script(
			'dql-toc',
			DQL_URI . '/assets/js/toc.js',
			array(),
			dql_asset_version( 'assets/js/toc.js' ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'dql_enqueue_styles', 20 );
