<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'WR_Factory') ):

/**
 * WooRechnung Invoice Factory Component.
 *
 * This class provides a factory to create invoices from WooCommerce
 * orders. In the factoring process, the order properties are mapped
 * onto the invoice properties that the WooRechnung API expects.
 *
 * @version  1.0.0
 * @package  WooRechnung\Common
 * @author   Zweischneider
 */
final class WR_Factory
{
    /**
     * The plugin instance via dependency injection.
     *
     * @var WR_Plugin
     */
    private $_plugin;

    /**
     * Create a new instance of this invoice factory.
     *
     * @param  WR_Plugin $plugin
     * @return void
     */
    public function __construct(WR_Plugin $plugin)
    {
        $this->_plugin = $plugin;
    }

    /**
     * Create an invoice from a given order (using the adapter).
     *
     * @param  WR_Order_Adapter $order
     * @return array
     */
    public function create_invoice( WR_Order_Adapter $order )
    {
        $plugin_settings            = $this->_plugin->get_settings();
        $order_status               = $order->get_status();
        $number_original            = $order->get_order_number();
        $payment_method             = $order->get_payment_method();
        $number_modified            = $plugin_settings->get_order_number( $number_original );
        $status_paid                = $plugin_settings->mark_invoice_as_paid( $payment_method );
        $payment_date               = $order->get_date_paid();
        $payment_date               = is_null($payment_date) ? null : (int) $payment_date->getTimestamp();
        $base_location              = wc_get_base_location();
        $base_country               = $base_location['country'];
        $customer_note              = $order->get_customer_note();

        $result = array();

        $result['order_id']         = (string) $order->get_id();
        $result['order_key']        = (string) $order->get_order_key();
        $result['order_number']     = (string) $number_modified;
        $result['order_date']       = (int) $order->get_date_created()->getTimestamp();
        $result['invoice_currency'] = (string) $order->get_currency();
        $result['invoice_paid']     = (bool) $status_paid;
        $result['taxes_included']   = (bool) $order->get_prices_include_tax();
        $result['payment_method']   = (string) $order->get_payment_method();
        $result['payment_title']    = (string) $order->get_payment_method_title();
        $result['payment_date']     = $payment_date;
        $result['base_country']     = (string) $base_country;
        $result['customer_note']    = (string) $customer_note;

        $result['meta']             = $this->make_meta_data( $order );
        $result['billing']          = $this->make_billing( $order );
        $result['shipping']         = $this->make_shipping( $order );
        $result['items']            = $this->make_items( $order );

        return $result;
    }

    /**
     * Create the meta data for the invoice.
     *
     * @param  WR_Order_Adapter $order
     * @return array
     */
    private function make_meta_data( WR_Order_Adapter $order )
    {
        $meta = $order->get_meta_data();
        $result = array();

        foreach ($meta as $key => $value)
        {
            $data = $value->get_data();
            $element = array();
            $element['key'] = $data['key'];
            $element['value'] = $data['value'];
            $result[] = $element;
        }

        return $result;
    }

    private function make_billing( WR_Order_Adapter $order )
    {
        $result = array();

        $result['customer_no']      = (string) $order->get_customer_id();
        $result['first_name']       = (string) $order->get_billing_first_name();
        $result['last_name']        = (string) $order->get_billing_last_name();
        $result['company']          = (string) $order->get_billing_company();
        $result['address_1']        = (string) $order->get_billing_address_1();
        $result['address_2']        = (string) $order->get_billing_address_2();
        $result['city']             = (string) $order->get_billing_city();
        $result['state']            = (string) $order->get_billing_state();
        $result['postcode']         = (string) $order->get_billing_postcode();
        $result['country']          = (string) $order->get_billing_country();
        $result['email']            = (string) $order->get_billing_email();
        $result['phone']            = (string) $order->get_billing_phone();
        $result['salutation']       = (string) $order->get_billing_title();
        $result['vat_id']           = (string) $order->get_billing_vat_id();

        return $result;
    }

