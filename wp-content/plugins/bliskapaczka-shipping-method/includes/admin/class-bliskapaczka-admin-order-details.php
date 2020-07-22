<?php
/**
 * Extends WooCommerce Admin Order Detail
 *
 * @author      Bliskapaczka.pl
 * @category    Admin
 * @package     Bliskapaczka/Admin/Order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Operations in WooCommerce admin order details view
 */
class Bliskapaczka_Admin_Order_Details {

	/**
	 * Print information from bliskapaczka.pl shipping
	 *
	 * @param WC_Order $order Woo Commerce order.
	 */
	public function shipping_details( WC_Order $order ) {

		// We take a shiping method, and check for bliskapaczka data.
		$method_id = $this->helper()->getWCShipingMethodId( $order );

		if ( Bliskapaczka_Courier_Shipping_Method::get_identity() !== $method_id 
				&& Bliskapaczka_Map_Shipping_Method::get_identity() !== $method_id
				&& 'flexible_shipping' !== $method_id
			) {
			return; // Shiping aren't from bliskapaczka, so we do nothing.
		}
		
		$bliska_order_id = $order->get_meta( '_bliskapaczka_order_id', true, 'view' );
	
		if ( empty( $bliska_order_id ) ) {
			return; // We didn't have bliska paczka data
		}

		$waybill_urls = $this->helper()->getWaybillUrls( $bliska_order_id );
		$waybill_html = '';

		if ( is_array( $waybill_urls ) && count( $waybill_urls ) > 0 ) {
			$waybill_html = '<tr><td colspan="2">';

			foreach ( $waybill_urls as $url ) {
				$waybill_html .= sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $url ), esc_html( __( 'Waybill', 'bliskapaczka-shipping-method' ) ) );
			}

			$waybill_html .= '</td></tr>';
		}

		$content =
			'<div class="bliskapaczka-wc-admin-shipping-details">
				<h3>Bliskapaczka.pl - ' . esc_html( $order->get_shipping_method() ) . '</h3>
				<table>
					<tr>
						<td>' . esc_html( __( 'Order number', 'bliskapaczka-shipping-method' ) ) . ':</td>
						<td>' . esc_html( $bliska_order_id ) . '</td>
					</tr>
					' . $waybill_html . '
				</table>
			</div>';

		echo wp_kses_post( $content );
	}

	/**
	 * Returns Bliskapaczaka helper
	 *
	 * @return Bliskapaczka_Shipping_Method_Helper
	 */
	private function helper() {
		return Bliskapaczka_Shipping_Method_Helper::instance();
	}

}
/**
 * Detailed info about point to point delivery
 *
 * @param array $formatted_meta Formatted order metadata.
 */
function bliskapaczka_order_meta_data_view( $formatted_meta ) {
	foreach ( $formatted_meta as $obj ) {
		if ( '_bliskapaczka_posCode' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Point code', 'bliskapaczka-shipping-method' ) );
		}
		if ( '_bliskapaczka_posOperator' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Operator', 'bliskapaczka-shipping-method' ) );
		}
		if ( '_bliskapaczka_posInfo' === $obj->display_key ) {
			$obj->display_key = esc_html( __( 'Point info', 'bliskapaczka-shipping-method' ) );
		}
	}

	return $formatted_meta;
}
add_filter( 'woocommerce_order_item_get_formatted_meta_data', 'bliskapaczka_order_meta_data_view', 10, 2 );
