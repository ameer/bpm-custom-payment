<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ameer.ir
 * @since      1.0.0
 *
 * @package    Cpm
 * @subpackage Cpm/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cpm
 * @subpackage Cpm/public
 * @author     Ameer Mousavi <me@ameer.ir>
 */
class Cpm_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;
	protected $allowed_functions = ['submitPayment', 'confirmRedirect'];
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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}
	function cpm_form_generator()
	{
		$current_user = wp_get_current_user();
		$options = get_option($this->plugin_name);
		wp_enqueue_script($this->plugin_name . '-vue');
		wp_enqueue_script($this->plugin_name . '-vuelidate');
		wp_enqueue_script($this->plugin_name . '-validators');
		wp_enqueue_script($this->plugin_name . '-main');
		wp_enqueue_script($this->plugin_name . "-form-js");
		ob_start();
		require_once("partials/cpm-public-display.php");

		return ob_get_clean();
	}
	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/cpm-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
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
		wp_register_script($this->plugin_name . '-vue', plugin_dir_url(__FILE__) . '../assets/js/vue-v2.6.12.dev.js', array(), $this->version, false);
		wp_register_script($this->plugin_name . '-vuelidate', plugin_dir_url(__FILE__) . '../assets/js/vuelidate.min.js', array($this->plugin_name . '-vue'), $this->version, false);
		wp_register_script($this->plugin_name . '-validators', plugin_dir_url(__FILE__) . '../assets/js/validators.min.js', array($this->plugin_name . '-vuelidate'), $this->version, false);
		wp_register_script($this->plugin_name . '-main', plugin_dir_url(__FILE__) . 'js/cpm-main.js', array($this->plugin_name . '-validators'), $this->version, false);
		wp_register_script($this->plugin_name . '-form-js', plugin_dir_url(__FILE__) . 'js/cpm-form.js', array($this->plugin_name . '-vue', 'jquery'), $this->version, false);
		wp_localize_script(
			$this->plugin_name . '-vue',
			'cpm',
			array(
				'ajax_url' => admin_url('admin-ajax.php'),
				'_nonce' => wp_create_nonce('cpm_ajax')
			)
		);
	}
	public function ajax_handler()
	{
		/**
		 * Do not forget to check your nonce for security!
		 *
		 * @link https://codex.wordpress.org/Function_Reference/wp_verify_nonce
		 */
		if (!wp_verify_nonce($_POST['_nonce'], 'cpm_ajax')) {
			wp_send_json_error();
			die();
		}
		/**
		 * OR you can use check_ajax_referer
		 *
		 * @link https://codex.wordpress.org/Function_Reference/check_ajax_referer
		 * @link https://tommcfarlin.com/secure-ajax-requests-in-wordpress/
		 * @link https://wordpress.stackexchange.com/questions/48110/wp-verify-nonce-vs-check-admin-referer
		 */
		if (!check_ajax_referer('cpm_ajax', '_nonce', false)) {
			wp_send_json_error('Invalid security token sent.');
			die();
		}
		$func = sanitize_text_field($_POST["func"]);
		if (!in_array($func, $this->allowed_functions)) {
			wp_send_json_error('Invalid Function Name!');
			die();
		}
		$options = get_option($this->plugin_name);
		$current_user = wp_get_current_user();
		require_once plugin_dir_path(__FILE__) . '../includes/class-helpers.php';
		require_once plugin_dir_path(__FILE__) . '../includes/gateways/bpm/payment.php';
		$helpers = new Cpm_Helpers($this->plugin_name);
		$gateway = new Cpm_BPM($this->plugin_name, $options);
		switch ($func) {
			case 'submitPayment':
				$user_id = $current_user->ID;
				mt_srand($this->make_seed());
				$orderId = mt_rand();
				$data = array(
					"amount" => intval($_POST['amount']),
					"mobileNumber" => $_POST['mobileNumber'],
					"orderId" => $orderId,
					"additionalData" => ''
				);
				$cost_withprofit = $helpers->get_formal_amount($options['formal_rate_percent'], $data['amount']);
				$data['additionalData'] = $helpers->get_additional_data($options['formal_account_id'], $options['informal_account_id'], $data['orderId'], $data['mobileNumber'], $cost_withprofit, $data['amount']);
				// Temporary save order data to database.
				set_transient($data['orderId'], [$user_id, $data['amount'], $data['mobileNumber']], 1000);
				$res = $gateway->getBankToken($data['amount'], $data['orderId'], $user_id, $data['additionalData']);
				die(json_encode($res));
				break;
			default:
				die('CPM function not found!');
				break;
		}
		die();
	}
	public function insert_row_to_db($saleOrderId, $trx_amount, $trx_resCode, $trx_refId, $trx_saleReferenceId, $user_id)
	{
		global $wpdb;
		date_default_timezone_set('Asia/Tehran');
		$current_user = wp_get_current_user();
		$table = $wpdb->prefix . 'cpm_transactions';
		$data = array(
			'user_id'     => $user_id,
			'saleOrderId'     => $saleOrderId,
			'trx_datetime'     => date('Y-m-d H:i:s'),
			'trx_amount'     => $trx_amount,
			'trx_resCode'     => $trx_resCode,
			'trx_refId'     => $trx_refId,
			'trx_saleReferenceId'     => $trx_saleReferenceId,

		);
		$format = array('%d', '%d', '%s', '%d', '%s', '%s', '%s');

		$wpdb->insert($table, $data, $format);

		return $wpdb->insert_id;
	}
	public function make_seed()
	{
		list($usec, $sec) = explode(' ', microtime());
		return $sec + $usec * 1000000;
	}
	public function confirm_payment($atts)
	{
		error_log(print_r($_REQUEST, 1));
		$orderInfo = get_transient($_REQUEST['SaleOrderId']);
		// // Verifying transaction
		// $send_sale_order_id = get_post_meta($order_id, 'behpardakht_SaleOrderId', true);
		// $send_reference_id  = get_post_meta($order_id, 'behpardakht_RefId', true);
		if (!$orderInfo) {
			return __('<p>Transaction Not found!</p>', 'cpm');
		}
		$saleOrderId = $_REQUEST['SaleOrderId'];
		$saleReferenceId = isset($_REQUEST['SaleReferenceId']) ? $_REQUEST['SaleReferenceId'] : __('Undefined', 'cpm');
		$finalAmount = isset($_REQUEST['FinalAmount']) ? $_REQUEST['FinalAmount'] : $orderInfo[1];
		$resCode = $_REQUEST['ResCode'];
		$refId = $_REQUEST['RefId'];
		$user_id = $orderInfo[0];
		$user = get_user_by('id', $user_id);
		$user_display_name = $user->display_name;
		require_once plugin_dir_path(__FILE__) . '../includes/gateways/bpm/payment.php';
		$options = get_option($this->plugin_name);
		$gateway = new Cpm_BPM($this->plugin_name, $options);
		$verify = $gateway->verify($saleOrderId, $saleReferenceId);
		if ($verify['ok']) {
			$settle = $gateway->settle($saleOrderId, $saleReferenceId);
			if ($settle['ok']) {
				$this->insert_row_to_db($saleOrderId, $finalAmount, $resCode, $refId, $saleReferenceId, $user_id);
				ob_start();
				require_once("partials/cpm-success-page.php");
				return ob_get_clean();
			} else {
				// Settle failed. lets reverse
				$reverse = $gateway->reverse($saleOrderId, $saleReferenceId);
				if ($reverse['ok']) {
					// Return reverse success
					$this->insert_row_to_db($saleOrderId, $finalAmount, $resCode, $refId, $saleReferenceId, $user_id);
					ob_start();
					require_once("partials/cpm-reverse-success-page.php");
					return ob_get_clean();
				} else {
					// Return reverse failure
					$this->insert_row_to_db($saleOrderId, $finalAmount, $resCode, $refId, $saleReferenceId, $user_id);
					ob_start();
					require_once("partials/cpm-reverse-failure-page.php");
					return ob_get_clean();
				}
			}
		} else {
			// Return transaction verification failure
			$this->insert_row_to_db($saleOrderId, $finalAmount, $resCode, $refId, $saleReferenceId, $user_id);
			ob_start();
			require_once("partials/cpm-verification-failure-page.php");
			return ob_get_clean();
		}
	}
}
