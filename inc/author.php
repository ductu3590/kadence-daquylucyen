<?php
/**
 * Trang tác giả (/author/<tên>/).
 *
 * author.php tự dựng toàn bộ trang, cùng cách làm và cùng lý do với
 * category.php: bố cục là danh sách hàng ngang không cột bên, khung nội dung
 * của Kadence không dựng được.
 *
 * Mọi thứ trên đầu trang lấy từ HỒ SƠ NGƯỜI DÙNG trong wp-admin, không có ô cấu
 * hình nào khác:
 *
 *   - Tiểu sử             ← "Thông tin tiểu sử" (Biographical Info) của WordPress.
 *   - Chức danh           ← ô "Chức danh" (inc/single.php).
 *   - Link mạng xã hội    ← "Trang web" của WordPress, và Facebook, X, "URL hồ sơ
 *                            bổ sung" mà Rank Math thêm vào hồ sơ — đọc qua
 *                            dql_author_links() ở inc/single.php.
 *   - Nguyên tắc khi viết ← ô mới ở cuối file này.
 *
 * Trường nào trống thì phần đó không in, đầu trang tự co lại.
 *
 * Lọc theo chủ đề bằng tham số ?chu-de=<slug>, KHÔNG dùng ?cat=. Có tham số cat
 * thì WordPress coi trang là trang chuyên mục và nạp category.php — thứ tự
 * template của WordPress xét chuyên mục trước tác giả.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

const DQL_TG_NGUYEN_TAC = 'dql_nguyen_tac';

/* ===========================================================================
 * 1. Lọc theo chủ đề
 * ========================================================================= */

/**
 * Cho WordPress nhận tham số ?chu-de=.
 *
 * @param string[] $vars Các tham số truy vấn công khai.
 * @return string[]
 */
function dql_author_query_vars( $vars ) {
	$vars[] = 'chu-de';

	return $vars;
}
add_filter( 'query_vars', 'dql_author_query_vars' );

/**
 * Chuyên mục đang lọc trên trang tác giả, nếu có.
 *
 * @return WP_Term|null
 */
function dql_author_chu_de() {
	$slug = sanitize_title( (string) get_query_var( 'chu-de' ) );

	if ( '' === $slug ) {
		return null;
	}

	$term = get_term_by( 'slug', $slug, 'category' );

	return $term instanceof WP_Term ? $term : null;
}

/**
 * Truy vấn chính của trang tác giả.
 *
 * Dùng tax_query chứ không đặt 'category_name': đặt query var đó sau khi
 * WordPress đã phân tích URL thì các cờ is_category() không đổi, nhưng hai cách
 * cùng tồn tại dễ khiến người sửa sau nghĩ trang này là trang chuyên mục.
 *
 * @param WP_Query $query Truy vấn.
 */
function dql_author_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() || ! $query->is_author() ) {
		return;
	}

	// Cùng lý do với trang danh sách bài: bài ghim là để chọn khối nổi bật ở
	// trang chủ, không phải để nhảy lên đầu trang tác giả. Xem
	// dql_archive_bo_uu_tien_ghim() trong inc/archive.php.
	$query->set( 'ignore_sticky_posts', true );

	$slug = sanitize_title( (string) $query->get( 'chu-de' ) );

	if ( '' === $slug ) {
		return;
	}

	$query->set(
		'tax_query', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
		array(
			array(
				'taxonomy' => 'category',
				'field'    => 'slug',
				'terms'    => $slug,
			),
		)
	);
}
add_action( 'pre_get_posts', 'dql_author_pre_get_posts' );

/**
 * Trang đã lọc theo chủ đề thì không cho lập chỉ mục.
 *
 * Mỗi chủ đề là một bản cắt của trang tác giả, nội dung trùng với trang
 * chuyên mục. Để Google lập chỉ mục cả chục bản như vậy chỉ làm loãng trang gốc.
 *
 * @param array $robots Chỉ thị robots của WordPress.
 * @return array
 */
function dql_author_robots( $robots ) {
	if ( is_author() && dql_author_chu_de() ) {
		$robots['noindex'] = true;
		$robots['follow']  = true;
		unset( $robots['index'], $robots['max-image-preview'] );
	}

	return $robots;
}
add_filter( 'wp_robots', 'dql_author_robots' );

