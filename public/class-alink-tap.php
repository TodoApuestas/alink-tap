<?php
/**
 * Alink Tap.
 *
 * @package   Alink_Tap
 * @author    Alain Sanchez <luka.ghost@gmail.com>
 * @license   GPL-2.0+
 * @link      http://www.linkedin.com/in/mrbrazzi/
 * @copyright 2014 Alain Sanchez
 */

/**
 * Plugin class. This class should ideally be used to work with the
 * public-facing side of the WordPress site.
 *
 * If you're interested in introducing administrative or dashboard
 * functionality, then refer to `class-alink-tap-admin.php`
 *
 * @package Alink_Tap
 * @author  Alain Sanchez <luka.ghost@gmail.com>
 */
class Alink_Tap {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.1.0.1
	 *
	 * @var     string
	 */
	const VERSION = '1.1.3';

	/**
	 *
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'alink-tap';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

    private $default_options;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
//		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        /**
         * Define the default options
         *
         * @since     1.0.1
         */
        $this->default_options = array(
            'domain' => $_SERVER["HTTP_HOST"],
            'url_sync_link' => 'http://www.todoapuestas.org/tdapuestas/web/api/blocks-bookies/%s/%s/listado-bonos-bookies.json/?access_token=%s&_=%s',
            'url_get_country_from_ip' => 'http://www.todoapuestas.org/tdapuestas/web/api/geoip/country-by-ip.json/%s/?access_token=%s&_=%s',
            'plurals' => 1,
            'tracked_web_category' => 'apuestas'
        );

		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 *
		 * add_action ( 'hook_name', 'your_function_name', [priority], [accepted_args] );
		 *
		 * add_filter ( 'hook_name', 'your_filter', [priority], [accepted_args] );
		 */
        add_action( 'sync_hourly_event', array( $this, 'remote_sync' ) );
        add_action( 'alink_tap_remote_sync', array( $this, 'remote_sync' ) );
        add_action( 'wp' , array( $this, 'active_remote_sync'));

        add_filter( 'the_content', array( $this, 'execute_linker' ), 9 );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();

