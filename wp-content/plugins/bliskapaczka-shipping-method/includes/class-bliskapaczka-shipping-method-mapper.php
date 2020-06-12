<?php

/**
 * Bliskapaczka Core class
 */
class Bliskapaczka_Shipping_Method_Mapper
{

    const OPERATORS_WITH_INSURANCE = ['FEDEX', 'DPD'];
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
        if ($data['operatorName'] === 'FEDEX') {
            $data['deliveryType'] = 'D2P';
            $data = $this->_prepareDestinationData($data, $order);
        }
        $data['parcel'] = [
            'dimensions' => $this->getParcelDimensions($helper, $settings)
        ];
        $data = $this->_prepareSenderData($data, $helper, $settings);

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
            $data['deliveryType'] = 'P2D';
        }
        $data['parcel'] = [
            'dimensions' => $this->getParcelDimensions($helper, $settings)
        ];
        unset($data['destinationCode']);
        $data = $this->_prepareSenderData($data, $helper, $settings);
        $data = $this->_prepareDestinationData($data, $order);
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
        if (!empty($shippingAddress['address_2'])) {
            $buildingNumber = explode('/', $shippingAddress['address_2'])[0];
            $flatNumber = explode('/', $shippingAddress['address_2'])[1];
            $street = $shippingAddress['address_1'];
        } else {
            $buildingNumber = $this->getBuildingNumber($shippingAddress['address_1']);
            $flatNumber = $this->getFlatNumber($shippingAddress['address_1']);
            $street = implode(' ', explode(' ', $shippingAddress['address_1'], -1));
        }
        $data['receiverStreet'] = $street;
        $data['receiverBuildingNumber'] = $buildingNumber;
        $data['receiverFlatNumber'] = $flatNumber;
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
        $numbers = explode('/', substr(strrchr($address, ' '), 1));
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
        $numbers = explode('/', substr(strrchr($address, ' '), 1));
        if (isset($numbers[1])) {
            return $numbers[1];
        }
        return '';
    }

    /**
     * @param $data
     * @param Bliskapaczka_Shipping_Method_Helper $helper
     *
     * @return mixed
     * @throws \Bliskapaczka\ApiClient\Exception
     */
    public function prepareCod($data, WC_Order $order)
    {
        $data['codValue'] = $order->get_total();

        return $data;
    }


    /**
     * Add incurance if needed
     * @param $order
     * @param $data
     *
     * @return array
     */
    public function prepareInsuranceDataIfNeeded($data, WC_Order $order)
    {
        if (in_array($data['operatorName'], self::OPERATORS_WITH_INSURANCE)) {
            $data['parcel']['insuranceValue'] = $order->get_total();
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
            $data['codPayoutBankAccountNumber'] = str_replace(
                ' ', '', $settings[$helper::BANK_ACCOUNT_NUMBER]
            );
        }
        return $data;
    }

    /**
     * Prepare data for pickup.
     *
     * @param \Bliskapaczka_Map_Shipping_Method $bliskapaczka
     * @param string[] $orders_numbers Array of shipping numbers from bliskapaczka API
     *
     * @return array
     */
    public function prepareDataForPickup(\Bliskapaczka_Map_Shipping_Method $bliskapaczka, array $orders_numbers)
    {
		// Default pickup are next day.
    	$day_of_week = intval(date("N"));
    	$days = 1;
		// If today is Monday or Saturday than order the picup on Monday.
    	if ( $day_of_week >= 5 && $day_of_week < 7 ) {
    		$days = (7 - $day_of_week) + 1;
    	}

        $pickup_day = (new \DateTime())->modify(sprintf('+%d days', $days));

        return ['orderNumbers' => $orders_numbers,
                'pickupWindow' => [
                	'date' => $pickup_day->format('Y-m-d'),
                    'timeRange' => [
                        'from' => '08:00',
                        'to' => '16:00'
                    ]
                ],
				'pickupAddress' => [
					'street'         => $bliskapaczka->settings[ \Bliskapaczka_Shipping_Method_Helper::SENDER_STREET ],
					'buildingNumber' => $bliskapaczka->settings[ \Bliskapaczka_Shipping_Method_Helper::SENDER_BUILDING_NUMBER ],
					'flatNumber'     => $bliskapaczka->settings[ \Bliskapaczka_Shipping_Method_Helper::SENDER_FLAT_NUMBER ],
					'city'           => $bliskapaczka->settings[ \Bliskapaczka_Shipping_Method_Helper::SENDER_CITY ],
					'postCode'       => $bliskapaczka->settings[ \Bliskapaczka_Shipping_Method_Helper::SENDER_POST_CODE ],
				]
    		];
    }
}
