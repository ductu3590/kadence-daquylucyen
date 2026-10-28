<?php
/**
 * Tùy chọn Customizer riêng cho trang chủ.
 *
 * Dùng API Customizer chuẩn của WordPress, không dùng hệ control riêng của
 * Kadence. Lý do: các tùy chọn ở đây đơn giản (chữ và chọn chuyên mục), dùng
 * API chuẩn thì không phụ thuộc vào thay đổi nội bộ của Kadence khi nó cập nhật.
 *
 * Tùy chọn giao diện chung (chế độ tối, dải chuyên mục, hạt đá) nằm ở
 * inc/theme-mode.php; tùy chọn cột bên ở inc/sidebar.php.
 *
 * @package kadence-daquylucyen
 */

defined( 'ABSPATH' ) || exit;

/**
 * Số trụ nội dung tối đa trên trang chủ.
 *
 * Bản 1.x cố định ba trụ. Từ 2.0 site mở rộng sang văn hóa, du lịch, ẩm thực
 * Lục Yên nên cho tới sáu — lưới ba cột, hai hàng. Chọn ít hơn thì lưới tự co.
 */
const DQL_SO_TRU = 6;

/**
 * Danh sách tùy chọn trang chủ.
 *
 * Gom vào một hàm để đăng ký Customizer và hàm đọc giá trị dùng chung một
 * nguồn — tránh cảnh sửa mặc định ở một chỗ mà quên chỗ kia.
 *
 * @return array
 */
