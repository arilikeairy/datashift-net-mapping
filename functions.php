<?php
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}

//make custom content type
class ds_initiative {

	function ds_initiative() {
		add_action('init',array($this,'create_post_type'));
		add_action('init',array($this,'create_sdg_category'));
		add_action('init',array($this,'create_continent_category'));
		add_action('init',array($this,'create_focusarea_category'));
		add_action('manage_ds_initiative_posts_columns',array($this,'columns'),10,2);
		add_action('manage_ds_initiative_posts_custom_column',array($this,'column_data'),11,2);
	}

	function create_post_type() {
		$labels = array(
			'name'               => 'Initiatives',
			'singular_name'      => 'Initiative',
			'menu_name'          => 'Initiatives',
			'name_admin_bar'     => 'Initiative',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Initiative',
			'new_item'           => 'New Initiative',
			'edit_item'          => 'Edit Initiative',
			'view_item'          => 'View Initiative',
			'all_items'          => 'All Initiatives',
			'search_items'       => 'Search Initiatives',
			'parent_item_colon'  => 'Parent Initiative',
			'not_found'          => 'No Initiatives Found',
			'not_found_in_trash' => 'No Initiatives Found in Trash'
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_nav_menus'   => true,
			'show_in_menu'        => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'menu_icon'           => 'dashicons-admin-appearance',
			'capability_type'     => 'post',
			'hierarchical'        => false,
			'supports'            => array( 'title', 'editor', 'author', 'custom-fields' ),
            'taxonomies' 		  => array( 'sdg' ),
			'has_archive'         => true,
			'rewrite'             => array( 'slug' => 'initiatives' ),
			'query_var'           => true
		);

		register_post_type( 'initiative', $args );
	}

	function create_sdg_category() {

		$labels = array(
			'name'                       => 'SDGs',
			'singular_name'              => 'SDG',
			'search_items'               => 'SDGs',
			'popular_items'              => 'Popular SDGs',
			'all_items'                  => 'All SDGs',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit SDG',
			'update_item'                => 'Update SDG',
			'add_new_item'               => 'Add New SDG',
			'new_item_name'              => 'New SDG Name',
			'separate_items_with_commas' => 'Separate SDGs with commas',
			'add_or_remove_items'        => 'Add or remove SDGs',
			'choose_from_most_used'      => 'Choose from most used SDGs',
			'not_found'                  => 'No SDGs found',
			'menu_name'                  => 'SDGs',
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'sdg' ),
		);

		register_taxonomy('sdg','initiative',$args);
	}

	function create_continent_category() {

		$labels = array(
			'name'                       => 'Continents',
			'singular_name'              => 'Continent',
			'search_items'               => 'Continents',
			'popular_items'              => 'Popular Continents',
			'all_items'                  => 'All Continents',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Continent',
			'update_item'                => 'Update Continent',
			'add_new_item'               => 'Add New Continents',
			'new_item_name'              => 'New Continent Name',
			'separate_items_with_commas' => 'Separate Continents with commas',
			'add_or_remove_items'        => 'Add or remove Continents',
			'choose_from_most_used'      => 'Choose from most used Continents',
			'not_found'                  => 'No Continents found',
			'menu_name'                  => 'Continents',
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'continent' ),
		);

		register_taxonomy('continent','initiative',$args);
	}
	
		function create_focusarea_category() {

		$labels = array(
			'name'                       => 'Focus Areas',
			'singular_name'              => 'Focus Area',
			'search_items'               => 'Focus Areas',
			'popular_items'              => 'Popular Focus Areas',
			'all_items'                  => 'All Focus Areas',
			'parent_item'                => null,
			'parent_item_colon'          => null,
			'edit_item'                  => 'Edit Focus Area',
			'update_item'                => 'Update Focus Area',
			'add_new_item'               => 'Add New Focus Areas',
			'new_item_name'              => 'New Focus Area Name',
			'separate_items_with_commas' => 'Separate Focus Areas with commas',
			'add_or_remove_items'        => 'Add or remove Focus Areas',
			'choose_from_most_used'      => 'Choose from most used Focus Areas',
			'not_found'                  => 'No Focus Areas found',
			'menu_name'                  => 'Focus Areas',
		);

		$args = array(
			'hierarchical'          => true,
			'labels'                => $labels,
			'show_ui'               => true,
			'show_admin_column'     => true,
			'update_count_callback' => '_update_post_term_count',
			'query_var'             => true,
			'rewrite'               => array( 'slug' => 'focusarea' ),
		);

		register_taxonomy('focusarea','initiative',$args);
	}
}

new ds_initiative();


?>