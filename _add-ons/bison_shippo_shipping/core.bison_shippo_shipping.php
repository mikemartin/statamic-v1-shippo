<?php
require_once('vendor/autoload.php');

class Core_bison_shippo_shipping extends Core
{
		/**
		* Get Rates
		*
		* Retrieve cached rates or get rates from API
		*
		* @return array
		**/
		public function getRates() {
			$customer = $this->addon->api('bison')->getCustomerInfo();
			$rates_id = $customer['shipping_zip'] . date("Ym");

			// check cache to see if we've found this one before
			if ($this->cache->exists($rates_id)) {
				$rates = $this->cache->getYAML($rates_id);
			} else {
				$rates = $this->getRatesFromAPI();
				// cache it
				$this->cache->putYAML($rates_id, $rates);
			}

			// all the region details (from the config file)
			return $rates;
		}

		/**
		* Get Rates from API
		*
		* Retrieve all available shipping rates from Shippo API
		*
		* @return array
		**/
    public function getRatesFromAPI()
    {
			$cart_items = $this->addon->api('bison')->getCartItems();
			$shipment = $this->createShipment($cart_items);

			// Rates are stored in the `rates_list` array inside the shipment object
			$rates = $shipment['rates_list'];

			foreach ($rates as $i => $rate) {
				if ($rate['servicelevel_token'] != null) {
					$i = $rate['servicelevel_token'];
				} else {
					$i = Slug::make($rate['provider'] . '_' . $rate['servicelevel_name'], array('lowercase' => true,'separator' => '_','custom_replacements' => array('.' => '','®' => '','™' => '','©' => '')));
				}
				$vars[$i] = array(
					'label' => $rate['provider'] . ' - ' . $rate['servicelevel_name'] . ' ($' . $rate['amount'] . ')',
					'provider' => $rate['provider'],
					'servicelevel' => $rate['servicelevel_name'],
					'price' => $rate['amount'],
					'value' => $i,
					'object_id' => $rate['object_id']
				);
			}

			return $vars;
    }


		/**
		 * Rate Details
		 *
		 * Gets a value from the active shipping option
		 *
		 * @return string
		 */
		public function rateDetails($key)
		{
			$rates = $this->getRates();
			$customer = $this->addon->api('bison')->getCustomerInfo();

			if ($customer['shipping_option'] != '') {
				$option = $customer['shipping_option'];
			} else {
				$keys = array_keys($rates);
				$option = $keys[0];
			}

			switch ($key) {
				case 'value':
					return $option;
					break;
				default:
					return $rates[$option][$key];
					break;
			}

		}

		public function createShipment($items) {
			Shippo::setApiKey($this->fetchConfig('api_key'));

			$bison_config = $this->addon->api('bison')->getBisonConfig();
			$total_weight = 0;
			$customer = $this->addon->api('bison')->getCustomerInfo();

			foreach ($items as $item) {
				$total_weight += $item[$bison_config['weight_field']] * $item['quantity'];
			}

			$from_address = array(
					'object_purpose' => 'PURCHASE',
					'name' => $this->config['sender_name'],
					'company' => $this->config['sender_company'],
					'street1' => $this->config['sender_street1'],
					'city' => $this->config['sender_city'],
					'state' => $this->config['sender_state'],
					'zip' => $this->config['sender_zip'],
					'country' => $this->config['sender_country'],
					'phone' => $this->config['sender_phone'],
					'email' => $this->config['sender_email']
			);

			$to_address = array(
					'object_purpose' => 'PURCHASE',
					'name' => $customer['first_name'] . " " . $customer['last_name'],
					'street1' => $customer['shipping_address_1'],
					'city' => $customer['shipping_city'],
					'state' => $customer['shipping_state'],
					'zip' => $customer['shipping_zip'],
					'country' => $customer['shipping_country'],
					'email' => $customer['email'],
			);

			// Parcel information array
			$parcel = array(
					'length'=> '3.25',
					'width'=> '5.75',
					'height'=> '1.75',
					'distance_unit'=> 'in',
					'weight'=> $total_weight,
					'mass_unit'=> 'lb',
			);

			// Shipment object
			$shipment = Shippo_Shipment::create(
			array(
					'object_purpose'=> 'PURCHASE',
					'address_from'=> $from_address,
					'address_to'=> $to_address,
					'parcel'=> $parcel,
					'async'=> false,
			));

			return $shipment;
		}
}
