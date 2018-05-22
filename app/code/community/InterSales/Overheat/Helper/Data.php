<?php
/**
 * Default helper
 *
 * @category   InterSales
 * @package    InterSales_Overheat
 * @author     Daniel Rose <dr@intersales.de>
 */ 
class InterSales_Overheat_Helper_Data extends Mage_Core_Helper_Abstract {
    const XML_PATH_GENERAL_ACTIVE = 'intersales_overheat/general/active';
    const XML_PATH_GENERAL_TRACKING_CODE = 'intersales_overheat/general/tracking_code';

    const CUSTOMJS_TYPE_ONLOAD = 0;
    const CUSTOMJS_TYPE_ONCLICK = 1;
    const CUSTOMJS_TYPE_ONSUBMIT = 2;

    const SESSION_COMMANDS_VARNAME = 'overheat_commands';
    const SESSION_CUSTOMJS_VARNAME = 'overheat_customjs';
    const SESSION_PRODUCTS_VARNAME = 'overheat_products';

    /**
     * Has given or current store a tracking code
     *
     * @param null $store
     * @return bool
     */
    public function isActive($store = null) {
        return Mage::getStoreConfigFlag(self::XML_PATH_GENERAL_ACTIVE, $store);
    }

    /**
     * Has given or current store a tracking code
     *
     * @param null $store
     * @return bool
     */
    public function hasTrackingCode($store = null) {
        return $this->getTrackingCode($store) != '';
    }

    /**
     * Retrieve tracking code for given or current store
     *
     * @param null $store
     * @return mixed
     */
    public function getTrackingCode($store = null) {
        return Mage::getStoreConfig(self::XML_PATH_GENERAL_TRACKING_CODE, $store);
    }

    public function filterBlock($block, $html)
    {
//        $eventhandlerCollection = Mage::getResourceModel('intersales_overheat/eventhandler_collection')->load();
//
//        foreach ($eventhandlerCollection as $eventhandler)
//        {
//            if ($eventhandler->doesMatchBlock($block))
//            {
//                $domDocument = new DOMDocument();
//                $domDocument->loadHTML($html);
//                $domDocument->preserveWhiteSpace = false;
//
//                $domXPath = new DOMXPath($domDocument);
//
//                $matchElements = $eventhandler->matchElements($domDocument, $domXPath);
//
//                if (!empty($matchElements))
//                {
//                    $defaultParams = array();
//                    Mage::dispatchEvent('intersales_overheat_event_match', array('eventhandler' => $eventhandler, 'block' => $block, 'dom_document' => $domDocument, 'matched_elements' => $matchElements, 'params' => $defaultParams));
//                    Mage::dispatchEvent('intersales_overheat_event_match_' . $eventhandler->getObserverName(), array('eventhandler' => $eventhandler, 'block' => $block, 'dom_document' => $domDocument, 'matched_elements' => $matchElements, 'params' => $defaultParams));
//
//                    $html = $domDocument->saveHTML();
//                }
//            }
//        }
//        return $html;
    }

    public function clearStacks()
    {
        // clear session commandstack and customjsstack
        Mage::getSingleton('core/session')->setData(self::SESSION_COMMANDS_VARNAME, array());
        Mage::getSingleton('core/session')->setData(self::SESSION_CUSTOMJS_VARNAME, array());
        Mage::getSingleton('core/session')->setData(self::SESSION_PRODUCTS_VARNAME, array('default' => array()));
    }

    public function getCommands()
    {
        // return commandstack
        $currentCommands = Mage::getSingleton('core/session')->getData(self::SESSION_COMMANDS_VARNAME);
        return $currentCommands;
    }

    public function getCustomJs()
    {
        // return customjsstack
        $currentCustomJs = Mage::getSingleton('core/session')->getData(self::SESSION_CUSTOMJS_VARNAME);
        return $currentCustomJs;
    }

    public function getProducts()
    {
        // return products
        $currentProducts = Mage::getSingleton('core/session')->getData(self::SESSION_PRODUCTS_VARNAME);
        return $currentProducts;
    }

    public function setCommands($commands)
    {
        // set commandstack
        Mage::getSingleton('core/session')->setData(self::SESSION_COMMANDS_VARNAME, $commands);
    }

    public function setCustomJs($customJs)
    {
        // set customjsstack
        Mage::getSingleton('core/session')->setData(self::SESSION_CUSTOMJS_VARNAME, $customJs);
    }

    public function setProducts($products)
    {
        // set products
        Mage::getSingleton('core/session')->setData(self::SESSION_PRODUCTS_VARNAME, $products);
    }

