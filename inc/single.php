<?php
/**
 * Trang bài viết theo bản 1: đầu bài → ảnh bìa → thân bài có cột lề trái.
 *
 * Bố cục do theme tự dựng ở template-parts/content/single-entry.php, không đi
 * theo Customizer của Kadence. Lý do: production đã lưu sẵn bộ tùy chọn cũ
 * (ảnh bìa nằm TRÊN tiêu đề, khung bài dạng hộp, khổ hẹp) trong theme mod, nên
 * đặt giá trị mặc định ở inc/brand.php không có tác dụng ở đó. Ép bằng bộ lọc
 * kadence_post_layout thì local và production chạy cùng một bố cục.
 *
 * File này lo phần dữ liệu và hành vi; markup nằm ở template.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Ép bố cục bài viết: tràn ngang, không hộp, không cột bên, không ảnh bìa
 * của Kadence (template tự in ảnh bìa sau đầu bài).
 *
 * Tràn ngang để từng dải tự chọn khổ: đầu bài 800, ảnh bìa 1000, thân bài
 * 220 + 720, dải "Đọc tiếp" nền trắng chạm hai mép.
 *
 * @param array $layout Bố cục Kadence tính ra.
 * @return array
 */
function dql_single_layout( $layout ) {
	if ( ! is_singular( 'post' ) ) {
		return $layout;
	}

	$layout['layout']   = 'fullwidth';
	$layout['boxed']    = 'unboxed';
	$layout['feature']  = 'hide';
	$layout['title']    = 'normal';
	$layout['sidebar']  = 'disable';
	$layout['vpadding'] = 'hide';

	return $layout;
}
add_filter( 'kadence_post_layout', 'dql_single_layout', 20 );

// Mục lục và thời gian đọc giờ do template in ở đúng chỗ của bản 1.
remove_action( 'kadence_single_before_inner_content', 'dql_toc_render' );
remove_action( 'kadence_single_before_entry_content', 'dql_single_reading_time' );

/**
 * Đường dẫn đầu bài: Trang chủ › chuyên mục cha › chuyên mục.
 *
 * Dừng ở chuyên mục, không lặp lại tiêu đề bài: tiêu đề nằm ngay bên dưới, cỡ
 * 44px. Không in schema — Rank Math đã in BreadcrumbList cho bài viết.
 *
 * @param WP_Term|null $cat Chuyên mục chính của bài.
 */
function dql_single_breadcrumb( $cat ) {
	$muc = array(
		array(
			'ten' => __( 'Trang chủ', 'kadence-daquylucyen' ),
			'url' => home_url( '/' ),
		),
	);

	if ( $cat ) {
		foreach ( array_reverse( get_ancestors( $cat->term_id, 'category' ) ) as $cha_id ) {
			$cha = get_term( $cha_id, 'category' );

			if ( $cha && ! is_wp_error( $cha ) ) {
				$muc[] = array(
					'ten' => $cha->name,
					'url' => get_category_link( $cha->term_id ),
				);
			}
		}

		$muc[] = array(
			'ten' => $cat->name,
			'url' => get_category_link( $cat->term_id ),
		);
	}
	?>
	<nav class="dql-crumb dql-bai__crumb" aria-label="<?php esc_attr_e( 'Đường dẫn', 'kadence-daquylucyen' ); ?>">
		<?php foreach ( $muc as $i => $item ) : ?>
			<?php if ( $i ) : ?>
				<?php dql_the_icon( 'phai', array( 'stroke' => 2 ) ); ?>
			<?php endif; ?>
			<a href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['ten'] ); ?></a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Thuật ngữ từ điển có mặt trong bài, theo thứ tự xuất hiện.
 *
 * Dùng đúng chỉ mục của plugin dql-core (chữ hoa thường, từ đồng nghĩa), nên
 * danh sách khớp với các chữ được gạch chân trong thân bài. Chống trùng theo ID
 * thuật ngữ: "độ cứng Mohs" và "Mohs" là một mục.
 *
 * @param int $post_id ID bài viết.
 * @param int $toi_da  Số thuật ngữ tối đa.
 * @return array[] Mỗi phần tử gồm tu, link.
 */
