<?php
/**
 * Cột bên — trang chủ và trang bài viết dựng riêng.
 *
 * Vấn đề của bản trước: cả ba nơi (trang chủ, bài viết, chuyên mục) dùng chung
 * MỘT vùng widget `sidebar-primary`. Đặt widget "Bài viết mới nhất" vào đó thì
 * ở trang chủ nó nằm ngay cạnh khối "Bài mới nhất" — cùng một danh sách in hai
 * lần, cách nhau 3cm. Và vì chỉ có một vùng nên không thể cho trang chủ và trang
 * bài hai nội dung khác nhau.
 *
 * Cách làm ở đây: mỗi trang một cột bên, dựng từ các KHỐI do theme lo sẵn, xen
 * giữa là các vùng widget để đặt quảng cáo và nội dung tự do.
 *
 *   Trang chủ  — người mới, chưa biết site có gì → dẫn đi khám phá
 *     quảng cáo · khám phá theo chủ đề · đăng ký email · từ điển · ô tự do
 *
 *   Bài viết   — đang đọc, đã quan tâm chủ đề → giữ lại và chuyển đổi
 *     quảng cáo · bài cùng chuyên mục · đăng ký email · tra mệnh · ô tự do ·
 *     quảng cáo dính theo cuộn
 *
 * Không khối nào lặp lại nội dung của cột chính bên cạnh nó. Đó là ràng buộc
 * chính khi chọn các khối này.
 *
 * Bật/tắt từng khối trong Customizer → Cột bên.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/* ===========================================================================
 * 1. Vùng widget
 * ========================================================================= */

/**
 * Đăng ký các vùng widget của cột bên.
 *
 * Ba vùng quảng cáo và hai ô tự do. Vùng nào không có widget thì không in ra gì
 * — giống các vùng quảng cáo trong inc/footer.php.
 */
