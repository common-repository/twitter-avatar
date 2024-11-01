<?php
/*
Plugin Name: Twitter Avatar
Plugin URI: http://businessxpand.com
Description: Allows a User to enter their Twitter username when posting a comment on your blog and a link to their Twitter page will appear next to their comment. This plugin will also replace the avatar on the comment with their picture on Twitter.

Two small changes to your theme's comments.php file is needed for this plugin to work correctly. 
Author: BusinessXpand.com
Version: 1.1
Author URI: http://businessxpand.com
*/

/**
 * @package Twitter Avatar
 * @author BusinessXpand.com
 * @version 1.1
 */

/**
 * Twitter Avatar
 *
 * @copyright 2009 Business Xpand
 * @license GPL v2.0
 * @author Thomas McGregor
 * @version 1.1
 * @link http://www.businessxpand.com/
 * @since File available since Release 0.9
 */

if ( !class_exists( 'BxNews' ) ) include_once( 'class-bx-news.php' );

if( !class_exists('twitter_avatar') ) {
		include('class.messageStack.php');
		class twitter_avatar {
				var $profile_image_url, $messageStack;
		
				function twitter_avatar()
				{
						$this->profile_image_url = array();
						$this->messageStack = new messageStack();
				
						add_action( 'comment_post', array( &$this,'update_comment_twitter' ) );
						add_filter( 'get_avatar', array(&$this, 'get_twitter_avatar' ), 1, 3 );
						register_activation_hook( __FILE__, array( &$this, 'init' ) );


						add_action('init', array( &$this, 'processAdmin' ) );
						add_action('admin_menu', array( &$this, 'adminMenu' ) );
				}
				
				function adminMenu() {
						add_menu_page( 'Twitter Avatar', 'Twitter Avatar', 'level_7', 'twitter-avatar/twitter-avatar.php', array( &$this, 'adminPage' ) );
				}
				
				function processAdmin() {
						$error = false;
						if(isset($_POST['action']) && $_POST['action'] == 'twitter_avatar_update_settings' && isset($_POST['submit']) && $_POST['submit'] == 'Update Settings') {
								if($_POST['avatar_size'] != '') {
										update_option('twitter_avatar_size', intval($_POST['avatar_size']));
										$this->messageStack->addMessage('Settings saved', 'updated');
								}
								else {
										update_option('twitter_avatar_size', '');
										$this->messageStack->addMessage('Settings saved', 'updated');
								}
						}
				}
				
				function adminPage() {
						$output = "<div class='wrap'>" . "\r\n";
						$output .= "<h2>Twitter Avatar - Options</h2>\r\n";
						$output .= $this->messageStack->outputMessages();
						$output .= "<form action=''	method='post' name='settings_form' id='settings_form' />\r\n";
						$output .= "<input type='hidden' name='action' value='twitter_avatar_update_settings' />\r\n";
						$output .= "<table class='form-table'>\r\n";
						$output .= "	<tbody>\r\n";
						$output .= "		<tr>\r\n";
						$output .= "			<th scope='row'>Avatar size</th>\r\n";
						$output .= "			<td>\r\n";
						$output .= "				<input type='text' name='avatar_size' id='avatar_size' value='" . get_option('twitter_avatar_size') . "' size='4' />&nbsp;pixels\r\n";
						$output .= "			</td>\r\n";
						$output .= "		</tr>\r\n";
						$output .= "		<tr>\r\n";
						$output .= "			<td colspan='2'><strong>Note:</strong> Entering a size will override the any value given in template files. Twitter outputs an avatar that is 73 x 73 pixels in size, entering a size larger than this will affect the quality of the avatar shown. To use the standard avatar size, or to use the size specified in the theme, leave the field blank.</td>\r\n";
						$output .= "		</tr>\r\n";
						$output .= "	</tbody>\r\n";
						$output .= "</table>\r\n";
						$output .= "<p class='submit'>\r\n";
						$output .= "<input type='submit' value='Update Settings' class='button-primary' name='submit' />\r\n";
						$output .= "</p>\r\n";
						$output .= "</form>\r\n";
						$output .= "</div>";
						echo $output;
				}

				function init() 
				{
					global $wpdb;
				
					if($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "comments_meta'") != $wpdb->prefix.'comments_meta') {
						$sql = "CREATE TABLE `" . $wpdb->prefix . "comments_meta` (
										`comments_meta_id` BIGINT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
										`comment_id` BIGINT( 20 ) UNSIGNED NOT NULL ,
										`meta_key` VARCHAR( 255 ) NULL DEFAULT NULL ,
										`meta_value` LONGTEXT NULL DEFAULT NULL
										)";
						
						require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
						dbDelta($sql);
					}
				}

				function update_comment_twitter($comment_id)
				{
						if( isset($_POST['author_twitter']) && !empty($_POST['author_twitter']) && $_POST['author_twitter'] != '') {
								$author_twitter = $_POST['author_twitter'];
								setcookie('author_twitter' . COOKIEHASH, $author_twitter, time()+60*60*24*30); // Added in version 0.9.1
								$this->update_comment_meta($comment_id, 'author_twitter', $author_twitter);
						}
				}

				function update_comment_meta($comment_id, $meta_key, $meta_value)
				{
						global $wpdb;
			
						$wpdb->query( $wpdb->prepare( "INSERT INTO " . $wpdb->prefix . "comments_meta ( comments_meta_id, comment_id, meta_key, meta_value ) VALUES ( NULL, %d, %s, %s )", $comment_id, $meta_key, $meta_value ) );
				}
				
				function get_author_twitter($comment_id)
				{
						global $wpdb;
						
						$author_twitter = $wpdb->get_row( "SELECT meta_value FROM " . $wpdb->prefix . "comments_meta WHERE comment_id = $comment_id AND meta_key = 'author_twitter' " );
						
						if ( is_null($author_twitter) || !$author_twitter->meta_value || $author_twitter->meta_value == '') {
							return '';
						}
						
						return $author_twitter->meta_value;
				}
				
				function get_twitter_avatar($avatar, $id_or_email, $size) {
						global $comment;
						
						$saved_size = get_option('twitter_avatar_size');
						if($saved_size !== false && $saved_size != '') {
								$avatar = str_replace("'$size'", $saved_size, $avatar);
								//$avatar = str_replace("'$size'", $saved_size, $avatar);
						}
						
						preg_match_all('/<\s*img [^\>]*src\s*=\s*[\""\']?([^\""\'\s>]*)/i', $avatar, $src_matches);
						$twitter_username = $this->get_author_twitter( $comment->comment_ID );

						if($twitter_username != '') {
								if( isset( $this->profile_image_url[$twitter_username] ) ) {
										return str_replace($src_matches[1][0], $this->profile_image_url[$twitter_username], $avatar);
								}
								else {
										$result = $this->get_user_info( $twitter_username );
										$this->profile_image_url[$twitter_username] = str_replace('_normal', '_bigger', $result->profile_image_url );
															
										if( $result !== false) {
											return str_replace($src_matches[1][0], $this->profile_image_url[$twitter_username], $avatar);
										}
								}
						}
						
						return $avatar;
				}
							
				function get_user_info($username){
						$request = 'http://twitter.com/users/' . $username . '.xml';
						return $this->process($request);
				}
				
				function process($url,$postargs=false)
				{
						$ch = curl_init($url);
		
						if($postargs !== false){
								curl_setopt ($ch, CURLOPT_POST, true);
								curl_setopt ($ch, CURLOPT_POSTFIELDS, $postargs);
						}
						
						
						curl_setopt($ch, CURLOPT_VERBOSE, 1);
						curl_setopt($ch, CURLOPT_NOBODY, 0);
						curl_setopt($ch, CURLOPT_HEADER, 0);                   
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);           
		
						$response = curl_exec($ch);
						
						$responseInfo=curl_getinfo($ch);
						curl_close($ch);
						
						
						if(intval($responseInfo['http_code'])==200){
								if(class_exists('SimpleXMLElement')){
										$xml = new SimpleXMLElement($response);
										return $xml;
								}else{
										return $response;    
								}
						}else{
								return false;
						}
				}  
		}
}

