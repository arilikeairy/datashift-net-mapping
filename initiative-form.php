<?php
/**
* Template Name: New Initiative Form
* Adds a new custom content type: initiative and any additional meta-data
* H/T: http://code.tutsplus.com/tutorials/posting-via-the-front-end-inserting--wp-27034
**/


 //validate the form

if ( isset( $_POST['submitted'] ) ) {

	// if the required fields aren't filled
	if ( isset ($_POST['postTitle'])  && $_POST['postTitle'] == '' ) {
		$postTitleError = "Please fill out the name of the initative.";
	} else {
		
		//https://refactored.co/blog/how-to-use-wp_insert_post-tax_input
		//$custom_tax = array ('sdg' => $sdgs_submitted,'continent' => $continent_submitted);
		//die(print_r($custom_tax));
		
		$post_information = array(
			'post_title' => wp_strip_all_tags( $_POST['postTitle'] ),
			'post_content' => $_POST['postContent'],
			'post_type' => 'initiative',
			'post_status' => 'pending'
		);

		// if the antispam field is empty 
		if(isset($_POST['url']) && $_POST['url'] == ''){
			$post_id = wp_insert_post( $post_information );
			//http://wordpress.stackexchange.com/questions/18236/attaching-taxonomy-data-to-post-with-wp-insert-post
		}
		
		if ( $post_id ) {
			
			if ( isset ($_POST['sdg']) ) {
				$sdgs_submitted = $_POST['sdg'];
				wp_set_object_terms( $post_id, $sdgs_submitted, 'sdg' );
			}
			
			if ( isset ($_POST['continent']) ) {
				$continent_submitted = $_POST['continent'];
				wp_set_object_terms( $post_id, $continent_submitted, 'continent' );
			}	
			
			//Add email address as post meta data (custom field)
			if ( isset ($_POST['submitterEmail']) ) {
				$submitteremail = $_POST['submitterEmail'];
				add_post_meta($post_id, 'submitterEmail', $submitteremail);
			}
			if ( isset ($_POST['initiativeURL']) ) {
				$initiativeURL = $_POST['initiativeURL'];
				add_post_meta($post_id, 'initiativeURL', $initiativeURL);
			}
			if ( isset ($_POST['initiativeStartDate']) ) {
				$initiativeStartDate = $_POST['initiativeStartDate'];
				add_post_meta($post_id, 'initiativeStartDate', $initiativeStartDate);
			}
			if ( isset ($_POST['initiativeDataCollection']) ) {
				$initiativeDataCollection = $_POST['initiativeDataCollection'];
				$initiativeDataCollection = implode(", ",$initiativeDataCollection);
				add_post_meta($post_id, 'initiativeDataCollection', $initiativeDataCollection);
			}
			$postsuccess = "Thank you!  <br><br>You'll be automatically redirected shortly.";
		}
	}
} 
get_header(); ?>
<style type="text/css">
.antispam { display: none; }
</style>

<div class="container"><div class="template-page"><div class="post-entry"><div class="entry-content-wrapper clearfix"><div class="tabcomtainer">

