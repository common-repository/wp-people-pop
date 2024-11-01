<?php
if( !class_exists( 'ShowList' ) )
{
    /**
     * A ShowList class that provides 3 additional meta fields
     */
    class ShowList
    {
		/**
		 * The Constructor
		 */
		public function __construct()
		{
		} // END public function __construct()
		
		/**
		 * hook into WP's init action hook
		 */
		//[peoplepop] shortcode function 
		public function peoplepop_sc_func( $atts ) {
			wp_enqueue_style( 'listcss', plugin_dir_url( __FILE__ ) . '../css/listcss.css' );
			
			$a = shortcode_atts( array(
				'list' => '0',
			), $atts );
			
			global $wpdb;
			$listoptions = $wpdb->get_row( "SELECT option_value FROM $wpdb->options WHERE option_id = '" . $a['list'] . "'", ARRAY_A );
			$people = json_decode( $listoptions['option_value'], true );
			
			//load people posts from database
			$args = array(
						'post_type' => 'post-type-people',
						'posts_per_page' => -1,
						'post__in' => array_filter( $people['people'] )
					);
			$loop = new WP_Query( $args );
			$found = array();
			if ( $loop->have_posts() ) {
				while ( $loop->have_posts() ) : $loop->the_post();
					global $post;
					$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
					if( $large_image_url[0] == '' ) {
						$large_image_url[0] = esc_attr( plugin_dir_url( __FILE__ ) ) . '../res/blankprofile.jpg';
					}
					//default to yes and check if exists since new feature in update
					$imgh = 'auto';
					if( array_key_exists('forcesq', $people ) && $people['forcesq'] == 'yes' ) {
						$imgh = $people['wx'] . $people['wx_length'];
						$found[ $post->ID ] = '<div class="pp_listing" style="width: ' . $people['wx'] . $people['wx_length'] . ';height: '
						                      . $people['hx'] . $people['hx_length'] . ';margin-right:' . $people['mrmt'] . $people['mrmt_length'] . ';margin-bottom:'
						                      . $people['mrmt'] . $people['mrmt_length'] . ';background-color:' . $people['bg'] . ';float:' . $people['float']
						                      . ';"><div class="forced-img" style="background-color:#ccc;background-image:url(' . $large_image_url[0]	. ');height:' . $imgh
						                      . ';background-size:cover;">&nbsp;</div><div class="pp_info"><h3>' . get_the_title() . '</h3>';
					} else {
						$found[ $post->ID ] = '<div class="pp_listing" style="width: ' . $people['wx'] . $people['wx_length'] . ';height: '
						                      . $people['hx'] . $people['hx_length'] . ';margin-right:' . $people['mrmt'] . $people['mrmt_length'] . ';margin-bottom:'
						                      . $people['mrmt'] . $people['mrmt_length'] . ';background-color:' . $people['bg'] . ';float:' . $people['float']
						                      . ';"><img src="' . $large_image_url[0]	. '" style="width:100%;height:auto;" /><div class="pp_info"><h3>' . get_the_title() . '</h3>';
					}
					
					//get meta data
					$people_meta = get_post_meta( $post->ID );
					if( get_post_meta($post->ID, 'title', true) != '' ) {
						$found[ $post->ID ] .= '<span class="pp_title">' . $people_meta['title'][0] . '</span><br />';
					}
					if( get_post_meta($post->ID, 'position', true) != '' ) {
						$found[ $post->ID ] .= $people_meta['position'][0] . '<br />';
					}
					if( get_post_meta($post->ID, 'department', true) != '' ) {
						$found[ $post->ID ] .= $people_meta['department'][0] . '<br />';
					}
					if( get_post_meta($post->ID, 'email', true) != '' ) {
						$found[ $post->ID ] .= $people_meta['email'][0] . '<br />';
					}
					if( get_post_meta($post->ID, 'phone', true) != '' ) {
						$found[ $post->ID ] .= $people_meta['phone'][0] . '<br />';
					}
					if( get_post_meta($post->ID, 'fax', true) != '' ) {
						$found[ $post->ID ] .= $people_meta['fax'][0] . '<br />';
					}
					if( get_post_meta($post->ID, 'quote', true) != '' ) {
						$found[ $post->ID ] .= '<div class="pp_quote">"' . $people_meta['quote'][0] . '"</div>';
					}
					if( get_post_meta($post->ID, 'additional-summary', true) != '' ) {
						$found[ $post->ID ] .= '<div class="pp_summary">' . $people_meta['additional-summary'][0] . '</div>';
					}
					$found[ $post->ID ] .= '<a href="' . esc_url( get_permalink() ) . '">&raquo; Learn More</a>';
						
					$found[ $post->ID ] .= '</div></div>';
				endwhile;
				$found[ $post->ID ] .= '<style type="text/css">' . $people['css'] . '</style>';
			} else {
				echo __( 'No People Found' );
			}
			wp_reset_postdata();
			
			$list = '';
			foreach( array_filter( $people['people'] ) as $person ) {
				$list .= $found[ $person ];
			}
			
			return $list;
		} // END public function init()
		
		/**
		 * hook into WP's admin_init action hook
		 */
		public function admin_init()
		{           			
		} // END public function admin_init()
    } // END class ShowList
} // END if(!class_exists('ShowList'))