    private function make_shipping( WR_Order_Adapter $order )
    {
        $result = array();

        $result['first_name']       = (string) $order->get_shipping_first_name();
        $result['last_name']        = (string) $order->get_shipping_last_name();
        $result['company']          = (string) $order->get_shipping_company();
        $result['address_1']        = (string) $order->get_shipping_address_1();
        $result['address_2']        = (string) $order->get_shipping_address_2();
        $result['city']             = (string) $order->get_shipping_city();
        $result['state']            = (string) $order->get_shipping_state();
        $result['postcode']         = (string) $order->get_shipping_postcode();
        $result['country']          = (string) $order->get_shipping_country();
        $result['salutation']       = (string) $order->get_shipping_title();
        $result['vat_id']           = (string) $order->get_shipping_vat_id();

        return $result;
    }

    /**
     * Create all items for this invoice.
     *
     * @param  WR_Order_Adapter $adapter
     * @return array
     */
    private function make_items( WR_Order_Adapter $order )
    {
        $results = array();
        $results = array_merge($results, $this->make_products($order));
        $results = array_merge($results, $this->make_shippings($order));
        $results = array_merge($results, $this->make_fees($order));
        $results = array_merge($results, $this->make_discounts($order));
        return $results;
    }

    /**
     * Create all product items for this invoice.
     *
     * @param  WR_Order_Adapter $adapter
     * @return array
     */
    private function make_products( WR_Order_Adapter $adapter )
    {
        $order = $adapter->get_order();
        $products = array();


        foreach ($order->get_items() as $product_item)
        {
            $product = $product_item->get_product();
            $product_taxes = $product_item->get_taxes();
            $product_rate = $product_taxes['subtotal'];

            //$product_rate = array_filter( $product_rate );
            $product_rate = array_flip( $product_rate );
            $product_rate = array_shift( $product_rate );
            $product_service = $product->get_meta('_service');
            $product_service = ($product_service === 'yes');
            $product_subtype = $product_service ? 'service' : null;

            if ( ! empty( $product_rate ) )
            {
                $wc_tax = WC_Tax::_get_tax_rate( $product_rate );
                $tax_name = $wc_tax['tax_rate_name'];
                $tax_rate = $wc_tax['tax_rate'];
            }
            else
            {
                $tax_name = null;
                $tax_rate = null;
            }

            $result = array();
            $result['type'] = 'product';
            $result['subtype'] = $product_subtype;
            $result['tax_rate'] = $tax_rate;
            $result['tax_name'] = $tax_name;

            $result['description'] = $this->get_product_description($product_item);

            $result['price_net'] = $order->get_item_subtotal( $product_item, false, true );
            $result['price_gross'] = $order->get_item_subtotal( $product_item, true, true );
            
            $result['total_price_net'] = $order->get_item_total( $product_item, false, true );
            $result['total_price_gross'] = $order->get_item_total( $product_item, true, true );

            $result['name'] = $product_item->get_name();
            $result['quantity'] = $product_item->get_quantity();

            $result['number'] = $product->get_sku(); // $product_item->get_product()->get_sku();
            $result['unit'] = $product->get_meta('_unit'); // $product_item->get_product()->get_meta('_unit');
            $result['manage_stock'] = $product->get_manage_stock();
            $result['is_in_stock'] = $product->is_in_stock();
            $result['stock_quantity'] = $product->get_stock_quantity();

            $products[] = $result;
        }

        return $products;
    }

