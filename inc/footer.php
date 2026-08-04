<?php
/**
 * Footer riêng của site, và các vùng đặt quảng cáo.
 *
 * Kadence gắn footer của nó vào action 'kadence_footer'. Gỡ hook đó ra rồi gắn
 * hàm của mình vào cùng chỗ, thay vì ghi đè footer.php — cách này giữ nguyên
 * mọi wrapper và hook khác của Kadence, và trả lại footer gốc chỉ bằng việc xóa
 * một dòng.
 *
 * Đánh đổi cần biết: các tùy chọn Footer trong Customizer của Kadence sẽ không
 * còn tác dụng, vì footer giờ do file này dựng.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Thay footer của Kadence bằng footer của site.
 */
function dql_replace_footer() {
	remove_action( 'kadence_footer', 'Kadence\footer_markup', 10 );
	add_action( 'kadence_footer', 'dql_footer_markup', 10 );
}
add_action( 'init', 'dql_replace_footer' );

/**
 * Đăng ký các vùng đặt quảng cáo.
 *
 * Dùng widget thay vì ô nhập trong Customizer: widget nhận được mọi thứ, từ mã
 * Google AdSense, ảnh banner tự đặt, cho tới một khối HTML bất kỳ. Vùng nào
 * không có widget thì không in ra gì cả, không để lại khoảng trống.
 */
