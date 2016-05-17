<?php
/**
 * The main model for the Extended Permissions plugin.
 * 
 * @package    extended-permissions
 * @subpackage classes/model
 * @author     Crystal Barton <atrus1701@gmail.com>
 */
if( !class_exists('ExtendedPermissions_Model') ):
class ExtendedPermissions_Model
{
	/**
	 * The only instance of the current model.
	 * @var  ConnectionsHub_Model
	 */
	private static $instance = null;

	/**
	 * The last error saved by the model.
	 * @var  string
	 */
	public $last_error = null;
	
	
	/**
	 * Private Constructor.  Needed for a Singleton class.
	 */
	protected function __construct() { }


	/**
	 * Sets up the "children" models used by this model.
	 */
	protected function setup_models()
	{
	}
	

	/**
	 * Get the only instance of this class.
	 * @return  ConnectionsHub_Model  A singleton instance of the model class.
	 */
	public static function get_instance()
	{
		if( self::$instance	=== null )
		{
			self::$instance = new ExtendedPermissions_Model();
			self::$instance->setup_models();
		}
		return self::$instance;
	}


//========================================================================================
//========================================================================= Log file =====


	/**
	 * Clear the log.
	 */
	public function clear_log()
	{
		file_put_contents( EXTENDED_PERMISSIONS_LOG_FILE );
	}
	

	/**
	 * Write a line to a log file.
	 * @param  string  $text  The line of text to insert into the log.
	 * @param  bool  $newline  True if a new line character should be inserted after the line, otherwise False.
	 */
	public function write_to_log( $text = '', $newline = true )
	{
		$text = print_r( $text, true );
		if( $newline ) $text .= "\n";
		file_put_contents( EXTENDED_PERMISSIONS_LOG_FILE, $text, FILE_APPEND );
	}
	
	
//========================================================================================
//===============================================  =====
	
	
	/**
	 * 
	 */
	public function get_permissions()
	{
		return get_option( 'extended-permissions', array() );
	}
	
	
	/**
	 * 
	 */
	public function get_permissions_for_taxonomy( $taxonomy_name, $term_id )
	{
		$extended_permissions = $this->get_permissions();
		if( ! array_key_exists( $taxonomy_name, $extended_permissions ) ) {
			return array();
		}

		$term = get_term( $term_id, $taxonomy_name );
		if( ! $term ) return false;
		
		$terms = array( $term->term_id );
		
		if( is_taxonomy_hierarchical( $taxonomy_name ) ) {
			while( $term->parent ) {
				$term = get_term( $term->parent, $taxonomy_name );
				if( ! $term ) break;
				$terms[] = $term->term_id;
			}
		}
		
		$permitted_users = array();
		
		foreach( $terms as $tid ) {
			if( array_key_exists( $tid, $extended_permissions[ $taxonomy_name ] ) ) {
				$permitted_users = array_merge(
					$permitted_users,
					$extended_permissions[ $taxonomy_name ][ $tid ]
				);
			}
		}
		
		return $permitted_users;
	}
	
	
	/**
	 *
	 */
	public function user_has_permission( $taxonomy_name, $term_id, $user_id )
	{
		$term_permissions = $this->get_permissions_for_taxonomy( $taxonomy_name, $term_id );
		return in_array( $user_id, $term_permissions );
	}
	
	
	/**
	 *
	 */
	public function save_permissions_for_taxonomy( $taxonomy_name, $term_id, $users )
	{
		$extended_permissions = $this->get_permissions();
		
		if( ! empty( $users ) )
		{
			if( ! array_key_exists( $taxonomy_name, $extended_permissions ) ) {
				$extended_permissions[ $taxonomy_name ] = array();
			}
			$extended_permissions[ $taxonomy_name ][ $term_id ] = $users;
		}
		else
		{
			if( array_key_exists( $taxonomy_name, $extended_permissions ) ) {
				unset( $extended_permissions[ $taxonomy_name ] );
			}
		}
		
		update_option( 'extended-permissions', $extended_permissions );
	}
	
	
	public function user_is_post_editor( $post_id, $user_id )
	{
		if( ! is_numeric( $post_id ) ) {
			return false;
		}
		
		$post_id = intval( $post_id );
		if( 0 === $post_id ) return false;
		
		$post = get_post( $post_id );
		if( ! $post ) return false;
		
		$taxonomies = get_object_taxonomies( $post->post_type );
		foreach( $taxonomies as $taxonomy_name )
		{
			$terms = wp_get_post_terms(
				$post_id,
				$taxonomy_name
			);
			
			foreach( $terms as $term ) {
				if( $this->user_has_permission( $taxonomy_name, $term->term_id, $user_id ) ) {
					return true;
				}
			}
		}
		
		return false;
	}
	
	
	public function user_is_editor( $user_id )
	{
		$extended_permissions = $this->get_permissions();
		
		foreach( $extended_permissions as $taxonomy_name => $term_permissions ) {
			foreach( $term_permissions as $term => $users ) {
				if( in_array( $user_id, $users ) ) return true;
			}
		}
		
		return false;
	}

} // class ExtendedPermissions_Model
endif; // if( !class_exists('ExtendedPermissions_Model') ):