    private function get_product_description($product_item)
    {
        $settings = $this->_plugin->get_settings();
        $setting = $settings->get_line_description();
        $variation_id = $product_item->get_variation_id();

        if ( $setting == 'short' ) {
            $product = $product_item->get_product();
            $description = $product->get_short_description();
            $description = strip_tags($description);
            return $description;
        }

        if ( $setting == 'article' ) {
            $product = $product_item->get_product();
            $description = $product->get_description();
            $description = strip_tags($description);
            return $description;
        }

        if ( $setting == 'variation' && ! empty( $variation_id ) ) {
            $variation = new WC_Product_Variation($variation_id);
            $description = $variation->get_variation_description();
            $description = strip_tags($description);
            return $description;
        }

        if ( $setting == 'variation_title' && ! empty( $variation_id ) ) {
            $variation = new WC_Product_Variation($variation_id);
            $attributes = $variation->get_variation_attributes();
            $description = join(', ', $attributes);
            return $description;
        }

        if ( $setting == 'meta_data' ) {
            $properties = array();
            foreach ( $product_item->get_meta_data() as $property ) {
                $properties[] = "{$property->key}: {$property->value}";
            }
            $description = join(PHP_EOL, $properties);
            return $description;
        }

        if ($setting == 'mini_desc') {
            $product = $product_item->get_product();
            $description = $product->get_meta('_mini_desc');
            $description = strip_tags(strval($description));
            return $description;
        }

        return '';
    }

