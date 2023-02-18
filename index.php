// Create custom post type for vCards
function create_vcard_post_type() {
  register_post_type( 'vcard',
    array(
      'labels' => array(
        'name' => __( 'Staff Info' ),
        'singular_name' => __( 'biodata' )
      ),
      'public' => true,
      'has_archive' => true,
      'exclude_from_search' => true,
	  //'menu_icon' => 'dashicons-businessman',
      'supports' => array('title', 'thumbnail'),
	  'rewrite' => array(
       'slug' => '/bio')
    )
  );
}
add_action( 'init', 'create_vcard_post_type' );
