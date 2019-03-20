<?php

// prevent direct access
defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );

$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRIPPED );
$author = get_the_author_meta( 'display_name', $item->author );
$modified = mysql2date( get_option( 'date_format' ), $item->modified ) . ' at ' . mysql2date( get_option( 'time_format' ), $item->modified );

?>
<div class="wrap customjscss">



	<h2 class="customjscss-main-title"><span><?php _e('RabbitBuilder Global Central JS CSS', RBJSCSS_PLUGIN_NAME); ?></span></h2>
	<div class="customjscss-messages" id="customjscss-messages">
	</div>
	<p class="customjscss-actions">
		<a href="?page=<?php echo $page; ?>&action=new&type=js" class="page-title-action"><?php _e('Add JS Code', RBJSCSS_PLUGIN_NAME); ?></a>
		<a href="?page=<?php echo $page; ?>&action=new&type=css" class="page-title-action"><?php _e('Add CSS Code', RBJSCSS_PLUGIN_NAME); ?></a>
		<a href="?page=<?php echo $page; ?>&action=new&type=html" class="page-title-action"><?php _e('Add HTML Code', RBJSCSS_PLUGIN_NAME); ?></a>
	</p>
	<!-- customjscss app -->
	<div id="customjscss-app-item" class="customjscss-app" style="display:none;">
		<div class="customjscss-loader-wrap">
			<div class="customjscss-loader">
				<div class="customjscss-loader-bar"></div>
				<div class="customjscss-loader-bar"></div>
				<div class="customjscss-loader-bar"></div>
				<div class="customjscss-loader-bar"></div>
			</div>
		</div>
		<div class="customjscss-wrap">
			<div class="customjscss-header">
				<input class="customjscss-title" type="text" placeholder="<?php _e('Title', RBJSCSS_PLUGIN_NAME); ?>" al-text="appData.config.title">
			</div>
			<div class="customjscss-workplace">



				<div class="customjscss-options" al-attr.class.customjscss-options-active="appData.config.options.showOptionsPanel">
					<div class="customjscss-options-header" al-on.click="appData.fn.toggleOptionsPanel(appData);">
						<h3><?php _e('Options', RBJSCSS_PLUGIN_NAME); ?></h3>
					</div>
					<div class="customjscss-options-data">
						<table>
							<tbody>

								<?php /*
								<tr>
									<th><label for="customjscss-active"><?php _e('Active', RBJSCSS_PLUGIN_NAME); ?></label></th>
									<td><div al-checkbox="appData.config.active"></div></td>
								</tr>

								<tr al-if="appData.config.type == 'css' || appData.config.type == 'js'">
									<th>
										<label for="customjscss-minify"><?php _e('Minify output', RBJSCSS_PLUGIN_NAME); ?></label>
										<div al-if="appData.plan == 'lite'" class="customjscss-pro" title="<?php _e('Available only in the pro version', RBJSCSS_PLUGIN_NAME); ?>">Pro</div>
									</th>
									<td><div al-checkbox="appData.config.options.minify"></div></td>
								</tr>
								*/ ?>

								<tr al-if="appData.config.type == 'css'">
									<th>
										<label for="customjscss-preprocessor"><?php _e('Preprocessor', RBJSCSS_PLUGIN_NAME); ?></label>
										<div al-if="appData.plan == 'lite'" class="customjscss-pro" title="<?php _e('Available only in the pro version', RBJSCSS_PLUGIN_NAME); ?>">Pro</div>
									</th>
									<td>
										<select id="customjscss-preprocessor" al-value="appData.config.options.preprocessor">
											<option value="none"><?php _e('None', RBJSCSS_PLUGIN_NAME); ?></option>
											<option value="scss"><?php _e('Scss', RBJSCSS_PLUGIN_NAME); ?></option>
											<?php /* <option value="less"><?php _e('Less', RBJSCSS_PLUGIN_NAME); ?></option> */ ?>
										</select>
									</td>
								</tr>


								<?php if( defined('RBNETWORK') ): ?>

									<tr style="display:none">
										<th><label for="customjscss-linking-type"><?php _e('Linking type', RBJSCSS_PLUGIN_NAME); ?></label></th>
										<td>
											<select id="customjscss-linking-type" al-value="appData.config.options.file">
											<option value="internal"><?php _e('Internal', RBJSCSS_PLUGIN_NAME); ?></option>
											</select>
										</td>
									</tr>

								<?php else: ?>

									<tr al-if="appData.config.type != 'html'">
										<th><label for="customjscss-linking-type"><?php _e('Linking type', RBJSCSS_PLUGIN_NAME); ?></label></th>
										<td>
											<select id="customjscss-linking-type" al-value="appData.config.options.file">
											<option value="internal"><?php _e('Internal', RBJSCSS_PLUGIN_NAME); ?></option>
											<option value="external"><?php _e('External', RBJSCSS_PLUGIN_NAME); ?></option>
											</select>
										</td>
									</tr>

								<?php endif; ?>


								<tr>
									<th><label for="customjscss-where-on-page-header"><?php _e('Where on page', RBJSCSS_PLUGIN_NAME); ?></label></th>
									<td>
										<select id="customjscss-where-on-page-header" al-value="appData.config.options.whereonpage">
											<option value="header"><?php _e('Header', RBJSCSS_PLUGIN_NAME); ?></option>
											<option value="footer"><?php _e('Footer', RBJSCSS_PLUGIN_NAME); ?></option>
										</select>
									</td>
								</tr>
								<tr>
									<th><label for="customjscss-where-in-site"><?php _e('Where in site', RBJSCSS_PLUGIN_NAME); ?></label></th>
									<td>
										<select id="customjscss-where-in-site" al-value="appData.config.options.whereinsite">
											<option value="user"><?php _e('User side', RBJSCSS_PLUGIN_NAME); ?></option>
											<option value="admin"><?php _e('Admin side', RBJSCSS_PLUGIN_NAME); ?></option>
											<option value="both"><?php _e('All', RBJSCSS_PLUGIN_NAME); ?></option>
										</select>
									</td>
								</tr>

								<?php /*
								<tr>
									<th><label for="customjscss-filter"><?php _e('Filter', RBJSCSS_PLUGIN_NAME); ?></label></th>
									<td>
										<select id="customjscss-filter" al-select="appData.config.options.filter">
											<option al-repeat="filter in appData.filters" al-option="filter.id">{{filter.title}}</option>
										</select>
									</td>
								</tr>
								*/ ?>

								<tr style="display:none">
									<th><label for="customjscss-filter"><?php _e('Filter', RBJSCSS_PLUGIN_NAME); ?></label></th>
									<td>
										<select>
											<option value="i1">None</option>
										</select>
									</td>
								</tr>




							</tbody>
						</table>
					</div>
				</div>


				<?php
				require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/elementor_functions.php' );
				showElementorUsagePanel();
				?>





				<div class="customjscss-editor" al-attr.class.customjscss-editor-active="appData.config.options.showEditorPanel">
					<div class="customjscss-editor-header" al-on.click="appData.fn.toggleEditorPanel(appData);">
						<h3><?php _e('Editor', RBJSCSS_PLUGIN_NAME); ?></h3>
					</div>
					<div class="customjscss-editor-data">
						<pre id="customjscss-notepad" class="customjscss-notepad"></pre>
						<div class="customjscss-info">
							<span><?php echo sprintf(__('Last edited by %1$s on %2$s', RBJSCSS_PLUGIN_NAME), $author, $modified); ?></span>
							<div id="customjscss-resizable-handle" class="customjscss-resizable-handle"></div>
						</div>
					</div>
				</div>
			</div>
			<br>
			<button class="customjscss-button customjscss-button-submit" al-on.click="appData.fn.saveConfig(appData);"><?php _e('Save Changes', RBJSCSS_PLUGIN_NAME); ?></button>
		</div>
	</div>
	<!-- /end customjscss app -->
</div>