function dql_single_glossary_terms( $post_id, $toi_da = 6 ) {
	static $cache = array();

	if ( isset( $cache[ $post_id ] ) ) {
		return $cache[ $post_id ];
	}

	$cache[ $post_id ] = array();

	if ( ! class_exists( 'DQL_Glossary' ) ) {
		return $cache[ $post_id ];
	}

	$chi_muc = DQL_Glossary::chi_muc();
	$post    = get_post( $post_id );

	if ( ! $chi_muc || ! $post ) {
		return $cache[ $post_id ];
	}

	$cach_viet = array_keys( $chi_muc );
	usort(
		$cach_viet,
		function ( $a, $b ) {
			return mb_strlen( $b, 'UTF-8' ) - mb_strlen( $a, 'UTF-8' );
		}
	);

	$mau = '/(?<![\p{L}\p{N}])('
		. implode( '|', array_map( 'preg_quote', $cach_viet ) )
		. ')(?![\p{L}\p{N}])/iu';

	$text = wp_strip_all_tags( strip_shortcodes( $post->post_content ) );

	if ( ! preg_match_all( $mau, $text, $m ) ) {
		return $cache[ $post_id ];
	}

	foreach ( $m[1] as $tu ) {
		$khoa = mb_strtolower( $tu, 'UTF-8' );

		if ( ! isset( $chi_muc[ $khoa ] ) ) {
			continue;
		}

		$tt = $chi_muc[ $khoa ];

		if ( ! isset( $cache[ $post_id ][ $tt['id'] ] ) ) {
			$cache[ $post_id ][ $tt['id'] ] = array(
				'tu'   => $tt['tu'],
				'link' => $tt['link'],
			);
		}

		if ( count( $cache[ $post_id ] ) >= $toi_da ) {
			break;
		}
	}

	return $cache[ $post_id ];
}

/**
 * Hộp "Thuật ngữ trong bài" ở cột lề trái.
 */
function dql_single_glossary_box() {
	$terms = dql_single_glossary_terms( get_the_ID() );

	if ( ! $terms ) {
		return;
	}
	?>
	<div class="dql-bai__tn">
		<p class="dql-bai__tn-tieu-de"><?php esc_html_e( 'Thuật ngữ trong bài', 'kadence-daquylucyen' ); ?></p>
		<ul class="dql-bai__tn-ds">
			<?php foreach ( $terms as $t ) : ?>
				<li><a href="<?php echo esc_url( $t['link'] ); ?>"><?php echo esc_html( $t['tu'] ); ?></a></li>
			<?php endforeach; ?>
		</ul>
	</div>
	<?php
}

/**
 * Thẻ cuối bài: "Thẻ:" rồi các chip, viền trên.
 */
function dql_single_tags() {
	$tags = get_the_tags();

	if ( ! $tags || is_wp_error( $tags ) ) {
		return;
	}
	?>
	<div class="dql-bai__the">
		<span class="dql-bai__the-nhan"><?php esc_html_e( 'Thẻ:', 'kadence-daquylucyen' ); ?></span>
		<?php foreach ( $tags as $tag ) : ?>
			<a class="dql-bai__the-chip" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" rel="tag"><?php echo esc_html( $tag->name ); ?></a>
		<?php endforeach; ?>
	</div>
	<?php
}

/**
 * Liên kết mạng xã hội của tác giả, lấy từ hồ sơ người dùng.
 *
 * Đọc cả trường của WordPress (Trang web), trường Rank Math thêm vào hồ sơ
 * (facebook, twitter, additional_profile_urls). Trường nào trống thì không in.
 *
 * @param int $user_id ID tác giả.
 * @return array[] Mỗi phần tử gồm url, ten, icon.
 */
function dql_author_links( $user_id ) {
	$urls = array();

	foreach ( array( 'user_url', 'facebook', 'twitter' ) as $khoa ) {
		$gia_tri = trim( (string) get_the_author_meta( $khoa, $user_id ) );

		if ( 'twitter' === $khoa && '' !== $gia_tri && ! preg_match( '#^https?://#i', $gia_tri ) ) {
			$gia_tri = 'https://x.com/' . ltrim( $gia_tri, '@' );
		}

		if ( '' !== $gia_tri ) {
			$urls[] = $gia_tri;
		}
	}

	$them = (string) get_the_author_meta( 'additional_profile_urls', $user_id );

	foreach ( preg_split( '/\s+/', $them ) as $u ) {
		if ( '' !== $u ) {
			$urls[] = $u;
		}
	}

	$mang = array(
		'facebook.com'  => array( 'Facebook', 'facebook' ),
		'x.com'         => array( 'X', 'x' ),
		'twitter.com'   => array( 'X', 'x' ),
		'linkedin.com'  => array( 'LinkedIn', 'linkedin' ),
		'instagram.com' => array( 'Instagram', 'instagram' ),
		'youtube.com'   => array( 'YouTube', 'youtube' ),
		'tiktok.com'    => array( 'TikTok', 'tiktok' ),
		'pinterest.com' => array( 'Pinterest', 'pinterest' ),
	);

	$ra = array();

	foreach ( array_unique( $urls ) as $u ) {
		$u = esc_url_raw( $u );

		if ( ! $u ) {
			continue;
		}

		$host = preg_replace( '/^www\./', '', (string) wp_parse_url( $u, PHP_URL_HOST ) );
		$loai = isset( $mang[ $host ] ) ? $mang[ $host ] : array( __( 'Trang web', 'kadence-daquylucyen' ), 'web' );

		$ra[] = array(
			'url'  => $u,
			'ten'  => $loai[0],
			'icon' => $loai[1],
		);
	}

	return $ra;
}

