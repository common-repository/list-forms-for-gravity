<?php
/*
Plugin Name: List-Forms for Gravity
Plugin URI: wpgear.xyz/gravity-list-forms/
Description: Description for each Forms on List-Forms. You may be interested in the Plugin for Export Records to Standard Excel Tables: <a href="http://wpgear.xyz/gv-excel-export/">"GV Excel-Export"</a> for "Gravity View".
Version: 2.5
Author: WPGear
Author URI: http://wpgear.xyz
License: GPLv2
*/

	$ListFormsGravity_plugin_url = plugin_dir_url( __FILE__); // со слэшем на конце	
	
	/* Admin Console - Styles.
	----------------------------------------------------------------- */	
	function ListFormsGravity_admin_style ($hook) {
		global $ListFormsGravity_plugin_url;
		
		$screen = get_current_screen();
		$screen_base = $screen->base;			
		
		if ( is_ListForms_GravityForms($screen_base) ) {
			wp_enqueue_style ('list_forms_gravity_style', $ListFormsGravity_plugin_url .'style.css');
			
			wp_enqueue_script ('list_forms_gravity_gf', $ListFormsGravity_plugin_url .'includes/gravity-list-forms_gf.js');
		}
		
		if ( is_ListForms_GravityView($screen_base) ) {
			wp_enqueue_style ('list_forms_gravity_style', $ListFormsGravity_plugin_url .'style.css');			
			
			wp_enqueue_script ('list_forms_gravity_gv', $ListFormsGravity_plugin_url .'includes/gravity-list-forms_gv.js');
		}

		if ( is_Page_EditForm_GravityView($screen_base) ) {
			wp_enqueue_style ('list_forms_gravity_style', $ListFormsGravity_plugin_url .'style.css');
		}		

	}
	add_action ('admin_enqueue_scripts', 'ListFormsGravity_admin_style' );

	/* Registering Script with Gravity Forms when running on "no-conflict mode"
	----------------------------------------------------------------- */
	function ListFormsGravity_register_safe_script_GF( $scripts ){
		$scripts[] = "list_forms_gravity_gf";
		return $scripts;
	}
	add_filter('gform_noconflict_scripts', 'ListFormsGravity_register_safe_script_GF' );	

	/* Registering Styles with Gravity Forms when running on "no-conflict mode"
	----------------------------------------------------------------- */
	function ListFormsGravity_register_safe_styles_GF( $styles ){
		$styles[] = "list_forms_gravity_style";
		return $styles;
	}
	add_filter('gform_noconflict_styles', 'ListFormsGravity_register_safe_styles_GF' );
	
	/* Registering Script with Gravity View when running on "no-conflict mode"
	----------------------------------------------------------------- */
	function ListFormsGravity_register_safe_script_GV( $scripts ){
		$scripts[] = "list_forms_gravity_gv";
		return $scripts;
	}
	add_filter('gravityview_noconflict_scripts', 'ListFormsGravity_register_safe_script_GV' );	
	
	/* Registering Styles with Gravity View when running on "no-conflict mode"
	----------------------------------------------------------------- */
	function ListFormsGravity_register_safe_styles_GV( $styles ){
		$styles[] = "list_forms_gravity_style";
		return $styles;
	}
	add_filter('gravityview_noconflict_styles', 'ListFormsGravity_register_safe_styles_GV' );	

	/* Check Page for: GF List-Forms. Exclude SubPages.
	----------------------------------------------------------------- */	
	function is_ListForms_GravityForms ($screen_base) {
		$Result = false;
		
		if ($screen_base == 'toplevel_page_gf_edit_forms') {				
			$Result = isset($_REQUEST['id']) ? false : true;
		}
		
		return $Result;
	}	
	
	/* Check Page for: GV List-Forms. Exclude SubPages.
	----------------------------------------------------------------- */	
	function is_ListForms_GravityView ($screen_base) {
		$Result = false;
		
		if ($screen_base == 'edit') {
			if (isset($_REQUEST['post_type']) && $_REQUEST['post_type'] == 'gravityview') {
				$Result = true;
			}
		}			
		
		return $Result;
	}

	/* Check Page for: GV EditForm.
	----------------------------------------------------------------- */	
	function is_Page_EditForm_GravityView ($screen_base) {
		$Result = false;
		
		if ($screen_base == 'post') {
			if (isset($_REQUEST['post']) && isset($_REQUEST['action'])) {
				if ($_REQUEST['action'] == 'edit') {
					$Post_ID = $_REQUEST['post'];
					$Post_Type = get_post_type ($Post_ID);
					
					if ($Post_Type == 'gravityview'){			
						$Result = true;
					}
				}
			}
		}	
		
		return $Result;
	}
	
	/* Add Field 'Form-Description' to GV Metabox
	----------------------------------------------------------------- */ 	
	function ListFormsGravity_Add_DescriptionToMetabox_GV() {
		// Action from: wp-content\plugins\gravityview\includes\admin\metaboxes\views\data-source.php
		$Description = '';
		
		$Post_ID = isset($_REQUEST['post']) ? sanitize_text_field ($_REQUEST['post']) : null;
				
		if ($Post_ID) {
			$Description = get_post_meta($Post_ID, 'description', true);
		}
		
		?>
		<div>
			<label for="list-forms-gravity_description_gv" title="Description for this View" style="vertical-align: top;">Description</label>
			<textarea id="list-forms-gravity_description_gv" name="list-forms-gravity_description_gv" rows="2" class="list-forms-gravity_description_gv"><?php echo $Description; ?></textarea>
		</div>
		<?php
	}
	add_action( 'gravityview/metaboxes/data-source/after', 'ListFormsGravity_Add_DescriptionToMetabox_GV' );

	/* After Save Form GV.
	----------------------------------------------------------------- */ 
	function ListFormsGravity_AfterSaveForm_GV($Post_ID) {
		$Description = isset($_REQUEST['list-forms-gravity_description_gv']) ? sanitize_text_field ($_REQUEST['list-forms-gravity_description_gv']) : null;
		
		if (! is_null ($Description)) {
			update_post_meta ($Post_ID, 'description', $Description);
		}		
	}
	add_action( 'gravityview_view_saved', 'ListFormsGravity_AfterSaveForm_GV' );
	
	/* AJAX Processing
	----------------------------------------------------------------- */    
    function ListFormsGravity_Ajax(){		
		include_once ('includes/ajax_gravity-list-forms.php');
    }
	add_action( 'wp_ajax_list_forms_gravity', 'ListFormsGravity_Ajax' );