<?php if ( $postTitleError != '' ) { ?>
    <span class="error"><?php echo $postTitleError; ?></span>
    <div class="clearfix"></div>
<?php } ?>
<?php if ( $postsuccess != '' ) { ?>
    <span class="success"><br><br><br><br><?php echo $postsuccess; ?><br><br><br><br></span>
	<meta http-equiv="refresh" content="5; url=http://datashift.zardtech.com/learning-zone/visualizations/" />
    <div class="clearfix"></div>
<?php } else { ?>
<div id="intro">
<!-- get page content -->
<?php the_content(); ?>
</div>
<form action="" id="primaryPostForm" class="submit-your-initiative main_color" method="POST">
 <h2>Submit a new initiative</h2>
    <fieldset>
        <label for="postTitle">Initiative Title (Required):</label> 
        <input type="text" name="postTitle" id="postTitle" class="required" value="<?php if ( isset( $_POST['postTitle'] ) ) echo $_POST['postTitle']; ?>" />
    </fieldset>
 
    <fieldset>
        <label for="postContent">Description (Required):</label>
        <textarea name="postContent" id="postContent" rows="8" cols="30" class="required" <?php if ( isset( $_POST['postContent'] ) ) { if ( function_exists( 'stripslashes' ) ) { echo stripslashes( $_POST['postContent'] ); } else { echo $_POST['postContent']; } } ?>></textarea>
    </fieldset>
	
    <fieldset>
        <label for="postSDG">Related UN Sustainable Development Goals:</label>
		<ul style="list-style-type:none;">
		<?php
		//GET SDG TERMS FROM DATABASE
//Get all terms from wp_terms that match up with ids of 'sdg' terms from wp_term_taxonomy
$sdg_term_ids = $wpdb->get_results("SELECT `term_id` FROM `wp_term_taxonomy` WHERE `taxonomy`='sdg'", ARRAY_A);
if ( $sdg_term_ids ) {
	foreach ( $sdg_term_ids as $sdg_term_id ) {
		$sdg_id_list .= $sdg_term_id['term_id'].", ";
	}
	$sdg_id_list = substr($sdg_id_list, 0, -2);
}
//Get the term names based on the ids
$sdg_term_names = $wpdb->get_results("SELECT * FROM `wp_terms` WHERE `term_id` IN ($sdg_id_list) ORDER BY `name` ASC ",ARRAY_A);
if ( $sdg_term_names ) {
	foreach ( $sdg_term_names as $sdg_term_name ) {
		echo '<li><input type="checkbox" name="sdg[]" value="'.$sdg_term_name['name'].'"> '.$sdg_term_name['name'].'</input></li>';
	}
}
?>
    </ul>
	</fieldset>
	
    <p class="antispam">Leave this empty: <input type="text" name="url" /></p>
	
	 <fieldset>
        <label for="postContinent">Continent:</label>
		<ul style="list-style-type:none;">
		<?php
		//GET CONTINENT TERMS FROM DATABASE
//Get all terms from wp_terms that match up with ids of 'continent' terms from wp_term_taxonomy
$continent_term_ids = $wpdb->get_results("SELECT `term_id` FROM `wp_term_taxonomy` WHERE `taxonomy`='continent'", ARRAY_A);
if ( $continent_term_ids ) {
	foreach ( $continent_term_ids as $continent_term_id ) {
		$continent_id_list .= $continent_term_id['term_id'].", ";
	}
	$continent_id_list = substr($continent_id_list, 0, -2);
}
//Get the term names based on the ids
$continent_term_names = $wpdb->get_results("SELECT * FROM `wp_terms` WHERE `term_id` IN ($continent_id_list) ORDER BY `name` ASC ",ARRAY_A);
if ( $continent_term_names ) {
	foreach ( $continent_term_names as $continent_term_name ) {
		echo '<li><input type="checkbox" name="continent[]" value="'.$continent_term_name['name'].'"> '.$continent_term_name['name'].'</input></li>';
	}
}
?>
    </ul>
	</fieldset>
	
	<fieldset>
        <label for="initiativeURL">Website where you can find the initiative:</label> 
        <input type="text" name="initiativeURL" id="initiativeURL"/>
    </fieldset>
	
	<fieldset>
        <label for="initiativeStartDate">Year initiative started:</label> 
        <input type="text" name="initiativeStartDate" id="initiativeStartDate"/>
    </fieldset>
	
		<fieldset>
        <label for="initiativeDataCollection">How data are collected:</label> 
        <ul style="list-style-type:none;">
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="SMS">SMS</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Voice/Phone">Voice / Phone</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Twitter">Twitter</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Other Social Media">Other Social Media</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Sensors/Hardware">Sensors / Hardware</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="GPS">GPS</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Reports">Reports</input></li>
			<li><input type="checkbox" name="initiativeDataCollection[]" id="initiativeDataCollection" value="Website">Text input via a website</input></li>
		</ul>
    </fieldset>
	
	<fieldset>
        <label for="submitterEmail">If you want to receive the DataShift newsletter, submit your email address:</label> 
        <input type="text" name="submitterEmail" id="submitterEmail"/>
    </fieldset>
	
    <fieldset>
        <input type="hidden" name="submitted" id="submitted" value="true" />
        <input type="submit" value="Submit your Initiative"></input>
    </fieldset>
 
</form>
<?php wp_nonce_field( 'post_nonce', 'post_nonce_field' ); 
} // end else ?>
</div></div></div></div></div>

<?php get_footer(); ?>