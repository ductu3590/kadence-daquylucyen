<?php
/**
 * Tìm kiếm: endpoint cho gợi ý tức thời, và truy vấn dùng chung cho trang kết quả.
 *
 * Dùng REST API thay vì admin-ajax.php: đường dẫn sạch, trả JSON đúng chuẩn, và
 * không phải nạp toàn bộ tầng admin của WordPress cho mỗi lần gõ phím.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Số ký tự tối thiểu mới bắt đầu tìm.
 *
 * Dưới ngưỡng này, gần như mọi bài đều khớp nên kết quả vô nghĩa, mà máy chủ
 * vẫn phải chạy truy vấn cho từng phím người dùng gõ.
 */
define( 'DQL_SEARCH_MIN', 2 );

/**
 * Đăng ký endpoint gợi ý tìm kiếm.
 */
function dql_register_search_route() {
	register_rest_route(
		'dql/v1',
		'/search',
		array(
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => 'dql_search_endpoint',
			'permission_callback' => '__return_true', // Chỉ đọc nội dung đã công khai.
			'args'                => array(
				'q' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'dql_register_search_route' );

/**
 * Trả kết quả gợi ý, đã phân loại sẵn.
 *
 * @param WP_REST_Request $request Yêu cầu.
 * @return WP_REST_Response
 */
function dql_search_endpoint( $request ) {
	$q = trim( (string) $request->get_param( 'q' ) );

	if ( mb_strlen( $q, 'UTF-8' ) < DQL_SEARCH_MIN ) {
		return rest_ensure_response(
			array(
				'tu_dien' => array(),
				'bai'     => array(),
				'tong'    => 0,
			)
		);
	}

	$tu_dien = array();

	// Từ điển đứng trước: khi người đọc gõ một thuật ngữ, thứ họ cần là định
	// nghĩa ngay lập tức, không phải một danh sách bài viết có nhắc tới nó.
	if ( post_type_exists( 'dql_thuat_ngu' ) ) {
		$g = new WP_Query(
			array(
				'post_type'      => 'dql_thuat_ngu',
				'post_status'    => 'publish',
				's'              => $q,
				'posts_per_page' => 3,
				'no_found_rows'  => true,
			)
		);

		foreach ( $g->posts as $p ) {
			$tu_dien[] = array(
				'tieu_de' => get_the_title( $p ),
				'url'     => get_permalink( $p ),
				'nghia'   => wp_trim_words( wp_strip_all_tags( $p->post_content ), 16, '…' ),
			);
		}
	}

	$bai = array();

	$b = new WP_Query(
		array(
			'post_type'      => 'post',
			'post_status'    => 'publish',
			's'              => $q,
			'posts_per_page' => 5,
			// Cần tổng số bài khớp cho nút "Xem cả N kết quả" của màn tìm kiếm.
			'no_found_rows'  => false,
		)
	);

	foreach ( $b->posts as $p ) {
		$cat = dql_post_main_category( $p );
		$img = get_the_post_thumbnail_url( $p, 'thumbnail' );

		$bai[] = array(
			'tieu_de'    => get_the_title( $p ),
			'url'        => get_permalink( $p ),
			'chuyen_muc' => $cat ? $cat->name : '',
			'mau'        => $cat ? dql_category_color_key( $cat ) : 'ruby',
			'anh'        => $img ? $img : '',
			'ngay'       => get_the_modified_date( 'd/m/Y', $p ),
			'phut'       => (int) dql_reading_time( $p ),
			'thoi_gian'  => sprintf(
				/* translators: %d: số phút đọc. */
				_n( '%d phút đọc', '%d phút đọc', dql_reading_time( $p ), 'kadence-daquylucyen' ),
				dql_reading_time( $p )
			),
		);
	}

	return rest_ensure_response(
		array(
			'tu_dien' => $tu_dien,
			'bai'     => $bai,
			'tong'    => count( $tu_dien ) + count( $bai ),
			'so_bai'  => (int) $b->found_posts,
			'xem_het' => add_query_arg( 's', rawurlencode( $q ), home_url( '/' ) ),
		)
	);
}

/**
 * Nạp script và CSS cho tìm kiếm.
 */
function dql_search_assets() {
	// Ô tìm kiếm tức thời chỉ có ở trang chủ; CSS trang kết quả chỉ cần khi tìm.
	$can_js  = is_front_page();
	$can_css = is_front_page() || is_search();

	if ( ! $can_css ) {
		return;
	}

	wp_enqueue_style(
		'dql-search',
		DQL_URI . '/assets/css/search.css',
		array( 'dql-main' ),
		dql_asset_version( 'assets/css/search.css' )
	);

	if ( ! $can_js ) {
		return;
	}

	wp_enqueue_script(
		'dql-search',
		DQL_URI . '/assets/js/search.js',
		array(),
		dql_asset_version( 'assets/js/search.js' ),
		true
	);

	wp_localize_script(
		'dql-search',
		'dqlSearch',
		array(
			'endpoint' => esc_url_raw( rest_url( 'dql/v1/search' ) ),
			'min'      => DQL_SEARCH_MIN,
			'chuoi'    => array(
				'tuDien'    => __( 'Từ điển', 'kadence-daquylucyen' ),
				'baiViet'   => __( 'Bài viết', 'kadence-daquylucyen' ),
				'xemHet'    => __( 'Xem tất cả kết quả', 'kadence-daquylucyen' ),
				'khongThay' => __( 'Không tìm thấy kết quả nào', 'kadence-daquylucyen' ),
				'dangTim'   => __( 'Đang tìm…', 'kadence-daquylucyen' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dql_search_assets', 20 );
