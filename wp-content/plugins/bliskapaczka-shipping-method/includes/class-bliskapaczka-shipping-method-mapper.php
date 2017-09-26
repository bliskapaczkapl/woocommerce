<?php

/**
 * Bliskapaczka Core class
 */
class Bliskapaczka_Shipping_Method_Mapper
{

	/**
     * Prepare mapped data for Bliskapaczka API
     *
     * @param WC_Order $order
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @return array
     */
    public function getData(WC_Order $order, Bliskapaczka_Shipping_Method_Helper $helper)
    {
        $data = [];

        $shippingAddress = $order->get_address('shipping');
        $billingAddress = $order->get_address('billing');

        $data['receiverFirstName'] = $shippingAddress['first_name'];
        $data['receiverLastName'] = $shippingAddress['last_name'];
        $data['receiverPhoneNumber'] = $helper->telephoneNumberCeaning($billingAddress['phone']);
        $data['receiverEmail'] = $billingAddress['email'];

        foreach ( $order->get_items( array( 'shipping' ) ) as $item_id => $item ) {
            $shipping_item_id = $item_id;
        }

        $data['operatorName'] = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator' );
        $data['destinationCode'] = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posCode' );

        $data['parcel'] = [
            'dimensions' => $this->getParcelDimensions($helper)
        ];

        // $data = $this->_prepareSenderData($data, $helper);

        return $data;
    }

    /**
     * Get parcel dimensions in format accptable by Bliskapaczka API
     *
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @return array
     */
    protected function getParcelDimensions(Bliskapaczka_Shipping_Method_Helper $helper)
    {
        return $helper->getParcelDimensions();
    }

    /**
     * Prepare sender data in fomrat accptable by Bliskapaczka API
     *
     * @param array $data
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareSenderData($data, Bliskapaczka_Shipping_Method_Helper $helper)
    {
        if (Mage::getStoreConfig($helper::SENDER_EMAIL)) {
            $data['senderEmail'] = Mage::getStoreConfig($helper::SENDER_EMAIL);
        }

        if (Mage::getStoreConfig($helper::SENDER_FIRST_NAME)) {
            $data['senderFirstName'] = Mage::getStoreConfig($helper::SENDER_FIRST_NAME);
        }

        if (Mage::getStoreConfig($helper::SENDER_LAST_NAME)) {
            $data['senderLastName'] = Mage::getStoreConfig($helper::SENDER_LAST_NAME);
        }

        if (Mage::getStoreConfig($helper::SENDER_PHONE_NUMBER)) {
            $data['senderPhoneNumber'] = $helper->telephoneNumberCeaning(
                Mage::getStoreConfig($helper::SENDER_PHONE_NUMBER)
            );
        }

        if (Mage::getStoreConfig($helper::SENDER_STREET)) {
            $data['senderStreet'] = Mage::getStoreConfig($helper::SENDER_STREET);
        }

        if (Mage::getStoreConfig($helper::SENDER_BUILDING_NUMBER)) {
            $data['senderBuildingNumber'] = Mage::getStoreConfig($helper::SENDER_BUILDING_NUMBER);
        }

        if (Mage::getStoreConfig($helper::SENDER_FLAT_NUMBER)) {
            $data['senderFlatNumber'] = Mage::getStoreConfig($helper::SENDER_FLAT_NUMBER);
        }

        if (Mage::getStoreConfig($helper::SENDER_POST_CODE)) {
            $data['senderPostCode'] = Mage::getStoreConfig($helper::SENDER_POST_CODE);
        }

        if (Mage::getStoreConfig($helper::SENDER_CITY)) {
            $data['senderCity'] = Mage::getStoreConfig($helper::SENDER_CITY);
        }

        return $data;
    }

}
