<?php
/*
Plugin Name: LeisRef
Plugin URI: https://github.com/bireme/leisref-wp-plugin/
Description: VHL Legislation Directory WordPress plugin
Author: BIREME/OPAS/OMS
Version: 0.4
Author URI: http://reddes.bvsalud.org/
*/

define('LEISREF_VERSION', '0.4' );

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
        private $service_url = 'http://fi-admin.data.bvsalud.org/';
        private $similar_docs_url = 'http://similardocs.bireme.org/SDService';

        /**
         * Construct the plugin object
         */
        public function __construct() {
            // register actions

            add_action( 'init', array(&$this, 'load_translation'));
            add_action( 'admin_menu', array(&$this, 'admin_menu'));
            add_action( 'plugins_loaded', array(&$this, 'plugin_init'));
            add_action( 'wp_head', array(&$this, 'google_analytics_code'));
            add_action( 'template_redirect', array(&$this, 'theme_redirect'));
            add_action( 'widgets_init', array(&$this, 'register_sidebars'));
            add_action( 'after_setup_theme', array(&$this, 'title_tag_setup'));
            add_filter( 'get_search_form', array(&$this, 'search_form'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'settings_link') );
            add_filter( 'document_title_separator', array(&$this, 'title_tag_sep') );
            add_filter( 'document_title_parts', array(&$this, 'theme_slug_render_title'));
            add_filter( 'wp_title', array(&$this, 'theme_slug_render_wp_title'));

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
            global $leisref_texts;

		    // load internal plugin translations
		    load_plugin_textdomain( 'leisref', false,  LEISREF_PLUGIN_DIR . '/languages' );
            // load plugin translations
            $site_language = strtolower(get_bloginfo('language'));
            $lang = substr($site_language,0,2);

            $leisref_texts = @parse_ini_file(LEISREF_PLUGIN_PATH . "/languages/texts_" . $lang . ".ini", true);
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
		    global $wp, $leisref_service_url, $leisref_plugin_slug, $leisref_texts, $similar_docs_url;
		    $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){

                $leisref_service_url = $this->service_url;
                $leisref_plugin_slug = $this->plugin_slug;
                $similar_docs_url = $this->similar_docs_url;

                if ($pagename == $this->plugin_slug || $pagename == $this->plugin_slug . '/resource'
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

            $args2 = array(
                'name' => __('Legislation header', 'leisref'),
                'id'   => 'leisref-header',
                'description' => 'Legislation header',
                'before_widget' => '<section id="%1$s" class="row-fluid widget %2$s">',
                'after_widget'  => '</section>',
                'before_title'  => '<header class="row-fluid border-bottom marginbottom15"><h1 class="h1-header">',
                'after_title'   => '</h1></header>',
            );
            register_sidebar( $args2 );
		}

        function title_tag_sep(){
            return '|';
        }

        function theme_slug_render_title($title) {
            global $wp, $leisref_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $leisref_config = get_option('leisref_config');
                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $leisref_plugin_title = $leisref_config['plugin_title_' . $current_lang];
                }else{
                    $leisref_plugin_title = $leisref_config['plugin_title'];
                }
                $title['title'] = $leisref_plugin_title . " | " . get_bloginfo('name');
            }

            return $title;
        }

        function theme_slug_render_wp_title($title) {
            global $wp, $leisref_plugin_title;
            $pagename = '';

            // check if request contains plugin slug string
            $pos_slug = strpos($wp->request, $this->plugin_slug);
            if ( $pos_slug !== false ){
                $pagename = substr($wp->request, $pos_slug);
            }

            if ( is_404() && $pos_slug !== false ){
                $leisref_config = get_option('leisref_config');

                if ( function_exists( 'pll_the_languages' ) ) {
                    $current_lang = pll_current_language();
                    $leisref_plugin_title = $leisref_config['plugin_title_' . $current_lang];
                }else{
                    $leisref_plugin_title = $leisref_config['plugin_title'];
                }

                if ( $leisref_plugin_title )
                    $title = $leisref_plugin_title . ' | ';
                else
                    $title = '';
            }

            return $title;
        }

        function title_tag_setup() {
            add_theme_support( 'title-tag' );
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
            wp_enqueue_style('slick-css', '//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.css');
            wp_enqueue_style('slick-theme-css', '//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick-theme.css');
            wp_enqueue_style('leisref-page', LEISREF_PLUGIN_URL . 'template/css/style.css', array(), LEISREF_VERSION);
            wp_enqueue_script('slick-js', '//cdn.jsdelivr.net/gh/kenwheeler/slick@1.8.1/slick/slick.min.js');
            wp_enqueue_script('leisref-page', LEISREF_PLUGIN_URL . 'template/js/functions.js', array(), LEISREF_VERSION);
		}

		function register_settings(){
            register_setting('leisref-settings-group', 'leisref_config');
            wp_enqueue_style('oer' ,  LEISREF_PLUGIN_URL . 'template/css/admin.css');
            wp_enqueue_script('jquery-ui-sortable');
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
