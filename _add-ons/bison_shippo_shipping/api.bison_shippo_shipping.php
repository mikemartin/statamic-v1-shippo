<?php
require_once('vendor/autoload.php');

class API_bison_shippo_shipping extends API {
	public function calculateShipping()
	{
		$customer = $this->addon->api('bison')->getCustomerInfo();
		$shipping = 0;

		if ($customer['shipping_option'] != '') {
			Shippo::setApiKey($this->fetchConfig('api_key'));
			$shipping_options = $this->core->getRates();

			// This would be the index of the rate selected by the user
			$selected_rate_index = 1;

			// After the user has selected a rate, use the corresponding object_id
			$selected_rate = $customer['shipping_option'];
			$shipping_options = $this->core->getRates();
			$shipping = $shipping_options[$selected_rate]['price']*100;
		}

		return $shipping;
	}
}
