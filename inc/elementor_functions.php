<?php

function rabbitbuilder_js_css_check_ele(){
	
	$returnArray = array( 'RBJSCSS_ELEMENTOR_ENABLED' => false, 'RBJSCSS_ELEMENTOR_VERSION' => '0' );
	
	if( is_admin() ){
		
		if( is_plugin_active( 'elementor/elementor.php' ) ) {
			$RABBITBUILDERELEMENTORPLUGINDATA = get_plugin_data( WP_PLUGIN_DIR . '/elementor/elementor.php' );
			$returnArray['RBJSCSS_ELEMENTOR_ENABLED'] = true;
			$returnArray['RBJSCSS_ELEMENTOR_VERSION'] = $RABBITBUILDERELEMENTORPLUGINDATA['Version'];
		}

	}else{ //is_plugin_active functgion will not work on front end

		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		if ( is_plugin_active( 'elementor/elementor.php' ) ) {
			$RABBITBUILDERELEMENTORPLUGINDATA = get_plugin_data( WP_PLUGIN_DIR . '/elementor/elementor.php' );
			$returnArray['RBJSCSS_ELEMENTOR_ENABLED'] = true;
			$returnArray['RBJSCSS_ELEMENTOR_VERSION'] = $RABBITBUILDERELEMENTORPLUGINDATA['Version'];
		}

	} //else ends here
	
	return $returnArray;

}







function rabbitbuilder_js_css_get_ele_options( $version ){

  $elementor = array();
  
  if( $version >= '3' ){
	  
		$activeKitId 	= get_option( 'elementor_active_kit', 0 ); //add default emptry array
		$postMeta 		= get_post_meta( $activeKitId, '_elementor_page_settings' );

			


		if( isset( $postMeta[0]['system_colors'] ) && $postMeta[0]['system_colors'] != '' ){
			$elementor['color'] = $postMeta[0]['system_colors'];
		}else{
			$elementor['color'] = array();
		}
		
		if( isset( $postMeta[0]['custom_colors'] ) && $postMeta[0]['custom_colors'] != '' ){
			$elementor['picker'] = $postMeta[0]['custom_colors'];
		}else{
			$elementor['picker'] = array();
		}
		
		
		
		if( isset( $postMeta[0]['viewport_lg'] ) && $postMeta[0]['viewport_lg'] != '' ){
			$elementor['viewport_lg'] = $postMeta[0]['viewport_lg'];
		}else{
			$elementor['viewport_lg'] = '1025';
		}
		
		if( isset( $postMeta[0]['viewport_md'] ) && $postMeta[0]['viewport_md'] != '' ){
			$elementor['viewport_md'] = $postMeta[0]['viewport_md'];
		}else{
			$elementor['viewport_md'] = '768';
		}
		
		
		if( isset( $postMeta[0]['container_width']['size'] ) && $postMeta[0]['container_width']['size'] != '' ){
			$elementor['width'] = $postMeta[0]['container_width']['size'];
		}else{
			$elementor['width'] = '1140';
		}
		
		
		
		if( isset( $postMeta[0]['space_between_widgets']['size'] ) && $postMeta[0]['space_between_widgets']['size'] != '' ){
			$elementor['widgets_space'] = $postMeta[0]['space_between_widgets']['size'];
		}else{
			$elementor['widgets_space'] = '20';
		}
		
		

	  
	  
	  
	  
	  
  }else{ //if version 2 or old
	  
		$elementor['color'] 		= get_option( 'elementor_scheme_color', array() ); //add default emptry array
		$elementor['picker'] 		= get_option( 'elementor_scheme_color-picker', array() ); //add default emptry array
		$elementor['viewport_lg'] 	= get_option( 'elementor_viewport_lg', '1025' );
		$elementor['viewport_md'] 	= get_option( 'elementor_viewport_md', '768' );
		$elementor['width'] 		= get_option( 'elementor_container_width', '1140' );
		$elementor['typography'] = get_option( 'elementor_scheme_typography', array() ); //add default emptry array
  
  }

  
  

  return $elementor;

}






