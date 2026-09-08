<?php
/**
 * Velký mlýn functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Velký_mlýn
 */

if ( ! defined( 'VELKYMLYN_VERSION' ) ) {
	define( 'VELKYMLYN_VERSION', '1.5.0' );
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function velkymlyn_setup() {
	/*
		* Make theme available for translation.
		* Translations can be filed in the /languages/ directory.
		* If you're building a theme based on Velký mlýn, use a find and replace
		* to change 'velkymlyn' to the name of your theme in all the template files.
		*/
	load_theme_textdomain( 'velkymlyn', get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
		* Let WordPress manage the document title.
		* By adding theme support, we declare that this theme does not use a
		* hard-coded <title> tag in the document head, and expect WordPress to
		* provide it for us.
		*/
	add_theme_support( 'title-tag' );

	/*
		* Enable support for Post Thumbnails on posts and pages.
		*
		* @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		*/
	add_theme_support( 'post-thumbnails' );

	// Match the front end in the block editor and enable wide block alignment.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'editor-style.css' );
	add_theme_support( 'align-wide' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__( 'Primary', 'velkymlyn' ),
		)
	);

	/*
		* Switch default core markup for search form, comment form, and comments
		* to output valid HTML5.
		*/
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'velkymlyn_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		)
	);
}
add_action( 'after_setup_theme', 'velkymlyn_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function velkymlyn_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'velkymlyn_content_width', 640 );
}
add_action( 'after_setup_theme', 'velkymlyn_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function velkymlyn_widgets_init() {
	register_sidebar(
		array(
			'name'          => esc_html__( 'Sidebar', 'velkymlyn' ),
			'id'            => 'sidebar-1',
			'description'   => esc_html__( 'Add widgets here.', 'velkymlyn' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
add_action( 'widgets_init', 'velkymlyn_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function velkymlyn_scripts() {
	$style_path          = get_stylesheet_directory() . '/style.css';
	$style_version       = file_exists( $style_path ) ? filemtime( $style_path ) : VELKYMLYN_VERSION;
	$custom_js_path      = get_theme_file_path( '/js/custom.js' );
	$custom_js_version   = file_exists( $custom_js_path ) ? filemtime( $custom_js_path ) : VELKYMLYN_VERSION;

	wp_enqueue_style( 'velkymlyn-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css', array(), '5.0.2' );
	wp_enqueue_style( 'velkymlyn-fonts', 'https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap', array(), null );
	wp_enqueue_style( 'velkymlyn-bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css', array(), '1.13.1' );
	wp_enqueue_style( 'velkymlyn-slick', get_theme_file_uri( '/slick/slick.css' ), array(), VELKYMLYN_VERSION );
	wp_enqueue_style( 'velkymlyn-slick-theme', get_theme_file_uri( '/slick/slick-theme.css' ), array( 'velkymlyn-slick' ), VELKYMLYN_VERSION );
	wp_enqueue_style( 'velkymlyn-style', get_stylesheet_uri(), array( 'velkymlyn-bootstrap', 'velkymlyn-fonts', 'velkymlyn-bootstrap-icons', 'velkymlyn-slick-theme' ), $style_version );
	wp_style_add_data( 'velkymlyn-style', 'rtl', 'replace' );

	wp_enqueue_script( 'velkymlyn-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js', array(), '5.0.2', true );
	wp_enqueue_script( 'velkymlyn-slick', get_theme_file_uri( '/slick/slick.min.js' ), array( 'jquery' ), VELKYMLYN_VERSION, true );
	wp_enqueue_script( 'velkymlyn-navigation', get_template_directory_uri() . '/js/navigation.js', array(), VELKYMLYN_VERSION, true );
	wp_enqueue_script( 'velkymlyn-js', get_template_directory_uri() . '/js/custom.js', array( 'jquery', 'velkymlyn-bootstrap', 'velkymlyn-slick' ), $custom_js_version, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'velkymlyn_scripts' );

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Page-specific hero slider and image fallbacks.
 */
require get_template_directory() . '/inc/page-hero.php';

/**
 * Curated homepage block editing experience.
 */
require get_template_directory() . '/inc/homepage-editor.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

add_action( 'after_setup_theme', function() {
    add_image_size( 'three-two', 600, 400, true ); // 600x400px, hard crop, 3:2 ratio
});

/**
 * Render a compact, explicit schedule for an event card.
 *
 * Single-day events retain the existing weekday, date, and time layout.
 * Multi-day events show separate start and end boundaries so their times
 * cannot be mistaken for a same-day time range.
 *
 * @param int|WP_Post $event Event ID or post object.
 * @param bool $show_weekdays Whether to include weekday names.
 * @param bool $compact_single_day Render a single-day date and time on one line.
 * @return string
 */
function velkymlyn_get_event_schedule_html( $event, $show_weekdays = true, $compact_single_day = false ) {
	$event_id = is_object( $event ) && isset( $event->ID ) ? (int) $event->ID : absint( $event );
	if ( ! $event_id ) {
		return '';
	}

	$start_day      = tribe_get_start_date( $event_id, false, 'Y-m-d' );
	$end_day        = tribe_get_end_date( $event_id, false, 'Y-m-d' );
	$is_multi_day   = $start_day !== $end_day;
	$is_all_day     = tribe_event_is_all_day( $event_id );
	$start_datetime = tribe_get_start_date( $event_id, false, 'c' );
	$end_datetime   = tribe_get_end_date( $event_id, false, 'c' );

	ob_start();
	if ( ! $is_multi_day ) {
		$event_day  = tribe_get_start_date( $event_id, false, 'l' );
		$event_date = tribe_get_start_date( $event_id, false, 'j.n.' );
		?>
		<?php if ( $show_weekdays ) : ?><div class="text-lowercase fw-normal"><?php echo esc_html( $event_day ); ?></div><?php endif; ?>
		<?php if ( ! $compact_single_day ) : ?>
			<div class="datum fs-2"><time datetime="<?php echo esc_attr( $start_day ); ?>"><?php echo esc_html( $event_date ); ?></time></div>
		<?php endif; ?>
		<div class="small<?php echo $compact_single_day ? ' event-date__single-line' : ''; ?>">
			<?php if ( $compact_single_day ) : ?>
				<time datetime="<?php echo esc_attr( $start_day ); ?>"><?php echo esc_html( $event_date ); ?></time>
			<?php endif; ?>
			<?php if ( $is_all_day ) : ?>
				<?php esc_html_e( 'Celý den', 'velkymlyn' ); ?>
			<?php else : ?>
				<time datetime="<?php echo esc_attr( $start_datetime ); ?>"><?php echo esc_html( tribe_get_start_time( $event_id ) ); ?></time>
				<span aria-hidden="true"> – </span>
				<time datetime="<?php echo esc_attr( $end_datetime ); ?>"><?php echo esc_html( tribe_get_end_time( $event_id ) ); ?></time>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	$start_weekday = tribe_get_start_date( $event_id, false, 'l' );
	$end_weekday   = tribe_get_end_date( $event_id, false, 'l' );
	$start_date    = tribe_get_start_date( $event_id, false, 'j. n. Y' );
	$end_date      = tribe_get_end_date( $event_id, false, 'j. n. Y' );
	?>
	<?php if ( $show_weekdays ) : ?><div class="event-date__weekdays text-lowercase fw-normal"><?php echo esc_html( $start_weekday . ' – ' . $end_weekday ); ?></div><?php endif; ?>
	<div class="event-date__range">
		<div class="event-date__boundary">
			<span class="event-date__label"><?php esc_html_e( 'Od', 'velkymlyn' ); ?></span>
			<time datetime="<?php echo esc_attr( $start_datetime ); ?>">
				<span><?php echo esc_html( $start_date ); ?></span>
				<?php if ( ! $is_all_day ) : ?><span class="event-date__boundary-time"><?php echo esc_html( tribe_get_start_time( $event_id ) ); ?></span><?php endif; ?>
			</time>
		</div>
		<div class="event-date__boundary">
			<span class="event-date__label"><?php esc_html_e( 'Do', 'velkymlyn' ); ?></span>
			<time datetime="<?php echo esc_attr( $end_datetime ); ?>">
				<span><?php echo esc_html( $end_date ); ?></span>
				<?php if ( ! $is_all_day ) : ?><span class="event-date__boundary-time"><?php echo esc_html( tribe_get_end_time( $event_id ) ); ?></span><?php endif; ?>
			</time>
		</div>
	</div>
	<?php if ( $is_all_day ) : ?><div class="small event-date__all-day"><?php esc_html_e( 'Celý den', 'velkymlyn' ); ?></div><?php endif; ?>
	<?php

	return (string) ob_get_clean();
}

/**
 * Return occupancy data used by event-card templates.
 */
function velkymlyn_get_event_occupancy( $event_id ) {
	$event_id        = absint( $event_id );
	$available_is_set = $event_id && metadata_exists( 'post', $event_id, '_mlyn_event_available_places' );

	return array(
		'fully_occupied' => $available_is_set && 0 === (int) get_post_meta( $event_id, '_mlyn_event_available_places', true ),
		'note'           => $event_id ? (string) get_post_meta( $event_id, '_mlyn_event_occupancy_note', true ) : '',
	);
}

/**
 * Render the event location using the same categories as the detail page.
 */
function velkymlyn_get_event_location_html( $event_id ) {
	$locations = get_the_terms( $event_id, 'tribe_events_cat' );
	if ( empty( $locations ) || is_wp_error( $locations ) ) {
		return '';
	}

	return '<span class="event-location"><i class="bi bi-geo-alt" aria-hidden="true"></i><span><span class="screen-reader-text">' . esc_html__( 'Místo:', 'velkymlyn' ) . ' </span>' . esc_html( implode( ', ', wp_list_pluck( $locations, 'name' ) ) ) . '</span></span>';
}

function velkymlyn_render_upcoming_events() {
    $output = '';
    $events = tribe_get_events([
        'posts_per_page' => 4,
        'start_date'     => current_time('Y-m-d H:i:s'),
        'hide_upcoming'  => false,
    ]);

    if (empty($events)) {
        return '<p>Žádné nadcházející události.</p>';
    }

    ob_start();
    echo '<div class="event-card-list">';

    foreach ($events as $event) {
        $event_id = $event->ID;
		$occupancy = velkymlyn_get_event_occupancy( $event_id );
		$event_schedule = velkymlyn_get_event_schedule_html( $event, ! $occupancy['fully_occupied'], $occupancy['fully_occupied'] );
        $event_title = get_the_title($event_id);
		$event_excerpt = wp_trim_words( get_the_excerpt( $event_id ), 20, '...' );
        $event_excerpt_mobile = wp_trim_words( get_the_excerpt( $event_id ), 5, '...' );

		$event_image = get_the_post_thumbnail( $event_id, 'three-two', array( 'class' => 'img-fluid' ) );
		if (!$event_image) {
			$event_image = sprintf( '<img width="600" height="400" src="%s" class="img-fluid wp-post-image" alt="" decoding="async" fetchpriority="high">', esc_url( get_theme_file_uri( '/image/placeholder.jpg' ) ) );
		}
        $event_link = get_permalink($event_id);
        $event_location = velkymlyn_get_event_location_html( $event_id );
			$event_tags = get_the_terms( $event_id, 'post_tag');
	        ?>

	        <div class="event-card d-flex flex-wrap align-items-center<?php echo $occupancy['fully_occupied'] ? ' event-card--fully-occupied' : ''; ?>">
			<div class="row align-items-center justify-content-center">
				<div class="event-date text-start col-lg-2 col-12">
					<?php echo $event_schedule; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

					<?php if ( $occupancy['fully_occupied'] ) : ?>
						<div class="event-occupancy-note col-lg-3 col-12"><?php echo nl2br( esc_html( $occupancy['note'] ) ); ?></div>
					<?php else : ?>
						<div class="event-image col-lg-3 col-12">
							<?= $event_image ?>
						</div>
					<?php endif; ?>

            <div class="event-content col-lg-7 col-12" >

                <?php if ( ! $occupancy['fully_occupied'] ) : ?>
                <div class="event-card-meta mb-2">
                    <?php echo $event_location; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php if ( ! empty( $event_tags ) && ! is_wp_error( $event_tags ) ) : ?>
                <div class="event-tags">
                    <?php foreach ( $event_tags as $tag ) : 
                        // Načti barvu z ACF (pole pojmenuj např. "tag_color")
                        $tag_color = get_field( 'barva_stitku', 'term_' . $tag->term_id ); 
                    ?>
                        <div class="event-tag text-uppercase px-2 py-1 d-inline-block me-1 mb-1 <?= esc_attr( $tag->slug ); ?>"
                            style="background-color: <?= esc_attr( $tag_color ); ?>;">
                            <?= esc_html( $tag->name ) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <div class="event-card-heading">
                <h3 class="event-title d-block mb-1">
                    <a href="<?= esc_url($event_link) ?>">
                        <?= esc_html($event_title) ?>
                    </a>
                </h3>
                    <?php if ( $occupancy['fully_occupied'] ) : ?>
                        <?php echo $event_location; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <?php endif; ?>
                </div>

	                <?php if ( ! $occupancy['fully_occupied'] ) : ?>
	                    <div class="d-md-none event-description text-muted">
	                        <?= esc_html($event_excerpt_mobile) ?>
	                    </div>
	                    <div class="d-none d-lg-flex event-description text-muted">
	                        <?= esc_html($event_excerpt) ?>
	                    </div>
	                <?php endif; ?>
            </div>

			</div>
            
        </div>

        <?php
    }

    echo '</div>';
    return ob_get_clean();
}
add_shortcode( 'custom_events_list', 'velkymlyn_render_upcoming_events' );


/**
 * Return the tags currently used by published events.
 */
function velkymlyn_get_event_tag_terms() {
	static $tags = null;

	if ( null !== $tags ) {
		return $tags;
	}

	$event_ids = get_posts(
		array(
			'post_type'      => 'tribe_events',
			'post_status'    => 'publish',
			'fields'         => 'ids',
			'posts_per_page' => -1,
		)
	);
	$tags      = get_terms(
		array(
			'taxonomy'   => 'post_tag',
			'hide_empty' => true,
			'object_ids' => $event_ids,
			'orderby'    => 'name',
			'order'      => 'ASC',
		)
	);

	return is_wp_error( $tags ) ? array() : $tags;
}

/**
 * Return the event-tag slugs selected in the current calendar request.
 *
 * TEC's AJAX requests carry the visible calendar URL inside a `url` request
 * value, so read both the direct query string and the View URL object.
 */
function velkymlyn_get_selected_event_tags( $view = null ) {
	$raw_tags = null;

	if ( $view && method_exists( $view, 'get_url_object' ) ) {
		$url = $view->get_url_object();
		if ( $url && method_exists( $url, 'get_query_arg' ) ) {
			$raw_tags = $url->get_query_arg( 'event_tags', null );
		}
	}

	if ( null === $raw_tags ) {
		$raw_tags = tribe_get_request_var( 'event_tags', null );
	}

	if ( null === $raw_tags ) {
		$request_url = tribe_get_request_var( 'url', '' );
		if ( is_string( $request_url ) && '' !== $request_url ) {
			$query = wp_parse_url( $request_url, PHP_URL_QUERY );
			if ( is_string( $query ) ) {
				parse_str( $query, $request_args );
				$raw_tags = isset( $request_args['event_tags'] ) ? $request_args['event_tags'] : null;
			}
		}
	}

	if ( is_string( $raw_tags ) ) {
		$raw_tags = explode( ',', $raw_tags );
	}

	if ( ! is_array( $raw_tags ) ) {
		return array();
	}

	$tags = array();
	foreach ( array_slice( wp_unslash( $raw_tags ), 0, 100 ) as $raw_tag ) {
		if ( is_scalar( $raw_tag ) ) {
			$tags[] = sanitize_title( (string) $raw_tag );
		}
	}
	$tags = array_filter( array_unique( $tags ) );
	$available_tag_slugs = wp_list_pluck( velkymlyn_get_event_tag_terms(), 'slug' );

	// Selecting every available tag is equivalent to applying no filter.
	if ( $available_tag_slugs && ! array_diff( $available_tag_slugs, $tags ) ) {
		return array();
	}

	return array_values( $tags );
}

/**
 * Keep selected tags in TEC-generated search, navigation, and view URLs.
 */
function velkymlyn_add_event_tags_to_view_url_args( $query_args, $view = null ) {
	$selected_tags = velkymlyn_get_selected_event_tags( $view );

	if ( $selected_tags ) {
		$query_args['event_tags'] = $selected_tags;
	}

	return $query_args;
}
add_filter( 'tribe_events_views_v2_url_query_args', 'velkymlyn_add_event_tags_to_view_url_args', 10, 2 );
add_filter( 'tribe_events_views_v2_view_url_query_args', function( $query_args, $view_slug, $view ) {
	return velkymlyn_add_event_tags_to_view_url_args( $query_args, $view );
}, 10, 3 );
add_filter( 'tribe_events_views_v2_publicly_visible_views_query_args', 'velkymlyn_add_event_tags_to_view_url_args', 10, 1 );

/**
 * Match events carrying any of the selected tags in every TEC V2 view.
 */
add_filter( 'tribe_events_views_v2_view_repository_args', function( $repository_args, $context, $view ) {
	$selected_tags = velkymlyn_get_selected_event_tags( $view );

	if ( $selected_tags ) {
		$term_ids = get_terms(
			array(
				'taxonomy'   => 'post_tag',
				'hide_empty' => false,
				'slug'       => $selected_tags,
				'fields'     => 'ids',
			)
		);
		$event_ids = is_wp_error( $term_ids ) ? array() : get_objects_in_term( $term_ids, 'post_tag' );
		$event_ids = is_wp_error( $event_ids ) ? array() : array_map( 'intval', $event_ids );
		$event_ids = array_values( array_unique( $event_ids ) );

		if ( isset( $repository_args['post__in'] ) && is_array( $repository_args['post__in'] ) ) {
			$event_ids = array_values( array_intersect( $repository_args['post__in'], $event_ids ) );
		}

		$repository_args['post__in'] = $event_ids ? $event_ids : array( 0 );
	}

	return $repository_args;
}, 10, 3 );

/**
 * Render an expandable checkbox filter containing tags used by events.
 */
function velkymlyn_render_event_tag_filters( $file, $name, $template ) {
	$tags = velkymlyn_get_event_tag_terms();

	if ( ! $tags ) {
		return;
	}

	$selected_tags      = velkymlyn_get_selected_event_tags();
	$context            = tribe_context();
	$view_slug          = $context->get( 'view', 'list' );
	$keyword            = $context->get( 'keyword', '' );
	$event_display_mode = $context->get( 'event_display_mode', '' );
	$view_url           = $template && method_exists( $template, 'get' ) ? $template->get( 'url', '', false ) : '';
	$request_uri        = $view_url ? $view_url : ( isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '' );
	$request_path       = wp_parse_url( $request_uri, PHP_URL_PATH );
	$calendar_path      = '/' . trim( tribe_get_option( 'eventsSlug', 'events' ), '/' ) . '/';

	if ( is_string( $request_path ) && 0 === strpos( trailingslashit( $request_path ), $calendar_path ) ) {
		$form_action = home_url( $request_path );
		$request_query = wp_parse_url( $request_uri, PHP_URL_QUERY );
		$clear_args = array();
		if ( is_string( $request_query ) ) {
			parse_str( $request_query, $clear_args );
		}
		unset( $clear_args['event_tag'], $clear_args['event_tags'], $clear_args['tag'] );
		$clear_url = $clear_args ? add_query_arg( $clear_args, $form_action ) : $form_action;
	} else {
		$form_action = tribe_events_get_url( array( 'eventDisplay' => $view_slug ) );
		$clear_url   = remove_query_arg( array( 'event_tag', 'event_tags', 'tag' ), $form_action );
	}

	$grid_date = $template && method_exists( $template, 'get' ) ? $template->get( 'grid_date', '' ) : '';
	if ( 'month' === $view_slug && is_string( $grid_date ) && preg_match( '/^\d{4}-\d{2}/', $grid_date, $matches ) ) {
		$form_action = tribe_events_get_url(
			array(
				'eventDisplay' => 'month',
				'eventDate'    => $matches[0],
			)
		);
		$clear_url = $form_action;
	}

	if ( $keyword ) {
		$clear_url = add_query_arg( 'tribe-bar-search', $keyword, $clear_url );
	}
	if ( 'past' === $event_display_mode ) {
		$clear_url = add_query_arg( 'eventDisplay', 'past', $clear_url );
	}
	?>
	<div class="events-filter">
		<details class="events-filter__panel"<?php echo $selected_tags ? ' open' : ''; ?>>
			<summary class="events-filter__summary">
				<span><?php esc_html_e( 'Filtrovat podle štítků', 'velkymlyn' ); ?></span>
				<span class="events-filter__summary-meta">
					<?php if ( $selected_tags ) : ?>
						<span class="events-filter__count"><?php echo esc_html( sprintf( __( 'Vybráno: %d', 'velkymlyn' ), count( $selected_tags ) ) ); ?></span>
					<?php endif; ?>
					<i class="bi bi-chevron-down events-filter__chevron events-filter__chevron--closed" aria-hidden="true"></i>
					<i class="bi bi-chevron-up events-filter__chevron events-filter__chevron--open" aria-hidden="true"></i>
				</span>
			</summary>
			<form class="events-filter__form" action="<?php echo esc_url( $form_action ); ?>" method="get">
				<?php if ( $keyword ) : ?>
					<input type="hidden" name="tribe-bar-search" value="<?php echo esc_attr( $keyword ); ?>">
				<?php endif; ?>
				<?php if ( 'past' === $event_display_mode ) : ?>
					<input type="hidden" name="eventDisplay" value="past">
				<?php endif; ?>
				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'Štítky událostí', 'velkymlyn' ); ?></legend>
					<div class="events-filter__options">
						<label class="events-filter__option events-filter__option--all">
							<input type="checkbox" class="events-filter__all" <?php checked( ! $selected_tags ); ?>>
							<span><?php esc_html_e( 'Všechny', 'velkymlyn' ); ?></span>
						</label>
						<?php foreach ( $tags as $tag ) : ?>
							<label class="events-filter__option" for="event-tag-<?php echo esc_attr( (string) $tag->term_id ); ?>">
								<input id="event-tag-<?php echo esc_attr( (string) $tag->term_id ); ?>" class="events-filter__tag" type="checkbox" name="event_tags[]" value="<?php echo esc_attr( $tag->slug ); ?>" <?php checked( ! $selected_tags || in_array( $tag->slug, $selected_tags, true ) ); ?>>
								<span><?php echo esc_html( $tag->name ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</fieldset>
				<div class="events-filter__actions">
					<button class="tribe-common-c-btn events-filter__submit" type="submit"><?php esc_html_e( 'Použít filtry', 'velkymlyn' ); ?></button>
					<?php if ( $selected_tags ) : ?>
						<a class="events-filter__clear" href="<?php echo esc_url( $clear_url ); ?>"><?php esc_html_e( 'Zrušit filtry', 'velkymlyn' ); ?></a>
					<?php endif; ?>
				</div>
			</form>
		</details>
	</div>
	<?php
}
add_action( 'tribe_template_after_include:events/v2/components/events-bar', 'velkymlyn_render_event_tag_filters', 10, 3 );
