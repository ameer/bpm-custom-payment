<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ameer.ir
 * @since      1.0.0
 *
 * @package    Cpm
 * @subpackage Cpm/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cpm
 * @subpackage Cpm/admin
 * @author     Ameer Mousavi <me@ameer.ir>
 */
class Cpm_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	private $title_of_the_page = 'cpm_confirm_page_title';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	public function add_plugin_admin_menu()
	{

		/**
		 * Add a settings page for this plugin to the Settings menu.
		 *
		 * NOTE:  Alternative menu locations are available via WordPress administration menu functions.
		 *
		 *        Administration Menus: http://codex.wordpress.org/Administration_Menus
		 *
		 * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
		 *
		 * @link https://codex.wordpress.org/Function_Reference/add_options_page
		 *
		 * If you want to list plugin options page under a custom post type, then change 'plugin.php' to e.g. 'edit.php?post_type=your_custom_post_type'
		 */
		$options = get_option($this->plugin_name);
		if ($options && !$options['confirm_page_id']) {
			$options['confirm_page_id'] = $this->create_page($this->title_of_the_page, '[cpm-confirm-payment]');
			update_option($this->plugin_name, $options);
		}
		add_menu_page(__('Plugin settings page title', 'cpm'), __('Custom Payment', 'cpm'), 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'), 'dashicons-money-alt');
		add_submenu_page($this->plugin_name, __('Transactions List', 'cpm'), __('Transactions List', 'cpm'), 'manage_options', $this->plugin_name . '-trx-page', array($this, 'display_trx_list_page'));
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links($links)
	{
		$settings_link = array('<a href="' . admin_url('plugins.php?page=' . $this->plugin_name) . '">' . __('Settings', $this->plugin_name) . '</a>',);
		return array_merge($settings_link, $links);
	}
	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function display_plugin_setup_page()
	{

		include_once('partials/' . $this->plugin_name . '-admin-display.php');
	}
	public function display_trx_list_page()
	{
		require_once('class-trx-list.php');
		$table = new Cpm_Trx_Table();
		$data = $this->get_rows_from_db_as_associative_array();
		$table->items = $data;
		$table->prepare_items();
		echo '<div class="wrap">';
		$table->display();
		echo '</div>';
	}
	/**
	 * Validate fields from admin area plugin settings form ('exopite-lazy-load-xt-admin-display.php')
	 * @param  mixed $input as field form settings form
	 * @return mixed as validated fields
	 */
	public function validate($input)
	{

		$options = get_option($this->plugin_name);
		$options['terminal_id'] = (isset($input['terminal_id']) && !empty($input['terminal_id'])) ? esc_attr($input['terminal_id']) : '';
		$options['username'] = (isset($input['username']) && !empty($input['username'])) ? esc_attr($input['username']) : '';
		$options['password'] = (isset($input['password']) && !empty($input['password'])) ? esc_attr($input['password']) : '';
		$options['edtaj'] = (isset($input['edtaj']) && !empty($input['edtaj'])) ? 1 : 0;
		$options['formal_rate_percent'] = (isset($input['formal_rate_percent']) && !empty($input['formal_rate_percent'])) ? esc_attr($input['formal_rate_percent']) : '';
		$options['formal_account_id'] = (isset($input['formal_account_id']) && !empty($input['formal_account_id'])) ? esc_attr($input['formal_account_id']) : '';
		$options['informal_account_id'] = (isset($input['informal_account_id']) && !empty($input['informal_account_id'])) ? esc_attr($input['informal_account_id']) : '';

		return $options;
	}
	public function options_update()
	{

		register_setting($this->plugin_name, $this->plugin_name, array(
			'sanitize_callback' => array($this, 'validate'),
		));
	}
	public function create_page($title_of_the_page, $content, $parent_id = NULL)
	{
		$objPage = get_page_by_title($title_of_the_page, 'OBJECT', 'page');
		if (!empty($objPage)) {
			return $objPage->ID;
		}
		$page_id = wp_insert_post(
			array(
				'comment_status' => 'close',
				'ping_status'    => 'close',
				'post_author'    => 1,
				'post_title'     => ucwords($title_of_the_page),
				'post_name'      => strtolower(str_replace(' ', '-', trim($title_of_the_page))),
				'post_status'    => 'publish',
				'post_content'   => $content,
				'post_type'      => 'page',
				'post_parent'    =>  $parent_id //'id_of_the_parent_page_if_it_available'
			)
		);
		return $page_id;
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/cpm-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/cpm-admin.js', array('jquery'), $this->version, false);
	}
	public function get_rows_from_db_as_associative_array()
	{
		global $wpdb;
		$table = $wpdb->prefix . 'cpm_transactions';
		// 	"SELECT {$wpdb->users}.ID FROM {$wpdb->users} 
		// WHERE {$wpdb->users}.user_registered <= %s 
		// AND {$wpdb->usermeta}.meta_key='custom_status' 
		// AND {$wpdb->usermeta}.meta_value=0
		// INNER JOIN {$wpdb->usermeta} ON {$wpdb->users}.ID= {$wpdb->usermeta}.user_id
		$sql = "SELECT * FROM `" . $table . "`AS trx";
		return $wpdb->get_results($sql, ARRAY_A);
	}
}
