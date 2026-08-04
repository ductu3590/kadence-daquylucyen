<?php
/**
 * Các hàm nhỏ dùng trong template và trong nội dung bài.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ước lượng thời gian đọc một bài viết, tính bằng phút.
 *
 * Vì sao 200 từ mỗi phút: đây là tốc độ đọc hiểu trung bình với văn bản
 * thông tin, không phải tốc độ đọc lướt. Blog kiến thức đá quý có nhiều
 * thuật ngữ và số liệu nên người đọc chậm hơn văn kể chuyện.
 *
 * Hàm đếm cả chữ trong ảnh chú thích và bảng, nên với bài nhiều ảnh con số
 * sẽ hơi cao. Chấp nhận được — sai số này an toàn hơn là báo thấp rồi người
 * đọc thấy bài dài hơn dự kiến.
 *
 * @param int|WP_Post|null $post Bài viết. Bỏ trống thì lấy bài hiện tại.
 * @return int Số phút, tối thiểu 1.
 */
function dql_reading_time( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return 1;
	}

	$text  = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );
	$words = preg_match_all( '/\p{L}[\p{L}\p{M}]*/u', $text );

	if ( ! $words ) {
		return 1;
	}

	return max( 1, (int) ceil( $words / 200 ) );
}

/**
 * In ra dòng "X phút đọc".
 *
 * @param int|WP_Post|null $post Bài viết.
 */
function dql_the_reading_time( $post = null ) {
	$minutes = dql_reading_time( $post );

	printf(
		'<span class="dql-reading-time">%s</span>',
		esc_html(
			sprintf(
				/* translators: %d: số phút đọc ước tính. */
				_n( '%d phút đọc', '%d phút đọc', $minutes, 'kadence-daquylucyen' ),
				$minutes
			)
		)
	);
}

/**
 * In ra dòng "Cập nhật T8/2026".
 *
 * Vì sao là ngày SỬA chứ không phải ngày ĐĂNG: phần lớn nội dung của site được
 * chuyển về từ bản cũ với ngày đăng 2019–2020, trong khi bài đã được soát lại và
 * cập nhật. In ngày đăng gốc làm cả chuyên mục trông như bỏ hoang sáu năm, và đó
 * là một thông tin sai về thứ người đọc sắp đọc.
 *
 * Chỉ tới tháng, không tới ngày. Với bài kiến thức, "tháng 8/2026" đã trả lời
 * đúng câu hỏi người đọc đang hỏi — nội dung này còn mới không — còn ngày cụ thể
 * chỉ thêm nhiễu.
 *
 * @param int|WP_Post|null $post Bài viết.
 */
function dql_the_updated( $post = null ) {
	$post = get_post( $post );

	if ( ! $post ) {
		return;
	}

	printf(
		'<time class="dql-updated" datetime="%1$s">%2$s</time>',
		esc_attr( (string) get_post_modified_time( 'c', false, $post ) ),
		esc_html(
			sprintf(
				/* translators: 1: số tháng, 2: năm. */
				__( 'Cập nhật T%1$s/%2$s', 'kadence-daquylucyen' ),
				(string) get_post_modified_time( 'n', false, $post ),
				(string) get_post_modified_time( 'Y', false, $post )
			)
		)
	);
}

/**
 * Lấy ba chuyên mục làm trụ nội dung cho trang chủ.
 *
 * Ưu tiên lựa chọn trong Customizer. Nếu chủ site chưa chọn gì, lấy ba chuyên mục
 * nhiều bài nhất — để trang chủ không bao giờ hiện ra trống rỗng ngay sau khi cài.
 *
 * @return WP_Term[] Tối đa ba chuyên mục.
 */
function dql_home_pillars() {
	$pillars = array();

	for ( $i = 1; $i <= 3; $i++ ) {
		$term_id = absint( get_theme_mod( 'dql_pillar_' . $i, 0 ) );

		if ( ! $term_id ) {
			continue;
		}

		$term = get_term( $term_id, 'category' );

		if ( $term && ! is_wp_error( $term ) ) {
			$pillars[] = $term;
		}
	}

	if ( $pillars ) {
		return $pillars;
	}

	$fallback = get_categories(
		array(
			'number'     => 3,
			'orderby'    => 'count',
			'order'      => 'DESC',
			'hide_empty' => false,
		)
	);

	return is_array( $fallback ) ? $fallback : array();
}

/**
 * Đếm số bài của một chuyên mục, tính cả bài nằm trong các chuyên mục con.
 *
 * WordPress lưu sẵn $term->count nhưng con số đó chỉ đếm bài gán TRỰC TIẾP vào
 * chuyên mục. Với cấu trúc phân cấp của site này, ba chuyên mục cha không có bài
 * nào gán trực tiếp — toàn bộ bài nằm ở chuyên mục con. Dùng $term->count sẽ
 * hiện "0 bài viết" ngay dưới một trụ đang chứa 53 bài.
 *
 * @param WP_Term $term Chuyên mục.
 * @return int
 */
function dql_category_deep_count( $term ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$count    = (int) $term->count;
	$children = get_term_children( $term->term_id, 'category' );

	if ( is_wp_error( $children ) ) {
		return $count;
	}

	foreach ( $children as $child_id ) {
		$child = get_term( $child_id, 'category' );

		if ( $child && ! is_wp_error( $child ) ) {
			$count += (int) $child->count;
		}
	}

	return $count;
}

