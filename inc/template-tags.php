<?php
/**
 * Custom template tags for this theme.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package rock
 */

if ( ! function_exists( 'rock_paging_nav' ) ) :
/**
 * Display navigation to next/previous set of posts when applicable.
 */
function rock_paging_nav() {
	// Don't print empty markup if there's only one page.
	if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
		return;
	}
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Posts navigation', 'rock' ); ?></h1>
		<div class="nav-links">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'rock' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'rock' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'rock_post_nav' ) ) :
/**
 * Display navigation to next/previous post when applicable.
 */
function rock_post_nav() {
	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( get_post()->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous ) {
		return;
	}
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'rock' ); ?></h1>
		<div class="nav-links">
			<?php
				previous_post_link( '<div class="nav-previous">%link</div>', _x( '<span class="meta-nav">&larr;</span>&nbsp;%title', 'Previous post link', 'rock' ) );
				next_post_link(     '<div class="nav-next">%link</div>',     _x( '%title&nbsp;<span class="meta-nav">&rarr;</span>', 'Next post link',     'rock' ) );
			?>
		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}
endif;

if ( ! function_exists( 'rock_posted_on' ) ) :
/**
 * Prints HTML with meta information for the current post-date/time and author.
 */
function rock_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
		$time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
	}

	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_attr( get_the_modified_date( 'c' ) ),
		esc_html( get_the_modified_date() )
	);

	$posted_on = $time_string;

	return '<span class="posted-on">' . $posted_on . '</span>';

}
endif;

if( ! function_exists('rock_post_format') ):
/**
 * Prints the post format for the current post.
 */
function rock_post_format(){
	global $post;

	$format = get_post_format( get_the_ID() );

	if ( false === $format ) {
		$format = 'standard';
	}

	echo '<span class="post-format">' . $format . '</span>';
}

endif;

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function rock_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'rock_categories' ) ) ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,

			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'rock_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so rock_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so rock_categorized_blog should return false.
		return false;
	}
}

/**
 * Returns the URL of the featured image if it exists, otherwise returns false
 *
 * @return bool/string
 */
function rock_get_featured_image_url() {
	$featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'featured' );

	$featured_image_url = $featured_image_url[0];

	if ( $featured_image_url == "" )
		return false;
	else
		return $featured_image_url;
}

/**
 * Flush out the transients used in rock_categorized_blog.
 */
function rock_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'rock_categories' );
}
add_action( 'edit_category', 'rock_category_transient_flusher' );
add_action( 'save_post',     'rock_category_transient_flusher' );


/**
 * Post featured image for full or short
 *
 * If short/multiple view (not singular), image is linked.
 *
 * @since 1.0
 * @return string Featured image HTML
 */
function rock_post_image() {

	// Featured image
	$image = get_the_post_thumbnail( null, apply_filters('rock_post_image_size', 'rock-post-header'), array('class' => 'rock-image' ) );

	// Link if short / multiple
	if ( ! is_singular( get_post_type() ) ) {
		$image = '<a href="' . esc_url( get_permalink() ) . '" title="' . esc_attr( the_title_attribute( array( 'echo' => false ) ) ) . '">' . $image . '</a>';
	}

	echo apply_filters( 'rock_post_image', $image );

}


/**
 * Gets the content template
 *
 * @since 1.0
 */
function rock_get_content_template() {
  switch ( get_post_type() ) {
    case 'ctc_sermon':
      get_template_part( 'content', 'sermon' );
    break;
    case 'ctc_event':
      get_template_part( 'content', 'event' );
    break;
    case 'ctc_person':
      get_template_part( 'content', 'person' );
    break;
    case 'ctc_location':
      get_template_part( 'content', 'location' );
    break;
    default:
      get_template_part( 'content', get_post_format() );
    break;
  }
}

/**
 * Outputs a title for archive templates.
 *
 * Exactly the same as the built-in WordPress get_the_archive_title() function
 * but alters the output of custom post type archive titles to remove "Archive:"
 * from the front.
 *
 * @since 1.0
 * @return string Archive title
 */
function rock_get_the_archive_title() {
    if ( is_category() ) {
        $title = sprintf( __( 'Category: %s' ), single_cat_title( '', false ) );
    } elseif ( is_tag() ) {
        $title = sprintf( __( 'Tag: %s' ), single_tag_title( '', false ) );
    } elseif ( is_author() ) {
        $title = sprintf( __( 'Author: %s' ), '<span class="vcard">' . get_the_author() . '</span>' );
    } elseif ( is_year() ) {
        $title = sprintf( __( 'Year: %s' ), get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
    } elseif ( is_month() ) {
        $title = sprintf( __( 'Month: %s' ), get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
    } elseif ( is_day() ) {
        $title = sprintf( __( 'Day: %s' ), get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audio', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = sprintf( __( '%s' ), post_type_archive_title( '', false ) );
    } elseif ( is_tax() ) {
        $tax = get_taxonomy( get_queried_object()->taxonomy );
        /* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
        $title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
    } else {
        $title = __( 'Archives' );
    }

    /**
     * Filter the archive title.
     *
     * @since 4.1.0
     *
     * @param string $title Archive title to be displayed.
     */
    return apply_filters( 'rock_get_the_archive_title', $title );
}
