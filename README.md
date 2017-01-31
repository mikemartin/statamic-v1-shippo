
# Bison Shippo Shipping Rates

- Get carrier shipping rates from the Shippo API using `from_address`, `to_address` and `parcel` weight and dimensions.
- Caches rate using the current month and postal code
- Calculates shipping based on selected rate.
- Sends shipment to Shippo for label printing.

## Installing
1. Copy the 'bison_shippo_shipping' folder to the `_add-ons` folder in your Statamic website.
2. Enter your Shippo `api_key` in the addon config
3. Enter `sender` address in the addon config
4. Set Bison config `shipping_method` to `shippo_shipping`
5. Add `weight` field to product fieldset

## Tags

**Shipping Options:**
Outputs shipping estimate options from Shippo

```
{{ bison_shippo_shipping:shipping_options }}
  <option value="{{ value }}" {{ selected }}>{{ label }}</option>
{{ /bison_shippo_shipping:shipping_options }}
```

**Shipping Option:**
Gets a selected value from the active shipping option
`label` `provider` `servicelevel` `price` `value` `object_id`

```
{{ bison_shippo_shipping:shipping_option get="label" }}
```

**Tracking:**
Returns tracking number and tracking link.

```
{{ bison_shippo_shipping:tracking }}
```