    public function pushCommand($command, $parameters)
    {
        // push into session commandstack
        $currentCommands = $this->getCommands();
        if (!$currentCommands)
        {
            $currentCommands = array();
        }
        array_push($currentCommands, array('command' => $command, 'parameters' => $parameters));
        $this->setCommands($currentCommands);
    }

    public function pushCustomJs($javascriptString, $type, $opt = null)
    {
        // push into session customjsstack
        $currentCustomJs = Mage::getSingleton('core/session')->getData(self::SESSION_CUSTOMJS_VARNAME);
        if (!$currentCustomJs)
        {
            $currentCustomJs = array();
        }
        array_push($currentCustomJs, array('js' => $javascriptString, 'type' => $type, 'opt' => $opt));
        $this->setCustomJs($currentCustomJs);
    }

    public function encodeCommand($command, $parameters)
    {
        // create js for command
        return 'overheat(\'' . $command . '\', ' . json_encode($parameters) . ');';
    }

    public function encodeCustomJs($javascriptString, $type, $opt = null)
    {
        // create js for custom js
        $rslt = '';
        switch ($type)
        {
            default:
            case self::CUSTOMJS_TYPE_ONLOAD:
                $rslt = $javascriptString;
                break;
            case self::CUSTOMJS_TYPE_ONCLICK:
                $rslt = 'jQuery(\'' . $opt['css'] . '\').click(function(){' . $javascriptString . '});';
                break;
            case self::CUSTOMJS_TYPE_ONSUBMIT:
                $rslt = 'jQuery(\'' . $opt['css'] . '\').submit(function(){' . $javascriptString . '});';
                break;
        }
        return $rslt;
    }

    public function getStackJs()
    {
        // get commands + customjs, encode them and return as string
        $rslt = 'jQuery(function(){';

        $rslt .= 'console.log(\'Overheat: Executing buffered commands...\');';

        foreach ($this->getProducts() as $key => $productList)
        {
            if ($key == 'default')
            {
                foreach ($productList as $product)
                {
                    $this->pushCommand('product_view', $product);
                }
            }
            else
            {
                $this->pushCommand('product_view', $productList);
            }
        }
        foreach ($this->getCommands() as $command)
        {
            $rslt .= $this->encodeCommand($command['command'], $command['parameters']);
        }
        foreach ($this->getCustomJs() as $customJs)
        {
            $rslt .= $this->encodeCustomJs($customJs['js'], $customJs['type'], $customJs['opt']);
        }

        $rslt .= 'console.log(\'Overheat: Executed buffered commands\');';

        $rslt .= '});';
        return $rslt;
    }

    protected function _createProductInfo($product, $opt = null)
    {
        /** @var Mage_Catalog_Model_Product $product */

        // create array with product info
        $currentCategory = Mage::registry('current_category');
        $currentCategoryName = $currentCategory ? $currentCategory->getName() : 'None';
        $rslt = array('current_category' => $currentCategoryName);

        $rslt['item'] = array();
        $rslt['item']['sku'] = $product->getSku();
        $rslt['item']['internal_id'] = $product->getId();
        $rslt['item']['name'] = $product->getName();
        $rslt['item']['price'] = $product->getPrice();
        $rslt['item']['image_url'] = (String)Mage::helper('catalog/image')->init($product, 'thumbnail');
        $rslt['item']['currency'] = Mage::app()->getStore()->getCurrentCurrencyCode();

        if ($opt)
        {
            $rslt = array_merge($rslt, $opt);
        }

        return $rslt;
    }

    protected function _createListProductInfo($product, $opt = null)
    {
        /** @var Mage_Catalog_Model_Product $product */

        // create array with product info
        $rslt = array();

        $rslt['sku'] = $product->getSku();
        $rslt['internal_id'] = $product->getId();

        if ($opt)
        {
            $rslt = array_merge($rslt, $opt);
        }

        return $rslt;
    }

    public function createProductList($listIdentifier, $opt = null)
    {
        // push new member to session productobject
        $curProducts = $this->getProducts();

        if (!isset($curProducts[$listIdentifier]))
        {
            $curProducts[$listIdentifier] = array('items' => array());
        }

        if ($opt)
        {
            $curProducts[$listIdentifier] = array_merge($curProducts[$listIdentifier], $opt);
        }

        if (!isset($curProducts[$listIdentifier]['current_category']))
        {
            $currentCategory = Mage::registry('current_category');
            $curProducts[$listIdentifier]['current_category'] = $currentCategory ? $currentCategory->getName() : 'None';
        }

        if (!isset($curProducts[$listIdentifier]['current_page']))
        {
            $curProducts[$listIdentifier]['current_page'] = $listIdentifier;
        }

        $this->setProducts($curProducts);
    }