/**
 * Icon mạng xã hội (dạng khối, theo logo gốc của từng mạng).
 *
 * @param string $slug Tên icon.
 * @return string SVG.
 */
function dql_social_icon( $slug ) {
	$path = array(
		'facebook'  => '<path d="M13.5 21v-7.5h2.6l.4-3h-3V8.6c0-.9.3-1.5 1.5-1.5h1.6V4.4c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 3.9v2.3H7.9v3h2.6V21h3Z"/>',
		'x'         => '<path d="M17.8 3h3l-6.6 7.6L22 21h-6.1l-4.8-6.3L5.6 21h-3l7.1-8.1L2 3h6.2l4.3 5.7L17.8 3Zm-1 16.2h1.7L7.3 4.7H5.5l11.3 14.5Z"/>',
		'linkedin'  => '<path d="M6.9 8.8H3.6V20h3.3V8.8ZM5.2 3.5a1.9 1.9 0 1 0 0 3.8 1.9 1.9 0 0 0 0-3.8ZM20.4 13.8c0-3-1.6-5.2-4.4-5.2-1.4 0-2.4.8-2.8 1.5V8.8H10V20h3.3v-5.6c0-1.5.3-2.9 2.1-2.9 1.8 0 1.8 1.7 1.8 3V20h3.3l-.1-6.2Z"/>',
		'instagram' => '<path d="M12 7.3a4.7 4.7 0 1 0 0 9.4 4.7 4.7 0 0 0 0-9.4Zm0 7.7a3 3 0 1 1 0-6 3 3 0 0 1 0 6Zm4.9-8.9a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2ZM12 3c-2.4 0-2.7 0-3.7.1-3.4.2-5 1.8-5.2 5.2C3 9.3 3 9.6 3 12s0 2.7.1 3.7c.2 3.4 1.8 5 5.2 5.2 1 .1 1.3.1 3.7.1s2.7 0 3.7-.1c3.4-.2 5-1.8 5.2-5.2.1-1 .1-1.3.1-3.7s0-2.7-.1-3.7c-.2-3.4-1.8-5-5.2-5.2C14.7 3 14.4 3 12 3Zm0 1.6c2.4 0 2.6 0 3.6.1 2.4.1 3.6 1.3 3.7 3.7.1 1 .1 1.2.1 3.6s0 2.6-.1 3.6c-.1 2.4-1.3 3.6-3.7 3.7-1 .1-1.2.1-3.6.1s-2.6 0-3.6-.1c-2.4-.1-3.6-1.3-3.7-3.7-.1-1-.1-1.2-.1-3.6s0-2.6.1-3.6c.1-2.4 1.3-3.6 3.7-3.7 1-.1 1.2-.1 3.6-.1Z"/>',
		'youtube'   => '<path d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8ZM10 15V9l5.2 3L10 15Z"/>',
		'tiktok'    => '<path d="M16.6 5.8A4.3 4.3 0 0 1 15.5 3h-3.1v12.4a2.6 2.6 0 1 1-1.8-2.5V9.8a5.7 5.7 0 1 0 4.9 5.6V9.1a7.3 7.3 0 0 0 4.3 1.4V7.4a4.3 4.3 0 0 1-3.2-1.6Z"/>',
		'pinterest' => '<path d="M12 2a10 10 0 0 0-3.6 19.3c-.1-.8-.2-2 0-2.9l1.2-5s-.3-.6-.3-1.5c0-1.4.8-2.4 1.8-2.4.9 0 1.3.6 1.3 1.4 0 .9-.5 2.1-.8 3.3-.2 1 .5 1.8 1.5 1.8 1.8 0 3.1-1.9 3.1-4.6 0-2.4-1.7-4.1-4.2-4.1-2.9 0-4.5 2.1-4.5 4.4 0 .9.3 1.8.8 2.3l.1.4-.3 1.1c0 .2-.2.2-.4.1-1.3-.6-2.1-2.5-2.1-4 0-3.3 2.4-6.3 6.9-6.3 3.6 0 6.4 2.6 6.4 6 0 3.6-2.3 6.5-5.4 6.5-1.1 0-2.1-.6-2.4-1.2l-.7 2.5c-.2.9-.9 2.1-1.3 2.8A10 10 0 1 0 12 2Z"/>',
		'web'       => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm6.9 6h-2.9a15.6 15.6 0 0 0-1.4-3.6A8 8 0 0 1 18.9 8ZM12 4c.8 1.2 1.5 2.5 1.9 4h-3.8c.4-1.4 1.1-2.8 1.9-4ZM4.3 14a8.2 8.2 0 0 1 0-4h3.4a16.5 16.5 0 0 0 0 4H4.3Zm.8 2h2.9c.3 1.3.8 2.5 1.4 3.6A8 8 0 0 1 5.1 16ZM8 8H5.1a8 8 0 0 1 4.3-3.6C8.8 5.5 8.3 6.7 8 8Zm4 12c-.8-1.2-1.5-2.5-1.9-4h3.8c-.4 1.4-1.1 2.8-1.9 4Zm2.3-6H9.7a14.7 14.7 0 0 1 0-4h4.6a14.7 14.7 0 0 1 0 4Zm.3 5.6c.6-1.1 1.1-2.3 1.4-3.6h2.9a8 8 0 0 1-4.3 3.6Zm1.7-5.6a16.5 16.5 0 0 0 0-4h3.4a8.2 8.2 0 0 1 0 4h-3.4Z"/>',
	);

	return '<svg class="dql-icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" focusable="false">'
		. ( isset( $path[ $slug ] ) ? $path[ $slug ] : $path['web'] )
		. '</svg>';
}