/**
 * ID của các bài nằm ở khối nổi bật trên trang chủ.
 *
 * Hai khối trang chủ cùng cần danh sách này: khối nổi bật để hiển thị, khối bài
 * mới để loại trừ. Tính một lần rồi dùng chung.
 *
 * Vì sao không dùng 'offset' => 3 ở khối bài mới: khối nổi bật ưu tiên bài được
 * GHIM, còn offset đếm theo NGÀY ĐĂNG. Hai thứ tự đó khác nhau, nên bài ghim sẽ
 * lọt vào cả hai khối. Loại trừ theo ID thì không bao giờ sai.
 *
 * @return int[] Tối đa 3 ID.
 */
function dql_featured_post_ids() {
	static $ids = null;

	if ( null !== $ids ) {
		return $ids;
	}

	$can = 3;

	/*
	 * Bài ghim được ưu tiên, và phải tự xử lý ở đây.
	 *
	 * WordPress chỉ đưa bài ghim lên đầu ở TRUY VẤN CHÍNH của trang chủ. Truy
	 * vấn riêng như khối này thì không, kể cả khi đặt ignore_sticky_posts sai.
	 * Không tự xử lý thì chủ site mất hoàn toàn quyền chọn bài nào được làm nổi
	 * bật — khối luôn lấy 3 bài mới nhất, dù bài đó có đáng khoe hay không.
	 *
	 * Cách chọn bài nổi bật: mở Bài viết, dùng "Sửa nhanh" rồi tích "Dán bài này
	 * lên trang chủ".
	 */
	$ghim = array_map( 'intval', (array) get_option( 'sticky_posts', array() ) );
	$ids  = array();

	if ( $ghim ) {
		$q = new WP_Query(
			array(
				'post_type'           => 'post',
				'post__in'            => $ghim,
				'orderby'             => 'post__in',
				'posts_per_page'      => $can,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
				'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$ids = array_map( 'intval', $q->posts );
	}

	// Chưa đủ 3 thì lấy thêm bài mới nhất, bỏ qua những bài đã có.
	if ( count( $ids ) < $can ) {
		$q = new WP_Query(
			array(
				'post_type'           => 'post',
				'posts_per_page'      => $can - count( $ids ),
				'post__not_in'        => $ids,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
				'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
					array(
						'key'     => '_thumbnail_id',
						'compare' => 'EXISTS',
					),
				),
			)
		);

		$ids = array_merge( $ids, array_map( 'intval', $q->posts ) );
	}

	return $ids;
}

/**
 * Lấy ảnh đại diện cho một chuyên mục.
 *
 * WordPress không có khái niệm ảnh cho chuyên mục, nên ta mượn ảnh của bài mới
 * nhất trong chuyên mục đó (tính cả chuyên mục con). Cách này có hai cái lợi so
 * với bắt chủ site tự chọn ảnh cho từng mục: không phải làm gì thêm, và ảnh tự
 * đổi theo nội dung mới.
 *
 * Kết quả được nhớ tạm 12 giờ. Không nhớ tạm thì trang chủ chạy thêm ba truy vấn
 * mỗi lần có khách.
 *
 * @param WP_Term $term Chuyên mục.
 * @return int ID ảnh, hoặc 0 nếu không tìm được.
 */
function dql_category_image( $term ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return 0;
	}

	$khoa = 'dql_cat_img_' . $term->term_id;
	$cache = get_transient( $khoa );

	if ( false !== $cache ) {
		return (int) $cache;
	}

	$q = new WP_Query(
		array(
			'cat'                 => $term->term_id, // Bao gồm cả chuyên mục con.
			'posts_per_page'      => 1,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$id = $q->posts ? (int) get_post_thumbnail_id( $q->posts[0] ) : 0;

	set_transient( $khoa, $id, 12 * HOUR_IN_SECONDS );

	return $id;
}

/**
 * In ra biểu tượng cho một trụ nội dung.
 *
 * Vẽ trực tiếp bằng SVG thay vì dùng font biểu tượng: chỉ ba hình, nhúng thẳng
 * thì không tốn thêm một request nào và không có khoảng nhấp nháy lúc font chưa về.
 *
 * @param int $index Vị trí trụ, tính từ 0.
 */
function dql_pillar_icon( $index ) {
	$icons = array(
		// Viên đá cắt giác — kiến thức, phân loại đá.
		'<path d="M6 3h12l4 6-10 12L2 9z"/><path d="M2 9h20M9 3 6.5 9 12 21M15 3l2.5 6L12 21"/>',
		// Vòng âm dương — phong thủy, hợp mệnh.
		'<circle cx="12" cy="12" r="9"/><path d="M12 3a4.5 4.5 0 0 0 0 9 4.5 4.5 0 0 1 0 9"/><circle cx="12" cy="7.5" r="1"/><circle cx="12" cy="16.5" r="1"/>',
		// Con dấu kiểm định — giấy chứng nhận.
		'<circle cx="12" cy="9" r="6"/><path d="m8.5 14-1.5 7 5-2.5 5 2.5-1.5-7"/><path d="m9.5 9 1.8 1.8L14.5 7.5"/>',
	);

	$path = isset( $icons[ $index ] ) ? $icons[ $index ] : $icons[0];

	printf(
		'<svg class="dql-pillar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">%s</svg>',
		$path // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Chuỗi SVG cố định trong mã nguồn, không có dữ liệu người dùng.
	);
}
