<?php
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

// Save vCard information as .vcf file in media library
function save_vcard_information( $post_id ) {
  if ( 'vcard' != get_post_type( $post_id ) ) {
    return;
  }

$name = get_field( 'name', $post_id );
$company_name = get_field( 'company_name', $post_id );
$title = get_field( 'title', $post_id );
$address = get_field( 'work_address', $post_id );
$email = get_field( 'email', $post_id );
$phone = get_field( 'phone', $post_id );
$website = get_field( 'website', $post_id );
$facebook = get_field( 'facebook', $post_id );
$instagram = get_field( 'instagram', $post_id );
$youtube = get_field( 'youtube', $post_id );
$notes = get_field( 'notes', $post_id );
$field_image = get_field( 'image', $post_id );
$rev = date( "Y-m-d\TH:i:s", strtotime( get_the_modified_date( '', $post_id ) ) ) . 'Z';
$notes = str_replace("\r\n", "\n", $notes);
$notes = str_replace("\n", "\\n", $notes);
$thumbnail_id = get_post_thumbnail_id( $post_id );
$thumbnail = wp_get_attachment_image_src( $thumbnail_id, 'full' );
$image_path = $thumbnail[0];
$image_type = pathinfo( $image_path, PATHINFO_EXTENSION );
$image_data = file_get_contents( $image_path );
$base64_image = base64_encode( $image_data );

//print("Image path: {$image_path}\n");
//print("Image type: {$image_type}\n");
//print("Base64 encoded image: {$base64_image}\n");

$vcard = "BEGIN:VCARD\nVERSION:3.0\nN:{$name}\nORG:{$company_name}\nTITLE:{$title}\nADR:{$address}\nEMAIL;TYPE=WORK,INTERNET:{$email}\nTEL;TYPE=Contact,VOICE:{$phone}\nURL;TYPE=Website:{$website}\nURL;TYPE=Facebook:{$facebook}\nURL;TYPE=Instagram:{$instagram}\nURL;TYPE=Youtube:{$youtube}\nNOTE:{$notes}\nPHOTO;TYPE=JPEG;ENCODING=b:{$base64_image}\nEND:VCARD";
	
  $upload_dir = wp_upload_dir();
  $post_title = get_the_title( $post_id );
  $vcard_dir = $upload_dir['basedir'] . '/vcards';
  $file = $vcard_dir . '/' . sanitize_file_name( $post_title ) . '.vcf';

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
    return;
  }

  if ( ! file_exists( $vcard_dir ) ) {
    mkdir( $vcard_dir, 0755, true );
  }

  $args = array(
    'post_type' => 'attachment',
    'post_status' => 'inherit',
    'posts_per_page' => 1,
    'meta_query' => array(
      array(
        'key' => '_wp_attached_file',
        'value' => 'vcards/' . sanitize_file_name( $post_title ) . '.vcf',
        'compare' => 'LIKE',
      ),
    ),
  );
  $old_vcard = get_posts( $args );
  if ( $old_vcard ) {
    wp_delete_attachment( $old_vcard[0]->ID, true );
  }

  file_put_contents( $file, $vcard );

  $attachment = array(
    'guid'           => $upload_dir['baseurl'] . '/vcards/' . basename( $file ), 
    'post_mime_type' => 'text/x-vcard',
    'post_title'     => $post_title,
    'post_content'   => '',
    'post_status'    => 'inherit'
  );
  $attach_id = wp_insert_attachment( $attachment, $file, $post_id );

  update_field( 'vcard_file', $attach_id, $post_id );
}
add_action( 'save_post', 'save_vcard_information' );

//Hide vcf folder from appear in media library
function media_library_hide_vcards($where, $query) {
   if (
       (isset($_POST['action']) && ($_POST['action'] == 'query-attachments')) ||
       (isset($query->query['post_type']) && ($query->query['post_type'] == 'attachment'))
   ) {
      $where .= ' AND guid NOT LIKE "%wp-content/uploads/vcards%"';
   }

   return $where;
}

add_filter('posts_where', 'media_library_hide_vcards', 10, 2);

// Add Permalink Copy to Clickboard
// Add custom meta box to vcard post type edit screen
function vcard_permalink_meta_box() {
    add_meta_box(
        'vcard_permalink',
        'Permalink',
        'vcard_permalink_meta_box_callback',
        'vcard',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'vcard_permalink_meta_box' );

// Callback function to output the content of the custom meta box
function vcard_permalink_meta_box_callback( $post ) {
    $permalink = get_permalink( $post->ID );
    echo '<div style="background-color: #f1f1f1; padding: 10px;">';
    echo '<p><strong>Permalink:</strong> <input type="text" value="' . esc_url( $permalink ) . '" id="vcard-permalink" style="width: 100%;" readonly /></p>';
    echo '<button type="button" class="button button-primary" onclick="copyPermalink()">Copy to Clipboard</button>';
    echo '</div>';
    echo '<script>
    function copyPermalink() {
        var permalink = document.getElementById("vcard-permalink");
        permalink.select();
        document.execCommand("copy");
		alert("The link has been copied");
    }
    </script>';
}


function hide_permalink_boxes_vcard() {
global $post_type;
if( 'vcard' == $post_type ) {
echo '<style type="text/css">#comment-link-box, #edit-slug-box { display: none; };</style>';
}
}
add_action( 'admin_head-post-new.php', 'hide_permalink_boxes_vcard' );
add_action( 'admin_head-post.php', 'hide_permalink_boxes_vcard' );




?>