function dql_register_sidebar_areas() {
	$khung_qc = array(
		'before_widget' => '<div class="dql-ad__item %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="dql-ad__title">',
		'after_title'   => '</p>',
	);

	$khung_tu_do = array(
		'before_widget' => '<section id="%1$s" class="widget dql-sb-card %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="dql-sb-card__title">',
		'after_title'   => '</h2>',
	);

	register_sidebar(
		array_merge(
			$khung_qc,
			array(
				'name'        => __( 'Quảng cáo — cột bên trang chủ', 'kadence-daquylucyen' ),
				'id'          => 'dql-sb-home-ad',
				'description' => __( 'Ô đầu tiên trong cột bên trang chủ. Vị trí dễ thấy nhất của cột.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$khung_qc,
			array(
				'name'        => __( 'Quảng cáo — cột bên bài viết (trên)', 'kadence-daquylucyen' ),
				'id'          => 'dql-sb-post-ad',
				'description' => __( 'Ô đầu tiên trong cột bên bài viết, ngang tầm đoạn mở bài.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$khung_qc,
			array(
				'name'        => __( 'Quảng cáo — cột bên bài viết (dưới, dính)', 'kadence-daquylucyen' ),
				'id'          => 'dql-sb-post-ad-cuoi',
				'description' => __( 'Ô cuối cột bên bài viết. Dính lại khi cuộn nên theo người đọc suốt bài dài.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$khung_tu_do,
			array(
				'name'        => __( 'Cột bên trang chủ — ô tự do', 'kadence-daquylucyen' ),
				'id'          => 'dql-sb-home',
				'description' => __( 'Nằm cuối cột bên trang chủ. Đặt gì cũng được: banner, liên kết, khối HTML.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$khung_tu_do,
			array(
				'name'        => __( 'Cột bên bài viết — ô tự do', 'kadence-daquylucyen' ),
				'id'          => 'dql-sb-post',
				'description' => __( 'Nằm trước ô quảng cáo dính ở cuối cột bên bài viết.', 'kadence-daquylucyen' ),
			)
		)
	);
}
add_action( 'widgets_init', 'dql_register_sidebar_areas' );

/* ===========================================================================
 * 2. Tùy chọn
 * ========================================================================= */

/**
 * Danh sách khối bật/tắt được, kèm mặc định.
 *
 * Gom vào một hàm để phần đăng ký Customizer và phần đọc giá trị dùng chung một
 * nguồn — cùng lý do như dql_home_settings().
 *
 * @return array
 */
function dql_sidebar_modules() {
	return array(
		'dql_sb_home_doc_nhieu'  => array(
			'default' => true,
			'label'   => __( 'Trang chủ — Đọc nhiều nhất', 'kadence-daquylucyen' ),
		),
		'dql_sb_home_chuyen_muc' => array(
			'default' => true,
			'label'   => __( 'Trang chủ — Khám phá theo chủ đề', 'kadence-daquylucyen' ),
		),
		'dql_sb_home_newsletter' => array(
			'default' => true,
			'label'   => __( 'Trang chủ — Đăng ký nhận bài', 'kadence-daquylucyen' ),
		),
		'dql_sb_home_tu_dien'    => array(
			'default' => true,
			'label'   => __( 'Trang chủ — Từ điển đá quý', 'kadence-daquylucyen' ),
		),
		'dql_sb_post_lien_quan'  => array(
			'default' => true,
			'label'   => __( 'Bài viết — Bài cùng chuyên mục', 'kadence-daquylucyen' ),
		),
		'dql_sb_post_newsletter' => array(
			'default' => true,
			'label'   => __( 'Bài viết — Đăng ký nhận bài', 'kadence-daquylucyen' ),
		),
		'dql_sb_post_menh'       => array(
			'default' => true,
			'label'   => __( 'Bài viết — Ô tra đá hợp mệnh', 'kadence-daquylucyen' ),
		),
		'dql_sb_archive_top'     => array(
			'default' => true,
			'label'   => __( 'Chuyên mục — Đọc nhiều nhất', 'kadence-daquylucyen' ),
		),
	);
}

/**
 * Khối này có được bật không.
 *
 * @param string $key Khóa khối.
 * @return bool
 */
function dql_sidebar_on( $key ) {
	$modules = dql_sidebar_modules();
	$default = isset( $modules[ $key ]['default'] ) ? $modules[ $key ]['default'] : true;

	return (bool) get_theme_mod( $key, $default );
}

/**
 * Chữ hiển thị trong ô đăng ký email ở cột bên.
 *
 * Tách khỏi khối đăng ký lớn ở trang chủ vì cột bên chỉ rộng 300px — câu mô tả
 * dài của khối lớn xuống bảy dòng ở đây.
 *
 * @return array{title: string, desc: string}
 */
function dql_sidebar_newsletter_text() {
	return array(
		'title' => (string) get_theme_mod( 'dql_sb_newsletter_title', __( 'Nhận bài mới qua email', 'kadence-daquylucyen' ) ),
		'desc'  => (string) get_theme_mod( 'dql_sb_newsletter_desc', __( 'Có bài kiến thức mới là bạn nhận được một email. Không quảng cáo, không spam.', 'kadence-daquylucyen' ) ),
	);
}

/**
 * Đăng ký mục Customizer cho cột bên.
 *
 * @param WP_Customize_Manager $wp_customize Đối tượng Customizer.
 */
function dql_sidebar_customize( $wp_customize ) {
	$wp_customize->add_section(
		'dql_sidebar',
		array(
			'title'       => __( 'Cột bên', 'kadence-daquylucyen' ),
			'priority'    => 26,
			'description' => __( 'Chọn khối nào hiện ở cột bên trang chủ và cột bên bài viết. Quảng cáo và ô tự do đặt ở Giao diện → Cấu hình cột tiện ích.', 'kadence-daquylucyen' ),
		)
	);

	$priority = 10;

	foreach ( dql_sidebar_modules() as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'wp_validate_boolean',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'    => $config['label'],
				'section'  => 'dql_sidebar',
				'type'     => 'checkbox',
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	$chu = dql_sidebar_newsletter_text();

	$wp_customize->add_setting(
		'dql_sb_newsletter_title',
		array(
			'default'           => $chu['title'],
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_sb_newsletter_title',
		array(
			'label'       => __( 'Tiêu đề ô đăng ký email ở cột bên', 'kadence-daquylucyen' ),
			'section'     => 'dql_sidebar',
			'type'        => 'text',
			'priority'    => 100,
			'description' => __( 'Form dùng chung mã nhúng với khối đăng ký ở trang chủ — dán một lần trong mục "Trang chủ Đá Quý".', 'kadence-daquylucyen' ),
		)
	);

	$wp_customize->add_setting(
		'dql_sb_newsletter_desc',
		array(
			'default'           => $chu['desc'],
			'sanitize_callback' => 'sanitize_textarea_field',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_sb_newsletter_desc',
		array(
			'label'    => __( 'Mô tả ô đăng ký email ở cột bên', 'kadence-daquylucyen' ),
			'section'  => 'dql_sidebar',
			'type'     => 'textarea',
			'priority' => 110,
		)
	);
}
add_action( 'customize_register', 'dql_sidebar_customize' );

/* ===========================================================================
 * 3. Khung dựng chung
 * ========================================================================= */

/**
 * In một ô quảng cáo trong cột bên, nếu vùng đó có widget.
 *
 * Không dùng dql_ad_slot() của inc/footer.php: hàm đó bọc thêm .dql-container,
 * đúng cho các dải quảng cáo chạy ngang trang nhưng thừa một lớp lề trong một
 * cột đã hẹp sẵn. Class .dql-ad giữ nguyên nên nhãn "Quảng cáo" ở
 * .dql-ad::before vẫn tự có.
 *
 * @param string $id    Mã vùng widget.
 * @param string $extra Class bổ sung.
 */
function dql_sb_ad( $id, $extra = '' ) {
	if ( ! is_active_sidebar( $id ) ) {
		return;
	}
	?>
	<aside class="dql-ad dql-ad--cot <?php echo esc_attr( $extra ); ?>" aria-label="<?php esc_attr_e( 'Quảng cáo', 'kadence-daquylucyen' ); ?>">
		<?php dynamic_sidebar( $id ); ?>
	</aside>
	<?php
}

/**
 * Mở một thẻ trong cột bên.
 *
 * @param string $title Tiêu đề. Bỏ trống thì không in thanh tiêu đề.
 * @param string $extra Class bổ sung.
 */
function dql_sb_card_start( $title = '', $extra = '' ) {
	?>
	<section class="widget dql-sb-card <?php echo esc_attr( $extra ); ?>">
		<?php if ( $title ) : ?>
			<h2 class="dql-sb-card__title"><?php echo esc_html( $title ); ?></h2>
		<?php endif; ?>
	<?php
}

/**
 * Đóng một thẻ trong cột bên.
 */
function dql_sb_card_end() {
	echo '</section>';
}

/* ===========================================================================
 * 4. Các khối
 * ========================================================================= */

/**
 * Khám phá theo chủ đề — chuyên mục kèm số bài.
 *
 * Lấy chuyên mục CON, không lấy chuyên mục cha.
 *
 * Ba chuyên mục cha đã có nguyên một dải riêng ở giữa trang chủ ("ba trụ nội
 * dung"). In lại đúng ba liên kết đó ở cột bên là lặp lại — đúng thứ khiến bản
 * trước phải làm lại. Chuyên mục con là lối đi mà dải trụ chưa mở ra.
 *
 * Site chưa chia chuyên mục con thì lùi về chuyên mục cha, để cột bên không
 * trống trên một cài đặt WordPress mới.
 *
 * Dùng dql_category_deep_count() chứ không dùng $term->count: chuyên mục cha
 * của site này không có bài gán trực tiếp, toàn bộ nằm ở chuyên mục con.
 *
 * @param int $toi_da Số mục tối đa.
 */
function dql_sb_module_chuyen_muc( $toi_da = 8 ) {
	$mac_dinh = (int) get_option( 'default_category' );

	$cats = get_categories(
		array(
			'hide_empty' => false,
			'exclude'    => array( $mac_dinh ),
		)
	);

	$con = array_filter(
		$cats,
		function ( $c ) {
			return (int) $c->parent > 0;
		}
	);

	$nguon = $con ? $con : array_filter(
		$cats,
		function ( $c ) {
			return 0 === (int) $c->parent;
		}
	);

	$co_bai = array();

	foreach ( $nguon as $cat ) {
		$count = dql_category_deep_count( $cat );

		if ( $count ) {
			$co_bai[] = array( $cat, $count );
		}
	}

	if ( ! $co_bai ) {
		return;
	}

	// Nhiều bài nhất lên trước: đó cũng là nơi người đọc có nhiều thứ để đọc nhất.
	usort(
		$co_bai,
		function ( $a, $b ) {
			return $b[1] - $a[1];
		}
	);

	$co_bai = array_slice( $co_bai, 0, max( 1, (int) $toi_da ) );

	dql_sb_card_start( __( 'Khám phá theo chủ đề', 'kadence-daquylucyen' ), 'dql-sb-card--cat' );
	?>
	<ul class="dql-sb-cat">
		<?php foreach ( $co_bai as $item ) : ?>
			<li class="dql-sb-cat__item">
				<a class="dql-sb-cat__link" href="<?php echo esc_url( get_category_link( $item[0]->term_id ) ); ?>">
					<?php dql_the_category_badge( $item[0], 'nho' ); ?>
					<span class="dql-sb-cat__ten"><?php echo esc_html( $item[0]->name ); ?></span>
					<span class="dql-sb-cat__so"><?php echo esc_html( number_format_i18n( $item[1] ) ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php
	dql_sb_card_end();
}

/**
 * Ô đăng ký nhận bài qua email.
 *
 * Dùng chung mã nhúng với khối lớn ở trang chủ: chủ site dán một lần, hiện ở mọi
 * nơi cần. Theme không tự lưu email — đó là việc của plugin, xem
 * template-parts/home/newsletter.php.
 *
 * Chưa dán mã thì khối không hiện với khách, chỉ quản trị viên thấy lời nhắc.
 */
function dql_sb_module_newsletter() {
	$shortcode = trim( dql_home_option( 'dql_newsletter_form' ) );
	$is_admin  = current_user_can( 'manage_options' );

	if ( ! $shortcode && ! $is_admin ) {
		return;
	}

	$chu = dql_sidebar_newsletter_text();

	dql_sb_card_start( '', 'dql-sb-card--mail' . ( $shortcode ? '' : ' dql-sb-card--cho' ) );
	?>
	<div class="dql-sb-mail">
		<svg class="dql-sb-mail__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
			<rect x="2.5" y="5" width="19" height="14" rx="2.5"/>
			<path d="m3 7 8.3 5.6a1.3 1.3 0 0 0 1.4 0L21 7"/>
		</svg>

		<p class="dql-sb-mail__title"><?php echo esc_html( $chu['title'] ); ?></p>

		<?php if ( $chu['desc'] ) : ?>
			<p class="dql-sb-mail__desc"><?php echo esc_html( $chu['desc'] ); ?></p>
		<?php endif; ?>

		<div class="dql-sb-mail__form">
			<?php if ( $shortcode ) : ?>
				<?php echo do_shortcode( $shortcode ); ?>
			<?php else : ?>
				<p class="dql-sb-mail__notice">
					<?php esc_html_e( 'Chưa có mã form. Dán mã WPForms vào Customizer → Trang chủ Đá Quý → "Mã form đăng ký email". Ô này chỉ quản trị viên thấy cho tới lúc đó.', 'kadence-daquylucyen' ); ?>
				</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
	dql_sb_card_end();
}

/**
 * Từ điển đá quý — lời mời tra cứu kèm vài thuật ngữ làm mồi.
 *
 * Chỉ hiện khi plugin dql-core đang bật. Không có plugin thì không có từ điển,
 * và một cái nút dẫn tới trang 404 tệ hơn là không có nút nào.
 */
function dql_sb_module_tu_dien() {
	if ( ! post_type_exists( 'dql_thuat_ngu' ) ) {
		return;
	}

	$terms = get_posts(
		array(
			'post_type'        => 'dql_thuat_ngu',
			'post_status'      => 'publish',
			'posts_per_page'   => 8,
			'orderby'          => 'title',
			'order'            => 'ASC',
			'no_found_rows'    => true,
			'suppress_filters' => false,
		)
	);

	if ( ! $terms ) {
		return;
	}

	dql_sb_card_start( __( 'Từ điển đá quý', 'kadence-daquylucyen' ), 'dql-sb-card--tudien' );
	?>
	<p class="dql-sb-tudien__desc">
		<?php esc_html_e( 'Gặp một thuật ngữ lạ trong bài? Tra nghĩa ngay, không cần rời trang.', 'kadence-daquylucyen' ); ?>
	</p>

	<ul class="dql-sb-tudien__ds">
		<?php foreach ( $terms as $term ) : ?>
			<li>
				<a class="dql-sb-tudien__chip" href="<?php echo esc_url( get_permalink( $term ) ); ?>">
					<?php echo esc_html( get_the_title( $term ) ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>

	<a class="dql-sb-nut" href="<?php echo esc_url( get_post_type_archive_link( 'dql_thuat_ngu' ) ); ?>">
		<?php esc_html_e( 'Mở toàn bộ từ điển', 'kadence-daquylucyen' ); ?>
	</a>
	<?php
	dql_sb_card_end();
}

/**
 * Bài cùng chuyên mục với bài đang đọc.
 *
 * Vì sao khối này chứ không phải "bài mới nhất": người đọc đã tự chọn chủ đề
 * bằng việc mở bài này. Danh sách bài mới nhất trả về đúng thứ họ đã bỏ qua ở
 * trang chủ; bài cùng chuyên mục trả về thứ họ đang tìm.
 *
 * Chỉ lấy bài CÓ ảnh đại diện — cùng lý do như lưới bài trang chủ: một dòng
 * thiếu ảnh để lại ô xám và làm cả khối trông như đang hỏng.
 */
function dql_sb_module_lien_quan() {
	$cats = wp_get_post_categories( get_the_ID() );

	if ( ! $cats ) {
		return;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'category__in'        => $cats,
			'post__not_in'        => array( get_the_ID() ),
			'posts_per_page'      => 4,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'meta_query'          => array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
				array(
					'key'     => '_thumbnail_id',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	dql_sb_card_start( __( 'Cùng chuyên mục', 'kadence-daquylucyen' ), 'dql-sb-card--bai' );
	?>
	<ul class="dql-sb-bai">
		<?php
		while ( $q->have_posts() ) :
			$q->the_post();
			?>
			<li class="dql-sb-bai__item">
				<a class="dql-sb-bai__link" href="<?php the_permalink(); ?>">
					<span class="dql-sb-bai__anh">
						<?php the_post_thumbnail( 'dql-square', array( 'alt' => '', 'loading' => 'lazy' ) ); ?>
					</span>
					<span class="dql-sb-bai__chu">
						<span class="dql-sb-bai__ten"><?php the_title(); ?></span>
						<span class="dql-sb-bai__meta"><?php dql_the_reading_time(); ?></span>
					</span>
				</a>
			</li>
		<?php endwhile; ?>
	</ul>
	<?php
	dql_sb_card_end();

	wp_reset_postdata();
}

/**
 * Bài được đọc nhiều nhất.
 *
 * Số liệu do plugin dql-core đếm — lượt xem là DỮ LIỆU, phải sống sót khi đổi
 * theme. Theme chỉ đọc và hiển thị.
 *
 * Chưa có plugin, hoặc chưa đủ bài có lượt xem, thì khối này KHÔNG hiện với
 * khách. Một danh sách "đọc nhiều" chỉ có một bài trông như trang đang hỏng.
 * Quản trị viên thấy lời nhắc, giống cách xử lý khối tra mệnh và khối đăng ký.
 *
 * @param int $so_luong Số bài hiển thị.
 */
function dql_sb_module_doc_nhieu( $so_luong = 5 ) {
	$is_admin = current_user_can( 'manage_options' );
	$ids      = class_exists( 'DQL_Luot_Xem' ) ? DQL_Luot_Xem::top( $so_luong ) : array();

	// Bỏ bài đang đọc: nó không phải là gợi ý đọc tiếp.
	if ( is_singular( 'post' ) ) {
		$ids = array_values( array_diff( $ids, array( get_the_ID() ) ) );
	}

	if ( count( $ids ) < 3 ) {
		if ( ! $is_admin ) {
			return;
		}

		dql_sb_card_start( __( 'Đọc nhiều nhất', 'kadence-daquylucyen' ), 'dql-sb-card--cho' );
		?>
		<p class="dql-sb-mail__notice">
			<?php
			if ( class_exists( 'DQL_Luot_Xem' ) ) {
				esc_html_e( 'Chưa đủ số liệu. Khối này hiện khi có ít nhất 3 bài được đọc, và chỉ đếm lượt của khách — lượt xem của người có quyền sửa bài không tính. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' );
			} else {
				esc_html_e( 'Cần plugin dql-core đang bật để đếm lượt xem. Ô này chỉ quản trị viên thấy.', 'kadence-daquylucyen' );
			}
			?>
		</p>
		<?php
		dql_sb_card_end();

		return;
	}

	$q = new WP_Query(
		array(
			'post_type'           => 'post',
			'post__in'            => $ids,
			// Giữ đúng thứ tự xếp hạng. Thiếu dòng này thì WordPress trả về theo
			// ngày đăng và cả bảng xếp hạng thành vô nghĩa.
			'orderby'             => 'post__in',
			'posts_per_page'      => $so_luong,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		)
	);

	if ( ! $q->have_posts() ) {
		wp_reset_postdata();
		return;
	}

	dql_sb_card_start( __( 'Đọc nhiều nhất', 'kadence-daquylucyen' ), 'dql-sb-card--bai dql-sb-card--top' );
	?>
	<ol class="dql-sb-bai dql-sb-bai--xep-hang">
		<?php
		$thu_tu = 0;

		while ( $q->have_posts() ) :
			$q->the_post();
			$thu_tu++;
			?>
			<li class="dql-sb-bai__item">
				<a class="dql-sb-bai__link" href="<?php the_permalink(); ?>">
					<span class="dql-sb-bai__hang" aria-hidden="true"><?php echo esc_html( number_format_i18n( $thu_tu ) ); ?></span>
					<span class="dql-sb-bai__chu">
						<span class="dql-sb-bai__ten"><?php the_title(); ?></span>
						<span class="dql-sb-bai__meta"><?php dql_the_reading_time(); ?></span>
					</span>
				</a>
			</li>
		<?php endwhile; ?>
	</ol>
	<?php
	dql_sb_card_end();

	wp_reset_postdata();
}

/**
 * Lời mời dùng công cụ tra đá hợp mệnh.
 *
 * Chỉ dẫn tới công cụ ở trang chủ chứ không nhúng form vào cột bên: công cụ trả
 * kết quả bằng cách tải lại trang kèm tham số, nhúng ở đây sẽ ném người đọc ra
 * khỏi bài họ đang đọc dở.
 */
function dql_sb_module_menh() {
	if ( ! function_exists( 'dql_core_menh_form' ) ) {
		return;
	}

	dql_sb_card_start( '', 'dql-sb-card--menh' );
	?>
	<div class="dql-sb-menh">
		<svg class="dql-sb-menh__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
			<circle cx="12" cy="12" r="9"/>
			<path d="M12 3a4.5 4.5 0 0 0 0 9 4.5 4.5 0 0 1 0 9"/>
		</svg>

		<p class="dql-sb-menh__title"><?php esc_html_e( 'Đá nào hợp mệnh bạn?', 'kadence-daquylucyen' ); ?></p>
		<p class="dql-sb-menh__desc">
			<?php esc_html_e( 'Nhập năm sinh, biết ngay mệnh của bạn và những loại đá đi cùng.', 'kadence-daquylucyen' ); ?>
		</p>

		<a class="dql-sb-nut dql-sb-nut--dam" href="<?php echo esc_url( home_url( '/#dql-menh' ) ); ?>">
			<?php esc_html_e( 'Tra ngay', 'kadence-daquylucyen' ); ?>
		</a>
	</div>
	<?php
	dql_sb_card_end();
}

/* ===========================================================================
 * 5. Lắp cột bên
 * ========================================================================= */

/**
 * Cột bên trang chủ có nội dung để hiện không.
 *
 * Tắt hết khối và không đặt widget nào thì khối "Bài mới nhất" tự về một cột,
 * không để lại khoảng hở bên phải.
 *
 * @return bool
 */
function dql_sidebar_home_active() {
	return dql_sidebar_on( 'dql_sb_home_chuyen_muc' )
		|| ( dql_sidebar_on( 'dql_sb_home_doc_nhieu' ) && class_exists( 'DQL_Luot_Xem' ) )
		|| dql_sidebar_on( 'dql_sb_home_newsletter' )
		|| ( dql_sidebar_on( 'dql_sb_home_tu_dien' ) && post_type_exists( 'dql_thuat_ngu' ) )
		|| is_active_sidebar( 'dql-sb-home-ad' )
		|| is_active_sidebar( 'dql-sb-home' );
}

/**
 * In cột bên trang chủ.
 *
 * Gọi từ template-parts/home/latest.php.
 *
 * Thứ tự có chủ đích: quảng cáo lên đầu vì đó là chỗ duy nhất trong cột nằm
 * trong tầm mắt mà không phải cuộn. Từ điển xuống cuối vì nó là ngõ rẽ — hữu
 * ích, nhưng không nên đứng trước lời mời đăng ký.
 */
function dql_sidebar_home() {
	dql_sb_ad( 'dql-sb-home-ad' );

	// Đọc nhiều nhất lên đầu: với người lần đầu vào site, "người khác đọc gì"
	// là lối vào đáng tin hơn danh sách chuyên mục. Khối tự ẩn khi chưa đủ
	// số liệu (xem dql_sb_module_doc_nhieu).
	if ( dql_sidebar_on( 'dql_sb_home_doc_nhieu' ) ) {
		dql_sb_module_doc_nhieu( 4 );
	}

	if ( dql_sidebar_on( 'dql_sb_home_chuyen_muc' ) ) {
		dql_sb_module_chuyen_muc();
	}

	if ( dql_sidebar_on( 'dql_sb_home_newsletter' ) ) {
		dql_sb_module_newsletter();
	}

	if ( dql_sidebar_on( 'dql_sb_home_tu_dien' ) ) {
		dql_sb_module_tu_dien();
	}

	if ( is_active_sidebar( 'dql-sb-home' ) ) {
		dynamic_sidebar( 'dql-sb-home' );
	}
}

/**
 * In cột bên bài viết.
 *
 * Cắm vào 'kadence_before_sidebar' — hook nằm ngay trong <aside class="primary-sidebar">
 * của Kadence, trước phần widget. Nhờ vậy các khối của theme và vùng widget tự do
 * dùng chung một khung, một kiểu dáng, và một cơ chế dính khi cuộn.
 *
 * Vì sao không tự dựng cột bên như ở trang chủ: trang bài đi qua khung nội dung
 * của Kadence, tự dựng sẽ phải dựng lại cả lưới hai cột và mất tùy chọn bật/tắt
 * cột bên trong Customizer.
 */
function dql_sidebar_post() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	dql_sb_ad( 'dql-sb-post-ad' );

	if ( dql_sidebar_on( 'dql_sb_post_lien_quan' ) ) {
		dql_sb_module_lien_quan();
	}

	if ( dql_sidebar_on( 'dql_sb_post_newsletter' ) ) {
		dql_sb_module_newsletter();
	}

	if ( dql_sidebar_on( 'dql_sb_post_menh' ) ) {
		dql_sb_module_menh();
	}
}
add_action( 'kadence_before_sidebar', 'dql_sidebar_post' );

/**
 * In phần đầu cột bên trang chuyên mục, danh sách bài và trang lưu trữ.
 *
 * Chỉ một khối, đặt TRƯỚC vùng widget. Phần còn lại của cột vẫn là widget do
 * chủ site tự lắp — trang chuyên mục không có khối nào bắt buộc phải dựng sẵn
 * như hai trang kia.
 *
 * Cùng hook với cột bên bài viết, nên phải loại trừ lẫn nhau bằng điều kiện.
 */
function dql_sidebar_archive() {
	if ( is_singular() ) {
		return;
	}

	if ( dql_sidebar_on( 'dql_sb_archive_top' ) ) {
		dql_sb_module_doc_nhieu();
	}
}
add_action( 'kadence_before_sidebar', 'dql_sidebar_archive' );

/**
 * In ô quảng cáo ở cuối cột bên bài viết.
 *
 * Cắm vào 'kadence_after_sidebar' để nó nằm sau cả vùng widget tự do, tức là
 * phần tử cuối cùng của cột. CSS cho phần tử cuối dính lại khi cuộn, nên ô này
 * theo người đọc suốt một bài dài. Chưa đặt widget vào đây thì ô không in ra gì
 * và thẻ đứng trước nó nhận vai trò dính — xem mục 1 của assets/css/sidebar.css.
 */
function dql_sidebar_post_cuoi() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	dql_sb_ad( 'dql-sb-post-ad-cuoi', 'dql-sb--dinh' );
}
add_action( 'kadence_after_sidebar', 'dql_sidebar_post_cuoi' );

/**
 * Đặt tên dễ hiểu cho cột bên gốc của Kadence.
 *
 * Sau thay đổi này 'sidebar-primary' chỉ còn phục vụ trang chuyên mục, trang
 * danh sách bài và trang tìm kiếm. Trang chủ và trang bài viết đã có vùng riêng.
 *
 * @param array $args Tham số vùng widget.
 * @return array
 */
function dql_rename_sidebar( $args ) {
	if ( isset( $args['id'] ) && 'sidebar-primary' === $args['id'] ) {
		$args['name']        = __( 'Cột bên — trang chuyên mục', 'kadence-daquylucyen' );
		$args['description'] = __( 'Hiện bên phải trang chuyên mục, danh sách bài và trang tìm kiếm. Trang chủ và trang bài viết dùng vùng riêng.', 'kadence-daquylucyen' );
	}

	return $args;
}
add_filter( 'kadence_widget_area_args', 'dql_rename_sidebar' );
