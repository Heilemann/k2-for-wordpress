<?php
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header('Allow: POST');
	header("HTTP/1.1 405 Method Not Allowed");
	header("Content-type: text/plain");
    exit;
}

$k2_db_check = true;

function kill_data() {
	return '';
}

function check_db() {
	global $wpdb, $k2_db_check;

	if($k2_db_check) {
		// Check DB
		if(!$wpdb->dbh) {
			echo('Our database has issues. Try again later.');
		} else {
			echo('We\'re currently having site problems. Try again later.');
		}

		die();
	}
}

ob_start('kill_data');
register_shutdown_function('check_db');
require_once(dirname(__FILE__)."/../../../wp-config.php");
$k2_db_check = false;
ob_end_clean();

nocache_headers();

function fail($s) {
	header('HTTP/1.0 500 Internal Server Error');
	echo $s;
	exit;
}

$comment_post_ID = (int) $_POST['comment_post_ID'];

$status = $wpdb->get_row("SELECT post_status, comment_status FROM $wpdb->posts WHERE ID = '$comment_post_ID'");

if ( empty($status->comment_status) ) {
	do_action('comment_id_not_found', $comment_post_ID);
	fail( __('The post you are trying to comment on does not currently exist in the database.','k2_domain') );
} elseif ( 'closed' ==  $status->comment_status ) {
	do_action('comment_closed', $comment_post_ID);
	fail( __('Sorry, comments are closed for this item.','k2_domain') );
} elseif ( in_array($status->post_status, array('draft', 'pending') ) ) {
	do_action('comment_on_draft', $comment_post_ID);
	fail( __('The post you are trying to comment on has not been published.','k2_domain') );
}

$comment_author       = trim(strip_tags($_POST['author']));
$comment_author_email = trim($_POST['email']);
$comment_author_url   = trim($_POST['url']);
$comment_content      = trim($_POST['comment']);

// If the user is logged in
$user = wp_get_current_user();
if ( $user->ID ) {
	$comment_author       = $wpdb->escape($user->display_name);
	$comment_author_email = $wpdb->escape($user->user_email);
	$comment_author_url   = $wpdb->escape($user->user_url);
	if ( current_user_can('unfiltered_html') ) {
		if ( wp_create_nonce('unfiltered-html-comment_' . $comment_post_ID) != $_POST['_wp_unfiltered_html_comment'] ) {
			kses_remove_filters(); // start with a clean slate
			kses_init_filters(); // set up the filters
		}
	}
} else {
	if ( get_option('comment_registration') )
		fail( __('Sorry, you must be logged in to post a comment.','k2_domain') );
}

$comment_type = '';

if ( get_option('require_name_email') && !$user->ID ) {
	if ( 6 > strlen($comment_author_email) || '' == $comment_author )
		fail( __('Error: please fill the required fields (name, email).','k2_domain') );
	elseif ( !is_email($comment_author_email))
		fail( __('Error: please enter a valid email address.','k2_domain') );
}

if ( '' == $comment_content )
	fail( __('Error: please type a comment.','k2_domain') );


// Simple duplicate check
$dupe = "SELECT comment_ID FROM $wpdb->comments WHERE comment_post_ID = '$comment_post_ID' AND ( comment_author = '$comment_author' ";
if ( $comment_author_email ) $dupe .= "OR comment_author_email = '$comment_author_email' ";
$dupe .= ") AND comment_content = '$comment_content' LIMIT 1";
if ( $wpdb->get_var($dupe) ) {
	fail( __('Duplicate comment detected; it looks as though you\'ve already said that!','k2_domain') );
}


$commentdata = compact('comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'user_ID');

$comment_id = wp_new_comment( $commentdata );

$comment = get_comment($comment_id);
if ( !$user->ID ) {
	setcookie('comment_author_' . COOKIEHASH, $comment->comment_author, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_email_' . COOKIEHASH, $comment->comment_author_email, time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
	setcookie('comment_author_url_' . COOKIEHASH, clean_url($comment->comment_author_url), time() + 30000000, COOKIEPATH, COOKIE_DOMAIN);
}

@header('Content-type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

$comment->comment_type = 'comment';
$comment_index = $_POST['comment_count'] + 1;
?>

<li id="comment-<?php comment_ID(); ?>" class="<?php k2_comment_class($comment_index); ?>">
	<?php if (function_exists('gravatar')) { ?><a href="http://www.gravatar.com/" title="<?php _e('What is this?','k2_domain'); ?>"><img src="<?php gravatar("X", 32,  get_bloginfo('template_url')."/images/defaultgravatar.jpg"); ?>" class="gravatar" alt="<?php _e('Gravatar Icon','k2_domain'); ?>" /></a><?php } ?>
	<a href="#comment-<?php comment_ID(); ?>" class="counter" title="<?php _e('Permanent Link to this Comment','k2_domain'); ?>"><?php echo $comment_index; ?></a>
	<span class="commentauthor"><?php comment_author_link(); ?></span>

	<div class="comment-meta">
	<?php
		printf('<a href="#comment-%1$s" title="%2$s">%3$s</a>', 
			get_comment_ID(),
			(function_exists('time_since')?
				sprintf(__('%s ago.','k2_domain'),
					time_since(abs(strtotime($comment->comment_date_gmt . " GMT")), time())
				):
				__('Permanent Link to this Comment','k2_domain')
			),
			sprintf(__('%1$s at %2$s','k2_domain'),
				get_comment_date(__('M jS, Y','k2_domain')),
				get_comment_time()
			)
		);
	?>
	<?php if (function_exists('quoter_comment')) { quoter_comment(); } ?>
	<?php if (function_exists('jal_edit_comment_link')) { jal_edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">','</span>', '<em>(Editing)</em>'); } else { edit_comment_link(__('Edit','k2_domain'), '<span class="comment-edit">', '</span>'); } ?>
	</div>

	<div class="comment-content">
		<?php comment_text(); ?> 
	</div>

	<?php if ('0' == $comment->comment_approved) { ?><p class="alert"><strong><?php _e('Your comment is awaiting moderation.','k2_domain'); ?></strong></p><?php } ?>
</li>
