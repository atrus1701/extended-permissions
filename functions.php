<?php


add_action( 'admin_init', 'exp_admin_init' );
add_filter( 'user_has_cap', 'exp_has_permissions', 10, 3 );
add_action( 'edit_term', 'exp_edit_term', 10, 3 );
add_action( 'admin_enqueue_scripts', 'exp_admin_enqueue_scripts' );


if( ! function_exists( 'exp_admin_init' ) ):
function exp_admin_init()
{
	$taxonomies = get_taxonomies();
	foreach( $taxonomies as $tax_name ) {
		add_action( "${tax_name}_edit_form_fields", 'exp_edit_form_fields', 10, 2 );
	}
	
// 	global $pagenow;
// 	if( 'post.php' === $pagenow )
// 	{
// 		if( ! empty( $_POST ) && isset( $_POST['post_ID'] ) ) {
// 			$post_id = $_POST['post_ID'];
// 		} elseif( ! empty( $_GET ) && isset( $_GET['post'] ) ) {
// 			$post_id = $_GET['post'];
// 		} else {
// 			return;
// 		}
// 		
// 		if( current_user_can( 'edit_post', $post_id, 'exp_disable_filter' ) ) {
// 			return;
// 		}
// 
// 		$model = ExtendedPermissions_Model::get_instance();
// 		if( $model->user_is_post_editor( $post_id, get_current_user_id() ) ) {
// 			remove_meta_box( 'categorydiv', 'post', 'normal' );
// 		}
// 	}
}
endif;


/**
 * 
 */
if( ! function_exists( 'exp_has_permissions' ) ):
function exp_has_permissions( $allcaps, $cap, $args )
{
	if( 'exp_disable_filter' === $args[ count( $args ) - 1 ] ) {
		return $allcaps;
	}
	
	switch( $args[0] )
 	{
		case 'edit_post':
			if( count( $args ) < 3 ) break;
			
			$model = ExtendedPermissions_Model::get_instance();
			if( $model->user_is_post_editor( $args[2], $args[1] ) ) {
				$allcaps['edit_others_posts'] = true;
				$allcaps['edit_posts'] = true;
				$allcaps['edit_private_posts'] = true;
				$allcaps['edit_published_posts'] = true;
			}
			break;
		
		case 'edit_posts':
			if( is_admin() ) {
				$model = ExtendedPermissions_Model::get_instance();
				if( $model->user_is_editor( $args[1] ) ) {
					$allcaps['edit_posts'] = true;
					break;
				}
			}
			
		case 'edit_others_posts':
		case 'edit_published_posts':
		case 'edit_private_posts':
			global $pagenow;
			if( ! is_admin() ) break;
			
			switch( $pagenow )
			{
				case 'post.php':
					if( ! empty( $_POST ) && isset( $_POST['post_ID'] ) ) {
						$post_id = $_POST['post_ID'];
					} elseif( ! empty( $_GET ) && isset( $_GET['post'] ) ) {
						$post_id = $_GET['post'];
					} else {
						break;
					}
					
					$model = ExtendedPermissions_Model::get_instance();
					if( $model->user_is_post_editor( $post_id, $args[1] ) ) {
						$allcaps['edit_others_posts'] = true;
						$allcaps['edit_posts'] = true;
						$allcaps['edit_private_posts'] = true;
						$allcaps['edit_published_posts'] = true;
					}
					break;
				
				case 'edit.php':
					$allcaps['edit_others_posts'] = true;
					$allcaps['edit_posts'] = true;
					$allcaps['edit_private_posts'] = true;
					$allcaps['edit_published_posts'] = true;
					break;
			}
			break;
		
		default:
			break;
	}
	
	return $allcaps;
}
endif;


/**
 * 
 */
if( ! function_exists( 'exp_edit_form_fields' ) ):
function exp_edit_form_fields( $tag, $taxonomy )
{
	$model = ExtendedPermissions_Model::get_instance();
	$term_id = $tag->term_id;
	$permitted_users = $model->get_permissions_for_taxonomy( $taxonomy, $term_id );
	$all_users = get_users();

	?>
	<tr class="form-field term-editors-wrap">
		<th scope="row"><label for="editors"><?php _e( 'Editors' ); ?></label></th>
		<td>

		<input type="hidden" name="permitted_users[dummy_value]" value="1" />
		
		<div id="exp-user-list">
		
		<?php foreach( $all_users as $user ): ?>
			<div class="user">
			<input
				type="checkbox"
				name="permitted_users[<?php echo $user->ID; ?>]"
				value="1"
				<?php checked( in_array( $user->ID, $permitted_users ) ); ?> />
			<span class="search-text"><?php echo $user->display_name; ?> (<?php echo $user->user_login; ?>)</span>
			</div>
		<?php endforeach; ?>
		
		</div>
		</td>
	</tr>	
 	
 	<?php
}
endif;



/**
 *
 */
if( ! function_exists( 'exp_edit_term' ) ):
function exp_edit_term( $term_id, $tt_id, $taxonomy_name )
{
	if( empty( $_REQUEST ) || ! isset( $_REQUEST['permitted_users'] ) ) {
		return;
	}
	
	$permitted_users = $_REQUEST['permitted_users'];
	unset( $permitted_users['dummy_value'] );
	$permitted_users = array_keys( $permitted_users );
	
	$model = ExtendedPermissions_Model::get_instance();
	$model->save_permissions_for_taxonomy( $taxonomy_name, $term_id, $permitted_users );
}
endif;


if( ! function_exists( 'exp_admin_enqueue_scripts' ) ):
function exp_admin_enqueue_scripts( $hook )
{
	if( 'term.php' != $hook ) return;
	
	wp_enqueue_script( 'jquery.checkbox-search-selector', plugin_dir_url( __FILE__ ) . 'jquery.checkbox-search-selector.js' );
	wp_enqueue_script( 'extended-permissions', plugin_dir_url( __FILE__ ) . 'script.js' );
}
endif;
