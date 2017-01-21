<?php
require_once('vendor/autoload.php');

class API_bison_shippo_shipping extends API {
	public function calculateShipping()
  {
		Shippo::setApiKey($this->fetchConfig('api_key'));

    $shipping_options = $this->core->getRates();
    $customer = $this->addon->api('bison')->getCustomerInfo();

		// This would be the index of the rate selected by the user
		$selected_rate_index = 1;

		// After the user has selected a rate, use the corresponding object_id
		$selected_rate = $customer['shipping_option'];
		$shipping_options = $this->core->getRates();
		$selected_rate_price = $shipping_options[$selected_rate]['price'];

		return (float)$selected_rate_price*100;
  }
}
