<?php
/**
 * Template Name: JSON DUMP: Initiatives + SDGs
 **/

header("Content-Type: application/json");

//FOR THIS MAP:
//NODES are all initiatives and SDGs (specific categories from wp_terms); SDGs are styled differently
//LINKS are initiatives to terms

//NODES
//GET ALL PUBLISHED INITIATIVES FROM DATABASE
$published_initiatives = $wpdb->get_results("SELECT * FROM `wp_posts` WHERE `post_status`='publish' AND `post_type`='initiative'", ARRAY_A);

//target json: { "name": "XYZ Initiative", "group": 1, "type": "initiative", "descr": "This is a longer description", "url": "http://datashift.zardtech.com/?p=123" }
$node_list = '{ "nodes": [';
$json_node_id = 0;
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
}

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
		$tdesrc = $sdg_term_id['description'];
		$term_id_lookup[$tid] = $json_node_id;
		//make term description array
		$term_descr_lookup[$tid] = $tdesrc;
		$json_node_id++;
	}
	//take off last comma and space
	$sdg_id_list = substr($sdg_id_list, 0, -2);
}

//Get the term names based on the ids
$sdg_term_names = $wpdb->get_results("SELECT * FROM `wp_terms` WHERE `term_id` IN ($sdg_id_list)",ARRAY_A);

//target json: { "name": "XYZ SDG", "group": 2, "type": "sdg", "descr": "This is a longer description", "url": "http://datashift.zardtech.com/sdg/$slug" }
if ( $sdg_term_names ) {
	foreach ( $sdg_term_names as $sdg_term_name ) {
		$node_list .= '{"name":"'.$sdg_term_name['name'].'"';
		$node_list .= ', "group": 2, "type": "sdg", "descr": ';
		//search $term_descr_lookup for $sdg_term_name['term_id'] ... if found, add descr here
		$term_id = $sdg_term_name['term_id'];
		$node_list .= '"'.$term_descr_lookup[$term_id].'", ';
		$sdgslug = $sdg_term_name['slug'];
		$sdg_url = site_url()."/sdg/".$sdgslug;
		$node_list .= '"url": "'.$sdg_url.'", "id":'.$term_id.', "label":"'.$sdg_term_name['name'].'" }, ';
	}
	//take off last comma and space
	$node_list = substr($node_list, 0, -2);
}
$node_list .= ']';
//echo $node_list;

//LINKS
//target json: {"source": 1,"target": 0,"value": 1}
$link_list = ', "links": [';
//Where post id of above group (initiatives only) = object_id in wp_term_relationships table AND term_taxonomy_id = sdg_term_id
//sql: SELECT * FROM `wp_term_relationships` WHERE `object_id` IN ( 22, 18, 19, 21, 23 ) AND 'term_taxonomy_id IN (3, 4, 5, 6, 7, 8, 9, 12, 13, 14, 15, 16, 17, 18, 19, 20, 22);
$sdg_term_links = $wpdb->get_results("SELECT * FROM `wp_term_relationships` WHERE `object_id` IN ( $initiative_ids ) AND `term_taxonomy_id` IN ( $sdg_id_list )",ARRAY_A);
//echo "SELECT * FROM `wp_term_relationships` WHERE `object_id` IN ( ".$initiative_ids." ) AND `term_taxonomy_id` IN ( ".$sdg_id_list." )");

if ( $sdg_term_links ) {
	foreach ( $sdg_term_links as $sdg_term_link ) {
		//lookup ids for initiatives (source)
		$json_initiative_id = $sdg_term_link['object_id'];
		$link_list .= '{"source": '.$initiative_id_lookup[$json_initiative_id].',';
		//lookup ids for sdgs (target)
		$json_sdg_id = $sdg_term_link['term_taxonomy_id'];		
		$link_list .= '"target": '.$term_id_lookup[$json_sdg_id].',"value": 1}, ';
	}
	//take off last comma and space
	$link_list = substr($link_list, 0, -2);
}
$link_list .= ']}';


//ECHO IT TO THE WORLD
$json = $node_list.$link_list;
echo $json;

	
?>