<?php
/*
Plugin Name: Related Links Customized By Page
Description: Maintains link category names that match your page names, and lets you put custom links on each page. To have a link show up on a given page, put <code>&lt;?php srd_make_custom_link(); ?&gt;</code> where you want the link list to appear. Then assign each link the category name that matches the page(s) you want it to appear on.  
Author: Steven Ray
Author URI: http://stevenray.name
Version: 1.1
*/

/*
This program is free software; you can redistribute it and/or modify 
it under the terms of the GNU General Public License as published by 
the Free Software Foundation; version 2 of the License.

This program is distributed in the hope that it will be useful, 
but WITHOUT ANY WARRANTY; without even the implied warranty of 
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
GNU General Public License for more details. 

*/

//GENERATE LINK CATEGORIES FROM EXISTING PAGES ON ACTIVATION
register_activation_hook(WP_PLUGIN_DIR . '/related-links-customized-by-page/page_link_categories.php', 'srd_create_link_categories' );

	function srd_create_link_categories() {
		global $wpdb; // allows database calls to work
		
		//Get array of page names
		$pagenames= get_pages();
		$taxonomy="link_category";
		//Loop through array, turning each page name into a link category name
		foreach ( $pagenames as $pagg ) {
		$post_id = $pagg->ID;
		//Get page name
		$querystr1 = "SELECT post_title FROM wp_posts WHERE ID = '$post_id' ORDER BY post_date_gmt desc LIMIT 1";
		$srd_pagename = $wpdb->get_var($querystr1); //get single variable

		// Check if page name exists as a category name
		$querystr = "SELECT term_id FROM wp_terms JOIN wp_term_taxonomy USING (term_id) WHERE wp_terms.name = '$srd_pagename' AND wp_term_taxonomy.taxonomy = '$taxonomy'";
		$tag_ID = $wpdb->get_var($querystr); //get single variable
			
		//If it exists, run an update	
		if ($tag_ID) {
			$postslug= wp_unique_post_slug($srd_pagename); //creates properly formatted slug version of name
			$srd_update = wp_update_term($tag_ID, $taxonomy, array('name' => $srd_pagename, 'slug' => $postslug)); //updates name and slug
		}
		//if it doesn't exist, insert it
		else {
			$srd_insert = wp_insert_term($srd_pagename, $taxonomy); //Built-in WP function to insert new term
		}
		}
	}


//USE PAGE NAMES AS LINK CATEGORIES
add_action( 'trashed_post', 'srd_delete_link_category' ); //Deletes categories when page moved to trash
add_action( 'publish_page', 'srd_update_link_category' ); //Updates category when page is published

// Delete a category
	function srd_delete_link_category($post_id) {
		global $wpdb; // Globalizes database call

		// Get page name
		$pagename=get_the_title($post_id);
		
		//Get slug
		$post_data = get_post($post->ID, ARRAY_A);
		$pageslug = $post_data['post_name'];
		
		//Set taxonomy
		$taxonomy="link_category";			
	
		// Get the term id from the term with the same slug as the page
		$querystr = "SELECT term_id FROM wp_terms JOIN wp_term_taxonomy USING (term_id) WHERE wp_terms.slug = '$pageslug' AND wp_term_taxonomy.taxonomy = '$taxonomy'";
		$tag_ID = $wpdb->get_var($querystr);
		
		// Add a random number to the deleted page's slug so it won't interfere with future pages
		$update_slug = array();
		$update_slug['ID'] = $post_id;
		$update_slug['post_name'] = $pageslug."-".rand(100000,999999);
		wp_update_post($update_slug);
		
		//Delete the associated link category
		wp_delete_term( $tag_ID, $taxonomy );//Built-in WP function for deleting terms
}

//Add or update a category
	function srd_update_link_category($post_id) {
		global $wpdb; // allows database calls to work

		// Get page name
		$pagename=get_the_title($post_id);
		
		//Get slug
		$post_data = get_post($post->ID, ARRAY_A);
		$pageslug = $post_data['post_name'];
		
		//Set taxonomy
		$taxonomy="link_category";			
		
		// Check if old page name exists as a category name
		$querystr = "SELECT term_id FROM wp_terms JOIN wp_term_taxonomy USING (term_id) WHERE wp_terms.slug = '$pageslug' AND wp_term_taxonomy.taxonomy = '$taxonomy'";
		$tag_ID = $wpdb->get_var($querystr); //get single variable
			
		//If it exists, run an update	
		if ($tag_ID) {
			$srd_update = wp_update_term($tag_ID, $taxonomy, array('name' => $pagename, 'slug' => $pageslug));
		}
		//if it doesn't exist, insert it
		else {
			$srd_insert = wp_insert_term($pagename, $taxonomy, array('slug' => $pageslug));
		}
	}

//Make custom link list if link category slug matches page slug
	function srd_make_custom_link() {
		global $wpdb; // allows database calls to work

		//Get page slug
		$post_data = get_post($post->ID, ARRAY_A);
		$pageslug = $post_data['post_name'];	
		
		//Set taxonomy
		$taxonomy="link_category";			
		
		//Check to see if there are links with the same slug
		$querystr = "SELECT name FROM wp_terms JOIN wp_term_taxonomy USING (term_id) WHERE wp_terms.slug = '$pageslug' AND wp_term_taxonomy.taxonomy = '$taxonomy'";
		$category_name = $wpdb->get_var($querystr); //get single variable
			
		$bookmarks = get_bookmarks( array('orderby' =>'name', 'order'=>'ASC', 'category_name' => $category_name));
		
		//If so, display them
		if($bookmarks) {
			echo "<h3 class='related-links-title'>Related Links</h3>";
		
			// Loop through each bookmark and print formatted output
			foreach ( $bookmarks as $bm ) { 
				$srd_linklist .= printf( '<a class="related-link" href="%s">%s</a>', $bm->link_url, __($bm->link_name) );
			}
		}
	}

?>