<?php

/**
 * RTWPIdeas - client class for plugin
 *
 * PHP version 5
 *
 * @category Development
 * @package  RTWPIdeas
 * @author   kaklo <mehul.kaklotar@rtcamp.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     http://rtcamp.com
 */
if ( ! class_exists( 'RTWPIdeas' ) ) {

	class RTWPIdeas {
		
		/**
		 * @var mixed|void - RTWPIdeas Templates Path / URL
		 */
		public $templateURL;

		/**
		 *  Constructor
		 */
		public function __construct() {
			// DB Upgrade
			$updateDB = new RT_DB_Update(  RTBIZ_IDEAS_PATH . 'index.php', RTBIZ_IDEAS_PATH . 'app/schema/',false );
			add_action( 'rt_db_update_finished_' . str_replace( '-', '_', sanitize_title( $updateDB->rt_plugin_info->name ) ), array( $this,
			                                                                                                                          'init_call_flush_rewrite_rules'
				) );
			$updateDB->do_upgrade();
			$this -> init_attributes();
			add_filter( 'template_include', array( $this, 'rtwpideas_template' ) );
			add_filter( 'woocommerce_product_tabs', array( $this, 'woo_ideas_tab' ), 999,1 );
			$this->templateURL = apply_filters( 'rt_wp_ideas_template_url', 'rt_ideas' );
		}

		function do_flush_rewrite_rules(){
			flush_rewrite_rules(false);
		}

		function init_call_flush_rewrite_rules(){
			add_action('init',array($this,'do_flush_rewrite_rules'));
		}

		/**
		 * Init global variables
		 * 
		 * @global RTWPIdeasVotesModel $rtWpideasVotes
		 * @global RTWPIdeasAdmin $rtwpIdeasAdmin
		 */
		function init_attributes() {
			global $rtWpideasVotes, $rtwpIdeasAdmin,$rtWpIdeasSubscirber;
			$rtwpIdeasAdmin = new RTWPIdeasAdmin();
			$rtWpideasVotes = new RTWPIdeasVotesModel();
			$rtWpIdeasSubscirber = new RTWPIdeasSubscriberModel();
		}

		/**
		 * Redirect to plugins templates directory
		 * 
		 * @param type $template
		 * @return type
		 */
		function rtwpideas_template( $template ) {
			global $wp;
			//A Specific Custom Post Type
			if ( isset( $wp -> query_vars[ 'post_type' ] ) && $wp -> query_vars[ 'post_type' ] == RTBIZ_IDEAS_SLUG ) {
				add_thickbox();
				$template = rtideas_locate_template( 'archive-idea.php' );
			}
			return $template;
		}

		function woo_ideas_tab( $tabs ) {

			// Adds the new tab

			$tabs[ 'ideas_tab' ] = array(
				'title' => __( 'Ideas', 'wp-ideas' ),
				'priority' => 50,
				'callback' => array( $this, 'woo_ideas_tab_content' ),
			);

			return $tabs;
		}

		function woo_ideas_tab_content() {
			global $post;
			if ( isset( $post ) ) {
				echo sanitize_html_class( do_shortcode( '[wpideas post_id = ' . $post -> ID . ' ]' ) );
			}
		}

	}

}	