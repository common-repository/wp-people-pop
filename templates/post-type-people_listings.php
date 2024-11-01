<?php
/*

* WP People Pop List Builder *

In this template we allow the user to create lists based on people that have been added to the system.
Then we provide a short code to show these formatted lists on the client side of the web site

*/

//load our scripts and styles
wp_enqueue_script( 'listjs', plugin_dir_url( __FILE__ ) . '../js/listjs.js', array( 'jquery' ), '1.0', true );
wp_enqueue_script( 'jquery-ui-core' );
wp_enqueue_script( 'jquery-ui-draggable' );
wp_enqueue_script( 'jquery-ui-sortable' );
wp_enqueue_script( 'jquery-ui-slider' );
wp_enqueue_script( 'spectrum', plugin_dir_url( __FILE__ ) . '../js/bgrins-spectrum/spectrum.js', array( 'jquery' ), '1.0', true );
wp_enqueue_style( 'spectrum-css', plugin_dir_url( __FILE__ ) . '../js/bgrins-spectrum/spectrum.css' );
wp_enqueue_style( 'listcss-admin', plugin_dir_url( __FILE__ ) . '../css/listcss-admin.css' );
wp_enqueue_style( 'jqueryui-css', plugin_dir_url( __FILE__ ) . '../css/jquery-ui.theme.min.css' );

global $wpdb;
$pp_results = '';
$pp_verify  = '';

//take our user input, format and stash in database
if ( isset( $_POST['submitted'] ) ) {
	if ( $_POST['new_list'] == '' && $_POST['submitted'] == '1' ) {
		$pp_verify .= 'list name cannot be blank';
	}

	if ( $pp_verify == '' && $_POST['pp_del_id'] == '0' ) {

		$jsoninsert = array();
		if ( $_POST['wx'] != '' ) {
			$jsoninsert['wx']        = sanitize_text_field( intval( $_POST['wx'] ) );
			$jsoninsert['wx_length'] = sanitize_text_field( $_POST['wx_length'] );
		} else {
			$jsoninsert['wx']        = '300px';
			$jsoninsert['wx_length'] = 'px';
		}
		if ( $_POST['hx'] != '' ) {
			$jsoninsert['hx']        = sanitize_text_field( intval( $_POST['hx'] ) );
			$jsoninsert['hx_length'] = sanitize_text_field( $_POST['hx_length'] );
		} else {
			$jsoninsert['hx']        = 'auto';
			$jsoninsert['hx_length'] = 'px';
		}
		if ( $_POST['mrmt'] != '' ) {
			$jsoninsert['mrmt']        = sanitize_text_field( intval( $_POST['mrmt'] ) );
			$jsoninsert['mrmt_length'] = sanitize_text_field( $_POST['mrmt_length'] );
		} else {
			$jsoninsert['mrmt']        = '';
			$jsoninsert['mrmt_length'] = 'px';
		}

		$jsoninsert['css']    = sanitize_text_field( $_POST['pp_css'] );
		$jsoninsert['forcesq'] = sanitize_text_field( $_POST['pp_forcesq'] );
		$jsoninsert['bg']     = sanitize_text_field( $_POST['pp_bg_final'] );
		$jsoninsert['float']  = sanitize_text_field( $_POST['pp_float'] );
		$jsoninsert['people'] = explode( ";", sanitize_text_field( $_POST['pp_sort'] ) );
		$listname             = sanitize_text_field( str_replace( ' ', '-', strtolower( $_POST['new_list'] ) ) );

		//if submitted is = 1 then we are creating a new record in the database, otherwise we updated it
		if ( $_POST['submitted'] == '1' ) {
			$wpdb->insert(
				$wpdb->options,
				array(
					'option_name'  => '_people_pop_list_' . $listname,
					'option_value' => json_encode( $jsoninsert ),
					'autoload'     => 'yes'
				),
				array(
					'%s',
					'%s',
					'%s'
				)
			);
			$pp_results = "<p>Your new list's shortcode is: [peoplepop list='" . $wpdb->insert_id . "']<br /><br />Copy + Paste into any page or post</p>";
		} else {
			$wpdb->update(
				$wpdb->options,
				array(
					'option_value' => json_encode( $jsoninsert ),
					'autoload'     => 'yes'
				),
				array( 'option_id' => intval( sanitize_text_field( $_POST['submitted'] ) ) ),
				array(
					'%s',
					'%s',
					'%s'
				)
			);
		}

	} elseif ( $_POST['pp_del_id'] != '0' ) {
		$wpdb->delete(
			$wpdb->options,
			array( 'option_id' => intval( sanitize_text_field( $_POST['submitted'] ) ) )
		);
	}
}

?>
<h1>People Pop Listings</h1>
<div id='pp_results'><?php if ( $pp_verify != '' || $pp_results != '' ) { ?>
		<?php echo $pp_verify . $pp_results; //display success or errors ?>
	<?php } ?></div>

<p>If you've found this plugin helpful, please leave a review here: <br/>
	<a href="https://wordpress.org/support/view/plugin-reviews/wp-people-pop">https://wordpress.org/support/view/plugin-reviews/wp-people-pop</a>
</p>
<p>If you've had issues with the plugin, please leave a support request so we can give you a hand before you decide on
	the usefulness of it.
</p>
<p><a href='#new' id='people_tab_new'>Create New List</a> <a id='people_tab_edit' href='#edit'>Edit List/Get
		Shortcode</a></p>
