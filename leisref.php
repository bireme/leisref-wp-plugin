<?php
/*
Plugin Name: LeisRef
Plugin URI: https://github.com/bireme/leisref-wp-plugin/
Description: VHL Legislation Directory WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.1
Author URI: http://reddes.bvsalud.org/
*/

define('LEISREF_VERSION', '0.1' );

define('LEISREF_SYMBOLIC_LINK', false );
define('LEISREF_PLUGIN_DIRNAME', 'leisref' );

if(LEISREF_SYMBOLIC_LINK == true) {
    define('LEISREF_PLUGIN_PATH',  ABSPATH . 'wp-content/plugins/' . LEISREF_PLUGIN_DIRNAME );
} else {
    define('LEISREF_PLUGIN_PATH',  plugin_dir_path(__FILE__) );
}

define('LEISREF_PLUGIN_DIR',   plugin_basename( LEISREF_PLUGIN_PATH ) );
define('LEISREF_PLUGIN_URL',   plugin_dir_url(__FILE__) );

require_once(LEISREF_PLUGIN_PATH . '/settings.php');
require_once(LEISREF_PLUGIN_PATH . '/template-functions.php');

if(!class_exists('LeisRef_Plugin')) {
    class LeisRef_Plugin {

        private $plugin_slug = 'leisref';
        private $service_url = 'http://fi-admin.bvsalud.org/';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            add_action( 'init', array(&$this, 'load_translation') );
            add_action( 'admin_menu', array(&$this, 'admin_menu') );
            add_action( 'plugins_loaded', array(&$this, 'plugin_init') );
            add_action( 'wp_head', array(&$this, 'google_analytics_code') );
            add_action( 'template_redirect', array(&$this, 'theme_redirect') );
            add_action( 'widgets_init', array(&$this, 'register_sidebars') );
            add_filter( 'wp_title', array(&$this, 'page_title'), 10, 2 );
            add_filter( 'get_search_form', array(&$this, 'search_form') );
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );

        } // END public function __construct

        /**
         * Activate the plugin
         */
        public static function activate()
        {
            // Do nothing
        } // END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate()
        {
            // Do nothing
        } // END public static function deactivate

        function load_translation(){
		    // Translations
		    load_plugin_textdomain( 'leisref', false,  LEISREF_PLUGIN_DIR . '/languages' );
		}

		function plugin_init() {
		    $leisref_config = get_option('leisref_config');

		    if ( $leisref_config && $leisref_config['plugin_slug'] != ''){
		        $this->plugin_slug = $leisref_config['plugin_slug'];
		    }

		}

		function admin_menu() {

		    add_submenu_page( 'options-general.php', __('Legislation Settings', 'leisref'), __('Legislation', 'leisref'), 'manage_options', 'leisref', 'leisref_page_admin');

		    //call register settings function
		    add_action( 'admin_init', array(&$this, 'register_settings') );

		}

		function theme_redirect() {
		    global $wp, $leisref_service_url, $leisref_plugin_slug;
		    $pagename = $wp->query_vars["pagename"];

		    $leisref_service_url = $this->service_url;
		    $leisref_plugin_slug = $this->plugin_slug;

		    if ($pagename == $this->plugin_slug || preg_match('/detail\//', $pagename)
		        || $pagename == $this->plugin_slug . '/legislation-feed') {

		        add_action( 'wp_enqueue_scripts', array(&$this, 'template_styles_scripts') );

		        if ($pagename == $this->plugin_slug){
		            $template = LEISREF_PLUGIN_PATH . '/template/home.php';
		        }elseif ($pagename == $this->plugin_slug . '/legislation-feed'){
		            $template = LEISREF_PLUGIN_PATH . '/template/rss.php';
		        }else{
		            $template = LEISREF_PLUGIN_PATH . '/template/detail.php';
		        }
		        // force status to 200 - OK
		        status_header(200);

		        // redirect to page and finish execution
		        include($template);
		        die();
		    }
		}

		function register_sidebars(){
		    $args = array(
		        'name' => __('Legislation sidebar', 'leisref'),
		        'id'   => 'leisref-home',
		        'description' => 'Legislation Area',
		        'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
		        'after_widget'  => '</section>',
		        'before_title'  => '<h2 class="widgettitle">',
		        'after_title'   => '</h2>',
		    );
		    register_sidebar( $args );
		}

		function page_title(){
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ( strpos($pagename, $this->plugin_slug) === 0 ) { //pagename starts with plugin slug
		        return __('Legislation', 'leisref') . ' | ';
		    }
		}

		function search_form( $form ) {
		    global $wp;
		    $pagename = $wp->query_vars["pagename"];

		    if ($pagename == $this->plugin_slug || preg_match('/detail\//', $pagename)) {
		        $form = preg_replace('/action="([^"]*)"(.*)/','action="' . home_url($this->plugin_slug) . '"',$form);
		    }

		    return $form;
		}

		function template_styles_scripts(){
		    wp_enqueue_style ('leisref-page', LEISREF_PLUGIN_URL . 'template/css/style.css');
		}

		function register_settings(){
		    register_setting('leisref-settings-group', 'leisref_config');
		}

                function settings_link($links) {
                    $settings_link = '<a href="options-general.php?page=leisref.php">Settings</a>';
                    array_unshift($links, $settings_link);
                    return $links;
                }

		function google_analytics_code(){
		    global $wp;

		    $pagename = $wp->query_vars["pagename"];
		    $plugin_config = get_option('leisref_config');

		    // check if is defined GA code and pagename starts with plugin slug
		    if ($plugin_config['google_analytics_code'] != ''
		        && strpos($pagename, $this->plugin_slug) === 0){

		?>

		<script type="text/javascript">
		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', '<?php echo $plugin_config['google_analytics_code'] ?>']);
		  _gaq.push(['_setCookiePath', '/<?php echo $plugin_config['$this->plugin_slug'] ?>']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>

		<?php
		    } //endif
		}

	}
}

if(class_exists('LeisRef_Plugin'))
{
    // Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('LeisRef_Plugin', 'activate'));
    register_deactivation_hook(__FILE__, array('LeisRef_Plugin', 'deactivate'));

    // Instantiate the plugin class
    $wp_plugin_template = new LeisRef_Plugin();
}

?>
