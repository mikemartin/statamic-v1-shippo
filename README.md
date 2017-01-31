
# Bison Shippo Shipping Rates

Retrieve all available shipping rates from the Shippo API based on `from_address`, `to_address` and `parcel` weight and dimensions.

Calculates shipping based on selected rate.

## TODO
- Add caching for API response

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
('label', 'provider', 'servicelevel', 'price', 'value', 'object_id')

```
{{ bison_shippo_shipping:shipping_option get="label" }}
```

**Tracking:**
Returns tracking number and tracking link.

```
{{ bison_shippo_shipping:tracking }}
```
