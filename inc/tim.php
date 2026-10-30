<?php
/**
 * Màn tìm kiếm mở từ nút kính lúp trên header.
 *
 * Thay ngăn kéo tìm kiếm toàn màn hình của Kadence (một ô trơ trọi giữa nền
 * đen) bằng bảng tìm kiếm theo artboard "10 · Màn tìm kiếm (điện thoại)" và
 * "11 · Màn tìm kiếm (desktop)" trên canvas Đá Thô, trang Bản 1:
 *
 *   - Vừa mở: chip "Người đọc hay tìm" (gõ trong Customizer), ba bài mới cập
 *     nhật và lối tắt tới chuyên mục. Người chưa biết tìm gì vẫn có chỗ để đi.
 *   - Đang gõ: gợi ý tức thời từ endpoint dql/v1/search (inc/search.php) —
 *     thuật ngữ Từ điển trước, bài viết sau, từ khóa được tô màu.
 *
 * Nút kính lúp vẫn là nút của Kadence (cả header desktop lẫn điện thoại);
 * assets/js/tim.js chặn cú bấm trước khi tới Kadence và mở bảng này. Không có
 * JavaScript thì bảng không mở được — nhưng khi đó ngăn kéo của Kadence cũng
 * không mở được, nên không mất gì; trang kết quả /?s= vẫn dùng như cũ.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Các cụm "Người đọc hay tìm".
 *
 * WordPress không ghi lại người đọc đã tìm gì, nên danh sách do chủ site gõ tay
 * trong Customizer, mỗi dòng một cụm. Để trống thì khối này không hiện.
 *
 * @return string[]
 */
function dql_tim_hay_tim() {
	$tho = (string) get_theme_mod( 'dql_tim_hay_tim', dql_tim_hay_tim_mac_dinh() );
	$ds  = array_filter( array_map( 'trim', preg_split( '/\R/', $tho ) ) );

	return array_slice( array_values( array_unique( $ds ) ), 0, 8 );
}

/**
 * Danh sách mặc định — những thứ site đã có bài.
 *
 * @return string
 */
function dql_tim_hay_tim_mac_dinh() {
	return implode( "\n", array( 'Ruby', 'Spinel', 'Sapphire', 'Phân biệt đá thật giả', 'Đá hợp mệnh' ) );
}

/**
 * Ô nhập danh sách trong Customizer, cùng mục "Giao diện Đá Thô".
 *
 * @param WP_Customize_Manager $wp_customize Đối tượng Customizer.
 */
