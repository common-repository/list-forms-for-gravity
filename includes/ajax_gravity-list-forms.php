<?php
/*
 * List-Forms for Gravity
 * ajax_gravity-list-forms.php
 */	 
 
	$current_user = wp_get_current_user();	
	$User_Name 	= $current_user->user_login;
		
	$Mode 	= isset($_REQUEST['mode']) ? sanitize_text_field($_REQUEST['mode']) : null;
	$Forms	= isset($_REQUEST['forms']) ? sanitize_text_field($_REQUEST['forms']) : null;
	$Type 	= isset($_REQUEST['type']) ? sanitize_text_field($_REQUEST['type']) : null;

	global $ListFormsGravity_plugin_url;	

	$Forms_Notes = array();
	
	$Result = false; 
	
	// get_forms_description
	if ($Mode == 'get_forms_description') {		
		if ($Forms) {
			if ($Type == 'gf') {
				//Gravity Forms
				global $wpdb;
				
				$ListFormsGravity_gf_form_meta_table = $wpdb->prefix .'gf_form_meta';
				
				$Forms_Notes = array();

				// Prepare Query
				$Forms = explode(',', $Forms);			
				$Count_Forms = count($Forms);
				$FormsID = array_fill(0, $Count_Forms, '%d');
				$FormsID = implode( ', ', $FormsID);
						
				$Query = "SELECT form_id as ID, display_meta FROM $ListFormsGravity_gf_form_meta_table WHERE form_id IN ($FormsID)";
				$Records = $wpdb->get_results ($wpdb->prepare ($Query, $Forms), OBJECT_K);
				
				foreach ($Records as $key => $value) {
					$Meta = $value->display_meta;
					$Meta = json_decode($Meta);

					$Description = $Meta->description;
					
					$Meta = array (
						'id' => $key,
						'note' => $Description,
					);
					
					$Forms_Notes[] = $Meta;
				}				
			}
			
			if ($Type == 'gv') {
				//Gravity View
				$Forms = explode(',', $Forms);			
				$Count_Forms = count($Forms);
				
				foreach ($Forms as $Post_ID) {
					$Description = get_post_meta($Post_ID, 'description', true);
					
					$Meta = array (
						'id' => $Post_ID,
						'note' => $Description,
					);
					
					$Forms_Notes[] = $Meta;
				
				}				
			}				
		}
		
		$Result = true;	
	}
	
	$Obj_Request = new stdClass();
	$Obj_Request->status 	= 'OK';
	$Obj_Request->answer 	= $Result;
	$Obj_Request->notes 	= $Forms_Notes;

	wp_send_json($Obj_Request);    

	die; // Complete.