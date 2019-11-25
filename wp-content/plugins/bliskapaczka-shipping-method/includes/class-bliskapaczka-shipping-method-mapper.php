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
    public function getData(WC_Order $order, Bliskapaczka_Shipping_Method_Helper $helper, $settings)
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

        $data['deliveryType'] = 'P2P';

        $data['operatorName'] = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator' );
        $data['destinationCode'] = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posCode' );

        $data['parcel'] = [
            'dimensions' => $this->getParcelDimensions($helper, $settings)
        ];

        $data = $this->_prepareSenderData($data, $helper, $settings);
        $operators  = json_decode($helper->getOperatorsForWidget());
        foreach ($operators as $operator) {
            if ($operator->operator === $data['operatorName']) {
                $data['codValue'] = $operator->cod;
                break;
            }
        }

        return $data;
    }

    /**
     * @param WC_Order $order
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @param $settings
     *
     * @return array
     */
    public function getDataForCourier(WC_Order $order, Bliskapaczka_Shipping_Method_Helper $helper, $settings)
    {
        $data = $this->getData($order, $helper, $settings);
        foreach ( $order->get_items( array( 'shipping' ) ) as $item_id => $item ) {
            $shipping_item_id = $item_id;
        }
        $data['operatorName'] = wc_get_order_item_meta( $shipping_item_id, '_bliskapaczka_posOperator' );
        $data['deliveryType'] = 'D2D';
        if ($data['operatorName'] === 'POCZTA') {
            $data['deliveryType'] = 'P2P';
        }
        $data['parcel'] = [
            'dimensions' => $this->getParcelDimensions($helper, $settings)
        ];
        unset($data['destinationCode']);
        $data = $this->_prepareSenderData($data, $helper, $settings);
        $data = $this->_prepareDestinationData($data, $order);
        $data = $this->_prepareCODIfNeeded($data, $order, $helper);
        return $data;
    }

    /**
     * @param $data
     * @param WC_Order $order
     *
     * @return mixed
     */
    protected function _prepareDestinationData($data, WC_Order $order)
    {
        $shippingAddress = $order->get_address('shipping');
        $data['receiverStreet'] = $shippingAddress['address_1'];
        $data['receiverBuildingNumber'] = $this->getBuildingNumber($shippingAddress['address_2']);
        $data['receiverFlatNumber'] = $this->getFlatNumber($shippingAddress['address_2']);
        $data['receiverPostCode'] = $shippingAddress['postcode'];
        $data['receiverCity'] = $shippingAddress['city'];
        return $data;
    }

    /**
     * @param $address
     *
     * @return string
     */
    protected function getBuildingNumber($address)
    {
        $numbers = explode('/', $address);
        if (isset($numbers[0])) {
            return $numbers[0];
        }
        return '';
    }

    /**
     * @param $address
     *
     * @return string
     */
    protected function getFlatNumber($address)
    {
        $numbers = explode('/', $address);
        if (isset($numbers[1])) {
            return $numbers[1];
        }
        return '';
    }

    /**
     * @param $data
     * @param WC_Order $order
     * @param $helper
     *
     * @return mixed
     */
    protected function _prepareCODIfNeeded($data, WC_Order $order, Bliskapaczka_Shipping_Method_Helper $helper)
    {
        if ($order->get_payment_method() === 'cod') {
            $codValue = $helper->getCODValueForOperator($data['operatorName']);
            $data['codValue'] = $codValue;
        }
        return $data;
    }
    /**
     * Get parcel dimensions in format accptable by Bliskapaczka API
     *
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @return array
     */
    protected function getParcelDimensions(Bliskapaczka_Shipping_Method_Helper $helper, $settings)
    {
        return $helper->getParcelDimensions($settings);
    }

    /**
     * Prepare sender data in fomrat accptable by Bliskapaczka API
     *
     * @param array $data
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     * @param array $settings
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _prepareSenderData($data, Bliskapaczka_Shipping_Method_Helper $helper, $settings)
    {
        if ($settings[$helper::SENDER_EMAIL]) {
            $data['senderEmail'] = $settings[$helper::SENDER_EMAIL];
        }

        if ($settings[$helper::SENDER_FIRST_NAME]) {
            $data['senderFirstName'] = $settings[$helper::SENDER_FIRST_NAME];
        }

        if ($settings[$helper::SENDER_LAST_NAME]) {
            $data['senderLastName'] = $settings[$helper::SENDER_LAST_NAME];
        }

        if ($settings[$helper::SENDER_PHONE_NUMBER]) {
            $data['senderPhoneNumber'] = $helper->telephoneNumberCeaning(
                $settings[$helper::SENDER_PHONE_NUMBER]
            );
        }

        if ($settings[$helper::SENDER_STREET]) {
            $data['senderStreet'] = $settings[$helper::SENDER_STREET];
        }

        if ($settings[$helper::SENDER_BUILDING_NUMBER]) {
            $data['senderBuildingNumber'] = $settings[$helper::SENDER_BUILDING_NUMBER];
        }

        if ($settings[$helper::SENDER_FLAT_NUMBER]) {
            $data['senderFlatNumber'] = $settings[$helper::SENDER_FLAT_NUMBER];
        }

        if ($settings[$helper::SENDER_POST_CODE]) {
            $data['senderPostCode'] = $settings[$helper::SENDER_POST_CODE];
        }

        if ($settings[$helper::SENDER_CITY]) {
            $data['senderCity'] = $settings[$helper::SENDER_CITY];
        }

        if ($settings[$helper::BANK_ACCOUNT_NUMBER]) {
            $data['codPayoutBankAccountNumber'] = $settings[$helper::BANK_ACCOUNT_NUMBER];
        }
        return $data;
    }

}