function rabbitbuilder_js_css_replace_ele_key_text( $textToReplace ){
	
	$eleStatus = rabbitbuilder_js_css_check_ele(); //get ele options
	
	//if elementor is not enabled starts
	if( $eleStatus['RBJSCSS_ELEMENTOR_ENABLED'] == false ) {
		return $textToReplace; // do nothing.
	}//if elementor is not enabled ends
	


	$elementor = rabbitbuilder_js_css_get_ele_options( $eleStatus['RBJSCSS_ELEMENTOR_VERSION'] );
	
	
	
	if( $eleStatus['RBJSCSS_ELEMENTOR_VERSION'] >= '3' ){
		
		if( !isset( $elementor['color'][0]['color'] ) ){
			return $textToReplace;
		}
		
		$replaceArray = array(
			'[[e_color_primary]]'   => $elementor['color'][0]['color'],
			'[[e_color_secondary]]' => $elementor['color'][1]['color'],
			'[[e_color_text]]'      => $elementor['color'][2]['color'],
			'[[e_color_accent]]'    => $elementor['color'][3]['color'],


			'[[e_width]]'     		=> $elementor['width'],
			'[[e_widgets_space]]'   => $elementor['widgets_space'],
			'[[e_view_lg]]'   		=> $elementor['viewport_lg'],
			'[[e_view_md]]'   		=> $elementor['viewport_md'],
			
		);
		
		foreach( $elementor['picker'] as $key => $picker ){
			$replaceArray['[[e_color_'.$picker['_id'].']]'] = $picker['color'];
		}
		

   
	}else{
		
		$replaceArray = array(
			'[[e_color_primary]]'   => $elementor['color'][1],
			'[[e_color_secondary]]' => $elementor['color'][2],
			'[[e_color_text]]'      => $elementor['color'][3],
			'[[e_color_accent]]'    => $elementor['color'][4],

			'[[e_font_primary]]'          => $elementor['typography'][1]['font_family'],
			'[[e_font_primary_weight]]'   => $elementor['typography'][1]['font_weight'],
			'[[e_font_secondary]]'        => $elementor['typography'][2]['font_family'],
			'[[e_font_secondary_weight]]' => $elementor['typography'][2]['font_weight'],
			'[[e_font_body]]'             => $elementor['typography'][3]['font_family'],
			'[[e_font_body_weight]]'      => $elementor['typography'][3]['font_weight'],
			'[[e_font_accent]]'           => $elementor['typography'][4]['font_family'],
			'[[e_font_accent_weight]]'    => $elementor['typography'][4]['font_weight'],

			'[[e_width]]'     => $elementor['width'],
			'[[e_widgets_space]]'   => $elementor['widgets_space'],
			'[[e_view_lg]]'   => $elementor['viewport_lg'],
			'[[e_view_md]]'   => $elementor['viewport_md'],
		);
		
		foreach( $elementor['picker'] as $key => $picker ){
			$replaceArray['[[e_color_picker_'.$key.']]'] = $picker;
		}
		
	}
  

  $textToReplace = strtr( $textToReplace, $replaceArray ); //this will replace all the text

  return $textToReplace;
}






function rabbitbuilder_js_css_render_admin_panel( $title, $html ){

	$htmlFinal = '
	
	<style>
	span.eColorPreview{
	  background-color: #6ec1e4;
	  width: 20px;
	  height: 20px;
	  vertical-align: middle;
	  border: 1px solid #ccc;
	  display: inline-block;
	}
	.etips th{
	  max-width: 60px;
	}

	.etips th.odd{
	  background: #ececec;
	}
	.etips td{
	  max-width: 100px;
	}
	</style>
	

	<div class="customjscss-options">
		<div id="elementorTipsHeader" class="customjscss-options-header">
			<h3>'.$title.'</h3>
		</div>
		<div id="elementorTipsBody" class="customjscss-options-data">
			'.$html.'
		</div>
	</div>

	';
	echo $htmlFinal;

}





