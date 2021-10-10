<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}
class Cpm_Helpers
{
    private $plugin_name;
    public function __construct($plugin_name)
    {
        $this->plugin_name = $plugin_name;
    }
    public function get_formal_amount($formal_rate_percent,$amount)
	{
		$cost_withprofit = floor ($amount * ($formal_rate_percent / 100));
		return $cost_withprofit;
	}
    public function get_additional_data($formal_account_id, $informal_account_id, $order_id,$mobile,$cost_withprofit,$total_amount){
		if ($cost_withprofit == 0) {
			return  sprintf(
				__('Order ID: %d - Customer Mobile: %d', 'cpm'),
				$order_id,
				$mobile
			);
		}
		else{
			return sprintf('%d,%d,%d,;%d,%d,%d;',$formal_account_id,($total_amount - $cost_withprofit),$mobile,$informal_account_id,$cost_withprofit,$mobile);
		}
	}
}
