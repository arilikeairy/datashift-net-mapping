<?php
/**
  * Template Name: JSON DUMP: Focus areas only
 **/

header("Content-Type: application/json");

//FOR THIS MAP:
//NODES are all initiatives, SDGs, and focusareas (last 2 are categories from wp_terms)
//LINKS are initiatives to terms

//DO THIS ONE DIFFERENTLY -- ONLY GRAB INITIATIVES THAT ARE TAGGED WITH A FOCUS AREA
	//search wp_term_taxonomy for focus areas (get 29-32)
	//search wp_term_relationsips for obj ids that have those ids
	//only grab initiatives in that list

//NODES
$node_list = '{ "nodes": [';
$json_node_id = 0;

//GET ALL SDG TERMS FROM DATABASE
//Get all terms from wp_terms that match up with ids of 'sdg' terms from wp_term_taxonomy
//Where ids of 'sdg' terms from wp_term_taxonomy match those in wp_terms, add their names to an array
$sdg_term_ids = $wpdb->get_results("SELECT * FROM `wp_term_taxonomy` WHERE `taxonomy`='sdg'", ARRAY_A);

// make it into a pretty string
if ( $sdg_term_ids ) {
	foreach ( $sdg_term_ids as $sdg_term_id ) {
		$sdg_id_list .= $sdg_term_id['term_id'].", ";
		//make a terms lookup array for the links query: wordpress id => json id (starting at zero)
		$tid = $sdg_term_id['term_id'];
		$tdescr = $sdg_term_id['description'];
		$term_id_lookup[$json_node_id] = $tid;
		//get rid of fancy quotes
		$tdescr = str_replace('“','"',$sdg_term_id['description']);
		$tdescr = str_replace('”','"',$tdescr);
		//make term description array (wordpress id => descr)
		$term_descr_lookup[$tid] = $tdescr;
		$json_node_id++;
	}
	//take off last comma and space
	$sdg_id_list = substr($sdg_id_list, 0, -2);
}

//Get the term names based on the ids
$sdg_term_names = $wpdb->get_results("SELECT * FROM `wp_terms` WHERE `term_id` IN ($sdg_id_list)",ARRAY_A);

//target json: { "name": "XYZ SDG", "group": 2, "type": "theme", "descr": "This is a longer description", "url": "http://datashift.zardtech.com/sdg/$slug" }
if ( $sdg_term_names ) {
	foreach ( $sdg_term_names as $sdg_term_name ) {
		$node_list .= '{"name":"'.$sdg_term_name['name'].'"';
		$node_list .= ', "group": 2, "type": "sdg", "descr": ';
		//search $term_descr_lookup for $sdg_term_name['term_id'] ... if found, add descr here
		$sdgid = $sdg_term_name['term_id'];
		$term_id = array_search($sdgid, $term_id_lookup);
		$node_list .= '"'.$term_descr_lookup[$sdgid].'", ';
		$sdgslug = $sdg_term_name['slug'];
		$sdg_url = site_url()."/sdg/".$sdgslug;
		$node_list .= '"url": "'.$sdg_url.'", "id":'.$term_id.', "label":"'.$sdg_term_name['name'].'" }, ';
	}
}

//GET ALL focusarea TERMS FROM DATABASE
	//get the obj ids that are assocated with that wp term id --> if those are published initiatives, grab them
	//Get all terms from wp_terms that match up with ids of 'focusarea' terms from wp_term_taxonomy
$focusarea_term_ids = $wpdb->get_results("SELECT * FROM `wp_term_taxonomy` WHERE `taxonomy`='focusarea'", ARRAY_A);

// make it into a pretty string
if ( $focusarea_term_ids ) {
	foreach ( $focusarea_term_ids as $focusarea_term_id ) {
		$focusarea_id_list .= $focusarea_term_id['term_id'].", ";
		//make a terms lookup array for the links query: wordpress id => json id (starting at zero)
		$tid = $focusarea_term_id['term_id'];
		$term_id_lookup[$json_node_id] = $tid;
		//get rid of fancy quotes
		$tdescr = str_replace('“','"',$focusarea_term_id['description']);
		$tdescr = str_replace('”','"',$tdescr);
		//make term description array
		$term_descr_lookup[$tid] = $tdescr;
		$json_node_id++;
	}
	//take off last comma and space
	$focusarea_id_list = substr($focusarea_id_list, 0, -2);
}