function dql_tim_customize( $wp_customize ) {
	$wp_customize->add_setting(
		'dql_tim_hay_tim',
		array(
			'default'           => dql_tim_hay_tim_mac_dinh(),
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_tim_hay_tim',
		array(
			'label'       => __( 'Màn tìm kiếm: "Người đọc hay tìm"', 'kadence-daquylucyen' ),
			'description' => __( 'Mỗi dòng một cụm, tối đa 8. Hiện thành chip khi người đọc vừa mở ô tìm kiếm; bấm vào là tìm luôn. Để trống để ẩn.', 'kadence-daquylucyen' ),
			'section'     => 'dql_giao_dien',
			'type'        => 'textarea',
			'priority'    => 40,
		)
	);
}
add_action( 'customize_register', 'dql_tim_customize', 20 );

/**
 * Ba bài mới cập nhật cho trạng thái "vừa mở".
 *
 * @return WP_Post[]
 */
function dql_tim_bai_moi() {
	return get_posts(
		array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'numberposts'         => 3,
			'orderby'             => 'modified',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);
}

/**
 * Một dòng bài trong bảng tìm kiếm: ảnh vuông, chuyên mục, tiêu đề, ngày và
 * thời gian đọc. Cùng hình với dòng do tim.js dựng từ kết quả REST.
 *
 * @param WP_Post $p Bài viết.
 */
function dql_tim_dong_bai( $p ) {
	$cat = dql_post_main_category( $p );
	$mau = $cat ? dql_category_color_key( $cat ) : 'ruby';
	$anh = get_the_post_thumbnail(
		$p,
		'thumbnail',
		array(
			'alt'     => '',
			'loading' => 'lazy',
			'class'   => 'dql-tim__anh-img',
		)
	);
	?>
	<a class="dql-tim__bai" href="<?php echo esc_url( get_permalink( $p ) ); ?>" style="--dql-gem: var(--dql-da-<?php echo esc_attr( $mau ); ?>)">
		<span class="dql-tim__anh<?php echo $anh ? '' : ' dql-tim__anh--trong'; ?>" aria-hidden="true"><?php echo $anh ? $anh : dql_icon( 'da-quy', array( 'stroke' => 1.5 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Ảnh và icon do WordPress/theme dựng. ?></span>
		<span class="dql-tim__chu">
			<?php if ( $cat ) : ?>
				<span class="dql-tim__muc"><?php echo esc_html( $cat->name ); ?></span>
			<?php endif; ?>
			<span class="dql-tim__tieu-de"><?php echo esc_html( get_the_title( $p ) ); ?></span>
			<span class="dql-tim__meta">
				<?php
				printf(
					/* translators: 1: ngày cập nhật, 2: số phút đọc. */
					esc_html__( '%1$s · %2$d phút đọc', 'kadence-daquylucyen' ),
					esc_html( get_the_modified_date( 'd/m/Y', $p ) ),
					(int) dql_reading_time( $p )
				);
				?>
			</span>
		</span>
	</a>
	<?php
}

/**
 * In bảng tìm kiếm ở cuối trang, và gỡ ngăn kéo tìm kiếm của Kadence.
 *
 * Kadence gắn Kadence\search_modal vào wp_footer (ưu tiên 20) ngay lúc in nút
 * kính lúp. Hàm này chạy ở ưu tiên 19 nên gỡ được nó trước khi tới lượt.
 */
function dql_tim_render() {
	remove_action( 'wp_footer', 'Kadence\search_modal', 20 );

	$hay     = dql_tim_hay_tim();
	$bai     = dql_tim_bai_moi();
	$muc     = array_slice( dql_strip_terms(), 0, 6 );
	$nhan    = dql_strip_labels();
	$tu_dien = post_type_exists( 'dql_thuat_ngu' ) ? get_post_type_archive_link( 'dql_thuat_ngu' ) : '';
	$mui_ten = dql_icon( 'mui-ten', array( 'stroke' => 2 ) );

	// Tên site thường kèm khẩu hiệu ("Đá quý Lục Yên | Chuyên trang…"): chỉ
	// lấy phần trước dấu phân cách, không thì dòng tiêu đề dài hết bảng.
	$ten = trim( preg_split( '/\s+[|–—-]\s+/u', get_bloginfo( 'name' ) )[0] );
	?>
	<div id="dql-tim" class="dql-tim" role="dialog" aria-modal="true" aria-labelledby="dql-tim-ten" hidden>
		<div class="dql-tim__nen" data-dql-tim-dong></div>

		<div class="dql-tim__bang">
			<div class="dql-tim__dau">
				<p class="dql-tim__ten" id="dql-tim-ten">
					<span class="dql-tim__da" aria-hidden="true"></span>
					<?php
					/* translators: %s: tên site. */
					printf( esc_html__( 'Tìm trong %s', 'kadence-daquylucyen' ), esc_html( $ten ) );
					?>
				</p>
				<kbd class="dql-tim__phim dql-tim__chi-may">Esc</kbd>
				<button type="button" class="dql-tim__dong" data-dql-tim-dong aria-label="<?php esc_attr_e( 'Đóng tìm kiếm', 'kadence-daquylucyen' ); ?>">
					<?php dql_the_icon( 'dong', array( 'stroke' => 1.75 ) ); ?>
				</button>
			</div>

			<form class="dql-tim__form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
				<label class="screen-reader-text" for="dql-tim-nhap"><?php esc_html_e( 'Tìm bài viết và thuật ngữ', 'kadence-daquylucyen' ); ?></label>
				<div class="dql-tim__o">
					<?php dql_the_icon( 'tim', array( 'class' => 'dql-tim__kinh' ) ); ?>
					<input
						id="dql-tim-nhap"
						class="dql-tim__nhap"
						type="search"
						name="s"
						placeholder="<?php esc_attr_e( 'Tên đá, màu sắc, cách nhận biết…', 'kadence-daquylucyen' ); ?>"
						data-ngan="<?php esc_attr_e( 'Tên đá, cách nhận biết…', 'kadence-daquylucyen' ); ?>"
						autocomplete="off"
						autocapitalize="off"
						spellcheck="false"
						enterkeyhint="search"
						role="combobox"
						aria-autocomplete="list"
						aria-expanded="false"
						aria-controls="dql-tim-kq"
					>
					<button type="button" class="dql-tim__xoa" aria-label="<?php esc_attr_e( 'Xóa chữ đã gõ', 'kadence-daquylucyen' ); ?>" hidden>
						<?php dql_the_icon( 'dong', array( 'stroke' => 2.25 ) ); ?>
					</button>
					<button type="submit" class="dql-tim__gui"><?php esc_html_e( 'Tìm', 'kadence-daquylucyen' ); ?><?php echo $mui_ten; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon của theme. ?></button>
				</div>
				<button type="button" class="dql-tim__huy" data-dql-tim-dong><?php esc_html_e( 'Đóng', 'kadence-daquylucyen' ); ?></button>
			</form>

			<div class="dql-tim__than">
				<div class="dql-tim__goi" data-dql-tim-goi>
					<?php if ( $hay ) : ?>
						<section class="dql-tim__khoi dql-tim__khoi--hay">
							<h2 class="dql-tim__nhan"><?php esc_html_e( 'Người đọc hay tìm', 'kadence-daquylucyen' ); ?></h2>
							<div class="dql-tim__chips">
								<?php foreach ( $hay as $cum ) : ?>
									<a class="dql-tim__chip" href="<?php echo esc_url( add_query_arg( 's', rawurlencode( $cum ), home_url( '/' ) ) ); ?>" data-dql-tim-cum="<?php echo esc_attr( $cum ); ?>">
										<?php dql_the_icon( 'xu-huong', array( 'stroke' => 2 ) ); ?>
										<?php echo esc_html( $cum ); ?>
									</a>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>

					<div class="dql-tim__luoi">
						<?php if ( $bai ) : ?>
							<section class="dql-tim__khoi dql-tim__khoi--bai">
								<h2 class="dql-tim__nhan"><?php esc_html_e( 'Bài mới cập nhật', 'kadence-daquylucyen' ); ?></h2>
								<?php
								foreach ( $bai as $p ) {
									dql_tim_dong_bai( $p );
								}
								?>
							</section>
						<?php endif; ?>

						<?php if ( $muc ) : ?>
							<section class="dql-tim__khoi dql-tim__khoi--muc">
								<h2 class="dql-tim__nhan"><?php esc_html_e( 'Đi thẳng tới chuyên mục', 'kadence-daquylucyen' ); ?></h2>
								<div class="dql-tim__cmuc">
									<?php foreach ( $muc as $t ) : ?>
										<a class="dql-tim__cm" href="<?php echo esc_url( get_category_link( $t->term_id ) ); ?>">
											<?php dql_the_category_badge( $t, 'nho' ); ?>
											<span class="dql-tim__cm-ten"><?php echo esc_html( $nhan[ $t->slug ] ?? $t->name ); ?></span>
											<span class="dql-tim__cm-so"><?php echo esc_html( number_format_i18n( $t->count ) ); ?></span>
										</a>
									<?php endforeach; ?>
								</div>
							</section>
						<?php endif; ?>
					</div>
				</div>

				<div class="dql-tim__kq" id="dql-tim-kq" role="listbox" aria-labelledby="dql-tim-ten" hidden></div>
			</div>

			<div class="dql-tim__chan">
				<span class="dql-tim__goi-y-phim dql-tim__chi-may" aria-hidden="true">
					<span><kbd class="dql-tim__phim">↵</kbd><?php esc_html_e( 'mở', 'kadence-daquylucyen' ); ?></span>
					<span><kbd class="dql-tim__phim">↑</kbd><kbd class="dql-tim__phim">↓</kbd><?php esc_html_e( 'chọn', 'kadence-daquylucyen' ); ?></span>
					<span><kbd class="dql-tim__phim">Esc</kbd><?php esc_html_e( 'đóng', 'kadence-daquylucyen' ); ?></span>
				</span>
				<?php if ( $tu_dien ) : ?>
					<a class="dql-tim__tu-dien" href="<?php echo esc_url( $tu_dien ); ?>"><?php esc_html_e( 'Tra thuật ngữ trong Từ điển', 'kadence-daquylucyen' ); ?><?php echo $mui_ten; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Icon của theme. ?></a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'dql_tim_render', 19 );

/**
 * Nạp CSS và JS của màn tìm kiếm — ở mọi trang, vì nút kính lúp ở mọi trang.
 */
function dql_tim_assets() {
	wp_enqueue_style(
		'dql-tim',
		DQL_URI . '/assets/css/tim.css',
		array( 'dql-main' ),
		dql_asset_version( 'assets/css/tim.css' )
	);

	wp_enqueue_script(
		'dql-tim',
		DQL_URI . '/assets/js/tim.js',
		array(),
		dql_asset_version( 'assets/js/tim.js' ),
		true
	);

	wp_localize_script(
		'dql-tim',
		'dqlTim',
		array(
			'endpoint' => esc_url_raw( rest_url( 'dql/v1/search' ) ),
			'min'      => DQL_SEARCH_MIN,
			'icon'     => array(
				'da'     => dql_icon( 'da-quy', array( 'stroke' => 1.5 ) ),
				'sach'   => dql_icon( 'sach' ),
				'muiTen' => dql_icon( 'mui-ten', array( 'stroke' => 2 ) ),
			),
			'chuoi'    => array(
				'thuatNgu'  => __( 'Thuật ngữ', 'kadence-daquylucyen' ),
				'tuDien'    => __( 'Từ điển', 'kadence-daquylucyen' ),
				'baiViet'   => __( 'Bài viết', 'kadence-daquylucyen' ),
				/* translators: %d: số bài khớp. */
				'soKq'      => __( '%d kết quả', 'kadence-daquylucyen' ),
				/* translators: 1: số bài khớp, 2: chữ người đọc gõ. */
				'xemHet'    => __( 'Xem cả %1$d kết quả cho “%2$s”', 'kadence-daquylucyen' ),
				/* translators: %s: chữ người đọc gõ. */
				'khongThay' => __( 'Chưa thấy bài nào khớp “%s”. Thử tên đá khác, hoặc bấm Tìm để tìm trong toàn bộ nội dung.', 'kadence-daquylucyen' ),
				/* translators: %d: số phút đọc. */
				'phutDoc'   => __( '%d phút đọc', 'kadence-daquylucyen' ),
			),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'dql_tim_assets', 20 );
