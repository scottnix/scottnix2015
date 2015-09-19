<?php

/**
 * Force Thematic HTML5
 *
 * New Thematic feature that allows the child theme control of the HTML mode. This was previously
 * controlled from inside the WordPress Admin.
 *
 * https://github.com/ThematicTheme/Thematic/pull/113
 *
 */

add_theme_support( 'thematic_html5' );

/**
 * Remove Thematic Menu JavaScript
 *
 * Removes the default Thematic JS scripts (Superfish) completely.
 *
 * Reference http://thematictheme.com/forums/topic/correct-way-to-prevent-loading-thematic-scripts/
 *
 */

function childtheme_remove_superfish() {
  remove_theme_support('thematic_superfish');
 }
add_action('wp_enqueue_scripts','childtheme_remove_superfish', 9);

/**
 * Script Manager
 *
 * Setup for adding and removing scripts and styling. The deregister styles & scripts sections can
 * be used for conditional loading plugin scripts to only load where they are used.s
 *
 * Reference http://wpcandy.com/teaches/how-to-load-scripts-in-wordpress-themes/
 *
 */

function childtheme_script_manager() {
  // wp_register_script template ( $handle, $src, $deps, $ver, $in_footer );

  // registers modernizr script, childtheme path, no dependency, no version, loads in header
  wp_register_script('modernizr-js', get_stylesheet_directory_uri() . '/js/modernizr.js', false, false, false);
  // registers misc custom script, childtheme path, yes dependency is jquery, no version, loads in footer
  wp_register_script('custom-js', get_stylesheet_directory_uri() . '/js/custom.js', array('jquery'), false, true);
 // register styles which are to be queued in the theme
  wp_register_style('google-fonts', 'http://fonts.googleapis.com/css?family=Open+Sans:400,700');

  // enqueue the styles for use in theme
  wp_enqueue_style ('google-fonts');

  // enqueue the scripts for use in theme
  wp_enqueue_script ('modernizr-js');
  wp_enqueue_script('custom-js');
}
add_action('wp_enqueue_scripts', 'childtheme_script_manager');

// deregister styles
function childtheme_deregister_styles() {

}
add_action('wp_print_styles', 'childtheme_deregister_styles', 100);

// deregister scripts
function childtheme_deregister_scripts() {
  // removes themaitc-js which has more superfish scripts
  wp_dequeue_script('thematic-js');
}
add_action( 'wp_print_scripts', 'childtheme_deregister_scripts', 100 );

/**
 * Modernizr add 'no-js' class
 *
 * This filter adds the 'no-js' class to the HTML tag, which Modernizr will remove
 * (if Javascript is enabled) and replace it with a "js" class. This is super useful
 * for providing CSS fallbacks, but Modernizr does a ton more.
 *
 * Reference http://modernizr.com/docs/
 *
 */

function childtheme_html_class( $class_att ) {
  $class_att = "no-js";
  return $class_att;
}
add_filter( 'thematic_html_class', 'childtheme_html_class' );

/**
 * Add Favicon
 *
 * The Favicon is actually really complicated, but a quick and dirty method is to at
 * least add a 32x32 ,ico file (at the least).
 *
 * Reference http://www.jonathantneal.com/blog/understand-the-favicon/
 * Photoshop Plugin to save ICO files http://www.telegraphics.com.au/sw/
 *
 */

function childtheme_add_favicon() { ?>
<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/favicon.ico" />
<?php }
add_action('wp_head', 'childtheme_add_favicon');

/**
 * Clean up <head> of Site
 *
 * Wordpress by default throws in all kinds of relational links, for SEO purposes,
 * sometimes they work and sometimes they don't. A Plugin like WordPress SEO can
 * handle some of these also, but others are not included.
 *
 * Reference http://scottnix.com/polishing-thematics-head/
 *
 */

// remove really simple discovery
remove_action('wp_head', 'rsd_link');
// remove windows live writer xml
remove_action('wp_head', 'wlwmanifest_link');
// remove index relational link
remove_action('wp_head', 'index_rel_link');
// remove parent relational link
remove_action('wp_head', 'parent_post_rel_link');
// remove start relational link
remove_action('wp_head', 'start_post_rel_link');
// remove prev/next relational link
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
// remove short link
remove_action('wp_head', 'wp_shortlink_wp_head');

/**
 * Register Two Additional Menus
 *
 * Not always needed, but a lifesaver if you need more custom menus for widget areas.
 *
 */