<div id='pp_list_builder'>
	<form class='pp_form' id='pp_form' action='' method='POST'>
		<input type='hidden' name='submitted' id='submitted' value='1'/>
		<input type='hidden' name='pp_sort' id='pp_sort'/>
		<p id='pnew'><input type='text' name='new_list' id='new_list' placeholder="List Title"/></p>
		<p id='pedit'>
			<select name='listings' id='listings'>
				<option value='0'>Select a List</option>
				<?php
				foreach ( $wpdb->get_results( "SELECT option_id, option_name, option_value FROM " . $wpdb->options . " WHERE option_name LIKE '_people_pop_list_%';" ) as $key => $row ) {
				?>
				<option
					value='<?php echo esc_attr( $row->option_id . '|pp_split|' . str_replace( '\'', '&#39;', $row->option_value ) ) . '\'>' . esc_html( str_replace( '_people_pop_list_', '', $row->option_name ) ) . '</option>';
					}
					?>
				</select> <input type=' button
				' value='Delete List' id='pp_delete' />
				<input type='hidden' name='pp_del_id' id='pp_del_id' value='0'/>
		</p>
		<p><strong>Set Dimensions and Styles of Individual Listed People (div)</strong></p>
		<table border='0'>
			<tr>
				<td>
					Force Square Images:
				</td>
				<td>
					<select name="pp_forcesq" id="pp_forcesq" />
						<option value="yes">Yes</option>
						<option value="no">No</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Float:
				</td>
				<td>
					<select name='pp_float' id='pp_float'>
						<option value='left'>left</option>
						<option value='right'>right</option>
						<option value='none'>none</option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					Background Color:
				</td>
				<td>
					<input type='text' name='pp_bg' id='pp_bg' class='pp_bg'/>
					<input type='hidden' name='pp_bg_final' id='pp_bg_final'/>
					<em id='pp_bg-log'></em>
				</td>
			</tr>
			<tr>
				<td>
					Width x Height (px or %):
				</td>
				<td>
					<input type='text' name='wx' id='wx' size='4'/>
					<select name='wx_length' id='wx_length'>
						<option value='px'>px</option>
						<option value='pt'>pt</option>
						<option value='%'>%</option>
					</select> (default 300px) x <br/>
					<div id='wx_slide'></div>
					<input type='text' name='hx' id='hx' size='4'/>
					<select name='hx_length' id='hx_length'>
						<option value='px'>px</option>
						<option value='pt'>pt</option>
						<option value='%'>%</option>
					</select> (default auto)<br/>
					<div id='hx_slide'></div>
				</td>
			</tr>
			<tr>
				<td>
					Bottom &amp; Right Margin:
				</td>
				<td>
					<input size='4' type='text' name='mrmt' id='mrmt' value='10'/>
					<select name='mrmt_length' id='mrmt_length'>
						<option value='px'>px</option>
						<option value='pt'>pt</option>
						<option value='%'>%</option>
					</select><br/>
					<div id='mrmt_slide'></div>
				</td>
			</tr>
			<tr>
				<td>
					Custom CSS:
				</td>
				<td>
					<textarea type='text' name='pp_css' id='pp_css' cols='25' rows='4'></textarea>
					<br/>
					.pp_listing - div surrounding each person<br/>
					.pp_quote - quote div<br/>
					.pp_summary - summary/additional div<br/>
					.pp_title - title of person span
				</td>
			</tr>
			<tr>
				<td></td>
				<td>
					<p>
						<select name='people' id='people'>
							<?php
							$args = array(
								'post_type'      => 'post-type-people',
								'posts_per_page' => -1,
								'orderby'        => 'title',
								'order'          => 'ASC'
							);
							$loop = new WP_Query( $args );
							if ( $loop->have_posts() ) {
								while ( $loop->have_posts() ) : $loop->the_post();
									global $post;
									?>
									<option
									value='<?php echo esc_attr( $post->ID ); ?>'><?php esc_html( the_title() ); ?></option><?php
								endwhile;
							} else {
								echo __( '<option value=0>No People Found</option>' );
							}
							wp_reset_postdata();
							?>
						</select> <input type='button' id='add_people_btn' value='Add'/>
					</p>
					<ul id='pre_list'>
						<?php //load edit list ?>
					</ul>
					<p>
						<input type='submit' value='Save List!'/>
					</p>
				</td>
			</tr>
		</table>
	</form>
</div>
<div id='pp_sampler'>
	<div id='pps_css'></div>
	<div class='pps_listing'>
		<img src='<?php echo esc_attr( plugin_dir_url( __FILE__ ) ) . '../res/wp-people-pop.jpg'; ?>'/>
		<div class='pps_info'>
			<h3>Jane Doe</h3>
			<span class='pps_title'>Public Relations Specialist</span><br/>
			Communications Department<br/>
			jane@doe.com<br/>
			555-555-4444<br/>
			<div class="pps_quote">"Yea, hoopty do de do"</div>
			<div class="pps_summary">blah blah blah blah blah blah blah</div>
			<a href='#'>&raquo; Learn More</a>
		</div>
	</div>
	<div class='pps_listing'>
		<img src='<?php echo esc_attr( plugin_dir_url( __FILE__ ) ) . '../res/wp-people-pop.jpg'; ?>'/>
		<div class='pps_info'>
			<h3>John Doe</h3>
			<span class='pps_title'>Marketing Director</span><br/>
			Communications Department<br/>
			john@doe.com<br/>
			555-555-5555<br/>
			<div class="pps_quote">"Yea, hoopty do de do"</div>
			<div class="pps_summary">blah blah blah blah blah blah blah</div>
			<a href='#'>&raquo; Learn More</a>
		</div>
	</div>
</div>