function dql_register_ad_areas() {
	$chung = array(
		'before_widget' => '<div class="dql-ad__item %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<p class="dql-ad__title">',
		'after_title'   => '</p>',
	);

	register_sidebar(
		array_merge(
			$chung,
			array(
				'name'        => __( 'Quảng cáo — trang chủ', 'kadence-daquylucyen' ),
				'id'          => 'dql-ad-home',
				'description' => __( 'Hiện giữa khối bài nổi bật và ba trụ nội dung trên trang chủ.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$chung,
			array(
				'name'        => __( 'Quảng cáo — cuối bài viết', 'kadence-daquylucyen' ),
				'id'          => 'dql-ad-article',
				'description' => __( 'Hiện sau nội dung bài viết, trước phần bình luận.', 'kadence-daquylucyen' ),
			)
		)
	);

	register_sidebar(
		array_merge(
			$chung,
			array(
				'name'        => __( 'Quảng cáo — giữa bài viết', 'kadence-daquylucyen' ),
				'id'          => 'dql-ad-incontent',
				'description' => __( 'Tự chèn sau đoạn văn thứ 3. Bài dưới 6 đoạn sẽ bỏ qua để không cắt ngang bài ngắn.', 'kadence-daquylucyen' ),
			)
		)
	);
}
add_action( 'widgets_init', 'dql_register_ad_areas' );

/*
 * Việc đặt tên cho cột bên gốc của Kadence đã chuyển sang inc/sidebar.php, nơi
 * quản lý toàn bộ các vùng widget của cột bên.
 */

/**
 * In một vùng quảng cáo, nếu vùng đó có widget.
 *
 * @param string $id    Mã vùng.
 * @param string $extra Class bổ sung cho khung ngoài.
 */
function dql_ad_slot( $id, $extra = '' ) {
	if ( ! is_active_sidebar( $id ) ) {
		return;
	}
	?>
	<aside class="dql-ad <?php echo esc_attr( $extra ); ?>" aria-label="<?php esc_attr_e( 'Quảng cáo', 'kadence-daquylucyen' ); ?>">
		<div class="dql-container">
			<?php dynamic_sidebar( $id ); ?>
		</div>
	</aside>
	<?php
}

/**
 * In vùng quảng cáo cuối bài viết.
 */
function dql_article_ad() {
	if ( ! is_singular( 'post' ) ) {
		return;
	}

	dql_ad_slot( 'dql-ad-article', 'dql-ad--bai' );
}
add_action( 'kadence_single_after_content', 'dql_article_ad', 5 );

/**
 * Chèn quảng cáo vào giữa nội dung bài, sau đoạn văn thứ 3.
 *
 * Vì sao là đoạn 3: người đọc đã cuộn qua phần mở đầu và đang thực sự đọc, nên
 * quảng cáo ở đây được nhìn thấy nhiều hơn hẳn quảng cáo cuối bài — mà chưa cắt
 * ngang lúc họ vừa mới bắt đầu.
 *
 * Bài dưới 6 đoạn thì bỏ qua: chèn quảng cáo vào giữa một bài ngắn là cắt đôi
 * nó, và phần còn lại quá ít để người đọc quay lại mạch.
 *
 * @param string $content Nội dung bài.
 * @return string
 */
function dql_insert_incontent_ad( $content ) {
	if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
		return $content;
	}

	if ( ! is_active_sidebar( 'dql-ad-incontent' ) ) {
		return $content;
	}

	$sau_doan = (int) apply_filters( 'dql_ad_after_paragraph', 3 );
	$toi_thieu = (int) apply_filters( 'dql_ad_min_paragraphs', 6 );

	// Đếm thẻ đóng </p> ở cấp cao nhất. Cách này đủ tin cậy với nội dung do
	// trình soạn thảo khối sinh ra, và không phải dựng cả cây DOM chỉ để đếm.
	$vi_tri = array();
	$offset = 0;

	while ( false !== ( $tim = strpos( $content, '</p>', $offset ) ) ) {
		$vi_tri[] = $tim + 4;
		$offset   = $tim + 4;
	}

	if ( count( $vi_tri ) < $toi_thieu || ! isset( $vi_tri[ $sau_doan - 1 ] ) ) {
		return $content;
	}

	ob_start();
	dql_ad_slot( 'dql-ad-incontent', 'dql-ad--trong-bai' );
	$quang_cao = (string) ob_get_clean();

	if ( ! $quang_cao ) {
		return $content;
	}

	$diem = $vi_tri[ $sau_doan - 1 ];

	return substr( $content, 0, $diem ) . $quang_cao . substr( $content, $diem );
}
add_filter( 'the_content', 'dql_insert_incontent_ad', 40 );

/**
 * Dựng footer.
 */
function dql_footer_markup() {
	$dql_menu_footer = has_nav_menu( 'footer' );
	?>
	<footer class="dql-footer" role="contentinfo">

		<div class="dql-footer__main">
			<div class="dql-container">
				<div class="dql-footer__grid">

					<div class="dql-footer__col dql-footer__col--gioi-thieu">
						<?php
						/*
						 * Bản logo chữ trắng, dùng riêng cho nền xanh đậm của footer.
						 * Logo gốc có chữ màu navy — đặt lên nền footer sẽ gần như
						 * biến mất. Chưa có bản trắng thì lùi về tên site dạng chữ.
						 */
						$dql_footer_logo = (int) get_theme_mod( 'dql_footer_logo', 0 );

						if ( $dql_footer_logo && wp_get_attachment_image_url( $dql_footer_logo, 'full' ) ) :
							?>
							<a class="dql-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
								<?php
								echo wp_get_attachment_image(
									$dql_footer_logo,
									'full',
									false,
									array(
										'alt'     => esc_attr( get_bloginfo( 'name' ) ),
										'loading' => 'lazy',
									)
								);
								?>
							</a>
						<?php else : ?>
							<p class="dql-footer__ten"><?php bloginfo( 'name' ); ?></p>
						<?php endif; ?>

						<?php if ( get_bloginfo( 'description' ) ) : ?>
							<p class="dql-footer__mota"><?php bloginfo( 'description' ); ?></p>
						<?php endif; ?>
					</div>

					<div class="dql-footer__col">
						<h2 class="dql-footer__tieude"><?php esc_html_e( 'Chuyên mục', 'kadence-daquylucyen' ); ?></h2>
						<ul class="dql-footer__list">
							<?php
							$dql_cats = get_categories(
								array(
									'parent'     => 0,
									'hide_empty' => false,
									'exclude'    => array( (int) get_option( 'default_category' ) ),
								)
							);

							foreach ( $dql_cats as $dql_cat ) :
								if ( ! dql_category_deep_count( $dql_cat ) ) {
									continue;
								}
								?>
								<li>
									<a href="<?php echo esc_url( get_category_link( $dql_cat->term_id ) ); ?>">
										<?php echo esc_html( $dql_cat->name ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>

					<div class="dql-footer__col">
						<h2 class="dql-footer__tieude"><?php esc_html_e( 'Khám phá', 'kadence-daquylucyen' ); ?></h2>

						<?php if ( $dql_menu_footer ) : ?>
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'footer',
									'container'      => false,
									'menu_class'     => 'dql-footer__list',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						<?php else : ?>
							<ul class="dql-footer__list">
								<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Trang chủ', 'kadence-daquylucyen' ); ?></a></li>
							</ul>
						<?php endif; ?>
					</div>

					<div class="dql-footer__col dql-footer__col--tu-dien">
						<h2 class="dql-footer__tieude"><?php esc_html_e( 'Tra cứu nhanh', 'kadence-daquylucyen' ); ?></h2>

						<?php if ( post_type_exists( 'dql_thuat_ngu' ) ) : ?>
							<p class="dql-footer__mota">
								<?php esc_html_e( 'Không hiểu một thuật ngữ trong bài? Tra ngay trong từ điển đá quý.', 'kadence-daquylucyen' ); ?>
							</p>
							<a class="dql-footer__nut" href="<?php echo esc_url( get_post_type_archive_link( 'dql_thuat_ngu' ) ); ?>">
								<?php esc_html_e( 'Mở từ điển', 'kadence-daquylucyen' ); ?>
							</a>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>

		<div class="dql-footer__day">
			<div class="dql-container">
				<p class="dql-footer__banquyen">
					<?php
					printf(
						/* translators: 1: năm hiện tại, 2: tên website. */
						esc_html__( '© %1$s %2$s. Nội dung thuộc về tác giả.', 'kadence-daquylucyen' ),
						esc_html( wp_date( 'Y' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
					?>
				</p>
			</div>
		</div>

	</footer>
	<?php
}
