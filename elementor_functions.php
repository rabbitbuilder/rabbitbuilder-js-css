<?php


function rabbitbuilder_js_css_get_ele_options(){

  $elementor = array();

  $elementor['color'] = get_option( 'elementor_scheme_color', array() ); //add default emptry array
  $elementor['typography'] = get_option( 'elementor_scheme_typography', array() ); //add default emptry array
  $elementor['picker'] = get_option( 'elementor_scheme_color-picker', array() ); //add default emptry array
  

  $elementor['width'] = get_option( 'elementor_container_width', '1140' );
  if( $elementor['width'] == ''){ //sometimes it happens that blank value will sent so check this first
    $elementor['width'] = '1140';
  }

  $elementor['viewportLg'] = get_option( 'elementor_viewport_lg', '1025' );
  if( $elementor['viewportLg'] == ''){ //sometimes it happens that blank value will sent so check this first
    $elementor['viewportLg'] = '1025';
  }

  $elementor['viewportMd'] = get_option( 'elementor_viewport_md', '768' );
  if( $elementor['viewportMd'] == ''){ //sometimes it happens that blank value will sent so check this first
    $elementor['viewportMd'] = '768';
  }

  return $elementor;

}






function rabbitbuilder_js_css_replace_ele_key_text( $textToReplace ){

  if( is_admin() ){
    if( !is_plugin_active( 'elementor/elementor.php' ) ) {
    	return $textToReplace; // do nothing. Retun the text unmodified.
    }
  }else{

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    // check for plugin using plugin name
    if ( !is_plugin_active( 'elementor/elementor.php' ) ) {
      return $textToReplace; // do nothing. Retun the text unmodified.
    }

  }


  $elementor = rabbitbuilder_js_css_get_ele_options();

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
    '[[e_view_lg]]'   => $elementor['viewportLg'],
    '[[e_view_md]]'   => $elementor['viewportMd'],
	
   );
   
   
   
   foreach( $elementor['picker'] as $key => $picker ){
	   $replaceArray['[[e_color_picker_'.$key.']]'] = $picker;
   }


  $textToReplace = strtr( $textToReplace, $replaceArray ); //this will replace all the text

  return $textToReplace;
}





function rabbitbuilder_js_css_show_ele_usage_panel(){

  $elementorEnabled = true;
  $title = __( 'How to use Elementor Global Styles?', RBJSCSS_PLUGIN_NAME);
  $html = '';




  if( !is_plugin_active( 'elementor/elementor.php' ) ) {
  	$title = __('Elementor Plugin is disabled!', RBJSCSS_PLUGIN_NAME);
    $html .= '<p>This is just a notice that Elementor Plugin is disabled or not installed. You will be able to use this plugin without any issues
    however certain features will not work.';
    $elementorEnabled = false;
  }


  if( $elementorEnabled ){ //now check if the Elementor Global styles are enabled or not

    $elementorDisableColorSchemes = get_option( 'elementor_disable_color_schemes' );
    $elementorDisableTypographySchemes = get_option( 'elementor_disable_typography_schemes' );

    $elementor = rabbitbuilder_js_css_get_ele_options();

    if($elementorDisableColorSchemes == 'yes'){
      $elementorEnabled = false;
    }

    if($elementorDisableTypographySchemes == 'yes'){
      $elementorEnabled = false;
    }

    if($elementorEnabled == false){
      $html .= '<h4 style="color:red;">Some Elementor Global Syltes are disabled! Certain Features will not work. Please enable them from Elementor -> Settings -> Disable Default Colors / Disable Default Fonts.</h4>';
    }

    $html .= '<p>Example: This keyword <code>[[e_color_primary]]</code> will be converted into value of Elementor\'s global primary color. Following is complete list of all <strong>keywords:</strong></p>';

    $html .= '

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

    <table class="etips" style="margin-bottom: 40px;"><tbody>

        <tr>
          <th class="odd"><label><strong>Global Colors</strong></label></th>
          <td><code>[[e_color_primary]]</code> = '.$elementor['color'][1].' <span class="eColorPreview" style="background-color: '.$elementor['color'][1].'"></span></td>
          <td><code>[[e_color_secondary]]</code> = '.$elementor['color'][2].' <span class="eColorPreview" style="background-color: '.$elementor['color'][2].'"></span></td>
          <td><code>[[e_color_text]]</code> = '.$elementor['color'][3].' <span class="eColorPreview" style="background-color: '.$elementor['color'][3].'"></span></td>
          <td><code>[[e_color_accent]]</code> = '.$elementor['color'][4].' <span class="eColorPreview" style="background-color: '.$elementor['color'][4].'"></span></td>
        </tr>


        <tr>
          <th class="odd"><label><strong>Break Points</strong></label></th>
          <td><code>[[e_view_lg]]</code> = '.$elementor['viewportLg'].'</td>
          <td><code>[[e_view_md]]</code> = '.$elementor['viewportMd'].'</td>
        </tr>

	</tbody></table>
  
  


	<table class="etips" style="margin-bottom: 40px;"><tbody>

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


	<table class="etips"><tbody>
	<tr><th class="odd"><label><strong>Global Picker</strong></label></th>
	';
	
	foreach( $elementor['picker'] as $key => $picker){

		$html .= '<td><code>[[e_color_picker_'.$key.']]</code> = '.$picker.' <span class="eColorPreview" style="background-color: '.$picker.'"></span></td>';
		if( $key % 4 === 0 )	$html .= '</tr><tr><th></th>';
	}

	$html .= '</tbody></table>';

  }





  $htmlFinal = '

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
