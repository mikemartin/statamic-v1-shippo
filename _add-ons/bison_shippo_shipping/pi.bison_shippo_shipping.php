<?php
require_once('vendor/autoload.php');

class Plugin_bison_shippo_shipping extends Plugin
{

	/**
	* Shipping Options
	*
	* Outputs shipping options
	*
	* @return string Parsed template HTML
	**/
	public function shipping_options()
	{
		$use_select = $this->fetchParam('select', true, null, true);
		$name = $this->fetchParam('name', 'shipping_option');

		$attributes_string = '';
		if ($attr = $this->fetchParam('attr', false)) {
			$attributes_array = Helper::explodeOptions($attr, true);
			foreach ($attributes_array as $key => $value) {
				$attributes_string .= " {$key}=\"{$value}\"";
			}
		}

		$vars = $this->core->getRates();

		$html = Parse::tagLoop($this->content, $vars);

		if ($use_select) {
			$html = "<select name=\"$name\" $attributes_string>$html</select>";
		}
		return $html;
	}


	/**
	* Shipping Option
	*
	* Gets a selected value from the active shipping option
	*
	* @return string
	*/
	public function shipping_option()
	{
		$shipping_options = $this->core->getRates();
		$customer = $this->addon->api('bison')->getCustomerInfo();

		if ($customer['shipping_option'] != '') {
			$option = $customer['shipping_option'];
		} else {
			$keys = array_keys($shipping_options);
			$option = $keys[0];
		}

		$key = $this->fetchParam('get', 'value');

		switch ($key) {
			case 'value':
			return $option;
			break;
			default:
			return $shipping_options[$option][$key];
			break;
		}
	}

	/**
	* Tracking
	*
	* Return tracking details
	*
	* @return string
	**/
	public function tracking() {

		$order_details = $this->addon->api('bison')->session->get('order_details');
		$order_items = $order_details['items'];
		$shipment = $this->core->createShipment($order_items);
		$selected_rate = $this->core->rateDetails('object_id');

		$transaction = Shippo_Transaction::create(array(
			'rate'=> $selected_rate,
			'async'=> false,
		));

		return "<a href='" . $transaction['tracking_url_provider'] . "' target='_blank'>" . $transaction['tracking_number'] . "</a>";
	}
}