function childtheme_register_menus() {
  register_nav_menu('secondary-menu', 'Secondary Menu');
  register_nav_menu('tertiary-menu', 'Tertiary Menu');
}
add_action('thematic_child_init', 'childtheme_register_menus');

/**
 * Remove Widgets in Admin.
 *
 * All this does is removes the widgets from being selected in the admin. This is helpful
 * if you aren't using the widgets, no point in looking at them or having to explain why
 * they are there.
 *
 * update: 0.1.1 no longer hidding widget areas that are not often used by default, tech
 * savvy pepole (who dig into the php) weren't aware they are an option.
 */

function childtheme_hide_areas( $content ) {

  // often used
  // unset( $content['Primary Aside'] );
  // unset( $content['Secondary Aside'] );
  // unset( $content['1st Subsidiary Aside'] );
  // unset( $content['2nd Subsidiary Aside'] );
  // unset( $content['3rd Subsidiary Aside'] );

  // not often used
  // unset( $content['Index Top'] );
  // unset( $content['Index Insert'] );
  // unset( $content['Index Bottom'] );
  // unset( $content['Single Top'] );
  // unset( $content['Single Insert'] );
  // unset( $content['Single Bottom'] );
  // unset( $content['Page Top'] );
  // unset( $content['Page Bottom'] );
  return $content;
}
add_filter('thematic_widgetized_areas', 'childtheme_hide_areas');

/**
 * Responsive Menu Structure
 *
 * Modified to add toggle in link format instead of <h3> that is defaulted from the parent
 * theme. his basic structure comes from a mobile pattern from the link below.
 *
 * Reference http://codepen.io/bradfrost/pen/vljdx
 *
 */

function childtheme_override_access() {
  ?>
  <div id="access" role="navigation">
    <a class="menu-toggle" href="#">Menu</a>
    <?php
    if ( ( function_exists( 'has_nav_menu' ) ) && ( has_nav_menu( apply_filters( 'thematic_primary_menu_id', 'primary-menu' ) ) ) ) {
      echo  wp_nav_menu( thematic_nav_menu_args () );
    } else {
      echo  thematic_add_menuclass( wp_page_menu( thematic_page_menu_args () ) );
    }
    ?>
  </div><!-- #access -->
  <?php
}

/**
 * Modify Navigational Elements
 *
 * The Navigation Above feature of Thematic is pretty silly (and ugly), so that is
 * completely removed. The second function removes the functionality on single posts
 * there are much better ways to handle this (plugins).
 *
 */

// override completely removes nav above functionality
function childtheme_override_nav_above() {
    // silence
}

function childtheme_override_nav_below() {
  if ( !is_single() ) { ?>
    <nav id="nav-below" class="navigation" role="navigation">
      <?php if ( function_exists( 'wp_pagenavi' ) ) {
        wp_pagenavi();
      } else { ?>
        <div class="nav-previous"><?php next_posts_link( sprintf ('<span class="meta-nav">&laquo;</span> %s', __('Older posts', 'thematic') ) ) ?></div>
        <div class="nav-next"><?php previous_posts_link( sprintf ('%s <span class="meta-nav">&raquo;</span>',__( 'Newer posts', 'thematic') ) ) ?></div>
      <?php } ?>
    </nav>
  <?php }
}

/**
 * Thematic Featured Image Size
 *
 * Appears on anything with an excerpt set, the default is 100x100 which is ridiculously
 * small, this swaps it out for a more manageable 300x300, but can be easily changed by
 * modifying the sizes.
 *
 */

function childtheme_post_thumb_size( $size ) {
  $size = array(300,300);
  return $size;
}
add_filter('thematic_post_thumb_size', 'childtheme_post_thumb_size');

/*
 * Modify Widget Titles
 *
 * Thematic now inputs an H1 for the asides, in HTML5 this is ok, but SEO's will cringe.
 * I haven't really seen any data showing that using multiple H1's for what is essentially
 * aside or off topic is fine for search engines, and from what I have seen no one has
 * really been bold enough to jump on that bandwagon, so this reverts them back to H5's instead.
 *
 */

function childtheme_before_widgettitle( $content ) {
  $content = "<h5 class=\"widgettitle\">";
  return $content;
}
add_filter( 'thematic_before_title', 'childtheme_before_widgettitle');

function childtheme_after_widgettitle( $content ) {
  $content = "</h5>\n";
  return $content;
}
add_filter( 'thematic_after_title', 'childtheme_after_widgettitle');