function rabbitbuilder_js_css_show_ele_usage_panel(){
	
	$eleStatus = rabbitbuilder_js_css_check_ele();
	
	$title = __( 'How to use Elementor Global Styles?', RBJSCSS_PLUGIN_NAME);
	$html = '';


	
	//if elementor is not enabled starts
	if( $eleStatus['RBJSCSS_ELEMENTOR_ENABLED'] == false ) {
		$title = __('Elementor notice!', RBJSCSS_PLUGIN_NAME);
		$html .= '<p>This is just a notice that Elementor Plugin is disabled or not installed. You will be able to use this plugin without any issues however certain features will not work.</p>';
		
		rabbitbuilder_js_css_render_admin_panel( $title, $html );
		return; //return from here
	}//if elementor is not enabled ends
	


	$elementor = rabbitbuilder_js_css_get_ele_options( $eleStatus['RBJSCSS_ELEMENTOR_VERSION'] );

	//if we are using elementor new version 3 with global css starts
	if( $eleStatus['RBJSCSS_ELEMENTOR_VERSION'] >= '3' ){ //if elementor version 3
	
	
		if( !isset( $elementor['color'][0]['color'] ) ){ //if no elementor global settings found.
			
			$title = __('Elementor notice!', RBJSCSS_PLUGIN_NAME);
			$html .= '<p>Elementor Global Settings not found. Click <a href="http://go.elementor.com/panel-layout-settings" target="_blank">Learn More</a> to set global settings.</p>';
			
			rabbitbuilder_js_css_render_admin_panel( $title, $html );
			return; //return from here
		}
		
		

		$html .= '<p style="margin-bottom: 40px;">Example: Use this keyword <code>[[e_color_primary]]</code> in your css/scss code and it will be converted into value of Elementor\'s global primary color <strong>"'.$elementor['color'][0]['color'].'"</strong>.</p>';
	
		$html .= '

		<table class="etips"><tbody>

			<tr>
			  <th class="odd"><label><strong>Global Colors</strong></label></th>
			  <td><code>[[e_color_'.$elementor['color'][0]['_id'].']]</code> = '.$elementor['color'][0]['color'].' <span class="eColorPreview" style="background-color: '.$elementor['color'][0]['color'].'"></span></td>
			  <td><code>[[e_color_'.$elementor['color'][1]['_id'].']]</code> = '.$elementor['color'][1]['color'].' <span class="eColorPreview" style="background-color: '.$elementor['color'][1]['color'].'"></span></td>
			  <td><code>[[e_color_'.$elementor['color'][2]['_id'].']]</code> = '.$elementor['color'][2]['color'].' <span class="eColorPreview" style="background-color: '.$elementor['color'][2]['color'].'"></span></td>
			  <td><code>[[e_color_'.$elementor['color'][3]['_id'].']]</code> = '.$elementor['color'][3]['color'].' <span class="eColorPreview" style="background-color: '.$elementor['color'][3]['color'].'"></span></td>
			</tr>

		</tbody></table>
		
		
		
		<table class="etips" style="margin-bottom: 40px;"><tbody>
		<tr><th class="odd" style="max-width: 29px;"><label><strong>Global Custom Colors</strong></label></th>
		';
		
		
		foreach( $elementor['picker'] as $key => $picker){

			$html .= '<td><code>[[e_color_' .$picker['_id']. ']]</code> = ('.$picker['title'].') <span class="eColorPreview" style="background-color: ' .$picker['color']. '"></span></td>';
			if( $key % 4 === 3 )	$html .= '</tr><tr><th></th>';
		}



		$html .= '
		
		
			<table class="etips" style="margin-bottom: 40px;"><tbody>
	
			<tr>
			  <th class="odd" style="max-width: 29px;"><label><strong>Break Points</strong></label></th>
			  <td><code>[[e_view_lg]]</code> = '.$elementor['viewport_lg'].'</td>
			  <td><code>[[e_view_md]]</code> = '.$elementor['viewport_md'].'</td>
			</tr>
			
			
			<tr>
			  <th class="odd" style="max-width: 29px;"><label><strong>Container Width</strong></label></th>
			  <td><code>[[e_width]]</code> = '.$elementor['width'].'</td>
			  <td><code>[[e_widgets_space]]</code> = '.$elementor['widgets_space'].'</td>
			</tr>
			
			
		</tbody></table>
		';
		
		
	}else{ //if elementor version 3 else
		
		$elementorDisableColorSchemes = get_option( 'elementor_disable_color_schemes' );
		$elementorDisableTypographySchemes = get_option( 'elementor_disable_typography_schemes' );

		if($elementorDisableColorSchemes == 'yes'){
		  $eleStatus['RBJSCSS_ELEMENTOR_ENABLED'] = false;
		}

		if($elementorDisableTypographySchemes == 'yes'){
		  $eleStatus['RBJSCSS_ELEMENTOR_ENABLED'] = false;
		}

		if( $eleStatus['RBJSCSS_ELEMENTOR_ENABLED'] == false ){
		  $html .= '<h4 style="color:red;">Some Elementor Global Syltes are disabled! Certain Features will not work. Please enable them from Elementor -> Settings -> Disable Default Colors / Disable Default Fonts.</h4>';
		}
		
		
		$html .= '<p style="margin-bottom: 40px;">Example: Use this keyword <code>[[e_color_primary]]</code> in your css/scss code and it will be converted into value of Elementor\'s global primary color <strong>"'.$elementor['color'][1].'"</strong>.</p>';
		
		
		$html .= '

		<table class="etips"><tbody>

        <tr>
          <th class="odd"><label><strong>Global Colors</strong></label></th>
          <td><code>[[e_color_primary]]</code> = '.$elementor['color'][1].' <span class="eColorPreview" style="background-color: '.$elementor['color'][1].'"></span></td>
          <td><code>[[e_color_secondary]]</code> = '.$elementor['color'][2].' <span class="eColorPreview" style="background-color: '.$elementor['color'][2].'"></span></td>
          <td><code>[[e_color_text]]</code> = '.$elementor['color'][3].' <span class="eColorPreview" style="background-color: '.$elementor['color'][3].'"></span></td>
          <td><code>[[e_color_accent]]</code> = '.$elementor['color'][4].' <span class="eColorPreview" style="background-color: '.$elementor['color'][4].'"></span></td>
        </tr>

		</tbody></table>
		
		
		
		<table class="etips" style="margin-bottom: 40px;"><tbody>
		<tr><th class="odd"><label><strong>Global Custom Colors</strong></label></th>
		';
		
		
		foreach( $elementor['picker'] as $key => $picker){

			$html .= '<td><code>[[e_color_picker_'.$key.']]</code> = '.$picker.' <span class="eColorPreview" style="background-color: '.$picker.'"></span></td>';
			if( $key % 4 === 0 )	$html .= '</tr><tr><th></th>';
		}



		$html .= '
		<table class="etips" style="margin-bottom: 40px;"><tbody>
		
			<tr>
			  <th class="odd"><label><strong>Break Points</strong></label></th>
			  <td><code>[[e_view_lg]]</code> = '.$elementor['viewport_lg'].'</td>
			  <td><code>[[e_view_md]]</code> = '.$elementor['viewport_md'].'</td>
			</tr>
			
			
			<tr>
			  <th class="odd"><label><strong>Container Width</strong></label></th>
			  <td><code>[[e_width]]</code> = '.$elementor['width'].'</td>
			</tr>
			

			<tr>
			  <th class="odd"><label><strong>Gobal Fonts</strong></label></th>
			  <td><code>[[e_font_primary]]</code> = '.$elementor['typography'][1]['font_family'].'</td>
			  <td><code>[[e_font_primary_weight]]</code> = '.$elementor['typography'][1]['font_weight'].'</td>
			  <td><code>[[e_font_secondary]]</code> = '.$elementor['typography'][2]['font_family'].'</td>
			  <td><code>[[e_font_secondary_weight]]</code> = '.$elementor['typography'][2]['font_weight'].'</td>
			</tr>

			<tr>
			  <th></th>
			  <td><code>[[e_font_body]]</code> = '.$elementor['typography'][3]['font_family'].'</td>
			  <td><code>[[e_font_body_weight]]</code> = '.$elementor['typography'][3]['font_weight'].'</td>
			  <td><code>[[e_font_accent]]</code> = '.$elementor['typography'][4]['font_family'].'</td>
			  <td><code>[[e_font_accent_weight]]</code> = '.$elementor['typography'][4]['font_weight'].'</td>
			</tr>

		</tbody></table>
		';
		

		
	} //if elementor version 3 or 2 ends here

	
	


	
	rabbitbuilder_js_css_render_admin_panel( $title, $html );
	return; //return from here
  





} //rabbitbuilder_js_css_show_ele_usage_panel ends