//Get the term names based on the ids
$focusarea_term_names = $wpdb->get_results("SELECT * FROM `wp_terms` WHERE `term_id` IN ($focusarea_id_list)",ARRAY_A);
//target json: { "name": "XYZ focusarea", "group": 2, "type": "focusarea", "descr": "This is a longer description", "url": "http://datashift.zardtech.com/focusarea/$slug"}
if ( $focusarea_term_names ) {
	foreach ( $focusarea_term_names as $focusarea_term_name ) {
		$node_list .= '{"name":"'.$focusarea_term_name['name'].'"';
		$node_list .= ', "group": 3, "type": "focusarea",';
		//search $term_descr_lookup for $focusarea_term_name['term_id'] ... if found, add descr here
		$cid = $focusarea_term_name['term_id'];
		//get json id
		$term_id = array_search($cid, $term_id_lookup);
		$node_list .= '"id":'.$term_id.', '.$term_descr_lookup[$cid].', ';
		$focus_slug = $focusarea_term_name['slug'];
		$focus_url = site_url()."/focusarea/".$focus_slug;
		$node_list .= '"url": "'.$focus_url.'", "label":"'.$focusarea_term_name['name'].'" }, ';
	}
	//take off last comma and space
}

//GET *SOME* PUBLISHED INITIATIVES FROM DATABASE

$limited_list = $wpdb->get_results("SELECT `object_id` FROM `wp_term_relationships` WHERE `term_taxonomy_id` IN ($focusarea_id_list)",ARRAY_A);
if ( $limited_list ) {
	foreach ( $limited_list as $limited_list_id ) {
		$focusarea_obj_id_list .= $limited_list_id['object_id'].", ";
	}
	$focusarea_obj_id_list = substr($focusarea_obj_id_list, 0, -2);
}

$published_initiatives = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE	`post_status`='publish' AND `post_type`='initiative' AND `ID` IN ($focusarea_obj_id_list)", ARRAY_A);

//target json: { "name": "XYZ Initiative", "group": 1, "type": "initiative", "descr": "This is a longer description", "url": "http://datashift.zardtech.com/?p=123" }

if ( $published_initiatives ) {
	foreach ( $published_initiatives as $published_initiative ) {
		$node_list .= '{"name":"'.$published_initiative['post_title'].'"';
		$prettydescr = stripslashes($published_initiative['post_content']);
		$prettydescr = str_replace ( '"' , '\"' , $prettydescr );
		$prettydescr = strip_tags($prettydescr);
		$prettydescr = trim($prettydescr);
		//make a lookup string for the links query
		$initiative_ids .= $published_initiative['ID'].', ';
		//make a lookup array for the links json: wordpress id => json id (starting at zero)
		$id = $published_initiative['ID'];
		$initiative_id_lookup[$id] = $json_node_id;
		$node_list .= ', "group": 1, "type": "initiative", "descr": "'.$prettydescr.'", ';
		$node_url = site_url()."/?p=".$id;
		$node_list .= '"url": "'.$node_url.'", "id": '.$json_node_id.' }, ';
		$json_node_id++;
	}
	$initiative_ids = substr($initiative_ids, 0, -2);
	$node_list = substr($node_list, 0, -2);
}


//LINKS

$node_list .= ']';

//wordpress ids for terms
$wp_term_ids = implode(",", array_values($term_id_lookup));

//LINKS
//target json: {"source": 1,"target": 0,"value": 1}
$link_list = ', "links": [';
//WHERE POST ID OF ABOVE GROUP = OBJECT ID IN WP_TERM_RELATIONSHIPS TABLE, GET THE ASSOCIATED TERM_TAXONOMY_ID IF IT IS ONE OF THOSE ABOVE
//sql: SELECT * FROM `wp_term_relationships` WHERE `object_id` IN ( 22, 18, 19, 21, 23, 57, 129, 142 );
//$sdg_term_links = $wpdb->get_results("SELECT * FROM `wp_term_relationships` WHERE `object_id` IN ( $initiative_ids )",ARRAY_A);
//ALTERNATIVE: get * from wp_term_relationships where term_taxonomy_id is in the list of sdgs + focusareas + focus areas
$links = $wpdb->get_results("SELECT * FROM `wp_term_relationships` WHERE `term_taxonomy_id` IN ( $wp_term_ids ) AND `object_id` IN ( $initiative_ids )",ARRAY_A);

if ( $links ) {
	foreach ( $links as $link ) {
		//for json "source," lookup json ids for initiatives
		$wpid = $link['object_id'];
		$link_list .= '{"source": '.$initiative_id_lookup[$wpid].',';
		//for json "target," lookup ids for sdgs + focusareas + focus areas
		$thisid = array_search($link['term_taxonomy_id'],$term_id_lookup);
		$link_list .= '"target": '.$thisid.',"value": 1}, ';
	}
	//take off last comma and space
	$link_list = substr($link_list, 0, -2);
}
$link_list .= ']}';

//ECHO IT TO THE WORLD
$json = $node_list.$link_list;
echo $json;

?>