/*
 * Modify Search Widget
 *
 * This is pretty much required for responsive sites, you can set it with CSS, but this
 * is a backup to make sure the box isn't super big. Also the second function allows you
 * to change the text, the default text is sub optimal, "Type to search and hit enter" or
 * something like that, way too long.
 *
 */

// shorten the input box length
function childtheme_search_form_length() {
  return "16";
}
add_filter('thematic_search_form_length', 'childtheme_search_form_length');

// change the default search box text
function childtheme_search_field_value() {
  return "Search";
}
add_filter('search_field_value', 'childtheme_search_field_value');







// new shit

//disable comments
function remove_comments () {
  remove_action('thematic_comments_template', 'thematic_include_comments', 5);
}
add_action('thematic_child_init', 'remove_comments');



// kill the post header information, loading this below in the post footer
function childtheme_override_postheader_postmeta() {
    // silence!
}


// example of changing up the display of the entry-utility for a different look
function childtheme_override_postfooter() {
    $post_type = get_post_type();
    $post_type_obj = get_post_type_object($post_type);
    $tagsection = get_the_tags();

    // Display nothing for "Page" post-type
    if ( $post_type == 'page' ) {
        $postfooter = '';
    // For post-types other than "Pages" press on
    } else {
        $postfooter = '<footer class="entry-utility">';
        $postfooter .= '<ul class="main-utilities">';
        $postfooter .= '<li><svg class="icon-calendar"><use xlink:href="' . get_stylesheet_directory_uri() . '/icons/icons.svg#icon-calendar"></use></svg>' . thematic_postmeta_entrydate() . '</li>';
        $postfooter .= '</ul>';
        $postfooter .= "</footer><!-- .entry-utility -->\n";
    }
    // Put it on the screen
    echo apply_filters( 'thematic_postfooter', $postfooter ); // Filter to override default post footer
}

function childtheme_postmeta_entrydate($entrydate) {
    $entrydate = '<span class="meta-prep meta-prep-entry-date">' . __('', 'thematic') . '</span>';
    $entrydate .= '<span class="entry-date">';
    $entrydate .= get_the_time( thematic_time_display() );
    $entrydate .= '</span>';
    return $entrydate;
}
add_filter('thematic_postmeta_entrydate', 'childtheme_postmeta_entrydate');

// remove unneeded code from postcategory
function childtheme_override_postfooter_postcategory() {
    $postcategory = '<span class="cat-links">';
    if ( is_category() && $cats_meow = thematic_cats_meow(', ') ) {
        $postcategory .= __('<span class="meta-prep meta-prep-category">Also posted in</span> ', 'thematic') . $cats_meow;
    } else {
        $postcategory .= __('<span class="meta-prep meta-prep-category">Posted in</span> ', 'thematic') . get_the_category_list(', ');
    }
    $postcategory .= '</span>';
    return apply_filters('thematic_postfooter_postcategory',$postcategory);
}

// remove unneeded code from posttags
function childtheme_override_postfooter_posttags() {
    if ( is_single() && !is_object_in_taxonomy( get_post_type(), 'category' ) ) {
        $tagtext = __('<span class="meta-prep meta-prep-tags">Tagged</span>', 'thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span> ');
    } elseif ( is_single() ) {
        $tagtext = __('<span class="meta-prep meta-prep-tags">Tagged</span>', 'thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span> ');
    } elseif ( is_tag() && $tag_ur_it = thematic_tag_ur_it(', ') ) {
        $posttags = '<span class="tag-links">' . __('<span class="meta-prep meta-prep-tags">Also tagged</span> ', 'thematic') . $tag_ur_it . '</span>' . "\n\n\t\t\t\t\t\t";
    } else {
        $tagtext = __('<span class="meta-prep meta-prep-tags">Tagged</span>', 'thematic');
        $posttags = get_the_tag_list("<span class=\"tag-links\"> $tagtext ",', ','</span>' . "\n\n\t\t\t\t\t\t");
    }
    return apply_filters('thematic_postfooter_posttags',$posttags);
}




// add random div example
function childtheme_aboveheader() { ?>
  <div class="header-wrap">
<?php }
add_action('thematic_aboveheader', 'childtheme_aboveheader');

// add random div example
function childtheme_belowheader() { ?>
  </div>
<?php }
add_action('thematic_belowheader', 'childtheme_belowheader');


// add random div example
function childtheme_abovefooter() { ?>
  <div class="footer-wrap">
<?php }
add_action('thematic_abovefooter', 'childtheme_abovefooter');

// add random div example
function childtheme_belowfooter() { ?>
  </div>
<?php }
add_action('thematic_belowfooter', 'childtheme_belowfooter');