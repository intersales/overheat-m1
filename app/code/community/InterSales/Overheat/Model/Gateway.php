<?php
/**
 * Gateway model
 *
 * @category   InterSales
 * @package    InterSales_Overheat
 * @author     Daniel Rose <dr@intersales.de>
 */
class InterSales_Overheat_Model_Gateway {
    protected $_apiUrl = 'https://app.overheat.it/plugin-api/v1/';

    /**
     * Retrieve tracking code for given validate code
     *
     * @param string $validateCode
     * @return string
     */
    public function getTrackingCode($validateCode) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_apiUrl . 'getTrackingCode' . DS . $validateCode);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $trackingCode = curl_exec($ch);

        if(!curl_errno($ch)) {
            $info = curl_getinfo($ch);

            if($info['http_code'] != 200) {
                $trackingCode = '';
            }
        }

        curl_close($ch);

        return $trackingCode;
    }
}