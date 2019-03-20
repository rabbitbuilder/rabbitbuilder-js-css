(function($){
   $(window).load(function() {
    
    $('#elementor-editor-wrapper').on('hover', function() {
        if( $('.elementor-panel-scheme-buttons div').length <= 3 ){
            $('.elementor-panel-scheme-buttons').append('<div id="rbRefreshPreview"><button class="elementor-button elementor-button-success">REFRESH PREVIEW AFTER APPLY</button></div>');
        } 
    });
    
    $('#elementor-editor-wrapper').on('click', '#rbRefreshPreview', function() {
        elementor.reloadPreview();
    });
	
	
	});
})(jQuery);