/**
 * Cùng việc trên, cho Rank Math — plugin này in thẻ robots riêng và gỡ bộ lọc
 * wp_robots của WordPress đi.
 *
 * @param array $robots Chỉ thị robots của Rank Math.
 * @return array
 */
function dql_author_robots_rank_math( $robots ) {
	if ( is_author() && dql_author_chu_de() ) {
		$robots['index'] = 'noindex';
	}

	return $robots;
}
add_filter( 'rank_math/frontend/robots', 'dql_author_robots_rank_math' );

/* ===========================================================================
 * 2. Số liệu của một tác giả
 * ========================================================================= */

/**
 * Số bài, số bài theo từng chuyên mục và ngày cập nhật gần nhất của tác giả.
 *
 * Nhớ tạm 12 giờ, xóa khi tác giả lưu một bài — xem dql_author_xoa_cache().
 *
 * @param int $user_id ID tác giả.
 * @return array{so_bai: int, muc: array<int, int>, cap_nhat: int}
 *         'muc' là term_id => số bài, nhiều bài nhất đứng đầu.
 */
function dql_author_stats( $user_id ) {
	$khoa  = 'dql_tg_stats_' . $user_id;
	$cache = get_transient( $khoa );

	if ( is_array( $cache ) ) {
		return $cache;
	}

	$q = new WP_Query(
		array(
			'author'              => $user_id,
			'post_type'           => 'post',
			'post_status'         => 'publish',
			// Trần 1000 chỉ để chặn trường hợp bất thường; site một người viết
			// cả đời cũng khó chạm tới. Lấy ID thôi nên vẫn nhẹ.
			'posts_per_page'      => 1000,
			'orderby'             => 'modified',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'fields'              => 'ids',
		)
	);

	$ids   = array_map( 'intval', $q->posts );
	$stats = array(
		'so_bai'   => count( $ids ),
		'muc'      => array(),
		'cap_nhat' => $ids ? (int) get_post_modified_time( 'U', true, $ids[0] ) : 0,
	);

	if ( $ids ) {
		$bo_qua = dql_uncategorized_id();
		$terms  = wp_get_object_terms( $ids, 'category', array( 'fields' => 'all_with_object_id' ) );

		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $t ) {
				if ( (int) $t->term_id === $bo_qua ) {
					continue;
				}

				$stats['muc'][ $t->term_id ] = isset( $stats['muc'][ $t->term_id ] ) ? $stats['muc'][ $t->term_id ] + 1 : 1;
			}

			arsort( $stats['muc'] );
		}
	}

	set_transient( $khoa, $stats, 12 * HOUR_IN_SECONDS );

	return $stats;
}

/**
 * Ba bài đọc nhiều nhất của tác giả, nếu plugin dql-core đang đếm lượt xem.
 *
 * Chưa đủ ba bài có lượt xem thì trả về rỗng — hai thẻ đứng một mình trông như
 * lưới bị gãy.
 *
 * @param int $user_id ID tác giả.
 * @return int[]
 */