if( !isset ( $twitter_avatar ) ) {
		$twitter_avatar = new twitter_avatar;
}

if( !function_exists( 'twitter_comment' ) ) {
		function twitter_comment($comment, $args, $depth) {
				global $twitter_avatar;
				$GLOBALS['comment'] = $comment;
		
				if ( 'div' == $args['style'] ) {
					$tag = 'div';
					$add_below = 'comment';
				} else {
					$tag = 'li';
					$add_below = 'div-comment';
				}
		?>
				<<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
				<?php if ( 'ul' == $args['style'] ) : ?>
				<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
				<?php endif; ?>
				<div class="comment-author vcard">
				<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['avatar_size'] ); ?>
				<?php 
					$author_twitter = $twitter_avatar->get_author_twitter( $comment->comment_ID );
					$author_twitter = ( strlen($author_twitter) > 0 ? " (<a href='http://twitter.com/$author_twitter'>@" . $author_twitter . "</a>)" : ''); ?>
				<?php printf(__('<cite class="fn">%s' . $author_twitter . '</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
				</div>
		<?php if ($comment->comment_approved == '0') : ?>
				<em><?php _e('Your comment is awaiting moderation.') ?></em>
				<br />
		<?php endif; ?>
		
				<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>"><?php printf(__('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'&nbsp;&nbsp;','') ?>
				</div>
		
				<?php comment_text() ?>
		
				<div class="reply">
				<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				</div>
				<?php if ( 'ul' == $args['style'] ) : ?>
				</div>
				<?php endif; ?>
		<?php
		}
}
?>