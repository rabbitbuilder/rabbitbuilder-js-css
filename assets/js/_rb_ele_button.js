(function($){
	
 
	
	/*
	$(window).load(function() {
		
		$('#elementor-panel-header-menu-button').click(function(){
			var newButtonHtml = '<div class="elementor-panel-menu-item elementor-panel-menu-item-global-colors"><div class="elementor-panel-menu-item-icon"><i class="eicon-paint-brush"></i></div><div class="elementor-panel-menu-item-title">Default Colors</div></div>';
			$( newButtonHtml ).insertBefore( '#elementor-panel-page-menu-content div.elementor-panel-menu-group:first-child .elementor-panel-menu-items .elementor-panel-menu-item-global-colors' );
			
			console.log('pressed');
		});
		   
		$('#elementor-editor-wrapper').on('hover', function() {

			if( $('.elementor-panel-scheme-buttons div').length <= 3 ){
				$('.elementor-panel-scheme-buttons').append('<div id="rbRefreshPreview"><button class="elementor-button elementor-button-success">REFRESH PREVIEW AFTER APPLY</button></div>');
			} 
		});
		
		$('#elementor-editor-wrapper').on('click', '#rbRefreshPreview', function() {
			elementor.reloadPreview();
		});

	});
	*/

	
	$( document ).ajaxComplete(function( event, xhr, settings ) {
		
		var fromData = false;
		var fromDataActions = false;
		
		if( typeof(settings.data) != "undefined" && settings.data !== null) {
			var fromData = decodeFormParamsRabbitBuilder( settings.data );
			
			if( typeof(fromData.actions) != "undefined" && fromData.data !== null) {
				var fromDataActions = JSON.parse( fromData.actions);
			}
		}
		
		
		
		
		
		if( fromDataActions != false && typeof(fromDataActions) == 'object' ){
			
			if (fromDataActions.hasOwnProperty('apply_scheme')) {
				if (fromDataActions.apply_scheme.hasOwnProperty('action')) {
					if( fromDataActions.apply_scheme.action == 'apply_scheme' ){
						if( fromDataActions.apply_scheme.data.scheme_name == 'color' ){
							elementor.reloadPreview();
						}
					}
				}
			}
			
			
			//version 3
			if (fromDataActions.hasOwnProperty('save_builder')) {
				if (fromDataActions.save_builder.hasOwnProperty('action')) {
					if( fromDataActions.save_builder.action == 'save_builder' ){
						if( fromDataActions.save_builder.data.status == 'publish' ){
							elementor.reloadPreview();
						}
						
					}
				}
				
			}
			
			
		}
			
	});


})(jQuery);



function decodeFormParamsRabbitBuilder( params ) {
  var pairs = params.split('&'),
      result = {};

  for (var i = 0; i < pairs.length; i++) {
    var pair = pairs[i].split('='),
        key = decodeURIComponent(pair[0]),
        value = decodeURIComponent(pair[1]),
        isArray = /\[\]$/.test(key),
        dictMatch = key.match(/^(.+)\[([^\]]+)\]$/);

    if (dictMatch) {
      key = dictMatch[1];
      var subkey = dictMatch[2];

      result[key] = result[key] || {};
      result[key][subkey] = value;
    } else if (isArray) {
      key = key.substring(0, key.length-2);
      result[key] = result[key] || [];
      result[key].push(value);
    } else {
      result[key] = value;
    }
  }

  return result;
}