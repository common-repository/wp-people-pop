<?php
$array_of_fields = array(
	'email' => array( 'type' => 'text', 'label' => 'Email' ),
	'phone' => array( 'type' => 'text', 'label' => 'Phone' ),
	'fax' => array( 'type' => 'text', 'label' => 'Fax' ),
	'department' => array( 'type' => 'text', 'label' => 'Department' ),
	'position' => array( 'type' => 'text', 'label' => 'Position' ),
	'title' => array( 'type' => 'text', 'label' => 'Title' ),
	'quote' => array( 'type' => 'textarea', 'label' => 'Quotes' ),
	'additional-summary' => array( 'type' => 'textarea', 'label' => 'Additional/Summary' )
	 );
	?>
<style type='text/css'>
table.people-metabox {width:100%; border-collapsed:collapsed}
.people-metabox th {font-weight:bold; text-align:left; max-width:25%; v-align:top}
.people-metabox td {padding: .2em .2em .2em .4eml;v-align:top}
.people-metabox textarea {width:98%}
</style>
<table class='people-metabox'>
<?php
foreach( $array_of_fields as $name => $options ) {

	switch( $options['type'] ){
		case 'text':
			make_textfield( $name, $options );
		break;
		case 'textarea':
			make_textareafield( $name, $options );
		break;
		case 'select':
			make_selectbox( $name, $options );
	}
} ?>
</table>
<?php

function make_textfield( $name, $options ){
	global $post;
	?>
	<tr id='did-<?php echo $post->ID; ?>'>
        <th>
            <label for='<?php echo $name; ?>'><?php echo $options['label'] ?></label>
        </th>
        <td>
            <input type='text' id='<?php echo $name; ?>' name='<?php echo $name; ?>' value='<?php echo @get_post_meta( $post->ID, $name, true ); ?>' />
        </td>
    </tr>
	<?php
}


function make_textareafield( $name, $options ){
	global $post;
	?>
	<tr>
        <th>
            <label for='<?php echo $name ?>'><?php echo $options['label']; ?></label>
        </th>
        <td>
            <textarea rows='5' cols='60' id='<?php echo $name; ?>' name='<?php echo $name; ?>'><?php echo @get_post_meta($post->ID, $name, true); ?></textarea>
        </td>
    </tr>   	
	<?php
}

function make_selectbox($name, $options){
	global $post;
	// used on listings post type
	$value = @get_post_meta( $post->ID, $name, true );
	?>
	<tr>
        <th >
            <label for='<?php echo $name; ?>'><?php echo $options['label']; ?></label>
        </th>
        <td>
         	<select name='<?php echo $name; ?>' id='<?php echo $name; ?>'>
         		<?php foreach( $options['options'] as $option => $text ) { ?>
         			<option value='<?php echo $option; ?>' <?php echo ( $option == $value ) ? 'checked' : ''; ?> ><?php echo $text; ?></option>
         		<?php } ?>
         	</select>
        </td>
    </tr>   	
	<?php	
}