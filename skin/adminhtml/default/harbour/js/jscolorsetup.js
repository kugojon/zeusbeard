Event.observe(window, 'load', function() {
	function jsColor(mainId, exceptions){
		if($$(mainId).length){
			var selection = 'input.input-text:not('+ exceptions +')';
			var selected_items = $$(mainId)[0].select(selection);
			selected_items.each(function(val){
				new jscolor.color(val);
			});
		}
	}
	jsColor('#meigee_harbour_design_base');
	jsColor('#meigee_harbour_design_catlabels');
	jsColor('#meigee_harbour_design_header', '#meigee_harbour_design_header_header_border_width, #meigee_harbour_design_header_header_search_border_width, #meigee_harbour_design_header_header_search_button_border_width, #meigee_harbour_design_header_login_submenu_link_divider_width, #meigee_harbour_design_header_cart_dropdown_total_border_width, #meigee_harbour_design_header_account_border_width, #meigee_harbour_design_header_account_submenu_link_divider_width, #meigee_harbour_design_header_menu_right_title_border_w');
	jsColor('#meigee_harbour_design_sticky_header', '#meigee_harbour_design_sticky_header_sticky_menu_link_border_width');
	jsColor('#meigee_harbour_design_rev_slider_but', '#meigee_harbour_design_rev_slider_but_buttons_transparent_bg_value, #meigee_harbour_design_rev_slider_but_buttons_transparent_border_value, #meigee_harbour_design_rev_slider_but_buttons_border_width, #meigee_harbour_design_rev_slider_but_buttons_transparent_bg_h_value, #meigee_harbour_design_rev_slider_but_buttons_transparent_border_h_value');
	
	jsColor('#meigee_harbour_design_parallax_banners', '#meigee_harbour_design_parallax_banners_colors1_button_border_width, #meigee_harbour_design_parallax_banners_colors2_button_border_width');
	jsColor('#meigee_harbour_design_page_not_found', '#meigee_harbour_design_page_not_found_button_border_width, #meigee_harbour_design_page_not_found_search_transparent_bg_value, #meigee_harbour_design_page_not_found_search_button_transparent_bg_value, #meigee_harbour_design_page_not_found_search_button_transparent_bg_h_value, #meigee_harbour_design_page_not_found_footer_links_transparent_bg_h_value');
	
	jsColor('#meigee_harbour_design_content', '#meigee_harbour_design_content_title_border_width');
	jsColor('#meigee_harbour_design_menu', '#meigee_harbour_design_menu_top_link_border_width, #meigee_harbour_design_menu_submenu_top_link_border_width, #meigee_harbour_design_menu_submenu_link_border_width');
	jsColor('#meigee_harbour_design_products', '#meigee_harbour_design_products_products_border_width, #meigee_harbour_design_products_products_divider_width');
	jsColor('#meigee_harbour_design_buttons', '#meigee_harbour_design_buttons_buttons_border_width, #meigee_harbour_design_buttons_buttons_2_border_width');
	jsColor('#meigee_harbour_design_social_links', '#meigee_harbour_design_social_links_social_links_border_width, #meigee_harbour_design_social_links_social_links_divider_width');
	jsColor('#meigee_harbour_design_footer', '#meigee_harbour_design_footer_top_block_border_width, #meigee_harbour_design_footer_top_block_title_divider_width, #meigee_harbour_design_footer_bottom_block_select_border_width');
	
});
