<?php

function wptouch_get_wordpress_menu_items( $parent = 0 ) {
	global $wpdb;
	
	$settings = wptouch_get_settings();	
	
	$sql = $wpdb->prepare( "SELECT * FROM {$wpdb->base_prefix}posts WHERE ID IN (SELECT object_id FROM {$wpdb->base_prefix}term_relationships WHERE term_taxonomy_id = %s) AND ID IN (SELECT post_id FROM {$wpdb->base_prefix}postmeta WHERE meta_key = '_menu_item_menu_item_parent' AND meta_value = '%d') ORDER BY menu_order", $settings->custom_menu_name, $parent );
	
	//echo $sql . "<br/>";
	
	$menu_items = $wpdb->get_results( $sql );
	
	return $menu_items;
}

function wptouch_wordpress_menu_has_children( $parent ) {
	global $wpdb;
	
	$settings = wptouch_get_settings();	
	
	$sql = $wpdb->prepare( "SELECT count(*) as c FROM {$wpdb->base_prefix}posts WHERE ID IN (SELECT object_id FROM {$wpdb->base_prefix}term_relationships WHERE term_taxonomy_id = %d) AND ID IN (SELECT post_id FROM {$wpdb->base_prefix}postmeta WHERE meta_key = '_menu_item_menu_item_parent' AND meta_value = '%d') ORDER BY menu_order", $settings->custom_menu_name, $parent );
	
//	echo $sql . "<br/>";
	
	$menu_items = $wpdb->get_row( $sql );

	if ( $menu_items && $menu_items->c ) {
		return true;
	}
	
	return false;
}

function wptouch_build_wordpress_menu_tree( $parent = 0, $depth = 1, &$data, $exclude_disabled = false, $parent_object = false ) {
	global $wptouch_pro;
	
	$items = wptouch_get_wordpress_menu_items( $parent );
	
	if ( $depth > 1 && $parent_object ) {
		$menu_data = wptouch_create_menu_item(
			$parent_object->page_id,
			$depth,
			$parent_object->title,
			'link',
			false,
			0,
			true,
			$parent_object->item_link,
			false
		);	
	
		$data[ $parent_object->title ] = $menu_data;
	}

	if ( $items ) {
		// iterate through all items
		foreach( $items as $item ) {
			$menu_item_type = get_post_meta( $item->ID, '_menu_item_type', TRUE );			
			$object_id = get_post_meta( $item->ID, '_menu_item_object_id', TRUE );
			
			$post_title = $item->post_title;
			
			if ( $object_id ) {
			
				switch( $menu_item_type ) {
					case 'post_type':					
						$post_info = get_post( $object_id );
						
						if ( !$post_title ) {
							$post_title = $post_info->post_title;	
						}					
						
						$menu_data = wptouch_create_menu_item(
							$item->ID,
							$depth,
							$post_title,
							'link',
							false,
							0,
							false,
							get_permalink( $object_id ),
							$wptouch_pro->get_class_for_webapp_ignore( get_permalink( $object_id ) )
						);						
						
						if ( wptouch_wordpress_menu_has_children( $item->ID ) ) {
							$sub_menu = array();
							
							wptouch_build_wordpress_menu_tree( $item->ID, $depth + 1, $sub_menu, $exclude_disabled, $menu_data );
							
							$menu_data->has_children = true;
							$menu_data->submenu = $sub_menu;
							$menu_data->item_type = 'menu';
						}	
	
						
						$data[ $post_title ] = $menu_data;
				
						break;
					case 'taxonomy':
						if ( !$post_title ) {
							$post_title = get_cat_name( $object_id );	
						}						
					
						$menu_data = wptouch_create_menu_item(
							$item->ID + WPTOUCH_ICON_CATEGORY_BASE,
							$depth,
							$post_title,
							'link',
							false,
							0,
							false,
							get_category_link( $object_id ),
							$wptouch_pro->get_class_for_webapp_ignore( get_category_link( $object_id ) )
						);						
						
						if ( wptouch_wordpress_menu_has_children( $item->ID ) ) {
							$sub_menu = array();
							
							wptouch_build_wordpress_menu_tree( $item->ID, $depth + 1, $sub_menu, $exclude_disabled, $menu_data );
							
							$menu_data->has_children = true;
							$menu_data->submenu = $sub_menu;
							$menu_data->item_type = 'menu';
						}	
	
						
						$data[ $post_title ] = $menu_data;
											
						break;
					case 'custom':
						$menu_data = wptouch_create_menu_item(
							$item->ID + WPTOUCH_ICON_CUSTOM_BASE,
							$depth,
							$post_title,
							'link',
							false,
							0,
							false,
							get_post_meta( $item->ID, '_menu_item_url', TRUE ),
							$wptouch_pro->get_class_for_webapp_ignore( get_post_meta( $item->ID, '_menu_item_url', TRUE ) )
						);								
						
						if ( wptouch_wordpress_menu_has_children( $item->ID ) ) {
							$sub_menu = array();
							
							wptouch_build_wordpress_menu_tree( $item->ID, $depth + 1, $sub_menu, $exclude_disabled, $menu_data );
							
							$menu_data->has_children = true;
							$menu_data->submenu = $sub_menu;
							$menu_data->item_type = 'menu';
						}	
	
						
						$data[ $post_title ] = $menu_data;
														
						break;
				}
			}
		}
			
	}
}