					restore_current_blog();
				}

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

					restore_current_blog();

				}

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.1
	 */
	private static function single_activate() {
		add_option('alink_tap_linker_remote_info', self::get_instance()->default_options);
        add_option('alink_tap_linker_remote', null);

        // execute initial synchronization
        self::get_instance()->remote_sync();
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		delete_option('alink_tap_linker_remote_info');
        delete_option('alink_tap_linker_remote');

        remove_action( 'sync_hourly_event', array( self::$instance, 'remote_sync' ) );
        remove_action( 'wp' , array( self::$instance, 'active_remote_sync'));

        remove_filter( 'the_content', array( self::$instance, 'execute_linker' ) );
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

    /**
     *
     * @since   1.0.1
     */
    public function active_remote_sync() {
        if ( !wp_next_scheduled( 'sync_hourly_event' ) ) {
            wp_schedule_event(time(), 'hourly', 'sync_hourly_event');
        }
    }

    /**
     * Execute synchronizations from todoapuestas.org server
     *
     * @since   1.0.1
     * @updated 1.1.0.0
     * @param string $d
     * @return array|void
     * @throws \Exception
     */
    public function remote_sync($d = null) {
        // do something every hour
        $option = get_option('alink_tap_linker_remote_info', $this->default_options);

        $oauthAccessToken = $this->get_oauth_access_token();

        $timestamp = new DateTime("now");

        $domain = $d;
        if(is_null($d) && empty($option['domain']))
            $domain = $_SERVER["HTTP_HOST"];
        else if(!empty($option['domain']))
            $domain = $option['domain'];

//        $remote_plurals = $option['plurals'];

        if (preg_match('/\\b(https?|ftp):\/\/www\.(?P<domain>[-A-Z0-9.]+)\\z/i', $domain, $regs)) {
            $domain = $regs['domain'];
        }

        // Get values from TAP
        $apiUrl = esc_url(sprintf($option['url_sync_link'], $option['tracked_web_category'], $domain, $oauthAccessToken, $timestamp->getTimestamp()));
        $apiResponse = wp_remote_get($apiUrl);
        if(strcmp($apiResponse['response']['code'], '200') != 0 || $apiResponse instanceof WP_Error){
            throw new Exception('Invalid API response');
        }
        $list_site_links = json_decode($apiResponse['body'], true);
        $atApiUrl = null;
        switch(preg_match('/^w{3}./', $domain)){
            case 1:
                $atApiUrl = esc_url(sprintf($option['url_sync_link'], $option['tracked_web_category'], preg_replace('/^w{3}./','',$domain), $oauthAccessToken, $timestamp->getTimestamp()));
                break;
            default:
                $atApiUrl = esc_url(sprintf($option['url_sync_link'], $option['tracked_web_category'], $domain, $oauthAccessToken, $timestamp->getTimestamp()));
                break;
        }
        $atLinks = array();
        if(strcmp($apiUrl, $atApiUrl) != 0){
            $apiResponse = wp_remote_get($atApiUrl);
            if(strcmp($apiResponse['response']['code'], '200') != 0){
                throw new Exception('Invalid API response');
            }
            $atLinks = json_decode($apiResponse['body'], true);
        }

        $list_site_links = array_merge($list_site_links, $atLinks);

        if(is_null($d) && !empty($list_site_links)){
            update_option('alink_tap_linker_remote', $list_site_links);
        }

        if(!is_null($d)){
            return $list_site_links;
        }
    }

    /**
     * Filter the content and change de occurrences of keyword to links
     *
     * @since   1.0.1
     * @updated 1.1.0.1
     * @param $content
     * @return string
     */
    public function execute_linker($content) {
        global $alink_tap_special_chars,$alink_tap_title_text;
        $pairs = $text = $plurals = $licencias = null;

        ini_set('memory_limit', -1);

        $list_site_links = get_option('alink_tap_linker_remote');
        if ( empty($list_site_links))
            return $content;

        // let's make use of that special chars setting.
        if (is_array($alink_tap_special_chars)){
            foreach ($alink_tap_special_chars as $char => $code){
                $content = str_replace($code,$char,$content);
            }
        }

        // needed below...
        $remote_info = get_option('alink_tap_linker_remote_info', $this->default_options);
        $plurals = $remote_info['plurals'];

        $usedUrls = array();

        $currentUrl = site_url(); // may not work on all hosting setups.

        $country = $this->get_country_by_ip($remote_info);

//        $url_get_country_from_ip = esc_url($remote_info['url_get_country_from_ip']);
//        $userUrl = $url_get_country_from_ip."?ip=". $ip;
//        $country = trim(file_get_contents($userUrl));

//        foreach ($pairs as $keyword => $url_array){
        foreach ( $list_site_links as $house ){
            $keyword = '';
            if(isset($house['nombre']))
                $keyword = $house['nombre'];

            // Compruebo si es un usuario de Spain. Si lo es, compruebo si la key es con licencia_esp, si no, paso al siguiente

            if(null != $country && isset($country['country']) && strcmp($country['country'],'Spain') === 0 && !empty($house)){
                $url = $house['urles'];
                if(!$house['licencia']) continue;
            } else {
                $url = $house['url'];
            }

//            if (in_array( $url, $usedUrls )) // don't link to the same URL more than once
//                continue;
//
//            if (strpos( $content, $url )){ // we've already used this URL, or it was manually inserted by author into post
//                $usedUrls[] = $url;
//                continue;
//            }

            if ($url == $currentUrl){ // don't link a page to itself
                $usedUrls[] = $url;
                continue;
            }

            // first, let's check whether we've got a "target" attribute specified.

            if (false!==strpos( $url, ' ' ) ){	// Let's not waste CPU resources unless we see a ' ' in the URL:
                $target = trim(   substr( $url, strpos($url,' ') )   );
                $target = ' target="'.$target.'"';
                $url = substr( $url, 0, strpos($url,' ') );
            }else{
                $target='';
            }

            // let's escape any '&' in the URL.

            $url = str_replace( '&amp;', '&', $url ); // this might seem unnecessary, but it prevents the next line from double-escaping the &

            $url = str_replace( '&', '&amp;', $url );


            // we don't want to link the keyword if it is already linked.

            // so let's find all instances where the keyword is in a link and precede it with &&&, which will be sufficient to avoid linking it. We use &&&, since WP would pass that

            // to us as &amp;&amp;&amp; (if it occured in a post), so it would never be in the $content on its own.

            // this has two steps. First, look for the keyword as linked text:

            $content = preg_replace( '|(<a[^>]+>)(.*)('.$keyword.')(.*)(</a[^>]*>)|Ui', '$1$2&&&$3$4$5', $content);



            // Next, look for the keyword inside tags. E.g. if they're linking every occurrence of "Google" manually, we don't want to find

            // <a href="http://google.com"> and change it to <a href="http://<a href="http://www.google.com">.com">

            // More broadly, we don't want them linking anything that might be in a tag. (e.g. linking "strong" would screw up <strong>).

            // if you get problems with KB linker creating links where it shouldn't, this is the regex you should tinker with, most likely. Here goes:

            $content = preg_replace( '|(<[^>]*)('.$keyword.')(.*>)|Ui', '$1&&&$2$3', $content);


            // I'm sure a true master of regular expressions wouldn't need the previous two steps, and would simply write the replacement expression (below) better. But this works for me.

            // set the title attribute:

            if (ALINK_TAP_USE_TITLES)
                $title = ' title="'.$alink_tap_title_text['before'].$alink_tap_title_text['after'].'"';

            // now that we've taken the keyword out of any links it appears in, let's look for the keyword elsewhere.

            if ( 1 != $plurals ){	 // we do basically the same thing whether we're looking for plurals or not. Let's do non-plurals option first:

                $content = preg_replace( '|(?<=[\s>;\'])('.$keyword.')(?=[\s<&,!\';:\./])|i', '<a href="'.$url.'" class="alink-tp"'.$target.$title.' rel="nofollow">$1</a>', $content/*, 1*/);	// that "1" at the end limits it to replacing the keyword only once per post => We quit this in TAP!!!!!

                /* some notes about that regular expression to make modifying it easier for you if you're new to these things:

                (?<=[\s>;"\'])

                    (?<=	marks it as a lookbehind assertion

                    to ensure that we are linking only complete words, we want keyword preceded by one of space, tag (>), entity (;) or certain kinds of punctuation (escaped with \ when necessary)

                    Note that '&' is NOT one of the allowed lookbehinds (or our '&&&' trick wouldn't work)

                (?=[\s<&.,\'";:\-])

                    (?=	marks this as a lookahead assertion

                    again, we link only complete words. Must be followed by space, tag (<), entity (&), or certain kinds of punctuation.

                    Note that some of the punctuations are escaped with \

                */



            }else{	// if they want us to look for plurals too:

                // this regex is almost identical to the non-plurals one, we just add an s? where necessary:

                $content = preg_replace( '|(?<=[\s>;\'])('.$keyword.'s?)(?=[\s<&,!\';:\./])|i', '<a href="'.$url.'" class="alink-tp"'.$target.$title.' rel="nofollow">$1</a>', $content/*, 1*/);	// that "1" at the end limits it to replacing once per post.

            }

        }

        // get rid of our '&&&' things.
        $content = str_replace( '&&&', '', $content);

        ini_restore('memory_limit');

        return $content;
    }

    public function get_default_options() {
        return $this->default_options;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function get_oauth_access_token()
    {
        session_start();
        if(isset($_SESSION['TAP_OAUTH_CLIENT'])){
            $now = new DateTime('now');
            if($now->getTimestamp() <= intval($_SESSION['TAP_OAUTH_CLIENT']['expires_in'])){
                $oauthAccessToken = $_SESSION['TAP_OAUTH_CLIENT']['access_token'];
                return $oauthAccessToken;
            }
            unset($_SESSION['TAP_OAUTH_CLIENT']);
        }

        $oauthUrl = get_option('TAP_OAUTH_CLIENT_CREDENTIALS_URL');
        $publicId = get_option('TAP_PUBLIC_ID');
        $secretKey = get_option('TAP_SECRET_KEY');
        if(empty($publicId) || empty($secretKey)){
            throw new Exception('No public or secret key given');
        }

        $oauthUrl = sprintf($oauthUrl, $publicId, $secretKey);
        $oauthResponse = wp_remote_get($oauthUrl);
        if($oauthResponse instanceof WP_Error || strcmp($oauthResponse['response']['code'], '200') !== 0){
            throw new \Exception('Invalid OAuth response');
        }

        $oauthResponseBody = json_decode($oauthResponse['body']);
        $oauthAccessToken = null;
        if($oauthResponseBody instanceof WP_Error || !is_object($oauthResponseBody)){
            throw new \Exception('Invalid OAuth access token');
        }
        $oauthAccessToken = $oauthResponseBody->access_token;

        if(!isset($_SESSION['TAP_OAUTH_CLIENT'])){
            $now = new DateTime('now');
            $_SESSION['TAP_OAUTH_CLIENT'] = array(
                'access_token' => $oauthAccessToken,
                'expires_in' => $now->getTimestamp() + intval($oauthResponseBody->expires_in)
            );
        }

        return $oauthAccessToken;
    }

    private function get_country_by_ip($remote_info)
    {
        session_start();
        $ip = $_SERVER['REMOTE_ADDR'];
        if(isset($_SESSION['TAP_ALINK_TAP'])){
            if(strcmp($ip, $_SESSION['TAP_ALINK_TAP']['client_ip']) === 0){
                $country = $_SESSION['TAP_ALINK_TAP']['client_country'];
                return $country;
            }
            unset($_SESSION['TAP_ALINK_TAP']);
        }

        $oauthAccessToken = $this->get_oauth_access_token();

        $now = new DateTime("now");

        $apiUrl = esc_url(sprintf($remote_info['url_get_country_from_ip'], $ip, $oauthAccessToken, $now->getTimestamp()));
        $apiResponse = wp_remote_get($apiUrl);
        if($apiResponse instanceof WP_Error || (!empty($apiResponse['body']) && strcmp($apiResponse['response']['code'], '200') !== 0)){
            throw new \Exception('Invalid API response');
        }
        $country = json_decode($apiResponse['body'], true);

        if(!isset($_SESSION['TAP_ALINK_TAP'])){
            $_SESSION['TAP_ALINK_TAP'] = array(
                'client_ip' => $ip,
                'client_country' => $country
            );
        }

        return $country;
    }
}
