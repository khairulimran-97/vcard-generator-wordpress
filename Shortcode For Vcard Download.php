<?php

function vcard_download_shortcode( $atts ) {
  $post_id = get_the_ID();
  $vcard_file = get_field( 'vcard_file', $post_id );
  if ( $vcard_file ) {
    $vcard_url = wp_get_attachment_url( $vcard_file );
    return '<a href="' . $vcard_url . '">Download vCard</a>';
  }
  return '';
}
add_shortcode( 'vcard_download', 'vcard_download_shortcode' );



function vcard_url_shortcode( $atts ) {
$post_id = get_the_ID();
$vcard_file = get_field( 'vcard_file', $post_id );
if ( $vcard_file ) {
$vcard_url = wp_get_attachment_url( $vcard_file );
return $vcard_url;
}
return '';
}
add_shortcode( 'vcard_url', 'vcard_url_shortcode' );