function dql_author_top( $user_id ) {
	if ( ! class_exists( 'DQL_Luot_Xem' ) ) {
		return array();
	}

	$khoa  = 'dql_tg_top_' . $user_id;
	$cache = get_transient( $khoa );

	if ( is_array( $cache ) ) {
		return $cache;
	}

	$q = new WP_Query(
		array(
			'author'              => $user_id,
			'post_type'           => 'post',
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

/**
 * Xóa số liệu đã nhớ tạm của tác giả khi một bài của họ đổi.
 *
 * Chạy ở post_updated chứ không chỉ save_post để bắt được cả lúc bài được
 * chuyển sang tác giả khác: khi đó số liệu của CẢ HAI người đều sai.
 *
 * @param int          $post_id     ID bài.
 * @param WP_Post|null $post_after  Bài sau khi lưu (chỉ có ở post_updated).
 * @param WP_Post|null $post_before Bài trước khi lưu (chỉ có ở post_updated).
 */
function dql_author_xoa_cache( $post_id, $post_after = null, $post_before = null ) {
	if ( 'post' !== get_post_type( $post_id ) ) {
		return;
	}

	$tac_gia = array( (int) get_post_field( 'post_author', $post_id ) );

	if ( $post_before instanceof WP_Post ) {
		$tac_gia[] = (int) $post_before->post_author;
	}

	foreach ( array_unique( array_filter( $tac_gia ) ) as $id ) {
		delete_transient( 'dql_tg_stats_' . $id );
		delete_transient( 'dql_tg_top_' . $id );
	}
}
add_action( 'post_updated', 'dql_author_xoa_cache', 10, 3 );
add_action( 'save_post', 'dql_author_xoa_cache' );
// before_ chứ không phải deleted_post: lúc deleted_post chạy, bài đã mất khỏi
// database nên không còn biết nó thuộc loại nào, của ai.
add_action( 'before_delete_post', 'dql_author_xoa_cache' );

/* ===========================================================================
 * 3. Các khối của trang tác giả
 *
 * Gọi từ author.php.
 * ========================================================================= */

/**
 * Hàng nút "Tác giả trên mạng".
 *
 * Mạng xã hội quen thuộc in icon kèm tên; trên điện thoại tên ẩn đi (vẫn còn
 * cho trình đọc màn hình) để cả hàng gọn trong một dòng nút tròn. Link lạ — mục
 * "URL hồ sơ bổ sung" và "Trang web" — in TÊN MIỀN thay cho chữ "Trang web",
 * vì hai link cùng ghi "Trang web" thì người đọc không biết bấm cái nào.
 *
 * @param int $user_id ID tác giả.
 */
function dql_author_mang( $user_id ) {
	$mang = dql_author_links( $user_id );

	if ( ! $mang ) {
		return;
	}

	// Mạng xã hội trước, link lạ sau. Trên điện thoại hai nhóm có hình dạng khác
	// nhau (nút tròn và dòng dài); để xen kẽ thì hàng nút tròn bị cắt làm đôi.
	$mang = array_merge(
		array_filter(
			$mang,
			function ( $m ) {
				return 'web' !== $m['icon'];
			}
		),
		array_filter(
			$mang,
			function ( $m ) {
				return 'web' === $m['icon'];
			}
		)
	);
	?>
	<div class="dql-tglink">
		<p class="dql-tglink__nhan"><?php esc_html_e( 'Tác giả trên mạng', 'kadence-daquylucyen' ); ?></p>
		<ul class="dql-tglink__ds">
			<?php
			foreach ( $mang as $m ) :
				$la_web = 'web' === $m['icon'];
				$ten    = $la_web ? preg_replace( '/^www\./', '', (string) wp_parse_url( $m['url'], PHP_URL_HOST ) ) : $m['ten'];
				?>
				<li class="dql-tglink__muc<?php echo $la_web ? ' dql-tglink__muc--web' : ''; ?>">
					<a class="dql-tglink__a" href="<?php echo esc_url( $m['url'] ); ?>" target="_blank" rel="noopener me">
						<?php
						if ( $la_web ) {
							dql_the_icon( 'lien-ket' );
						} else {
							echo dql_social_icon( $m['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG cố định.
						}
						?>
						<span class="dql-tglink__ten"><?php echo esc_html( $ten ); ?></span>
						<?php dql_the_icon( 'ra-ngoai', array( 'class' => 'dql-tglink__ra', 'stroke' => 2 ) ); ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Hộp "Nguyên tắc khi viết" bên phải đầu trang.
 *
 * Trả về false khi không có gì để hiện, để đầu trang thu về một cột — cùng cách
 * với hộp "Bắt đầu từ đâu" của trang chuyên mục.
 *
 * @param int $user_id ID tác giả.
 * @return bool Đã in ra hộp hay chưa.
 */
function dql_author_nguyen_tac( $user_id ) {
	$dong = (string) get_user_meta( $user_id, DQL_TG_NGUYEN_TAC, true );
	$dong = array_values( array_filter( array_map( 'trim', explode( "\n", $dong ) ) ) );

	if ( ! $dong ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
		?>
		<aside class="dql-batdau dql-batdau--cho">
			<h2 class="dql-batdau__title"><?php esc_html_e( 'Chưa có nguyên tắc khi viết', 'kadence-daquylucyen' ); ?></h2>
			<p class="dql-batdau__hint">
				<?php esc_html_e( 'Mở Người dùng → Hồ sơ, điền ô “Nguyên tắc khi viết”, mỗi dòng một ý. Ô này chỉ người sửa được hồ sơ thấy.', 'kadence-daquylucyen' ); ?>
			</p>
		</aside>
		<?php

		return true;
	}
	?>
	<aside class="dql-batdau dql-tgnt">
		<h2 class="dql-batdau__title">
			<?php dql_the_icon( 'la-chan', array( 'stroke' => 1.8 ) ); ?>
			<?php esc_html_e( 'Nguyên tắc khi viết', 'kadence-daquylucyen' ); ?>
		</h2>
		<ul class="dql-tgnt__ds">
			<?php foreach ( $dong as $y ) : ?>
				<li><?php dql_the_icon( 'dung', array( 'stroke' => 2 ) ); ?><span><?php echo esc_html( $y ); ?></span></li>
			<?php endforeach; ?>
		</ul>
	</aside>
	<?php

	return true;
}

/**
 * Dải chip "Đã viết về" — đồng thời là bộ lọc danh sách bài bên dưới.
 *
 * Một dải làm hai việc chứ không phải hai dải: chip dẫn sang trang chuyên mục ở
 * đầu trang và một hàng nút lọc ở giữa trang sẽ cùng liệt kê đúng những chủ đề
 * đó, người đọc phải đoán vì sao bấm hai cái giống nhau lại ra hai chỗ khác nhau.
 *
 * @param WP_User      $user  Tác giả.
 * @param array        $stats Kết quả dql_author_stats().
 * @param WP_Term|null $dang  Chủ đề đang lọc.
 */
function dql_author_chips( $user, $stats, $dang ) {
	if ( count( $stats['muc'] ) < 2 ) {
		// Một chủ đề duy nhất thì lọc cũng ra đúng danh sách đang thấy.
		return;
	}

	$terms = get_terms(
		array(
			'taxonomy'   => 'category',
			'include'    => array_keys( $stats['muc'] ),
			'hide_empty' => false,
		)
	);

	if ( is_wp_error( $terms ) || ! $terms ) {
		return;
	}

	usort(
		$terms,
		function ( $a, $b ) use ( $stats ) {
			return $stats['muc'][ $b->term_id ] - $stats['muc'][ $a->term_id ];
		}
	);

	$goc = get_author_posts_url( $user->ID );
	?>
	<nav class="dql-archive-nav dql-tgchip" aria-label="<?php esc_attr_e( 'Lọc bài theo chủ đề', 'kadence-daquylucyen' ); ?>">
		<span class="dql-tgchip__nhan"><?php esc_html_e( 'Đã viết về', 'kadence-daquylucyen' ); ?></span>

		<a class="dql-chip<?php echo $dang ? '' : ' is-current'; ?>" href="<?php echo esc_url( $goc ); ?>"<?php echo $dang ? '' : ' aria-current="page"'; ?>>
			<span><?php esc_html_e( 'Tất cả', 'kadence-daquylucyen' ); ?></span>
			<span class="dql-chip__count"><?php echo esc_html( number_format_i18n( $stats['so_bai'] ) ); ?></span>
		</a>

		<?php
		foreach ( $terms as $term ) :
			$on = $dang && $dang->term_id === $term->term_id;
			?>
			<a
				class="dql-chip dql-chip--gem<?php echo $on ? ' is-current' : ''; ?>"
				href="<?php echo esc_url( add_query_arg( 'chu-de', $term->slug, $goc ) ); ?>"
				<?php echo $on ? ' aria-current="page"' : ''; ?>
			>
				<?php dql_the_category_badge( $term, 'nho' ); ?>
				<span><?php echo esc_html( $term->name ); ?></span>
				<span class="dql-chip__count"><?php echo esc_html( number_format_i18n( $stats['muc'][ $term->term_id ] ) ); ?></span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Đầu trang tác giả.
 *
 * Ảnh đại diện cùng cách với hộp tác giả cuối bài: Gravatar với ảnh mặc định
 * trong suốt đè lên chữ cái đầu tên — chưa có Gravatar thì lộ chữ cái, không có
 * hình người xám.
 *
 * @param WP_User      $user Tác giả.
 * @param WP_Term|null $dang Chủ đề đang lọc.
 */
function dql_author_head( $user, $dang ) {
	$id      = (int) $user->ID;
	$ten     = $user->display_name;
	$bio     = trim( (string) get_the_author_meta( 'description', $id ) );
	$vai     = trim( (string) get_the_author_meta( 'dql_chuc_danh', $id ) );
	$stats   = dql_author_stats( $id );
	$chu_dau = mb_strtoupper( mb_substr( wp_strip_all_tags( $ten ), 0, 1, 'UTF-8' ), 'UTF-8' );

	// Dựng hộp trước, in đầu trang sau: phải biết hộp có nội dung hay không mới
	// chọn được lớp bố cục.
	ob_start();
	$co_hop = dql_author_nguyen_tac( $id );
	$hop    = ob_get_clean();
	?>
	<header class="dql-chead dql-tghead dql-hat">
		<div class="dql-container">
			<div class="dql-chead__split<?php echo $co_hop ? '' : ' dql-chead__split--don'; ?>">
				<div class="dql-chead__chu">
					<?php
					dql_crumb_in(
						array(
							array(
								'ten' => __( 'Trang chủ', 'kadence-daquylucyen' ),
								'url' => home_url( '/' ),
							),
							array(
								'ten' => $ten,
								'url' => get_author_posts_url( $id ),
							),
						)
					);
					?>

					<div class="dql-tghead__ten">
						<span class="dql-tghead__anh">
							<span class="dql-tghead__chu" aria-hidden="true"><?php echo esc_html( $chu_dau ); ?></span>
							<?php
							echo get_avatar( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar tự thoát.
								$id,
								272,
								'blank',
								'',
								array(
									'class'         => 'dql-tghead__img',
									'force_display' => true,
								)
							);
							?>
						</span>
						<div>
							<p class="dql-kicker"><?php esc_html_e( 'Tác giả', 'kadence-daquylucyen' ); ?></p>
							<h1 class="dql-chead__title"><?php echo esc_html( $ten ); ?></h1>
							<?php if ( '' !== $vai ) : ?>
								<p class="dql-tghead__vai"><?php echo esc_html( $vai ); ?></p>
							<?php endif; ?>
						</div>
					</div>

					<?php if ( '' !== $bio ) : ?>
						<?php
						// WordPress đã lọc thẻ HTML khi lưu ô tiểu sử. wpautop() giữ
						// nguyên chỗ xuống dòng người viết gõ trong wp-admin.
						?>
						<div class="dql-chead__mo-ta dql-tghead__bio"><?php echo wp_kses_post( wpautop( $bio ) ); ?></div>
					<?php elseif ( current_user_can( 'edit_user', $id ) ) : ?>
						<p class="dql-tghead__cho">
							<?php esc_html_e( 'Chưa có tiểu sử. Điền ô “Thông tin tiểu sử” trong Người dùng → Hồ sơ. Dòng này chỉ người sửa được hồ sơ thấy.', 'kadence-daquylucyen' ); ?>
						</p>
					<?php endif; ?>

					<?php dql_author_mang( $id ); ?>

					<dl class="dql-tgso">
						<div class="dql-tgso__o">
							<dt><?php esc_html_e( 'bài viết', 'kadence-daquylucyen' ); ?></dt>
							<dd><?php echo esc_html( number_format_i18n( $stats['so_bai'] ) ); ?></dd>
						</div>
						<div class="dql-tgso__o">
							<dt><?php esc_html_e( 'chuyên mục', 'kadence-daquylucyen' ); ?></dt>
							<dd><?php echo esc_html( number_format_i18n( count( $stats['muc'] ) ) ); ?></dd>
						</div>
						<?php if ( $stats['cap_nhat'] ) : ?>
							<div class="dql-tgso__o">
								<dt><?php esc_html_e( 'cập nhật gần nhất', 'kadence-daquylucyen' ); ?></dt>
								<dd><?php echo esc_html( wp_date( 'd/m/Y', $stats['cap_nhat'] ) ); ?></dd>
							</div>
						<?php endif; ?>
					</dl>
				</div>

				<?php echo $hop; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Markup đã thoát ở dql_author_nguyen_tac(). ?>
			</div>

			<?php dql_author_chips( $user, $stats, $dang ); ?>
		</div>
	</header>
	<?php
}

/**
 * Ba thẻ "Đọc nhiều nhất" của tác giả.
 *
 * Chỉ ở trang 1 và khi chưa lọc: người đã lật trang hay đã chọn chủ đề đang
 * tìm một bài cụ thể, ba thẻ to chắn ngang là quấy rầy.
 *
 * @param int          $user_id ID tác giả.
 * @param WP_Term|null $dang    Chủ đề đang lọc.
 */
function dql_author_top_cards( $user_id, $dang ) {
	if ( $dang || max( 1, (int) get_query_var( 'paged' ) ) > 1 ) {
		return;
	}

	$ids = dql_author_top( $user_id );

	if ( ! $ids ) {
		return;
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

		return;
	}
	?>
	<p class="dql-kicker dql-archive__kicker"><?php esc_html_e( 'Đọc nhiều nhất của tác giả', 'kadence-daquylucyen' ); ?></p>
	<div class="dql-cards dql-cards--3">
		<?php
		while ( $q->have_posts() ) :
			$q->the_post();
			dql_archive_card( true );
		endwhile;
		?>
	</div>
	<?php
	wp_reset_postdata();
}

/**
 * Dải mời mở Từ điển ở cuối trang.
 *
 * Trang tác giả không chèn dải tra mệnh giữa danh sách như trang chuyên mục:
 * người vào đây đang xem một người viết những gì, không phải đang tìm đá hợp
 * mệnh. Từ điển thì hợp — bài của ai cũng dẫn thuật ngữ về đó.
 */
function dql_author_tu_dien() {
	$url = post_type_exists( 'dql_thuat_ngu' ) ? get_post_type_archive_link( 'dql_thuat_ngu' ) : '';

	if ( ! $url ) {
		return;
	}
	?>
	<aside class="dql-ar-cta dql-tg-tudien">
		<span class="dql-gem dql-gem--vua dql-gem--ruby dql-ar-cta__icon"><?php dql_the_icon( 'sach' ); ?></span>

		<div class="dql-ar-cta__chu">
			<p class="dql-ar-cta__title"><?php esc_html_e( 'Gặp một thuật ngữ lạ trong bài?', 'kadence-daquylucyen' ); ?></p>
			<p class="dql-ar-cta__desc">
				<?php esc_html_e( 'Từ điển đá quý giải nghĩa rutile, corundum, huyết bồ câu… bằng lời thường.', 'kadence-daquylucyen' ); ?>
			</p>
		</div>

		<a class="dql-ar-cta__nut" href="<?php echo esc_url( $url ); ?>">
			<?php esc_html_e( 'Mở Từ điển', 'kadence-daquylucyen' ); ?>
		</a>
	</aside>
	<?php
}

/* ===========================================================================
 * 4. Ô "Nguyên tắc khi viết" trong hồ sơ người dùng
 * ========================================================================= */

/**
 * In ô nhập.
 *
 * @param WP_User $user Người dùng đang sửa.
 */
function dql_author_field_nguyen_tac( $user ) {
	?>
	<h2><?php esc_html_e( 'Trang tác giả', 'kadence-daquylucyen' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="dql_nguyen_tac"><?php esc_html_e( 'Nguyên tắc khi viết', 'kadence-daquylucyen' ); ?></label></th>
			<td>
				<textarea name="dql_nguyen_tac" id="dql_nguyen_tac" rows="4" class="large-text"><?php echo esc_textarea( (string) get_user_meta( $user->ID, DQL_TG_NGUYEN_TAC, true ) ); ?></textarea>
				<p class="description">
					<?php esc_html_e( 'Mỗi dòng một ý, hiện thành hộp bên phải đầu trang tác giả. Ví dụ: “Không rao bán, không gắn liên hệ mua hàng trong bài.” Để trống thì hộp không hiện.', 'kadence-daquylucyen' ); ?>
					<br>
					<?php esc_html_e( 'Tiểu sử lấy từ ô “Thông tin tiểu sử” ở trên; các nút mạng xã hội lấy từ ô Trang web, Facebook, X và “URL hồ sơ bổ sung”.', 'kadence-daquylucyen' ); ?>
				</p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'dql_author_field_nguyen_tac' );
add_action( 'edit_user_profile', 'dql_author_field_nguyen_tac' );

/**
 * Lưu ô nhập. Nonce do form hồ sơ của WordPress kiểm (update-user_{id}).
 *
 * @param int $user_id ID người dùng.
 */
function dql_author_save_nguyen_tac( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['dql_nguyen_tac'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	$gia_tri = sanitize_textarea_field( wp_unslash( $_POST['dql_nguyen_tac'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

	if ( '' === trim( $gia_tri ) ) {
		delete_user_meta( $user_id, DQL_TG_NGUYEN_TAC );
	} else {
		update_user_meta( $user_id, DQL_TG_NGUYEN_TAC, $gia_tri );
	}
}
add_action( 'personal_options_update', 'dql_author_save_nguyen_tac' );
add_action( 'edit_user_profile_update', 'dql_author_save_nguyen_tac' );