    /**
     * Create all shipping items for this invoice.
     *
     * @param  WR_Order_Adapter $adapter
     * @return array
     */
    private function make_shippings( WR_Order_Adapter $adapter )
    {
        $order = $adapter->get_order();

        $tax_rates = array();
        $gross_amounts = array();
        $net_amounts = array();
        $tax_amounts = array();
        $shippings = array();

        $settings = $this->_plugin->get_settings();
        $article_name = $settings->get_article_name_shipping();
        $article_number = $settings->get_article_number_shipping();

        foreach ($order->get_taxes() as $tax_item)
        {
            $rate_id = $tax_item->get_rate_id();
            $wc_tax = WC_Tax::_get_tax_rate($rate_id);
            $tax_rate = array();
            $tax_rate['name'] = $wc_tax['tax_rate_name'];
            $tax_rate['rate'] = $wc_tax['tax_rate'];
            $tax_rates[ $rate_id ] = $tax_rate;
            $tax_amounts[ $rate_id ] = 0;
            $gross_amounts[ $rate_id ] = 0;
            $net_amounts[ $rate_id ] = 0;
        }

        foreach ($order->get_items() as $product_item)
        {
            $product_taxes = $product_item->get_taxes();
            $product_rate = $product_taxes['subtotal'];
            $product_rate = array_filter( $product_rate );
            $product_rate = array_flip( $product_rate );
            $product_rate = array_shift( $product_rate );

            if ( ! empty( $product_rate ) )
            {
                $net_amount = $product_item->get_subtotal();
                $tax_amount = $product_item->get_subtotal_tax();
                $gross_amount = $net_amount + $tax_amount;
                $net_amounts [ $product_rate ] += $net_amount;
                $tax_amounts [ $product_rate ] += $tax_amount;
                $gross_amounts [ $product_rate ] += $gross_amount;
            }
        }

        $total_net = array_sum($net_amounts);
        $total_tax = array_sum($tax_amounts);
        $total_gross = array_sum($gross_amounts);

        foreach ( $order->get_items('shipping') as $shipping_item )
        {
            $shipping_name = $shipping_item->get_name();
            $shipping_name = ! empty( $article_name ) ? $article_name : $shipping_name;
            $shipping_number = ! empty( $article_number ) ? $article_number : null;

            $shipping_taxes = $shipping_item->get_taxes();
            $shipping_taxes = $shipping_taxes['total'];
            $shipping_taxes = array_filter($shipping_taxes);

            $shipping_tax = $shipping_item->get_total_tax();
            $shipping_net = $shipping_item->get_total();
            $shipping_gross = $shipping_net + $shipping_tax;

            // Case if no tax rates are applied to shipping
            // This is mostly the case when shipping is free
            // Use the first tax rate in order in this case if there is one
            // Pass the shipping untaxed if the hole order is untaxed

            if ( empty( $shipping_taxes ) )
            {
                $tax_rate = array_shift($tax_rates);
                $tax_name = ! empty( $tax_rate ) ? $tax_rate['name'] : null;
                $tax_rate = ! empty( $tax_rate ) ? $tax_rate['rate'] : null;

                $result = array();
                $result['type'] = 'shipping';
                $result['unit'] = null;
                $result['quantity'] = 1;
                $result['name'] = $shipping_name;
                $result['number'] = $shipping_number;
                $result['description'] = null;
                $result['price_net'] = $shipping_net;
                $result['price_gross'] = $shipping_gross;
                $result['tax_rate'] = $tax_rate;
                $result['tax_name'] = $tax_name;
                $shippings[] = $result;

                continue;
            }

            // Case if there is only one tax rate applied
            // This might be the case due to the setting to use the highest rate
            // Altough there are multiple tax rates in the order
            // Apply the single tax rate then and return the single item

            if ( count( $shipping_taxes ) === 1 )
            {
                $tax_rates = array_flip($shipping_taxes);
                $rate_id = array_shift($tax_rates);
                $wc_tax = WC_Tax::_get_tax_rate( $rate_id );
                $tax_name = $wc_tax['tax_rate_name'];
                $tax_rate = $wc_tax['tax_rate'];

                $result = array();
                $result['type'] = 'shipping';
                $result['unit'] = null;
                $result['quantity'] = 1;
                $result['name'] = $shipping_name;
                $result['number'] = $shipping_number;
                $result['description'] = null;
                $result['price_net'] = $shipping_net;
                $result['price_gross'] = $shipping_gross;
                $result['tax_rate'] = $tax_rate;
                $result['tax_name'] = $tax_name;
                $shippings[] = $result;

                continue;
            }

            // Case for multiple tax rates applied to the shipping costs
            // This is most likely the case when Germanized forces detailed calculation
            // Calculate the partial amounts in this case and add them to the items

            foreach( $shipping_taxes as $tax_id => $tax_amount )
            {
                $wc_tax = WC_Tax::_get_tax_rate( $tax_id );
                $tax_name = $wc_tax['tax_rate_name'];
                $tax_rate = $wc_tax['tax_rate'];

                $net_amount = $net_amounts[ $tax_id ];
                $gross_amount = $gross_amounts[ $tax_id ];
                $net_ratio = ($total_net > 0) ? ($net_amount / $total_net) : 0;
                $gross_ratio = ($total_gross > 0) ? ($gross_amount / $total_gross) : 0;
                $price_net = round( $shipping_net * $net_ratio, 4 );
                $price_gross = round( $shipping_gross * $gross_ratio, 4 );

                $result = array();
                $result['type'] = 'shipping';
                $result['unit'] = null;
                $result['quantity'] = 1;
                $result['name'] = $shipping_name;
                $result['number'] = $shipping_number;
                $result['description'] = null;
                $result['tax_rate'] = $tax_rate;
                $result['tax_name'] = $tax_name;
                $result['price_net'] = $price_net;
                $result['price_gross'] = $price_gross;
                $shippings[] = $result;
            }
        }

        return $shippings;
    }

