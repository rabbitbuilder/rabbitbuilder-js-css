<?php
/**
 * Main class and entry point
 */

 // prevent direct access
 defined( 'ABSPATH' ) or die( 'Hey, you can\t access this file, you silly human!' );



if(!class_exists('RabbitBuilderJsCss')) :

class RabbitBuilderJsCss {
	private $pluginBasename = NULL;

	private $ajax_action_item_update = NULL;
	private $ajax_action_item_update_status = NULL;
	private $ajax_action_filter_update = NULL;
	private $ajax_action_settings_update = NULL;
	private $ajax_action_settings_get = NULL;
	private $ajax_action_delete_data = NULL;

	private $css_admin_header_data = array();
	private $css_admin_footer_data = array();
	private $css_header_data = array();
	private $css_footer_data = array();

	private $js_admin_header_data = array();
	private $js_admin_footer_data = array();
	private $js_header_data = array();
	private $js_footer_data = array();

	private $html_admin_header_data = array();
	private $html_admin_footer_data = array();
	private $html_header_data = array();
	private $html_footer_data = array();

	public function __construct($pluginBasename) {
		$this->pluginBasename = $pluginBasename;
	}

	public function run() { //function run starts

		$upload_dir = wp_upload_dir();

		define( 'RBJSCSS_PLUGIN_UPLOAD_DIR', wp_normalize_path($upload_dir['basedir'] . '/' . RBJSCSS_PLUGIN_NAME ) );
		define( 'RBJSCSS_PLUGIN_UPLOAD_URL', $upload_dir['baseurl'] . '/' . RBJSCSS_PLUGIN_NAME );

		define('RBJSCSS_PLUGIN_PLAN', 'pro');


		if ( is_admin() ) {
			$this->ajax_action_filter_update = RBJSCSS_PLUGIN_NAME . '_ajax_filter_update';
			$this->ajax_action_filter_get = RBJSCSS_PLUGIN_NAME . '_ajax_filter_get';
			$this->ajax_action_item_update = RBJSCSS_PLUGIN_NAME . '_ajax_item_update';
			$this->ajax_action_item_update_status = RBJSCSS_PLUGIN_NAME . '_ajax_item_update_status';
			$this->ajax_action_settings_update = RBJSCSS_PLUGIN_NAME . '_ajax_settings_update';
			$this->ajax_action_settings_get = RBJSCSS_PLUGIN_NAME . '_ajax_settings_get';
			$this->ajax_action_delete_data =  RBJSCSS_PLUGIN_NAME . '_ajax_delete_data';

			//load_plugin_textdomain( RBJSCSS_PLUGIN_NAME, false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/' );

			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );

			// important, because ajax has another url
			add_action( 'wp_ajax_' . $this->ajax_action_item_update, array( $this, 'ajax_item_update' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_item_update_status, array( $this, 'ajax_item_update_status' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_filter_update, array( $this, 'ajax_filter_update' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_filter_get, array( $this, 'ajax_filter_get' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_settings_update, array( $this, 'ajax_settings_update' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_settings_get, array( $this, 'ajax_settings_get' ) );
			add_action( 'wp_ajax_' . $this->ajax_action_delete_data, array( $this, 'ajax_delete_data' ) );

			$this->prepare_code();

		} else { //if not admin
			add_action( 'wp', array( $this, 'prepare_code' ) );
		}


	} //function run ends





	/**
	 * Prepare upload directory
	 */
	function admin_notices() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if(!( $page === RBJSCSS_PLUGIN_NAME ||
			 $page === RBJSCSS_PLUGIN_NAME . '_filters' ||
			 $page === RBJSCSS_PLUGIN_NAME . '_settings')) {
				 return;
		}

		if( !file_exists( RBJSCSS_PLUGIN_UPLOAD_DIR ) ) {
			wp_mkdir_p( RBJSCSS_PLUGIN_UPLOAD_DIR );
		}

		if( !file_exists( RBJSCSS_PLUGIN_UPLOAD_DIR ) ) {
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . sprintf( __( 'The "%s" directory could not be created', RBJSCSS_PLUGIN_NAME ), '<b>' . RBJSCSS_PLUGIN_NAME . '</b>' ) . '</p>';
			echo '<p>' . __( 'Please run the following commands in order to make the directory', RBJSCSS_PLUGIN_NAME ) . '<br>';
			echo '<b>mkdir ' . RBJSCSS_PLUGIN_UPLOAD_DIR . '</b><br>';
			echo '<b>chmod 777 ' . RBJSCSS_PLUGIN_UPLOAD_DIR . '</b></p>';
			echo '</div>';
			return;
		}

		if( !wp_is_writable( RBJSCSS_PLUGIN_UPLOAD_DIR ) ) {
			echo '<div class="notice notice-error is-dismissible">';
			echo '<p>' . sprintf( __( 'The "%s" directory is not writable, therefore the css and js files cannot be saved.', RBJSCSS_PLUGIN_NAME ), '<b>' . RBJSCSS_PLUGIN_NAME . '</b>') . '</p>';
			echo '<p>' . __( 'Please run the following commands in order to make the directory', RBJSCSS_PLUGIN_NAME ) . '<br>';
			echo '<b>chmod 777 ' . RBJSCSS_PLUGIN_UPLOAD_DIR . '</b></p>';
			echo '</div>';
			return;
		}

		if(!file_exists( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . 'index.php' ) ) {
			$data = '<?php' . PHP_EOL . '// silence is golden' . PHP_EOL . '?>';
			@file_put_contents( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . 'index.php', $data );
		}
	}
















	/**
	 * Prepare JS, CSS or HTML code
	 */
	function prepare_code() {

		global $wpdb;
		$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;

		$query = 'SELECT `id`,`type`,`modified`,`options` FROM ' . $table . ' WHERE active ORDER BY type, priority';
		$items = $wpdb->get_results( $query);

		if ( empty($items) ){
			return false;
		}


		foreach( $items as $key => $item) {

			$id = $item->id;
			$version = strtotime( mysql2date('d M Y H:i:s', $item->modified ));
			$options = unserialize($item->options);

			// validate filter if it sets
			if($options->filter && !$this->validate_filter($options->filter)) {
				continue;
			}

			if($item->type == 'css') {
				if($options->whereonpage == 'header') {
					switch($options->whereinsite) {
						case 'user': $this->css_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->css_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->css_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->css_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				} if($options->whereonpage == 'footer') {
					switch($options->whereinsite) {
						case 'user': $this->css_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->css_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->css_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->css_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				}
			} else if($item->type == 'js') {
				if($options->whereonpage == 'header') {
					switch($options->whereinsite) {
						case 'user': $this->js_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->js_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->js_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->js_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				} if($options->whereonpage == 'footer') {
					switch($options->whereinsite) {
						case 'user': $this->js_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->js_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->js_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->js_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				}
			} else if($item->type == 'html') {
				if($options->whereonpage == 'header') {
					switch($options->whereinsite) {
						case 'user': $this->html_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->html_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->html_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->html_admin_header_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				} if($options->whereonpage == 'footer') {
					switch($options->whereinsite) {
						case 'user': $this->html_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'admin': $this->html_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options); break;
						case 'both': {
							$this->html_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
							$this->html_admin_footer_data[] = array('id' => $id, 'version' => $version, 'options' => $options);
						}
						break;
					}
				}
			}
		}

		add_action( 'admin_head', array( $this, 'print_admin_header' ) );
		add_action( 'admin_footer', array( $this, 'print_admin_footer' )) ;
		add_action( 'wp_head', array( $this, 'print_header' ) );
		add_action( 'wp_footer', array( $this, 'print_footer' ) );

	}


  function checkAndReGenerateFileIfNotFound( $file_name, $itemId, $options, $version = null ){ //function checkAndReGenerateFileIfNotFound starts

    global $wp_filesystem;
    require_once ( ABSPATH . '/wp-admin/includes/file.php' );
    WP_Filesystem();

    if ( !$wp_filesystem->exists( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name ) ) {

      global $wpdb;
  		$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;

  		$query = 'SELECT `data` FROM ' . $table . ' WHERE id =  '.$itemId;
  		$file_data = $wpdb->get_var( $query);

      //now we need to see if elementor plugin is installed and if yes we need to replace the text
      require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/elementor_functions.php' );
      $file_data = RabbitBuilderJsCss_Replace_Ele_Key_Text( $file_data );

      if( $options->preprocessor == 'scss' ){

        require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/lib/scssphp/scss.inc.php' );
        $scss = new Leafo\ScssPhp\Compiler;

        try {
          $scss->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
          $file_data = $scss->compile($file_data);
        } catch (exception $ex) {
          $error = true;
          $data['msg'] = __('Can\'t compile SCSS data.<br>Message: ', RBJSCSS_PLUGIN_NAME);
          $data['msg'] = $data['msg'] . $ex->getMessage();
        }

      }
      //now save the file
      wp_mkdir_p( RBJSCSS_PLUGIN_UPLOAD_DIR ); //create the dir
      file_put_contents( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name, $file_data );

      //now finally update the table
      $currentTime = current_time( 'timestamp', 1 );
      $currentTimemMySql = current_time( 'mysql', 1 );
		  $wpdb->update( $table, array( 'modified' => $currentTimemMySql ), array( 'id' => $itemId) );

      return $currentTime;
    }

    return $version;

  } //function checkAndReGenerateFileIfNotFound ends




	function print_code( $css_data, $js_data, $html_data ) {

		$begin = '<!-- ' . RBJSCSS_PLUGIN_NAME . ' begin -->' . PHP_EOL;
		$end = '<!-- ' . RBJSCSS_PLUGIN_NAME . ' end -->' . PHP_EOL;

		// CSS section
		$before = $begin . '<style type="text/css">' . PHP_EOL;
		$after = PHP_EOL . '</style>' . PHP_EOL . $end;

		foreach($css_data as $key => $item) {
			$file_name = $item['id'] . '.css';
			$options = $item['options'];


			//invokers edit
      $item['version'] = $this->checkAndReGenerateFileIfNotFound( $file_name, $item['id'], $options, $item['version'] );

			if($options->file == 'internal') {
				echo $before;
				include_once(RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name);
				echo $after;
			} else if($options->file == 'external') {
				echo '<link rel="stylesheet" href="' . RBJSCSS_PLUGIN_UPLOAD_URL . '/' . $file_name . '?v=' . $item['version'] . '" type="text/css" media="all" />' . PHP_EOL;
			}
		}

		// JS section
		$before = $begin . '<script type="text/javascript">' . PHP_EOL;
		$after = PHP_EOL . '</script>' . PHP_EOL . $end;

		foreach($js_data as $key => $item) {
			$file_name = $item['id'] . '.js';
			$options = $item['options'];

      //invokers edit
      $item['version'] = $this->checkAndReGenerateFileIfNotFound( $file_name, $item['id'], $options, $item['version'] );

			if($options->file == 'internal') {
				echo $before;
				include_once(RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name);
				echo $after;
			} else if($options->file == 'external') {
				echo '<script src="' . RBJSCSS_PLUGIN_UPLOAD_URL . '/' . $file_name . '?v=' . $item['version'] . '" type="text/javascript"></script>' . PHP_EOL;
			}
		}

		// HTML section
		$before = $begin . PHP_EOL;
		$after = PHP_EOL . $end;

		foreach($html_data as $key => $item) {
			$file_name = $item['id'] . '.html';
			$options = $item['options'];

      //invokers edit
      $item['version'] = $this->checkAndReGenerateFileIfNotFound( $file_name, $item['id'], $options );

			if($options->file == 'internal') {
				echo $before;
				include_once(RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name);
				echo $after;
			}
		}
	}

	function print_admin_header() {
		$this->print_code($this->css_admin_header_data, $this->js_admin_header_data, $this->html_admin_header_data);
	}

	function print_admin_footer() {
		$this->print_code($this->css_admin_footer_data, $this->js_admin_footer_data, $this->html_admin_footer_data);
	}

	function print_header() {
		$this->print_code($this->css_header_data, $this->js_header_data, $this->html_header_data);
	}

	function print_footer() {
		$this->print_code($this->css_footer_data, $this->js_footer_data, $this->html_footer_data);
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	function admin_menu() {
		// add "edit_posts" if we want to give access to author, editor and contributor roles
		add_menu_page( __( 'RabbitBuilder Global Central JS CSS', RBJSCSS_PLUGIN_NAME), __( 'RabbitBuilder JS/CSS', RBJSCSS_PLUGIN_NAME), 'edit_posts', RBJSCSS_PLUGIN_NAME, array( $this, 'admin_menu_page_items' ), 'dashicons-admin-appearance');
		//add_submenu_page( RBJSCSS_PLUGIN_NAME, __('RabbitBuilder Global Central JS CSS', RBJSCSS_PLUGIN_NAME), __( 'All Items', RBJSCSS_PLUGIN_NAME), 'edit_posts', RBJSCSS_PLUGIN_NAME, array( $this, 'admin_menu_page_items' ));
		//add_submenu_page( RBJSCSS_PLUGIN_NAME, __('RabbitBuilder Global Central JS CSS - Filters', RBJSCSS_PLUGIN_NAME), __( 'All filters', RBJSCSS_PLUGIN_NAME), 'edit_posts', RBJSCSS_PLUGIN_NAME . '_filters', array( $this, 'admin_menu_page_filters' ));
		//add_submenu_page( RBJSCSS_PLUGIN_NAME, __('RabbitBuilder Global Central JS CSS - Settings', RBJSCSS_PLUGIN_NAME), __( 'Settings', RBJSCSS_PLUGIN_NAME), 'edit_posts', RBJSCSS_PLUGIN_NAME . '_settings', array( $this, 'admin_menu_page_settings' ));
	}



	/**
	 * Show admin menu items page
	 */
	function admin_menu_page_items() {
		$page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );

		if( $page === RBJSCSS_PLUGIN_NAME ) {
			$plugin_url = plugin_dir_url( dirname(__FILE__) );

			// styles
			wp_enqueue_style( RBJSCSS_PLUGIN_NAME . '_admin_css', $plugin_url . 'assets/css/admin.min.css', array(), RBJSCSS_PLUGIN_VERSION, 'all' );
			wp_enqueue_style( RBJSCSS_PLUGIN_NAME . '_customjscssicons_css', $plugin_url . 'assets/css/customjscssicons.min.css', array(), RBJSCSS_PLUGIN_VERSION, 'all' );

			// scripts
			wp_enqueue_script( RBJSCSS_PLUGIN_NAME . '_admin_js', $plugin_url . 'assets/js/admin.min.js', array('jquery'), RBJSCSS_PLUGIN_VERSION, false );
			wp_enqueue_script( RBJSCSS_PLUGIN_NAME . '_ace', $plugin_url . 'assets/js/lib/ace/ace.js', array(), RBJSCSS_PLUGIN_VERSION, false );



			// global settings to help ajax work
			$globals = array(
				'plan' => RBJSCSS_PLUGIN_PLAN,
				'msg_pro_title' => __( 'Available only in Pro version', RBJSCSS_PLUGIN_NAME ),
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => wp_create_nonce( RBJSCSS_PLUGIN_NAME . '_ajax' ),
				'ajax_msg_error' => __( 'Uncaught Error', RBJSCSS_PLUGIN_NAME ) //Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information
			);

			$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );
			$type = filter_input( INPUT_GET, 'type', FILTER_SANITIZE_STRING );
			$action = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING );

			if( $action == 'new' || $action == 'edit' ) {

				$globals['ajax_action_update'] = $this->ajax_action_item_update;
				$globals['ajax_action_get'] = $this->ajax_action_filter_get;
				$globals['ajax_id'] = $id;
				$globals['ajax_type'] = $type;
				$globals['settings'] = NULL;
				$globals['config'] = NULL;


				$settings_key = RBJSCSS_PLUGIN_NAME . '_settings';
				$settings_value = get_option($settings_key);
				if( $settings_value ) {
					$globals['settings'] = json_encode(unserialize($settings_value));
				}


				// get item data from DB
				if( $id ) {
					global $wpdb;
					$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;

					$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $id);
					$item = $wpdb->get_row($query, OBJECT);
					if($item) {
						//{
						//id: null,
						//title: null,
						//data: null,
						//type: null,
						//active: true,
						//options: {...}
						//}
						$globals['config'] = json_encode( array(
							'title' => $item->title,
							'data' => $item->data,
							'type' => $item->type,
							'active' => ($item->active ? true : false),
							'options' => unserialize($item->options)
						));
					}
				} else {
					// new item
					$item = ( object ) array(
						'author' => get_current_user_id(),
						'date' => current_time( 'mysql', 1 ),
						'modified' => current_time( 'mysql', 1 )
					);
				}

				require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/page-item.php' );

			} else {

				$nonce = filter_input( INPUT_GET, '_wpnonce', FILTER_SANITIZE_STRING );

				if($action && $nonce && wp_verify_nonce($nonce, RBJSCSS_PLUGIN_NAME)) {
					global $wpdb;
					$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;

					if( $action == 'duplicate' ) {
						$result = false;

						$query = $wpdb->prepare( 'SELECT * FROM ' . $table . ' WHERE id=%s', $id);
						$item = $wpdb->get_row( $query, OBJECT );

						if( $item && (current_user_can('edit_posts') || get_current_user_id() == $item->author ) ) {

							$result = $wpdb->insert(
								$table,
								array(
									'title' => __('[Duplicate] ', RBJSCSS_PLUGIN_NAME) . $item->title,
									'data' => $item->data,
									'type' => $item->type,
									'active' => $item->active,
									'options' => $item->options,
									'author' => get_current_user_id(),
									'date' => current_time('mysql', 1),
									'modified' => current_time('mysql', 1)
							));

							//======================================
							// [filemanager] create an external file
							if( $result && wp_is_writable(RBJSCSS_PLUGIN_UPLOAD_DIR ) ) {
								$file_name_src = $item->id . '.' . $item->type;
								$file_name_dst = $wpdb->insert_id . '.' . $item->type;
								copy( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name_src, RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name_dst );
							}
							//======================================
						}
					}

					if( $action == 'delete' ) {
						$result = false;

						$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $id);
						$item = $wpdb->get_row($query, OBJECT);
						if($item && (current_user_can('edit_posts') || get_current_user_id()==$item->author) ) {
							$result = $wpdb->delete( $table, ['id'=>$id], ['%d']);

							//======================================
							// [filemanager] delete file
							$file_name = $item->id . '.' . $item->type;
							wp_delete_file( RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name );
							//======================================
						}
					}
				}

				$globals['ajax_action_update'] = $this->ajax_action_item_update_status;

				require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/list-table-items.php' );
				require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/page-items.php' );

			}

			// set global settings
			wp_localize_script( RBJSCSS_PLUGIN_NAME . '_admin_js', RBJSCSS_PLUGIN_NAME, $globals );
		}
	}





	/**
	 * Ajax update item state
	 */
	function ajax_item_update_status() {
		$error = false;
		$data = array();
		$config = filter_input( INPUT_POST, 'config', FILTER_UNSAFE_RAW );

		if( check_ajax_referer( RBJSCSS_PLUGIN_NAME . '_ajax', 'nonce', false ) ) {
			global $wpdb;

			$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;
			$config = json_decode($config);
			$result = false;

			if(isset($config->id) && isset($config->active)) {
				$query = $wpdb->prepare('SELECT * FROM ' . $table . ' WHERE id=%s', $config->id);
				$item = $wpdb->get_row($query, OBJECT );
				if($item && (current_user_can('edit_posts') || get_current_user_id()==$item->author) ) {
					$result = $wpdb->update(
						$table,
						array('active'=>$config->active),
						array('id'=>$config->id));
				}
			}

			if($result) {
				$data['id'] = $config->id;
				$data['msg'] = __( 'Item updated', RBJSCSS_PLUGIN_NAME );
			} else {
				$error = true;
				$data['msg'] = __( 'The operation failed, can\'t update item', RBJSCSS_PLUGIN_NAME );
			}
		} else {
			$error = true;
			$data['msg'] = __( 'The operation failed', RBJSCSS_PLUGIN_NAME );
		}

		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}

	/**
	 * Ajax update item data
	 */
	function ajax_item_update() { //function ajax_item_update starts

		$error = false;
		$data = array();
		$config = filter_input(INPUT_POST, 'config', FILTER_UNSAFE_RAW);

		if( check_ajax_referer( RBJSCSS_PLUGIN_NAME . '_ajax', 'nonce', false ) ) {

			global $wpdb;

			$table = $wpdb->prefix . RBJSCSS_PLUGIN_NAME;
			$config = json_decode($config);

			if( isset($config->id) ) {
				$result = false;

				$query = $wpdb->prepare( 'SELECT * FROM ' . $table . ' WHERE id=%s', $config->id);
				$item = $wpdb->get_row($query, OBJECT);
				if($item && (current_user_can('edit_posts') || get_current_user_id()==$item->author) ) {
					$result = $wpdb->update(
						$table,
						array(
							'title' => $config->title,
							'data' => $config->data,
							'type' => $config->type,
							'active' => $config->active,
							'options' => serialize($config->options),
							'author' => get_current_user_id(),
							//'date' => NULL,
							'modified' => current_time('mysql', 1)
						),
						array('id'=>$config->id));
				}

				if($result) {
					$data['id'] = $config->id;
					$data['msg'] = __('Item updated', RBJSCSS_PLUGIN_NAME);
				} else {
					$error = true;
					$data['msg'] = __('The operation failed, can\'t update item', RBJSCSS_PLUGIN_NAME);
				}
			} else {
				$result = $wpdb->insert(
					$table,
					array(
						'title' => $config->title,
						'data' => $config->data,
						'type' => $config->type,
						'active' => $config->active,
						'options' => serialize($config->options),
						'author' => get_current_user_id(),
						'date' => current_time('mysql', 1),
						'modified' => current_time('mysql', 1)
					));

				if($result) {
					$data['id'] = $config->id = $wpdb->insert_id;
					$data['msg'] = __('Item created', RBJSCSS_PLUGIN_NAME);
				} else {
					$error = true;
					$data['msg'] = __('The operation failed, can\'t create item', RBJSCSS_PLUGIN_NAME);
				}
			}

			//======================================
			// [filemanager] create an external file
			if( !$error && wp_is_writable( RBJSCSS_PLUGIN_UPLOAD_DIR) ) {


        //now we need to see if elementor plugin is installed and if yes we need to replace the text
  			require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/elementor_functions.php' );
  			$config->data = RabbitBuilderJsCss_Replace_Ele_Key_Text($config->data);

				$file_name = $config->id . '.' . $config->type;
				$file_path = RBJSCSS_PLUGIN_UPLOAD_DIR . '/' . $file_name;
				$file_data = $config->data;


				if( $config->type == 'css' ) {

					if($config->options->preprocessor == 'less') {

						$error = true;
						$data['msg'] = __( 'Less Preprocessor is not yet supported', RBJSCSS_PLUGIN_NAME );


					} else if($config->options->preprocessor == 'scss') {

						require_once( plugin_dir_path( dirname(__FILE__) ) . 'inc/lib/scssphp/scss.inc.php' );
						$scss = new Leafo\ScssPhp\Compiler;

						try {
							$scss->setFormatter('Leafo\ScssPhp\Formatter\Expanded');
							$file_data = $scss->compile($config->data);
						} catch (exception $ex) {
							$error = true;
							$data['msg'] = __('Can\'t compile SCSS data.<br>Message: ', RBJSCSS_PLUGIN_NAME);
							$data['msg'] = $data['msg'] . $ex->getMessage();
						}

					}
				}

        if(!$error) {
					@file_put_contents($file_path, $file_data); //save the file
        }

				/* disable minification
				if(!$error) {
					@file_put_contents($file_path, $file_data);


					if($config->options->minify) {

						require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/Minify.php' );
						require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/Exception.php' );

						$minifier = null;
						if($config->type == 'css') {
							require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/ConverterInterface.php' );
							require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/Converter.php' );
							require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/CSS.php' );
							$minifier = new MatthiasMullie\Minify\CSS;
						} else if($config->type == 'js') {
							require_once( plugin_dir_path( dirname(__FILE__) ) . 'includes/lib/minify/src/JS.php' );
							$minifier = new MatthiasMullie\Minify\JS;
						}

						if($minifier) {
							$minifier->add($file_path);
							$minifier->minify($file_path);
						}
					}
				}
				*/



			}
			//======================================
		} else {
			$error = true;
			$data['msg'] = __('The operation failed', RBJSCSS_PLUGIN_NAME);
		}

		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}

		wp_die(); // this is required to terminate immediately and return a proper response

	} //function ajax_item_update ends



	/**
	 * Ajax filter get data
	 */
	function ajax_filter_get() {
		$error = false;
		$data = array();
		$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

		if(check_ajax_referer(RBJSCSS_PLUGIN_NAME . '_ajax', 'nonce', false)) {
			switch($type) {
				case 'filters': {
				}
				break;

				default: {
					$error = true;
					$data['msg'] = __('The operation failed', RBJSCSS_PLUGIN_NAME);
				}
				break;
			}
		} else {
			$error = true;
			$data['msg'] = __('The operation failed', RBJSCSS_PLUGIN_NAME);
		}

		if($error) {
			wp_send_json_error($data);
		} else {
			wp_send_json_success($data);
		}

		wp_die(); // this is required to terminate immediately and return a proper response
	}


}

endif;

?>
