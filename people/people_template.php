<?php
if( !class_exists( 'PostTypePeople' ) )
{
    /**
     * A PostTypePeople class that provides 3 additional meta fields
     */
    class PostTypePeople
    {
        const POST_TYPE = 'post-type-people';
        private $_meta  = array(
            'email',
            'phone',
			'fax',
            'department',
            'position',
            'title',
            'quote',
            'additional-summary',
        );
		
		/**
		 * The Constructor
		 */
		public function __construct()
		{
			// register actions
			add_action( 'init', array( &$this, 'init' ) );
			add_action( 'admin_init', array( &$this, 'admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'create_list_builder' ) );
		} // END public function __construct()
		
		/**
		 * hook into WP's init action hook
		 */
		public function init()
		{
			$this->create_post_type();
			add_action( 'save_post', array( &$this, 'save_post' ) );
		} // END public function init()

		/**
		 * Create the post type
		 */
		public function create_post_type()
		{
			register_post_type( self::POST_TYPE,
				array(
					'labels' => array(
						'name' => 'People',
						'singular_name' => 'People',
						'add_new_item' => 'Add New Person',
						'edit_item' => 'Edit Person',
						'new_item' => 'New Person',
						'view_item' => 'View person',
						'search_items' => 'Search Person'
					),
					'public' => true,
					'has_archive' => true,
					'heirarchical' => true,
					'description' => __( 'This is a people to be populated in a list.' ),
					'supports' => array(
						'title',
						'editor', 
						'thumbnail',
						'page-attributes',
					),
				)
			);
		}
		
		public function create_list_builder() {
			add_submenu_page('edit.php?post_type=' . self::POST_TYPE, 'Build Listings', 'Build Listings', 'manage_options', 'admin.php?page=' . self::POST_TYPE . '-listings', array( $this, 'listings_display'));
		}
		
		public function listings_display() {
			include(sprintf("%s/../templates/%s_listings.php", dirname(__FILE__), self::POST_TYPE));  
		}

		/**
		 * Save the metaboxes for this custom post type
		 */
		public function save_post( $post_id )
		{
			// verify if this is an auto save routine. 
			// If it is our form has not been submitted, so we dont want to do anything
			if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			{
				return;
			}

			if( isset( $_POST['post_type'] ) && $_POST[ 'post_type' ] == self::POST_TYPE && current_user_can( 'edit_post', $post_id ) )
			{
				foreach( $this->_meta as $field_name )
				{
					// Update the post's meta field
					update_post_meta( $post_id, $field_name, $_POST[ $field_name ] );
				}
			}
			else
			{
				return;
			} // if($_POST['post_type'] == self::POST_TYPE && current_user_can('edit_post', $post_id))
		} // END public function save_post($post_id)
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{           
			// Add metaboxes
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
			
		} // END public function admin_init()
				
		/**
		 * hook into WP's add_meta_boxes action hook
		 */
		public function add_meta_boxes()
		{
			// Add this metabox to every selected post
			add_meta_box( 
				sprintf( 'wp_people_pop_%s_section', self::POST_TYPE ),
				"This Person's Information (all optional)",
				array( &$this, 'add_inner_meta_boxes' ),
				self::POST_TYPE
			);                  
		} // END public function add_meta_boxes()
		
		/**
		 * called off of the add meta box
		 */     
		public function add_inner_meta_boxes($post)
		{       
			// Render the job order metabox
			include( sprintf( "%s/../templates/%s_metabox.php", dirname(__FILE__), self::POST_TYPE ) );         
		} // END public function add_inner_meta_boxes($post)
		
    } // END class PostTypePeople
} // END if(!class_exists('PostTypePeople'))