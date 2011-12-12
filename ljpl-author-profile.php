<?php
/*
 Plugin Name: GPlus Link Author Profile
 Plugin URI: http://blog.ljasinski.pl
 Description: Simple plugin to get your author's profile linked in Google. 
 Grants autorship photo in Google SERP. Visit blog.ljasinski.pl for more details.
 All settings are per user, change them in your profile.
 Version: 1.2
 Author: Łukasz Jasiński
 Author URI: http://www.ljasinski.pl
 Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 It does *some* sanitation but beware who you allow to write bio in you site, because
 now default wordpress filter for bio is off!
 */

// #############################
// ### Add rel-author to the end of article

function jlpl_gplus_content_link($text) {
	$text .= 
		'<p class="author-link">' .
		get_the_author_meta('ljplcontentauthortext') .
		' <a href="' . 
		get_author_posts_url(get_the_author_meta( 'ID' )).
		'" rel="author">' .
		get_the_author_meta('display_name') . 
		'</a></p>';
	
	return $text;
}


	add_filter('the_content', 'jlpl_gplus_content_link');	
// #############################
// ### Adding link to author's profile 	
	
function jlpl_gplus_bio_link($text) {
	
	
	$find = '[gplus]';
		$gplus = get_the_author_meta('ljplgplus');		
		$replace = "
			<a rel=\"me\" href=\"".$gplus."\" title=\"My Google+ profile\">
			  <img src=\"http://www.google.com/images/icons/ui/gprofile_button-32.png\" width=\"32\" height=\"32\">
			</a>";
	$text = str_replace($find,$replace,$text);
	$text = strip_tags($text,'<a><img>');
	return $text;
}

// #############################
// ### Backend settings
remove_filter('pre_user_description', 'wp_filter_kses');


add_filter('pre_user_description', 'wp_filter_post_kses');
add_filter('pre_user_description', 'jlpl_gplus_bio_link');		
add_action( 'show_user_profile', 'ljpl_gplus_link_field_add' );
add_action( 'edit_user_profile', 'ljpl_gplus_link_field_add' );



function ljpl_gplus_link_field_add( $user ) { ?>

	<h3>GPlus Link Author Profile</h3>
	<table class="form-table">
		<tr>
			<th><label for="ljplgplus">Google+</label></th>
			<td>
				<input type="text" name="ljplgplus" id="ljplgplus" value="<?php echo esc_attr( get_the_author_meta( 'ljplgplus', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Paste your Google+ profile URL. After saving 
				insert [gplus] code into your bio to add a tagged link to your profile.
				</span>
			</td>
		</tr>
		<tr><th><label for="ljpl-content-author-show"></label></th>
			<td>
				<input type="checkbox" name="ljpl-content-author-show" id="ljpl-content-author-show"<?php if(get_the_author_meta( 'ljplcontentauthorshow', $user->ID ) ) echo ' checked="checked"' ?>" class="checkbox" /><br />
				<span class="description">Show a link at the end of each post
				</span>			
			</td>	
		</tr>
		<tr><th><label for="ljpl-content-author-text"></label></th>		
			<td>
				<input type="text" name="ljpl-content-author-text" id="ljpl-content-author-text" value="<?php echo esc_attr( get_the_author_meta( 'ljplcontentauthortext', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Add a text to the end of the post with
				a link to your author page. ex. "Other post by".
				</span>			
			</td>
			
		</tr>

	</table>
<?php }

add_action( 'personal_options_update', 'ljpl_gplus_link_field_save' );
add_action( 'edit_user_profile_update', 'ljpl_gplus_link_field_save' );


function ljpl_gplus_link_field_save( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	update_usermeta( $user_id, 'ljplgplus', $_POST['ljplgplus'] );
	update_usermeta( $user_id, 'ljpl-content-author-show', $_POST['ljpl-content-author-show'] );
	update_usermeta( $user_id, 'ljpl-content-author-text', $_POST['ljpl-content-author-text'] );
}