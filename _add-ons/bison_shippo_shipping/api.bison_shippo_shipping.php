<?php
require_once('vendor/autoload.php');

class API_bison_shippo_shipping extends API {
	public function calculateShipping()
  {
		$customer = $this->addon->api('bison')->getCustomerInfo();
		$shipping = 0;

		if ($customer['shipping_option'] != '') {
	    $shipping_options = $this->core->getRates();
			$selected_rate = $customer['shipping_option'];

			$shipping = $shipping_options[$selected_rate]['price']*100;
		}

		return $shipping;
  }
}
