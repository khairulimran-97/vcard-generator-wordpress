<?php

function add_vcard_permalink_column( $columns ) {
  $columns['vcard_permalink'] = __( 'Permalink' );
  return $columns;
}
add_filter( 'manage_vcard_posts_columns', 'add_vcard_permalink_column' );

function vcard_permalink_column_content( $column, $post_id ) {
  if ( $column == 'vcard_permalink' ) {
    $permalink = get_permalink( $post_id );
    echo '<input style="width: 70%;" type="text" value="' . $permalink . '" id="vcpermalink-' . $post_id . '" readonly />';
    echo '<button type="button" class="button button-medium" data-clipboard-target="#vcpermalink-' . $post_id . '">Copy</button>';
  }
}
add_action( 'manage_vcard_posts_custom_column', 'vcard_permalink_column_content', 10, 2 );

function add_vcard_permalink_column_style() {
  echo '<style>.column-vcard_permalink { width: 35%; }</style>';
}
add_action( 'admin_head', 'add_vcard_permalink_column_style' );

function load_jquery_in_admin() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('clipboard.js', 'https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js', array(), '2.0.4', true);
}
add_action( 'admin_enqueue_scripts', 'load_jquery_in_admin' );

function add_vcard_permalink_script() {
    ?>
    <script>
        jQuery(document).ready(function($){
            var clipboard = new ClipboardJS('.button-medium');
            clipboard.on('success', function(e) {
                alert('The permalink has been copied to your clipboard');
            });
        });
    </script>
    <?php
}
add_action('admin_footer', 'add_vcard_permalink_script');