    /**
     * Create all fee items for this invoice.
     *
     * @param  WR_Order_Adapter $adapter
     * @return array
     */
    private function make_fees( WR_Order_Adapter $adapter )
    {
        $order = $adapter->get_order();
        $fees = array();

        foreach ( $order->get_items('fee') as $fee_item )
        {
            $fee_taxes = $fee_item->get_taxes();
            $fee_taxes = $fee_taxes['total'];
            $fee_taxes = array_filter( $fee_taxes );

            if ( empty($fee_taxes) )
            {
                $result = array();
                $result['type'] = 'fee';
                $result['name'] = $fee_item->get_name();
                $result['unit'] = null;
                $result['number'] = null;
                $result['quantity'] = $fee_item->get_quantity();
                $result['description'] = $fee_item->get_name();
                $result['price_net'] = $fee_item->get_total();
                $result['price_gross'] = $fee_item->get_total();
                $result['tax_rate'] = null;
                $result['tax_name'] = null;
                $fees[] = $result;
            }

            foreach ( $fee_taxes as $tax_id => $tax_amount )
            {
                $wc_tax = WC_Tax::_get_tax_rate($tax_id);
                $tax_name = $wc_tax['tax_rate_name'];
                $tax_rate = $wc_tax['tax_rate'];
                $price_net = round( $tax_amount / ($tax_rate / 100), 4 );
                $price_gross = round( $price_net + $tax_amount, 4 );

                $result = array();
                $result['type'] = 'fee';
                $result['name'] = $fee_item->get_name();
                $result['unit'] = null;
                $result['number'] = null;
                $result['quantity'] = $fee_item->get_quantity();
                $result['description'] = $fee_item->get_name();
                $result['price_net'] = $price_net;
                $result['price_gross'] = $price_gross;
                $result['tax_rate'] = $tax_rate;
                $result['tax_name'] = $tax_name;
                $fees[] = $result;
            }
        }

        return $fees;
    }

    /**
     * Create all discount items for this invoice.
     *
     * @param  WR_Order_Adapter $adapter
     * @return array
     */
    public function make_discounts( WR_Order_Adapter $adapter )
    {
        $order = $adapter->get_order();
        $tax_rates = array();
        $discounts = array();
        $tax_rates[0] = array();
        $tax_rates[0]['rate'] = 0;
        $tax_rates[0]['name'] = null;
        $discounts[0] = 0;

        foreach ($order->get_taxes() as $tax)
        {
            $rate_id = $tax->get_rate_id();
            $wc_tax = WC_Tax::_get_tax_rate($rate_id);
            $tax_rate = array();
            $tax_rate['name'] = $wc_tax['tax_rate_name'];
            $tax_rate['rate'] = $wc_tax['tax_rate'];
            $tax_rates[ $rate_id ] = $tax_rate;
            $discounts[ $rate_id ] = 0;
        }

        $coupons = array();
        $names = array();

        foreach ( $order->get_items('coupon') as $coupon_item )
        {
            $coupon = array();
            $coupon['name'] = $coupon_item->get_name();
            $coupon['code'] = $coupon_item->get_code();
            $names[] = $coupon_item->get_name();
            $coupons[] = $coupon;
        }

        $names = join(', ', $names);

        /*foreach ($order->get_items() as $product_item)
        {
            $product_taxes = $product_item->get_taxes();
            $product_rates = $product_taxes['subtotal'];
            $product_rates = array_filter($product_rates);
            $product_rates = array_flip($product_rates);
            $product_rate = array_shift($product_rates);
            $product_rate = !empty($product_rate) ? $product_rate : 0;

            $total = $product_item->get_total();
            $subtotal = $product_item->get_subtotal();
            $discount = $total - $subtotal;
            $discounts[$product_rate] += $discount;
        }*/

        $results = array();

        foreach ($discounts as $rate_id => $discount)
        {
            if ($discount == 0) continue;

            $tax_rate = $tax_rates[$rate_id]['rate'];
            $tax_name = $tax_rates[$rate_id]['name'];
            $price_net = $discount;
            $price_gross = $discount * ( (100 + $tax_rate) / 100);

            $result = array();
            $result['type'] = 'discount';
            $result['name'] = "Rabatt".(!empty($names) ? " ({$names})" : '');
            $result['unit'] = null;
            $result['number'] = null;
            $result['quantity'] = 1;
            $result['description'] = 'Rabatt';
            $result['price_net'] = $price_net;
            $result['price_gross'] = $price_gross;
            $result['tax_rate'] = $tax_rate;
            $result['tax_name'] = $tax_name;
            $results[] = $result;

        }

        return $results;
    }

}

endif;
