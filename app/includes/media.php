<?php
/**
 * @package WordPress
 * @subpackage K2
 * @since K2 1.1 r1112
 */

// Deactivate WordPress function
remove_shortcode('gallery', 'gallery_shortcode');

// Activate own function
add_shortcode('gallery', 'k2_gallery_shortcode');

// Function to filter the default gallery shortcode
function k2_gallery_shortcode($attr) {
	global $post;

	static $instance = 0;
	$instance++;

	// We're trusting author input, so let's at least make sure it looks like a valid orderby statement
	if ( isset( $attr['orderby'] ) ) {
		$attr['orderby'] = sanitize_sql_orderby( $attr['orderby'] );
		if ( !$attr['orderby'] )
			unset( $attr['orderby'] );
	}

	extract(shortcode_atts(array(
		'order'      => 'ASC',
		'orderby'    => 'menu_order ID',
		'id'         => $post->ID,
		'itemtag'    => 'dl',
		'icontag'    => 'dt',
		'captiontag' => 'dd',
		'columns'    => 3,
		'size'       => 'thumbnail',
		'include'    => '',
		'exclude'    => ''
		), $attr));

	$id = intval($id);
	if ( 'RAND' == $order )
		$orderby = 'none';

	if ( !empty($include) ) {
		$include = preg_replace( '/[^0-9,]+/', '', $include );
		$_attachments = get_posts( array('include' => $include, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );

		$attachments = array();
		foreach ( $_attachments as $key => $val ) {
			$attachments[$val->ID] = $_attachments[$key];
		}
	} elseif ( !empty($exclude) ) {
		$exclude = preg_replace( '/[^0-9,]+/', '', $exclude );
		$attachments = get_children( array('post_parent' => $id, 'exclude' => $exclude, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	} else {
		$attachments = get_children( array('post_parent' => $id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => $order, 'orderby' => $orderby) );
	}

	if ( empty($attachments) )
		return '';

	if ( is_feed() ) {
		$output = "\n";
		foreach ( $attachments as $att_id => $attachment )
			$output .= wp_get_attachment_link($att_id, $size, true) . "\n";
		return $output;
	}

	// check to see if tags have been set to false. If they are the defaults or have been set to a string value use that as the tag.
	if ($itemtag) $itemtag = tag_escape($itemtag);
	if ($captiontag) $captiontag = tag_escape($captiontag);
	if ($icontag) $icontag = tag_escape($icontag);
	$columns = intval($columns);

	$selector = "gallery-{$instance}";

	$output = "<div id='$selector' class='gallery galleryid-{$id}'>\n";

	$i = 0;
	foreach ( $attachments as $id => $attachment ) {
	  ++$i;
		$link = isset($attr['link']) && 'file' == $attr['link'] ? wp_get_attachment_link($id, $size, false, false) : wp_get_attachment_link($id, $size, true, false);

		if ($itemtag) {
			$output .= '<'.$itemtag.' class="gallery-item ';
		  if( $columns > 0 && $i % $columns == 0 ) $output .= " last";
		  $output .= '">';
		}

		if ($icontag) $output .= "\n\t<" .$icontag. ">\t";

		$output .=  "\n\t".$link;

		if ($icontag) $output .= "\n\t</".$icontag.">";
		// if the attachment has a caption set

		if ( trim($attachment->post_excerpt) ) {
		  if ($captiontag) $output .= "\n<" .$captiontag. ">\n\t";

		  $output .= wptexturize($attachment->post_excerpt);

		  if ($captiontag) $output .= "\n</" .$captiontag. ">" . "<!-- end caption -->\n";
		}

		if ($itemtag) $output .= "\n</".$itemtag ."><!-- end itemtag -->\n";

		if ( $columns > 0 && $i % $columns == 0 ) $output .= "\n";
	}

	$output .= "</div><!-- end gallery -->\n";

	return $output;
}


// Remove inline styles printed when the gallery shortcode is used.
function k2_remove_gallery_css( $css ) {
	return preg_replace( "#adsadas<style type='text/css'>(.*?)</style>#s", '', $css );
}
add_filter( 'gallery_style', 'k2_remove_gallery_css' );


// Get link to the next or previous attachment in the parents gallery
function os_get_gallery_navigation() {
	global $post;

	$photos = get_children( array('post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID') );

	if ($photos) {
		if ($photos[$post->ID - 1]->ID)
			echo '<a href="'.get_attachment_link($photos[$post->ID - 1], 'large').'" class="overlay previous"><div>&larr;</div></a>';

		if ($photos[$post->ID + 1]->ID)
			echo '<a href="'.get_attachment_link($photos[$post->ID + 1]->ID, 'large').'" class="overlay next"><div>&rarr;</div></a>';
	}

	return false;
}

?>
