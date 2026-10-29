<?php
/**
 * Trang chuyên mục và trang danh sách bài.
 *
 * Từ 04/08/2026, trang CHUYÊN MỤC không đi qua khung nội dung của Kadence nữa —
 * category.php tự dựng toàn bộ, giống cách search.php đã làm. Lý do: bố cục đã
 * chốt (phương án B) là danh sách hàng ngang, không phải lưới thẻ, và không có
 * cột bên. Uốn lưới của Kadence thành danh sách bằng CSS thì mọi lần Kadence đổi
 * markup là một lần trang này gãy.
 *
 * Trang DANH SÁCH BÀI (`/bai-viet/`, tức is_home) dùng chung bộ khối đó qua
 * home.php — cùng đầu trang, cùng danh sách, chỉ khác ở chỗ nó không có chuyên
 * mục nào nên các hàm bên dưới đều nhận $term = null.
 *
 * Trang thẻ và trang lưu trữ theo ngày vẫn dùng khung của Kadence. Phần cuối
 * file giữ nguyên những gì cắm vào đó: thời gian đọc trên thẻ bài và vài mặc
 * định bố cục.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 * 1. Dải chip điều hướng chuyên mục
 * ========================================================================= */

/**
 * Các chuyên mục cần hiện thành nút bấm ở đầu trang lưu trữ.
 *
 * Quy tắc theo ngữ cảnh người đọc đang đứng:
 *
 *   - Đang ở chuyên mục CHA  → hiện các chuyên mục con, để đi sâu vào.
 *   - Đang ở chuyên mục CON  → hiện các chuyên mục anh em, để so sánh ngang.
 *   - Đang ở trang danh sách bài → hiện ba chuyên mục cha, làm điểm vào.
 *
 * @return array{cha: WP_Term|null, muc: WP_Term[]}
 */
function dql_archive_nav_terms() {
	$empty = array(
		'cha' => null,
		'muc' => array(),
	);

	if ( is_home() ) {
		$tops = get_categories(
			array(
				'parent'     => 0,
				'hide_empty' => false,
			)
		);

		// Bỏ chuyên mục rỗng, và bỏ luôn chuyên mục mặc định của WordPress.
		//
		// "Chưa phân loại" là cái rọ hứng bài chưa gán chuyên mục, không phải
		// một chủ đề. Để nó đứng cạnh ba trụ nội dung làm người đọc tưởng đó
		// cũng là một mảng kiến thức.
		$default_cat = dql_uncategorized_id();

		$tops = array_filter(
			$tops,
			function ( $t ) use ( $default_cat ) {
				return $t->term_id !== $default_cat && dql_category_deep_count( $t ) > 0;
			}
		);

		return array(
			'cha' => null,
			'muc' => array_values( $tops ),
		);
	}

	if ( ! is_category() ) {
		return $empty;
	}

	$current = get_queried_object();

	if ( ! $current || is_wp_error( $current ) ) {
		return $empty;
	}

	$children = get_categories(
		array(
			'parent'     => $current->term_id,
			'hide_empty' => false,
		)
	);

	if ( $children ) {
		return array(
			'cha' => null,
			'muc' => $children,
		);
	}

	if ( ! $current->parent ) {
		return $empty;
	}

	$parent   = get_term( $current->parent, 'category' );
	$siblings = get_categories(
		array(
			'parent'     => $current->parent,
			'hide_empty' => false,
		)
	);

	return array(
		'cha' => ( $parent && ! is_wp_error( $parent ) ) ? $parent : null,
		'muc' => $siblings,
	);
}

/**
 * In dải nút điều hướng chuyên mục.
 *
 * Gọi từ dql_archive_head(). Ở trang chuyên mục dải này là các chuyên mục anh
 * em hoặc con; ở trang danh sách bài là ba chuyên mục cha, làm điểm vào.
 */