    public function addProduct($product, $opt = null, $listIdentifier = null)
    {
        // push product to session productobject
        $curProducts = $this->getProducts();
        if (!$listIdentifier)
        {
            $listIdentifier = 'default';
            if (!isset($curProducts['default']))
            {
                $curProducts['default'] = array();
            }
        }
        if ($listIdentifier == 'default')
        {
            $productData = $this->_createProductInfo($product,$opt);
            array_push($curProducts[$listIdentifier], $productData);
        }
        else
        {
            $productData = $this->_createListProductInfo($product,$opt);
            array_push($curProducts[$listIdentifier]['items'], $productData);
        }
        $this->setProducts($curProducts);
    }

    public function addProductToCollection($product, $collection, $opt = null)
    {
        // push command collection_add ...
        $commandParams = array('collection' => $collection, 'item' => $this->_createProductInfo($product));
        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }

        $this->pushCommand('collection_add', $commandParams);
    }

    public function removeProductFromCollection($product, $collection, $opt = null)
    {
        // push command collection_remove
        $commandParams = array(
            'collection' => $collection,
            'item' => $this->_createProductInfo($product)
        );
        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }

        $this->pushCommand('collection_remove', $commandParams);
    }

    public function addCoupon($code, $value, $isFlat, $opt = null)
    {
        // push command coupon
        $commandParams = array(
            'name' => $code,
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode()
        );

        if ($isFlat)
        {
            $commandParams['value'] = $value;
        }
        else
        {
            $commandParams['value_percent'] = $value;
        }

        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }

        $this->pushCommand('coupon', $commandParams);
    }

    protected function _createOrderInfo($order, $opt = null)
    {
        /** @var Mage_Sales_Model_Order $order */
        $rslt = array(
            'current_page' => 'order_success',
            'order_number' => $order->getIncrementId(),
            'customer_number' => $order->getCustomerId(),
            'total' => $order->getGrandTotal(),
            'currency' => Mage::app()->getStore()->getCurrentCurrencyCode(),
            'shipping_provider' => $order->getShippingMethod(),
            'shipping_costs' => $order->getShippingInclTax(),
            'ship_to_country' => $order->getShippingAddress()->getCountry(),
            'ship_to_zip' => $order->getShippingAddress()->getPostcode(),
            'payment_provider' => $order->getPayment()->getMethod()
        );

        if ($opt)
        {
            $rslt = array_merge($rslt, $opt);
        }

        return $rslt;
    }

    public function checkoutSuccess($order, $opt = null)
    {
        // push command checkout_success - pass shipping/billinginfos as well
        $commandParams = $this->_createOrderInfo($order, $opt);

        $this->pushCommand('checkout_success', $commandParams);
    }

    public function referal($opt = null)
    {
        // push command referal
        $commandParams = array('referer' => Mage::app()->getRequest()->getServer('HTTP_REFERER'));

        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }
        $this->pushCommand('referal', $commandParams);
    }

    public function newsletterSubscribe($subscriber, $opt = null)
    {
        /** @var Mage_Newsletter_Model_Subscriber $subscriber */
        // push command newsletter_subscribe
        $commandParams = array('email' => $subscriber->getEmail());

        $ownerId = Mage::getModel('customer/customer')
            ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
            ->loadByEmail($subscriber->getEmail())
            ->getId();
        if ($ownerId)
        {
            $customer = Mage::getModel('customer/customer')->load($ownerId);
        }
        else
        {
            $customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        /** @var Mage_Customer_Model_Customer $customer */
        if ($customer && $customer->getId())
        {
            $commandParams['customer_number'] = $customer->getId();
            $commandParams['salutation'] = $customer->getPrefix();
            $commandParams['firstname'] = $customer->getFirstname();
            $commandParams['lastname'] = $customer->getLastname();
            $commandParams['company'] = $customer->getCompany();
        }

        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }

        $this->pushCommand('newsletter_subscribe', $commandParams);
    }

    public function review($product, $opt = null)
    {
        // push command review
        // TODO
    }

    public function feedback($text, $product = null, $opt = null)
    {
        // push command feedback
        // TODO
    }

    public function supportEmail($comment, $opt = null)
    {
        $commandParams = array(
            'topic' => $comment
        );

        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $commandParams['customer_number'] = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $commandParams['email'] = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        }

        if ($opt)
        {
            $commandParams = array_merge($commandParams, $opt);
        }

        $this->pushCommand('support_email', $commandParams);
    }
}