/**
 * Hộp tác giả cuối bài.
 *
 * Ảnh đại diện lấy từ Gravatar với ảnh mặc định TRONG SUỐT ('blank'): tác giả
 * chưa có Gravatar thì ảnh trong suốt để lộ chữ cái đầu tên vẽ bên dưới, thay
 * cho hình người xám của Gravatar. Không cần gọi mạng để kiểm tra trước.
 *
 * Tiểu sử để trống (site đã bỏ bio cá nhân) thì in một câu thật về tác giả:
 * số bài đã viết cho site.
 */
function dql_single_author_box() {
	$id = (int) get_the_author_meta( 'ID' );

	if ( ! $id ) {
		return;
	}

	$ten     = get_the_author_meta( 'display_name', $id );
	$bio     = trim( (string) get_the_author_meta( 'description', $id ) );
	$vai     = trim( (string) get_the_author_meta( 'dql_chuc_danh', $id ) );
	$so_bai  = (int) count_user_posts( $id, 'post', true );
	$link    = get_author_posts_url( $id );
	$mang    = dql_author_links( $id );
	$chu_dau = mb_strtoupper( mb_substr( wp_strip_all_tags( $ten ), 0, 1, 'UTF-8' ), 'UTF-8' );

	if ( '' === $vai ) {
		$vai = __( 'Tác giả', 'kadence-daquylucyen' );
	}
	?>
	<section class="dql-tac-gia" aria-label="<?php esc_attr_e( 'Về tác giả', 'kadence-daquylucyen' ); ?>">
		<a class="dql-tac-gia__anh" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
			<span class="dql-tac-gia__chu"><?php echo esc_html( $chu_dau ); ?></span>
			<?php
			echo get_avatar( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_avatar tự thoát.
				$id,
				256,
				'blank',
				'',
				array(
					'class'         => 'dql-tac-gia__img',
					'force_display' => true,
				)
			);
			?>
		</a>
		<div class="dql-tac-gia__than">
			<p class="dql-tac-gia__dau">
				<a class="dql-tac-gia__ten" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $ten ); ?></a>
				<span class="dql-tac-gia__vai"><?php echo esc_html( $vai ); ?></span>
			</p>
			<p class="dql-tac-gia__bio">
				<?php
				if ( '' !== $bio ) {
					echo wp_kses_post( $bio );
				} else {
					printf(
						/* translators: 1: tên tác giả, 2: số bài, 3: tên site. */
						esc_html__( '%1$s đã viết %2$s bài cho %3$s — từ nhận biết đá thật giả tới phong thủy và câu chuyện vùng đá Lục Yên.', 'kadence-daquylucyen' ),
						esc_html( $ten ),
						esc_html( number_format_i18n( $so_bai ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
			</p>
			<div class="dql-tac-gia__chan">
				<?php if ( $mang ) : ?>
					<ul class="dql-tac-gia__mxh">
						<?php foreach ( $mang as $m ) : ?>
							<li>
								<a href="<?php echo esc_url( $m['url'] ); ?>" target="_blank" rel="noopener me" aria-label="<?php echo esc_attr( $m['ten'] ); ?>">
									<?php echo dql_social_icon( $m['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG cố định. ?>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				<a class="dql-tac-gia__tat-ca" href="<?php echo esc_url( $link ); ?>">
					<?php
					printf(
						/* translators: %s: số bài. */
						esc_html__( 'Xem %s bài viết', 'kadence-daquylucyen' ),
						esc_html( number_format_i18n( $so_bai ) )
					);
					dql_the_icon( 'tiep', array( 'stroke' => 2 ) );
					?>
				</a>
			</div>
		</div>
	</section>
	<?php
}

/**
 * Bài trước / bài sau: thẻ có ảnh nhỏ, nhãn tiếng Việt.
 *
 * Thay cho the_post_navigation của Kadence (nhãn "Previous"/"Next" tiếng Anh,
 * không ảnh).
 */
function dql_single_post_nav() {
	$ben = array(
		'truoc' => get_previous_post(),
		'sau'   => get_next_post(),
	);

	if ( ! $ben['truoc'] && ! $ben['sau'] ) {
		return;
	}
	?>
	<nav class="dql-dh" aria-label="<?php esc_attr_e( 'Bài liền kề', 'kadence-daquylucyen' ); ?>">
		<?php foreach ( $ben as $huong => $bai ) : ?>
			<?php if ( ! $bai ) : ?>
				<span class="dql-dh__trong" aria-hidden="true"></span>
				<?php continue; ?>
			<?php endif; ?>
			<a class="dql-dh__the dql-dh__the--<?php echo esc_attr( $huong ); ?>" href="<?php echo esc_url( get_permalink( $bai ) ); ?>" rel="<?php echo 'truoc' === $huong ? 'prev' : 'next'; ?>">
				<?php if ( has_post_thumbnail( $bai ) ) : ?>
					<span class="dql-dh__anh"><?php echo get_the_post_thumbnail( $bai, 'thumbnail', array( 'alt' => '', 'loading' => 'lazy' ) ); ?></span>
				<?php endif; ?>
				<span class="dql-dh__chu">
					<span class="dql-dh__nhan">
						<?php if ( 'truoc' === $huong ) : ?>
							<?php dql_the_icon( 'trai', array( 'stroke' => 2 ) ); ?><?php esc_html_e( 'Bài trước', 'kadence-daquylucyen' ); ?>
						<?php else : ?>
							<?php esc_html_e( 'Bài sau', 'kadence-daquylucyen' ); ?><?php dql_the_icon( 'phai', array( 'stroke' => 2 ) ); ?>
						<?php endif; ?>
					</span>
					<span class="dql-dh__ten"><?php echo esc_html( get_the_title( $bai ) ); ?></span>
				</span>
			</a>
		<?php endforeach; ?>
	</nav>
	<?php
}

/**
 * Ô "Chức danh" trong hồ sơ người dùng — chữ trên nhãn cạnh tên tác giả.
 *
 * @param WP_User $user Người dùng đang sửa.
 */
function dql_user_chuc_danh_field( $user ) {
	?>
	<h2><?php esc_html_e( 'Hộp tác giả cuối bài', 'kadence-daquylucyen' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="dql_chuc_danh"><?php esc_html_e( 'Chức danh', 'kadence-daquylucyen' ); ?></label></th>
			<td>
				<input type="text" name="dql_chuc_danh" id="dql_chuc_danh" class="regular-text" value="<?php echo esc_attr( (string) get_user_meta( $user->ID, 'dql_chuc_danh', true ) ); ?>">
				<p class="description"><?php esc_html_e( 'Ví dụ: Biên tập viên, Thợ chế tác đá. Để trống thì hiện "Tác giả".', 'kadence-daquylucyen' ); ?></p>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'dql_user_chuc_danh_field' );
add_action( 'edit_user_profile', 'dql_user_chuc_danh_field' );

/**
 * Lưu ô "Chức danh". Nonce do form hồ sơ của WordPress kiểm (update-user_{id}).
 *
 * @param int $user_id ID người dùng.
 */
function dql_user_chuc_danh_save( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! isset( $_POST['dql_chuc_danh'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
		return;
	}

	update_user_meta( $user_id, 'dql_chuc_danh', sanitize_text_field( wp_unslash( $_POST['dql_chuc_danh'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
}
add_action( 'personal_options_update', 'dql_user_chuc_danh_save' );
add_action( 'edit_user_profile_update', 'dql_user_chuc_danh_save' );