function dql_archive_chips() {
	$nav = dql_archive_nav_terms();

	if ( ! $nav['muc'] ) {
		return;
	}

	$current_id = is_category() ? (int) get_queried_object_id() : 0;
	?>
	<nav class="dql-archive-nav" aria-label="<?php esc_attr_e( 'Chuyên mục liên quan', 'kadence-daquylucyen' ); ?>">
		<?php if ( $nav['cha'] ) : ?>
			<a class="dql-chip dql-chip--up" href="<?php echo esc_url( get_category_link( $nav['cha']->term_id ) ); ?>">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="m15 18-6-6 6-6"/>
				</svg>
				<?php echo esc_html( $nav['cha']->name ); ?>
			</a>
			<span class="dql-archive-nav__vach" aria-hidden="true"></span>
		<?php endif; ?>

		<?php
		foreach ( $nav['muc'] as $term ) :
			$count = dql_category_deep_count( $term );

			if ( ! $count ) {
				continue;
			}
			?>
			<a
				class="dql-chip dql-chip--gem<?php echo $term->term_id === $current_id ? ' is-current' : ''; ?>"
				href="<?php echo esc_url( get_category_link( $term->term_id ) ); ?>"
				<?php echo $term->term_id === $current_id ? ' aria-current="page"' : ''; ?>
			>
				<?php dql_the_category_badge( $term, 'nho' ); ?>
				<span><?php echo esc_html( $term->name ); ?></span>
				<span class="dql-chip__count"><?php echo esc_html( number_format_i18n( $count ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}


/* ===========================================================================
 * 2. Số liệu của một chuyên mục
 * ========================================================================= */

/**
 * Ngày cập nhật gần nhất và thời gian đọc trung bình của một chuyên mục.
 *
 * Hai con số này phải mở nội dung bài ra mới tính được, nên kết quả được nhớ tạm
 * 12 giờ và chỉ lấy mẫu 50 bài mới cập nhật nhất. Lấy mẫu chứ không quét hết là
 * có chủ đích: với một chuyên mục 500 bài, phần chênh lệch của con số trung bình
 * không đáng để nạp 500 nội dung bài vào bộ nhớ.
 *
 * Bộ nhớ tạm bị xóa khi có bài trong chuyên mục được lưu — xem dql_archive_xoa_cache().
 *
 * @param WP_Term|null $term Chuyên mục. Bỏ trống thì tính trên toàn bộ bài viết
 *                           của site — dùng cho trang danh sách bài.
 * @return array{cap_nhat: int, phut: int} Dấu thời gian UTC và số phút trung bình.
 */
function dql_archive_stats( $term = null ) {
	$trong = array(
		'cap_nhat' => 0,
		'phut'     => 0,
	);

	if ( $term && is_wp_error( $term ) ) {
		return $trong;
	}

	$khoa  = $term ? 'dql_ar_stats_' . $term->term_id : 'dql_ar_stats_blog';
	$cache = get_transient( $khoa );

	if ( is_array( $cache ) ) {
		return $cache;
	}

	$args = array(
		'post_status'         => 'publish',
		'posts_per_page'      => 50,
		'orderby'             => 'modified',
		'order'               => 'DESC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	);

	if ( $term ) {
		$args['cat'] = $term->term_id; // Gồm cả chuyên mục con.
	}

	$q = new WP_Query( $args );

	if ( ! $q->posts ) {
		set_transient( $khoa, $trong, HOUR_IN_SECONDS );

		return $trong;
	}

	$tong     = 0;
	$cap_nhat = 0;

	foreach ( $q->posts as $post ) {
		$tong += dql_reading_time( $post );

		$moc = (int) get_post_modified_time( 'U', true, $post );

		if ( $moc > $cap_nhat ) {
			$cap_nhat = $moc;
		}
	}

	$stats = array(
		'cap_nhat' => $cap_nhat,
		'phut'     => (int) round( $tong / count( $q->posts ) ),
	);

	set_transient( $khoa, $stats, 12 * HOUR_IN_SECONDS );

	return $stats;
}

/**
 * Xóa số liệu đã nhớ tạm của các chuyên mục chứa một bài.
 *
 * Không có bước này thì sửa một bài xong, dòng "Cập nhật gần nhất" ở đầu chuyên
 * mục vẫn giữ ngày cũ tới 12 giờ — đủ để tưởng là code hỏng.
 *
 * @param int $post_id ID bài viết.
 */
function dql_archive_xoa_cache( $post_id ) {
	if ( 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	// Số liệu của trang danh sách bài tính trên toàn site nên bài nào cũng đụng tới.
	delete_transient( 'dql_ar_stats_blog' );

	foreach ( wp_get_post_categories( $post_id ) as $term_id ) {
		foreach ( array_merge( array( $term_id ), get_ancestors( $term_id, 'category' ) ) as $id ) {
			// Chuyên mục cha cũng phải xóa: số liệu của nó tính cả bài của con.
			delete_transient( 'dql_ar_stats_' . $id );
			delete_transient( 'dql_ar_nen_tang_' . $id );
			delete_transient( 'dql_ar_top_' . $id );
		}
	}
}
add_action( 'save_post', 'dql_archive_xoa_cache' );
add_action( 'deleted_post', 'dql_archive_xoa_cache' );

/* ===========================================================================
 * 3. Ba bài nền tảng
 * ========================================================================= */

const DQL_NEN_TANG_META = '_dql_bai_nen_tang';

/**
 * Ba bài nền tảng của một chuyên mục.
 *
 * Thứ tự ưu tiên:
 *
 *   1. Ba bài chủ site tự chọn ở màn "Sửa chuyên mục". Đây mới là thứ đáng tin —
 *      "bài nền tảng" là một đánh giá về nội dung, máy không suy ra được.
 *   2. Ba bài đọc nhiều nhất TRONG chuyên mục, nếu plugin dql-core đang đếm.
 *      Không phải bài nền tảng, nhưng là phỏng đoán gần nhất mà dữ liệu cho phép.
 *   3. Rỗng — hộp không hiện, đầu trang tự thu về một cột.
 *
 * @param WP_Term $term Chuyên mục.
 * @return int[] Tối đa 3 ID bài đã đăng.
 */
function dql_archive_nen_tang( $term ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return array();
	}

	$chon = get_term_meta( $term->term_id, DQL_NEN_TANG_META, true );
	$chon = is_array( $chon ) ? array_map( 'absint', $chon ) : array();
	$chon = array_values( array_filter( array_unique( $chon ) ) );

	if ( $chon ) {
		// 'cat' lọc lại theo chuyên mục: bài đã chọn tay mà sau đó bị chuyển sang
		// chuyên mục khác thì không còn là bài nền tảng của chuyên mục này.
		$q = new WP_Query(
			array(
				'post_type'           => 'post',
				'post_status'         => 'publish',
				'post__in'            => $chon,
				'cat'                 => $term->term_id,
				'orderby'             => 'post__in',
				'posts_per_page'      => 3,
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'fields'              => 'ids',
			)
		);

		if ( $q->posts ) {
			return array_map( 'intval', $q->posts );
		}
	}

	// Chưa chọn tay thì đoán theo lượt xem. Truy vấn sắp xếp theo meta là loại
	// nặng nên nhớ tạm một giờ, cùng lý do với bảng xếp hạng của dql-core.
	if ( ! class_exists( 'DQL_Luot_Xem' ) ) {
		return array();
	}

	$khoa  = 'dql_ar_nen_tang_' . $term->term_id;
	$cache = get_transient( $khoa );

	if ( is_array( $cache ) ) {
		return $cache;
	}

	$q = new WP_Query(
		array(
			'cat'                 => $term->term_id,
			'post_status'         => 'publish',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'meta_key'            => DQL_Luot_Xem::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'             => 'meta_value_num',
			'order'               => 'DESC',
		)
	);

	$ids = count( $q->posts ) >= 3 ? array_map( 'intval', $q->posts ) : array();

	set_transient( $khoa, $ids, $ids ? HOUR_IN_SECONDS : 5 * MINUTE_IN_SECONDS );

	return $ids;
}

/* ===========================================================================
 * 4. Chọn bài nền tảng trong màn "Sửa chuyên mục"
 * ========================================================================= */

/**
 * Danh sách bài để chọn, giới hạn trong một chuyên mục.
 *
 * @param int $term_id ID chuyên mục.
 * @return WP_Post[]
 */
function dql_archive_bai_de_chon( $term_id ) {
	return get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'cat'                 => $term_id,
			'posts_per_page'      => 200,
			'orderby'             => 'title',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'suppress_filters'    => false,
		)
	);
}

/**
 * In ba ô chọn bài nền tảng.
 *
 * @param WP_Term $term Chuyên mục đang sửa.
 */
function dql_archive_field_nen_tang( $term ) {
	if ( ! $term || is_wp_error( $term ) ) {
		return;
	}

	$bai = dql_archive_bai_de_chon( $term->term_id );
	$da  = get_term_meta( $term->term_id, DQL_NEN_TANG_META, true );
	$da  = is_array( $da ) ? array_map( 'absint', $da ) : array();

	?>
	<tr class="form-field">
		<th scope="row"><label><?php esc_html_e( 'Ba bài nền tảng', 'kadence-daquylucyen' ); ?></label></th>
		<td>
			<?php
			// Đặt trong <td>, không đặt trước <tr>: một <input> nằm trực tiếp giữa
			// hai <tr> là HTML sai, trình duyệt sẽ nhấc nó ra khỏi bảng.
			wp_nonce_field( 'dql_nen_tang_' . $term->term_id, 'dql_nen_tang_nonce' );
			?>

			<?php if ( ! $bai ) : ?>
				<p class="description">
					<?php esc_html_e( 'Chuyên mục này chưa có bài viết nào để chọn.', 'kadence-daquylucyen' ); ?>
				</p>
			<?php else : ?>
				<?php for ( $i = 0; $i < 3; $i++ ) : ?>
					<p>
						<select name="dql_bai_nen_tang[]" style="max-width:100%;min-width:26rem">
							<option value="0"><?php esc_html_e( '— Chưa chọn —', 'kadence-daquylucyen' ); ?></option>
							<?php foreach ( $bai as $post ) : ?>
								<option
									value="<?php echo esc_attr( $post->ID ); ?>"
									<?php selected( isset( $da[ $i ] ) ? $da[ $i ] : 0, $post->ID ); ?>
								>
									<?php echo esc_html( get_the_title( $post ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</p>
				<?php endfor; ?>

				<p class="description">
					<?php esc_html_e( 'Hiện trong hộp “Chưa biết bắt đầu từ đâu?” ở đầu trang chuyên mục. Chọn những bài giải thích khái niệm gốc — người mới đọc xong là hiểu được phần còn lại.', 'kadence-daquylucyen' ); ?>
					<br>
					<?php esc_html_e( 'Bỏ trống cả ba thì hộp tự lấy ba bài đọc nhiều nhất trong chuyên mục; chưa đủ số liệu lượt xem thì hộp không hiện.', 'kadence-daquylucyen' ); ?>
				</p>
			<?php endif; ?>
		</td>
	</tr>
	<?php
}
add_action( 'category_edit_form_fields', 'dql_archive_field_nen_tang' );

/**
 * Lưu ba bài nền tảng.
 *
 * Chỉ chạy ở màn SỬA chuyên mục — màn "Thêm chuyên mục" không có ô này, vì lúc
 * đó chuyên mục chưa có bài nào để chọn.
 *
 * @param int $term_id ID chuyên mục.
 */
function dql_archive_save_nen_tang( $term_id ) {
	if ( ! isset( $_POST['dql_nen_tang_nonce'] ) ) {
		return;
	}

	$nonce = sanitize_text_field( wp_unslash( $_POST['dql_nen_tang_nonce'] ) );

	if ( ! wp_verify_nonce( $nonce, 'dql_nen_tang_' . $term_id ) ) {
		return;
	}

	if ( ! current_user_can( 'edit_term', $term_id ) ) {
		return;
	}

	$ids = isset( $_POST['dql_bai_nen_tang'] ) ? (array) wp_unslash( $_POST['dql_bai_nen_tang'] ) : array();
	$ids = array_map( 'absint', $ids );
	$ids = array_values( array_filter( array_unique( $ids ) ) );
	$ids = array_slice( $ids, 0, 3 );

	if ( $ids ) {
		update_term_meta( $term_id, DQL_NEN_TANG_META, $ids );
	} else {
		delete_term_meta( $term_id, DQL_NEN_TANG_META );
	}

	delete_transient( 'dql_ar_nen_tang_' . $term_id );
}
add_action( 'edited_category', 'dql_archive_save_nen_tang' );

/* ===========================================================================
 * 5. Các khối của trang chuyên mục
 *
 * Gọi từ category.php.
 * ========================================================================= */

/**
 * Tên của trang danh sách bài.
 *
 * Lấy từ trang đã chọn ở Cài đặt → Đọc → "Trang bài viết". Chưa chọn trang nào
 * thì dùng tên mặc định, để đầu trang không bao giờ trống tiêu đề.
 *
 * @return string
 */
function dql_archive_ten_trang_bai() {
	$id = (int) get_option( 'page_for_posts' );

	return $id ? get_the_title( $id ) : __( 'Bài viết', 'kadence-daquylucyen' );
}

/**
 * Đường dẫn phân cấp, kèm dữ liệu có cấu trúc cho công cụ tìm kiếm.
 *
 * Site không cài plugin SEO nào, nên nếu ở đây không in schema thì không nơi nào
 * in. BreadcrumbList là thứ Google dùng để vẽ đường dẫn thay cho URL trần trong
 * kết quả tìm kiếm — rẻ và thấy được ngay.
 *
 * @param WP_Term|null $term Chuyên mục hiện tại. Bỏ trống là trang danh sách bài.
 */
function dql_archive_breadcrumb( $term = null ) {
	$muc = array(
		array(
			'ten' => __( 'Trang chủ', 'kadence-daquylucyen' ),
			'url' => home_url( '/' ),
		),
	);

	if ( ! $term ) {
		// Trang danh sách bài chỉ có hai cấp: trang chủ rồi tới nó.
		//
		// get_permalink() trả về false khi chưa chọn trang nào ở Cài đặt → Đọc.
		// Cấp cuối in ra thẻ <span> nên không dùng tới URL, nhưng schema thì có —
		// và một 'item' bằng false là dữ liệu hỏng gửi thẳng cho Google.
		$url_bai = get_permalink( (int) get_option( 'page_for_posts' ) );

		$muc[] = array(
			'ten' => dql_archive_ten_trang_bai(),
			'url' => $url_bai ? $url_bai : home_url( '/' ),
		);
	} else {
		foreach ( array_reverse( get_ancestors( $term->term_id, 'category' ) ) as $cha_id ) {
			$cha = get_term( $cha_id, 'category' );

			if ( $cha && ! is_wp_error( $cha ) ) {
				$muc[] = array(
					'ten' => $cha->name,
					'url' => get_category_link( $cha->term_id ),
				);
			}
		}

		$muc[] = array(
			'ten' => $term->name,
			'url' => get_category_link( $term->term_id ),
		);
	}

	dql_crumb_in( $muc );
}

/**
 * In một đường dẫn phân cấp đã dựng sẵn, kèm schema BreadcrumbList.
 *
 * Tách khỏi dql_archive_breadcrumb() để trang tác giả (inc/author.php) dùng
 * chung đúng một markup và một khối schema, thay vì chép lại cả hai.
 *
 * @param array[] $muc Các cấp, mỗi cấp gồm 'ten' và 'url'. Cấp cuối là trang
 *                     hiện tại — in thành chữ, nhưng URL vẫn vào schema.
 */
function dql_crumb_in( $muc ) {
	$cuoi = count( $muc ) - 1;
	?>
	<nav class="dql-crumb" aria-label="<?php esc_attr_e( 'Đường dẫn', 'kadence-daquylucyen' ); ?>">
		<?php foreach ( $muc as $i => $item ) : ?>
			<?php if ( $i ) : ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="m9 18 6-6-6-6"/>
				</svg>
			<?php endif; ?>

			<?php if ( $i === $cuoi ) : ?>
				<span class="dql-crumb__nay" aria-current="page"><?php echo esc_html( $item['ten'] ); ?></span>
			<?php else : ?>
				<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['ten'] ); ?></a>
			<?php endif; ?>
		<?php endforeach; ?>
	</nav>
	<?php

	$schema = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => array(),
	);

	foreach ( $muc as $i => $item ) {
		$schema['itemListElement'][] = array(
			'@type'    => 'ListItem',
			'position' => $i + 1,
			'name'     => $item['ten'],
			'item'     => $item['url'],
		);
	}

	printf(
		'<script type="application/ld+json">%s</script>',
		wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_json_encode đã thoát chuỗi cho ngữ cảnh JSON.
	);
}

/**
 * Hộp "Chưa biết bắt đầu từ đâu?".
 *
 * Trả về false khi không có gì để hiện, để đầu trang biết mà thu về một cột thay
 * vì chừa lại một ô trống bên phải.
 *
 * @param WP_Term $term Chuyên mục hiện tại.
 * @return bool Đã in ra hộp hay chưa.
 */
function dql_archive_hop_bat_dau( $term ) {
	$ids      = dql_archive_nen_tang( $term );
	$is_admin = current_user_can( 'manage_options' );

	if ( ! $ids ) {
		if ( ! $is_admin ) {
			return false;
		}

		// Chủ site thấy lời nhắc, khách không thấy gì. Cùng cách xử lý ô trống
		// như khối đăng ký email và khối "Đọc nhiều nhất" ở cột bên.
		?>
		<aside class="dql-batdau dql-batdau--cho">
			<h2 class="dql-batdau__title"><?php esc_html_e( 'Chưa chọn bài nền tảng', 'kadence-daquylucyen' ); ?></h2>
			<p class="dql-batdau__hint">
				<?php esc_html_e( 'Mở Bài viết → Chuyên mục → Sửa chuyên mục này, chọn ba bài ở ô “Ba bài nền tảng”. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' ); ?>
			</p>
		</aside>
		<?php

		return true;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => 3,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();

		return false;
	}
	?>
	<aside class="dql-batdau">
		<h2 class="dql-batdau__title">
			<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
				<path d="M12 3 4 7v6c0 5 3.4 7.4 8 8 4.6-.6 8-3 8-8V7l-8-4Z"/>
				<path d="m9 12 2 2 4-4"/>
			</svg>
			<?php esc_html_e( 'Chưa biết bắt đầu từ đâu?', 'kadence-daquylucyen' ); ?>
		</h2>

		<p class="dql-batdau__hint">
			<?php esc_html_e( 'Ba bài nền tảng. Đọc xong là hiểu được phần còn lại của chuyên mục.', 'kadence-daquylucyen' ); ?>
		</p>

		<ol class="dql-batdau__ds">
			<?php
			while ( $q->have_posts() ) :
				$q->the_post();
				?>
				<li>
					<a href="<?php the_permalink(); ?>">
						<span class="dql-batdau__ten"><?php the_title(); ?></span>
						<span class="dql-batdau__meta"><?php dql_the_reading_time(); ?></span>
					</a>
				</li>
			<?php endwhile; ?>
		</ol>
	</aside>
	<?php
	wp_reset_postdata();

	return true;
}

/**
 * Đầu trang chuyên mục và trang danh sách bài.
 *
 * @param WP_Term|null $term Chuyên mục hiện tại. Bỏ trống là trang danh sách bài.
 */
function dql_archive_head( $term = null ) {
	$stats = dql_archive_stats( $term );
	$so    = (int) $GLOBALS['wp_query']->found_posts;

	if ( $term ) {
		$tieu_de = $term->name;
		$mo_ta   = term_description( $term->term_id );
	} else {
		$tieu_de = dql_archive_ten_trang_bai();

		/*
		 * Mô tả lấy từ NỘI DUNG của trang "Bài viết".
		 *
		 * WordPress bình thường vứt bỏ nội dung đó — trang được chọn làm trang
		 * danh sách bài thì chỉ còn cái tên được dùng. Đem nó ra làm câu dẫn ở
		 * đầu trang là chỗ dùng đúng nhất: chủ site sửa được ngay trong trình
		 * soạn thảo quen thuộc, không cần thêm một ô cấu hình mới.
		 *
		 * Không chạy qua bộ lọc 'the_content': ở đây chỉ cần một đoạn dẫn ngắn,
		 * để nguyên bộ lọc thì một shortcode hay một khối ảnh lọt vào sẽ phá vỡ
		 * đầu trang.
		 */
		$trang_bai = (int) get_option( 'page_for_posts' );
		$mo_ta     = $trang_bai ? wpautop( get_post_field( 'post_content', $trang_bai ) ) : '';
	}

	/*
	 * Hộp "Bắt đầu từ đâu" chỉ có ở trang chuyên mục.
	 *
	 * Hộp đó là ba bài nền tảng của MỘT chuyên mục; ở trang danh sách bài không
	 * có chuyên mục nào để chọn. Dải chip ngay bên dưới — ba chuyên mục cha — đã
	 * làm đúng việc dẫn lối vào rồi.
	 *
	 * Dựng hộp trước, in đầu trang sau: phải biết hộp có nội dung hay không mới
	 * chọn được lớp bố cục cho khối chữ bên trái.
	 */
	$co_hop = false;
	$hop    = '';

	if ( $term ) {
		ob_start();
		$co_hop = dql_archive_hop_bat_dau( $term );
		$hop    = ob_get_clean();
	}
	?>
	<header class="dql-chead dql-hat"<?php echo $term ? ' style="--dql-gem: var(--dql-da-' . esc_attr( dql_category_color_key( $term ) ) . ')"' : ''; ?>>
		<div class="dql-container">
			<div class="dql-chead__split<?php echo $co_hop ? '' : ' dql-chead__split--don'; ?>">
				<div class="dql-chead__chu">
					<?php dql_archive_breadcrumb( $term ); ?>

					<div class="dql-chead__ten">
						<?php
						// Huy hiệu đá lớn của chuyên mục. Trang danh sách bài không
						// có chuyên mục nào nên dùng viên đá chung của site.
						if ( $term ) {
							dql_the_category_badge( $term, 'rat-lon', 'dql-chead__gem' );
						} else {
							echo '<span class="dql-gem dql-gem--rat-lon dql-gem--ruby dql-chead__gem">' . dql_icon( 'da-quy', array( 'stroke' => 1.25 ) ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						<div>
							<p class="dql-kicker dql-kicker--gem"><?php echo $term ? esc_html__( 'Chuyên mục', 'kadence-daquylucyen' ) : esc_html__( 'Toàn bộ', 'kadence-daquylucyen' ); ?></p>
							<h1 class="dql-chead__title"><?php echo esc_html( $tieu_de ); ?></h1>
						</div>
					</div>

					<?php if ( $mo_ta ) : ?>
						<div class="dql-chead__mo-ta"><?php echo wp_kses_post( $mo_ta ); ?></div>
					<?php endif; ?>

					<div class="dql-chead__so">
						<span>
							<?php
							printf(
								/* translators: %s: số bài viết. */
								esc_html( _n( '%s bài viết', '%s bài viết', $so, 'kadence-daquylucyen' ) ),
								'<b>' . esc_html( number_format_i18n( $so ) ) . '</b>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							);
							?>
						</span>

						<?php if ( $stats['cap_nhat'] ) : ?>
							<span class="dql-chead__cham" aria-hidden="true"></span>
							<span>
								<?php esc_html_e( 'Cập nhật gần nhất', 'kadence-daquylucyen' ); ?>
								<b><?php echo esc_html( wp_date( 'd/m/Y', $stats['cap_nhat'] ) ); ?></b>
							</span>
						<?php endif; ?>

						<?php if ( $stats['phut'] ) : ?>
							<span class="dql-chead__cham" aria-hidden="true"></span>
							<span>
								<?php esc_html_e( 'Trung bình', 'kadence-daquylucyen' ); ?>
								<b>
									<?php
									printf(
										/* translators: %d: số phút đọc trung bình. */
										esc_html( _n( '%d phút', '%d phút', $stats['phut'], 'kadence-daquylucyen' ) ),
										(int) $stats['phut']
									);
									?>
								</b><?php esc_html_e( '/bài', 'kadence-daquylucyen' ); ?>
							</span>
						<?php endif; ?>
					</div>
				</div>

				<?php echo $hop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup đã thoát ở dql_archive_hop_bat_dau(). ?>
			</div>

			<?php dql_archive_chips(); ?>
		</div>
	</header>
	<?php
}

/**
 * Một dòng bài trong danh sách.
 *
 * Hai liên kết — ảnh và tiêu đề — chứ không bọc cả dòng trong một thẻ <a>.
 * Bọc cả dòng thì trình đọc màn hình phải đọc hết cả đoạn trích mới tới được
 * liên kết tiếp theo. Mũi tên bên phải chỉ là hình trang trí.
 *
 * @param bool $hien_cat In tên chuyên mục ở dòng thông tin hay không.
 *                       Trang chuyên mục thì KHÔNG: tên đó trùng đúng tiêu đề
 *                       trang, in lại trên từng dòng là nhiễu chứ không phải
 *                       thông tin. Trang danh sách bài thì CÓ: bài ở đó đến từ
 *                       mọi chuyên mục nên tên chuyên mục mới có việc để làm.
 */
function dql_archive_row( $hien_cat = false ) {
	?>
	<article <?php post_class( 'dql-row' ); ?>>
		<a class="dql-row__anh<?php echo has_post_thumbnail() ? '' : ' dql-row__anh--trong'; ?>" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php if ( has_post_thumbnail() ) : ?>
				<?php
				the_post_thumbnail(
					'dql-card',
					array(
						'alt'     => '',
						'loading' => 'lazy',
					)
				);
				?>
			<?php else : ?>
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
					<path d="M6 3h12l4 6-10 12L2 9z"/><path d="M2 9h20M9 3 6.5 9 12 21M15 3l2.5 6L12 21"/>
				</svg>
			<?php endif; ?>
		</a>

		<div class="dql-row__chu">
			<h2 class="dql-row__ten">
				<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h2>

			<p class="dql-row__trich">
				<?php
				// 26 từ: đủ để biết bài nói gì mà không biến danh sách thành một
				// trang chữ. Cắt từ nội dung chứ không dùng the_excerpt() để đoạn
				// trích không dính thẻ HTML rồi vỡ dòng khi bị khóa hai dòng.
				echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 26, '…' ) );
				?>
			</p>

			<div class="dql-row__meta">
				<?php
				if ( $hien_cat ) :
					$cat = dql_post_main_category();

					if ( $cat ) :
						echo dql_category_label( $cat, true, 'dql-row__cat' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã thoát trong dql_category_label().
						?>
						<span class="dql-row__cham" aria-hidden="true"></span>
						<?php
					endif;
				endif;
				?>

				<?php dql_the_reading_time(); ?>
				<span class="dql-row__cham" aria-hidden="true"></span>
				<?php dql_the_updated(); ?>
			</div>
		</div>

		<span class="dql-row__mui" aria-hidden="true">
			<?php dql_the_icon( 'phai' ); ?>
		</span>
	</article>
	<?php
}

/**
 * Một thẻ bài trong lưới — dùng cho ba bài mới nhất ở đầu trang chuyên mục.
 *
 * Vì sao ba bài đầu là thẻ còn phần sau là hàng ngang (từ bản 2.0): bài mới
 * nhất đáng được ảnh lớn như một trang tạp chí, nhưng với 20 bài còn lại thì
 * danh sách hàng ngang quét tiêu đề nhanh hơn — người vào blog kiến thức tìm
 * đúng bài, không lướt ảnh. Hai hình thức, hai việc.
 *
 * @param bool $hien_cat In nhãn chuyên mục hay không — cùng quy tắc với dql_archive_row().
 */
function dql_archive_card( $hien_cat = false ) {
	$cat = $hien_cat ? dql_post_main_category() : null;
	?>
	<article <?php post_class( 'dql-card' ); ?>>
		<a class="dql-card__link" href="<?php the_permalink(); ?>">
			<span class="dql-card__media dql-hat<?php echo has_post_thumbnail() ? '' : ' dql-card__media--trong'; ?>">
				<?php if ( has_post_thumbnail() ) : ?>
					<?php the_post_thumbnail( 'dql-card', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<?php dql_the_icon( 'da-quy', array( 'stroke' => 1.2 ) ); ?>
				<?php endif; ?>

				<?php if ( $cat ) : ?>
					<?php echo dql_category_label( $cat, false, 'dql-cat-label--noi' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Đã thoát trong dql_category_label(). ?>
				<?php endif; ?>
			</span>

			<span class="dql-card__body">
				<h2 class="dql-card__title"><?php the_title(); ?></h2>

				<span class="dql-card__trich">
					<?php echo esc_html( wp_trim_words( wp_strip_all_tags( strip_shortcodes( get_the_content() ) ), 22, '…' ) ); ?>
				</span>

				<span class="dql-card__meta">
					<?php dql_the_updated(); ?>
					<span class="dql-cham" aria-hidden="true"></span>
					<?php dql_the_reading_time(); ?>
				</span>
			</span>
		</a>
	</article>
	<?php
}

/**
 * Vòng lặp bài của trang chuyên mục và trang danh sách bài.
 *
 * Trang 1: ba bài đầu thành lưới thẻ, phần còn lại thành hàng ngang; dải tra
 * mệnh chèn sau ba hàng đầu. Trang 2 trở đi: toàn hàng ngang, không chèn gì —
 * người đã lật sang trang 2 đang tìm một bài cụ thể, chắn ngang là quấy rầy.
 *
 * Chỉ dùng thẻ khi trang có từ 5 bài trở lên: ba thẻ rồi tới một hàng lẻ loi
 * trông như danh sách bị gãy.
 *
 * @param bool $hien_cat In nhãn chuyên mục trên từng bài hay không.
 * @param bool $tap_chi  Dựng kiểu tạp chí (thẻ đầu trang, dải chèn giữa) hay
 *                       chỉ hàng ngang. Trang tác giả truyền false: nó đã có
 *                       ba thẻ "Đọc nhiều nhất" riêng ở trên danh sách.
 */
function dql_archive_loop( $hien_cat = false, $tap_chi = true ) {
	global $wp_query;

	$trang   = max( 1, (int) get_query_var( 'paged' ) );
	$so_bai  = (int) $wp_query->post_count;
	$so_the  = ( $tap_chi && 1 === $trang && $so_bai >= 5 ) ? 3 : 0;
	$chen    = ( $tap_chi && 1 === $trang ) ? $so_the + 3 : 0;
	$dem     = 0;
	$mo_rows = false;

	while ( have_posts() ) :
		the_post();
		$dem++;

		if ( 1 === $dem && $so_the ) {
			echo '<p class="dql-kicker dql-archive__kicker">' . esc_html__( 'Mới nhất', 'kadence-daquylucyen' ) . '</p>';
			echo '<div class="dql-cards dql-cards--3">';
		}

		if ( $dem <= $so_the ) {
			dql_archive_card( $hien_cat );

			if ( $dem === $so_the ) {
				echo '</div>';
			}

			continue;
		}

		if ( ! $mo_rows ) {
			echo '<div class="dql-rows">';
			$mo_rows = true;
		}

		dql_archive_row( $hien_cat );

		/*
		 * Đóng danh sách lại, chèn dải, rồi mở danh sách mới. Không đặt dải vào
		 * bên trong .dql-rows: các dòng dùng đường kẻ dưới để ngăn nhau, một
		 * khối lạ nằm giữa sẽ thừa ra một đường kẻ ngay trên nó.
		 */
		if ( $chen && $dem === $chen && $dem < $so_bai ) {
			echo '</div>';
			dql_archive_cta_menh();
			echo '<div class="dql-rows">';
		}
	endwhile;

	if ( $mo_rows ) {
		echo '</div>';
	}
}

/**
 * Dải mời dùng công cụ tra đá hợp mệnh, chèn giữa danh sách.
 *
 * Chỉ dẫn tới công cụ ở trang chủ chứ không nhúng form: công cụ trả kết quả bằng
 * cách tải lại trang kèm tham số, nhúng ở đây sẽ ném người đọc ra khỏi danh sách
 * họ đang duyệt dở. Cùng lý do như ô tra mệnh ở cột bên bài viết.
 */
function dql_archive_cta_menh() {
	if ( ! function_exists( 'dql_core_menh_form' ) ) {
		return;
	}
	?>
	<aside class="dql-ar-cta">
		<span class="dql-gem dql-gem--vua dql-gem--ruby dql-ar-cta__icon"><?php dql_the_icon( 'phong-thuy' ); ?></span>

		<div class="dql-ar-cta__chu">
			<p class="dql-ar-cta__title"><?php esc_html_e( 'Đá nào hợp mệnh bạn?', 'kadence-daquylucyen' ); ?></p>
			<p class="dql-ar-cta__desc">
				<?php esc_html_e( 'Nhập năm sinh, biết ngay mệnh của bạn và những loại đá đi cùng.', 'kadence-daquylucyen' ); ?>
			</p>
		</div>

		<a class="dql-ar-cta__nut" href="<?php echo esc_url( home_url( '/#dql-menh' ) ); ?>">
			<?php esc_html_e( 'Tra ngay', 'kadence-daquylucyen' ); ?>
		</a>
	</aside>
	<?php
}

/**
 * Phân trang, kèm một dòng cho biết đang đứng ở đâu trong tổng số bài.
 *
 * Dãy số trần không nói được "còn bao nhiêu nữa". Dòng chữ bên dưới nói.
 */
function dql_archive_pagination() {
	global $wp_query;

	$tong  = (int) $wp_query->max_num_pages;
	$trang = max( 1, (int) get_query_var( 'paged' ) );

	if ( $tong < 2 ) {
		return;
	}

	$links = paginate_links(
		array(
			'total'     => $tong,
			'current'   => $trang,
			'prev_text' => esc_html__( 'Trước', 'kadence-daquylucyen' ),
			'next_text' => esc_html__( 'Sau', 'kadence-daquylucyen' ),
			'type'      => 'list',
		)
	);

	if ( ! $links ) {
		return;
	}

	$moi_trang = (int) $wp_query->get( 'posts_per_page' );
	$dau       = ( $trang - 1 ) * $moi_trang + 1;
	$cuoi      = $dau + (int) $wp_query->post_count - 1;
	?>
	<nav class="dql-ar-phantrang" aria-label="<?php esc_attr_e( 'Phân trang bài viết', 'kadence-daquylucyen' ); ?>">
		<?php echo wp_kses_post( $links ); ?>
	</nav>

	<p class="dql-ar-vitri">
		<?php
		printf(
			/* translators: 1: trang hiện tại, 2: tổng số trang, 3: bài đầu, 4: bài cuối, 5: tổng số bài. */
			esc_html__( 'Trang %1$s / %2$s — đang xem bài %3$s–%4$s trong %5$s bài', 'kadence-daquylucyen' ),
			esc_html( number_format_i18n( $trang ) ),
			esc_html( number_format_i18n( $tong ) ),
			esc_html( number_format_i18n( $dau ) ),
			esc_html( number_format_i18n( $cuoi ) ),
			esc_html( number_format_i18n( $wp_query->found_posts ) )
		);
		?>
	</p>
	<?php
}

/**
 * Bài đọc nhiều nhất TRONG một chuyên mục.
 *
 * DQL_Luot_Xem::top() xếp hạng toàn site, không lọc theo chuyên mục được. Truy
 * vấn riêng ở đây, sắp xếp theo cùng một trường meta nên vẫn là cùng một bảng
 * xếp hạng, chỉ hẹp lại phạm vi.
 *
 * Nhớ tạm một giờ, cùng lý do với bảng xếp hạng của dql-core: sắp xếp theo meta
 * là truy vấn nặng, và thứ hạng chậm một giờ thì không ai nhận ra.
 *
 * @param WP_Term $term Chuyên mục.
 * @return int[]
 */
function dql_archive_top_trong_muc( $term ) {
	if ( ! class_exists( 'DQL_Luot_Xem' ) || ! $term || is_wp_error( $term ) ) {
		return array();
	}

	$khoa  = 'dql_ar_top_' . $term->term_id;
	$cache = get_transient( $khoa );

	if ( is_array( $cache ) ) {
		return $cache;
	}

	$q = new WP_Query(
		array(
			'cat'                 => $term->term_id,
			'post_status'         => 'publish',
			'posts_per_page'      => 12,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
			'meta_key'            => DQL_Luot_Xem::META, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'orderby'             => 'meta_value_num',
			'order'               => 'DESC',
		)
	);

	$ids = array_map( 'intval', $q->posts );

	set_transient( $khoa, $ids, $ids ? HOUR_IN_SECONDS : 5 * MINUTE_IN_SECONDS );

	return $ids;
}

/**
 * Dải "Đọc nhiều nhất" ở cuối trang.
 *
 * Trước đây khối này nằm ở cột bên (inc/sidebar.php). Trang chuyên mục không còn
 * cột bên, nên nó xuống đây — vẫn bật/tắt bằng đúng tùy chọn cũ trong Customizer.
 *
 * Ưu tiên bài trong CHÍNH chuyên mục đang xem: người đọc đã tự chọn chủ đề bằng
 * việc mở trang này, ném cho họ bài đọc nhiều nhất của một chủ đề khác là kéo họ
 * ra ngoài. Chỉ khi chuyên mục chưa đủ bốn bài có lượt xem mới lùi về bảng xếp
 * hạng toàn site — và khi đó tiêu đề nói đúng phạm vi, không nhận vơ.
 *
 * Loại trừ các bài đã nằm trong hộp "Bắt đầu từ đâu": hai khối cách nhau một màn
 * hình mà in lại cùng một bài thì trang trông như đang lặp.
 *
 * @param WP_Term|null $term Chuyên mục hiện tại. Bỏ trống là trang danh sách bài,
 *                           khi đó luôn dùng bảng xếp hạng toàn site.
 */
function dql_archive_top_strip( $term = null ) {
	if ( ! dql_sidebar_on( 'dql_sb_archive_top' ) ) {
		return;
	}

	$is_admin  = current_user_can( 'manage_options' );
	$bo_qua    = dql_archive_nen_tang( $term );
	$trong_muc = true;

	$ids = array_values( array_diff( dql_archive_top_trong_muc( $term ), $bo_qua ) );

	if ( count( $ids ) < 4 ) {
		$trong_muc = false;
		$ids       = class_exists( 'DQL_Luot_Xem' ) ? DQL_Luot_Xem::top( 12 ) : array();
		$ids       = array_values( array_diff( $ids, $bo_qua ) );
	}

	$ids = array_slice( $ids, 0, 4 );

	if ( count( $ids ) < 4 ) {
		if ( ! $is_admin ) {
			return;
		}
		?>
		<section class="dql-ar-top">
			<div class="dql-container">
				<p class="dql-ar-top__cho">
					<?php
					if ( class_exists( 'DQL_Luot_Xem' ) ) {
						esc_html_e( 'Dải “Đọc nhiều nhất” cần ít nhất 4 bài có lượt xem, chưa tính những bài đã nằm ở hộp đầu trang. Chỉ đếm lượt của khách — lượt xem của người có quyền sửa bài không tính. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' );
					} else {
						esc_html_e( 'Dải “Đọc nhiều nhất” cần plugin dql-core đang bật để đếm lượt xem. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' );
					}
					?>
				</p>
			</div>
		</section>
		<?php

		return;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'post__in'            => $ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();

		return;
	}
	?>
	<section class="dql-ar-top">
		<div class="dql-container">
			<div class="dql-ar-top__dau">
				<h2 class="dql-ar-top__title">
					<?php
					if ( $trong_muc ) {
						printf(
							/* translators: %s: tên chuyên mục. */
							esc_html__( 'Đọc nhiều nhất trong %s', 'kadence-daquylucyen' ),
							esc_html( $term->name )
						);
					} else {
						esc_html_e( 'Đọc nhiều nhất trên trang', 'kadence-daquylucyen' );
					}
					?>
				</h2>
			</div>

			<ol class="dql-ar-top__ds">
				<?php
				$thu_tu = 0;

				while ( $q->have_posts() ) :
					$q->the_post();
					$thu_tu++;
					?>
					<li>
						<a class="dql-ar-tin" href="<?php the_permalink(); ?>">
							<span class="dql-ar-tin__hang" aria-hidden="true"><?php echo esc_html( number_format_i18n( $thu_tu ) ); ?></span>
							<span class="dql-ar-tin__chu">
								<span class="dql-ar-tin__ten"><?php the_title(); ?></span>
								<span class="dql-ar-tin__meta"><?php dql_the_reading_time(); ?></span>
							</span>
						</a>
					</li>
				<?php endwhile; ?>
			</ol>
		</div>
	</section>
	<?php
	wp_reset_postdata();
}

/* ===========================================================================
 * 6. Truy vấn của trang danh sách bài
 * ========================================================================= */

/**
 * Bỏ ưu tiên bài ghim ở trang danh sách bài.
 *
 * WordPress mặc định nhấc bài ghim lên đầu trang 1 của chỉ mục blog. Trên site
 * này việc đó sai theo hai đường:
 *
 * 1. Bài ghim ở đây KHÔNG mang nghĩa "ghim vào danh sách bài". Chủ site tích
 *    "Dán bài này lên trang chủ" để chọn bài cho khối nổi bật ở TRANG CHỦ — xem
 *    dql_featured_post_ids() trong inc/template-tags.php. Việc chúng nhảy lên
 *    đầu /bai-viet/ là tác dụng phụ không ai yêu cầu.
 *
 * 2. Chúng bị in HAI LẦN. Đo được ngày 04/08/2026: 3 bài ghim hiện ở đầu trang 1
 *    rồi hiện lại đúng vị trí theo ngày đăng ở trang sau — 82 dòng in ra cho 79
 *    bài. Lỗi này có từ trước khi dựng lại giao diện; nó chỉ lộ ra khi dòng
 *    "đang xem bài 1–13 trong 79 bài" cho thấy trang 1 dài hơn các trang khác.
 *
 * Bỏ ghim ở đây cũng làm dòng đếm vị trí đúng tuyệt đối: mọi trang 10 bài, không
 * còn trang nào phình ra 13.
 *
 * @param WP_Query $query Truy vấn.
 */
function dql_archive_bo_uu_tien_ghim( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_home() ) {
		return;
	}

	$query->set( 'ignore_sticky_posts', true );
}
add_action( 'pre_get_posts', 'dql_archive_bo_uu_tien_ghim' );

/* ===========================================================================
 * 7. Những gì còn cắm vào khung Kadence
 *
 * Chỉ còn tác dụng ở trang "Bài viết", trang thẻ và trang lưu trữ theo ngày.
 * Trang chuyên mục không đi qua đường này nữa.
 * ========================================================================= */

/**
 * Thêm thời gian đọc vào phần thông tin của mỗi thẻ bài.
 */
function dql_archive_reading_time() {
	if ( is_singular() ) {
		return;
	}

	echo '<div class="entry-meta dql-loop-reading-time">';
	dql_the_reading_time();
	echo '</div>';
}
add_action( 'kadence_after_loop_entry_meta', 'dql_archive_reading_time' );

/**
 * Chỉnh mặc định bố cục trang lưu trữ.
 *
 * @param array $defaults Mặc định gốc của Kadence.
 * @return array
 */
function dql_archive_defaults( $defaults ) {
	// Bỏ nút "Đọc tiếp". Cả thẻ bài đã là một liên kết, thêm nút chỉ là một
	// đích bấm thứ hai dẫn tới cùng một chỗ.
	//
	// Lưu ý: gỡ 'readmore' khỏi post_archive_elements là KHÔNG đủ — đó là lỗi
	// đã mắc một lần. template-parts/content/entry_actions.php đọc từ một tùy
	// chọn riêng, và tự đặt mặc định enabled = true nếu tùy chọn đó vắng mặt.
	$defaults['post_archive_elements'] = array( 'feature', 'categories', 'title', 'meta', 'excerpt' );

	$defaults['post_archive_element_readmore'] = array(
		'enabled' => false,
		'label'   => '',
	);

	// Bỏ tên tác giả. Site một người viết, hiện "By admin" trên 81 thẻ bài
	// không cho người đọc thêm thông tin nào.
	$defaults['post_archive_element_meta']['author'] = false;

	return $defaults;
}
add_filter( 'kadence_theme_options_defaults', 'dql_archive_defaults', 20 );
