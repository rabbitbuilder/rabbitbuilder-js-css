<?php

// prevent direct access
defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );

$list_table = new RabbitBuilderJsCss_List_Table_Items();
$list_table->prepare_items();

?>

<div class="wrap customjscss">

	<h2 class="customjscss-main-title"><span><?php _e( RB_GLOBAL_JS_CSS_PAGE_TITLE ); ?></span></h2>
	<p class="customjscss-actions">
		<a href="?page=<?php echo $_REQUEST['page']; ?>&action=new&type=js" class="page-title-action"><?php _e( 'Add JS Code', RBJSCSS_PLUGIN_NAME ); ?></a>
		<a href="?page=<?php echo $_REQUEST['page']; ?>&action=new&type=css" class="page-title-action"><?php _e( 'Add CSS Code', RBJSCSS_PLUGIN_NAME ); ?></a>
		<a href="?page=<?php echo $_REQUEST['page']; ?>&action=new&type=html" class="page-title-action"><?php _e( 'Add HTML Code', RBJSCSS_PLUGIN_NAME ); ?></a>
	</p>
	<!-- customjscss app -->
	<div id="customjscss-app-items" class="customjscss-app">
		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>">
			<?php $list_table->display() ?>
		</form>
	</div>
	<!-- /end customjscss app -->
</div>