function dql_home_settings() {
	return array(
		'dql_hero_kicker'      => array(
			'default' => __( 'Đất ngọc Lục Yên', 'kadence-daquylucyen' ),
			'label'   => __( 'Dòng dẫn nhỏ trên câu định vị', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_hero_title'       => array(
			'default' => __( 'Hiểu đúng về đá quý — và về vùng đất sinh ra chúng', 'kadence-daquylucyen' ),
			'label'   => __( 'Câu định vị', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_hero_sub'         => array(
			'default' => __( 'Kiến thức đá quý, phong thủy, kiểm định; cùng văn hóa, con người và ẩm thực Lục Yên. Viết cho người đọc, không viết cho người bán.', 'kadence-daquylucyen' ),
			'label'   => __( 'Câu mô tả dưới', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_pillars_title'    => array(
			'default' => __( 'Lục Yên qua từng góc nhìn', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề khối trụ nội dung', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_menh_title'       => array(
			'default' => __( 'Bạn sinh năm nào?', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề ô tra mệnh', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_menh_desc'        => array(
			'default' => __( 'Nhập năm sinh để biết mệnh của bạn và những loại đá Lục Yên đi cùng mệnh đó.', 'kadence-daquylucyen' ),
			'label'   => __( 'Mô tả ô tra mệnh', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_spotlight_title'  => array(
			'default' => __( 'Chuyên mục tiêu điểm', 'kadence-daquylucyen' ),
			'label'   => __( 'Dòng dẫn nhỏ của khối chuyên mục tiêu điểm', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_latest_title'     => array(
			'default' => __( 'Bài mới nhất', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề khối bài mới', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_newsletter_title' => array(
			'default' => __( 'Thư từ Lục Yên', 'kadence-daquylucyen' ),
			'label'   => __( 'Tiêu đề khối đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'text',
		),
		'dql_newsletter_desc'  => array(
			'default' => __( 'Mỗi tháng một email: bài mới, phiên chợ đá, mùa vụ Lục Yên. Không quảng cáo, không spam.', 'kadence-daquylucyen' ),
			'label'   => __( 'Mô tả khối đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
		'dql_newsletter_form'  => array(
			'default' => '',
			'label'   => __( 'Mã form đăng ký email', 'kadence-daquylucyen' ),
			'type'    => 'textarea',
		),
	);
}

/**
 * Lấy giá trị một tùy chọn trang chủ, tự động rơi về mặc định.
 *
 * @param string $key Khóa tùy chọn.
 * @return string
 */
function dql_home_option( $key ) {
	$settings = dql_home_settings();
	$default  = isset( $settings[ $key ]['default'] ) ? $settings[ $key ]['default'] : '';

	return (string) get_theme_mod( $key, $default );
}

/**
 * Đăng ký mục Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Đối tượng Customizer.
 */
function dql_customize_register( $wp_customize ) {

	$wp_customize->add_section(
		'dql_home',
		array(
			'title'       => __( 'Trang chủ Đá Quý', 'kadence-daquylucyen' ),
			'priority'    => 25,
			'description' => __( 'Nội dung các khối trên trang chủ. Bố cục do template quyết định, không đổi được ở đây.', 'kadence-daquylucyen' ),
		)
	);

	// Các ô chữ.
	$priority = 10;
	foreach ( dql_home_settings() as $key => $config ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => $config['default'],
				'sanitize_callback' => 'textarea' === $config['type'] ? 'sanitize_textarea_field' : 'sanitize_text_field',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'    => $config['label'],
				'section'  => 'dql_home',
				'type'     => $config['type'],
				'priority' => $priority,
			)
		);
		$priority += 10;
	}

	// Trụ nội dung. Mỗi trụ là một chuyên mục; tối đa DQL_SO_TRU.
	for ( $index = 1; $index <= DQL_SO_TRU; $index++ ) {
		$wp_customize->add_setting(
			'dql_pillar_' . $index,
			array(
				'default'           => 0,
				'sanitize_callback' => 'absint',
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			'dql_pillar_' . $index,
			array(
				/* translators: %d: số thứ tự trụ nội dung. */
				'label'       => sprintf( __( 'Trụ %d — chuyên mục', 'kadence-daquylucyen' ), $index ),
				'section'     => 'dql_home',
				'type'        => 'select',
				'choices'     => dql_category_choices(),
				'priority'    => 200 + $index,
				'description' => 1 === $index
					? __( 'Bỏ trống cả sáu thì trang chủ tự lấy ba chuyên mục có nhiều bài nhất. Chọn 3 hoặc 6 trụ thì lưới đều nhất.', 'kadence-daquylucyen' )
					: '',
			)
		);
	}

	// Chuyên mục tiêu điểm — khối bốn bài của một chuyên mục, đặt sau ô tra mệnh.
	// Dùng để đưa một mảng mới của site (ví dụ Văn hóa Lục Yên) ra trang chủ.
	$wp_customize->add_setting(
		'dql_spotlight_cat',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_spotlight_cat',
		array(
			'label'       => __( 'Chuyên mục tiêu điểm', 'kadence-daquylucyen' ),
			'description' => __( 'Bốn bài mới nhất của chuyên mục này hiện thành một dải riêng trên trang chủ. Chưa chọn thì dải không hiện.', 'kadence-daquylucyen' ),
			'section'     => 'dql_home',
			'type'        => 'select',
			'choices'     => dql_category_choices(),
			'priority'    => 210,
		)
	);

	// Số bài trong khối "Bài mới nhất".
	$wp_customize->add_setting(
		'dql_latest_count',
		array(
			'default'           => 6,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		'dql_latest_count',
		array(
			'label'       => __( 'Số bài mới nhất hiển thị', 'kadence-daquylucyen' ),
			'section'     => 'dql_home',
			'type'        => 'number',
			'input_attrs' => array(
				'min'  => 3,
				'max'  => 12,
				'step' => 1,
			),
			'priority'    => 220,
		)
	);

	// Logo chân trang.
	//
	// Đặt cạnh logo header trong "Cài đặt site" chứ không tạo mục riêng, vì đây
	// là chuyện nhận diện thương hiệu — người đi tìm nó sẽ tìm ở chỗ có logo.
	$wp_customize->add_setting(
		'dql_footer_logo',
		array(
			'default'           => 0,
			'sanitize_callback' => 'absint',
			'transport'         => 'refresh',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Media_Control(
			$wp_customize,
			'dql_footer_logo',
			array(
				'label'       => __( 'Logo chân trang', 'kadence-daquylucyen' ),
				'description' => __( 'Bản chữ trắng. Chân trang nền than đá nên logo chữ tối đặt lên đó sẽ gần như biến mất. Bỏ trống thì chân trang hiện dấu hiệu đá ruby kèm tên site dạng chữ.', 'kadence-daquylucyen' ),
				'section'     => 'title_tagline',
				'mime_type'   => 'image',
				'priority'    => 9,
			)
		)
	);
}
add_action( 'customize_register', 'dql_customize_register' );

/**
 * Danh sách chuyên mục cho ô chọn trong Customizer.
 *
 * @return array
 */
function dql_category_choices() {
	$choices = array( 0 => __( '— Chưa chọn —', 'kadence-daquylucyen' ) );

	$categories = get_categories(
		array(
			'hide_empty' => false,
			'orderby'    => 'name',
		)
	);

	foreach ( $categories as $category ) {
		$tien_to = $category->parent ? '— ' : '';

		$choices[ $category->term_id ] = $tien_to . $category->name;
	}

	return $choices;
}
