<?php

/**
 * Observer model
 *
 * @category   InterSales
 * @package    InterSales_Overheat
 * @author     Daniel Rose <dr@intersales.de>
 */
class InterSales_Overheat_Model_Observer
{
    /**
     * Include tracking code
     *
     * @param Varien_Event_Observer $observer
     */
    public function includeTrackingCode(Varien_Event_Observer $observer)
    {
        if ($observer && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            $block = $observer->getBlock();

            if ($block instanceof Mage_Page_Block_Html_Head)
            {
                $overheatTrackingCodeBlock = Mage::app()->getLayout()->createBlock('core/text', 'overheat_tracking_code');
                $overheatTrackingCodeBlock->setText(Mage::helper('intersales_overheat')->getTrackingCode());

                $block->setChild('overheat_tracking_code', $overheatTrackingCodeBlock);
            }
        }
        return $observer;
    }

    public function coreBlockAbstractToHtmlAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Core_Block_Abstract $myBlock */
        if ($observer && ($myBlock = $observer->getBlock()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            $myName = $myBlock->getNameInLayout();
            $myClass = get_class($myBlock);
            $myTemplate = $myBlock->getTemplate();
            $myType = $myBlock->getType();
            $myTransport = $observer->getTransport();
            Mage::log("Observer.php:42: Block rendering: '" . $myName . "' - '" . $myTemplate . "' - '" . $myType . "' - '" . $myClass . "'.", Zend_Log::DEBUG, 'intersales_overheat.log');
            // This could be used to add markers into the html for css selecting, etc.
            //            if ($myTransport)
            //            {
            //                $myHtml = $myTransport->getHtml();
            //                if ($myHtml)
            //                {
            //                    $myTransport->setHtml($helper->filterBlock($myBlock, $myHtml));
            //                }
            //            }
            // this can be probably optimized by putting the name and type in a lookup table/hashmap
            if ($myName == "product.info" && is_a($myBlock, "Mage_Catalog_Block_Product_View")) // Product View
            {
                /** @var Mage_Catalog_Block_Product_View $myBlock */
                $helper->addProduct($myBlock->getProduct(), array('css_identifier' => '.product-view'));
            }
            else if ($myName == "product_list" && is_a($myBlock, "Mage_Catalog_Block_Product_List")) // Category view
            {
                /** @var Mage_Catalog_Block_Product_List $myBlock */
                $productCollection = $myBlock->getLoadedProductCollection();
                $listIdentifier = 'product_list_' . Mage::registry('current_category')->getName();
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.category-products'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myName == "search_result_list" && is_a($myBlock, "Mage_Catalog_Block_Product_List")) // Search view
            {
                /** @var Mage_Catalog_Block_Product_List $myBlock */
                $productCollection = $myBlock->getLoadedProductCollection();
                $listIdentifier = 'product_list_search';
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.category-products'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myType == "reports/product_viewed" && is_a($myBlock, "Mage_Reports_Block_Product_Viewed"))
            {
                /** @var Mage_Reports_Block_Product_Viewed $myBlock */
                $productCollection = $myBlock->getItemsCollection();
                $listIdentifier = 'product_list_recently_viewed';
                Mage::log(__METHOD__ . ": Recently Viewed: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.block-viewed'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myType == "reports/product_compared" && is_a($myBlock, "Mage_Reports_Block_Product_Compared"))
            {
                /** @var Mage_Reports_Block_Product_Compared $myBlock */
                $productCollection = $myBlock->getItemsCollection();
                $listIdentifier = 'product_list_recently_compared';
                Mage::log(__METHOD__ . ": Recently compared: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.block-compared'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myType == "catalog/product_compare_sidebar" && is_a($myBlock, "Mage_Catalog_Block_Product_Compare_Sidebar"))
            {
                /** @var Mage_Catalog_Block_Product_Compare_Sidebar $myBlock */
                $productCollection = $myBlock->getItems();
                $listIdentifier = 'product_list_compare_sidebar';
                Mage::log(__METHOD__ . ": Comparing sidebar: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.block-compare'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myType == "wishlist/customer_sidebar" && is_a($myBlock, "Mage_Wishlist_Block_Customer_Sidebar"))
            {
                /** @var Mage_Wishlist_Block_Customer_Sidebar $myBlock */
                $productCollection = $myBlock->getWishlistItems();
                $listIdentifier = 'product_list_wishlist_sidebar';
                Mage::log(__METHOD__ . ": Wishlist sidebar: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $item)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.block-wishlist'));
                    $helper->addProduct($item->getProduct(), null, $listIdentifier);
                }
            }
            else if ($myType == "wishlist/customer_wishlist" && is_a($myBlock, "Mage_Wishlist_Block_Customer_Wishlist"))
            {
                /** @var Mage_Wishlist_Block_Customer_Wishlist $myBlock */
                $productCollection = $myBlock->getWishlistItems();
                $listIdentifier = 'product_list_wishlist';
                Mage::log(__METHOD__ . ": Wishlist: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $item)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.my-wishlist'));
                    $helper->addProduct($item->getProduct(), null, $listIdentifier);
                }
            }
            else if ($myType == "checkout/cart" && is_a($myBlock, "Mage_Checkout_Block_Cart"))
            {
                /** @var Mage_Checkout_Block_Cart $myBlock */
                $productCollection = $myBlock->getItems();
                $listIdentifier = 'product_list_cart';
                Mage::log(__METHOD__ . ": Cart: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $item)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.cart-table'));
                    $helper->addProduct($item->getProduct(), null, $listIdentifier);
                }
            }
            else if ($myType == "checkout/cart_minicart" && is_a($myBlock, "Mage_Checkout_Block_Cart_Minicart"))
            {
                /** @var Mage_Checkout_Block_Cart_Minicart $myBlock */
                $productCollection = $myBlock->getItems();
                $listIdentifier = 'product_list_minicart';
                Mage::log(__METHOD__ . ": MiniCart: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $item)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.minicart-wrapper'));
                    $helper->addProduct($item->getProduct(), null, $listIdentifier);
                }
            }
            else if ($myType == "catalog/product_list_related" && is_a($myBlock, "Mage_Catalog_Block_Product_List_Related"))
            {
                /** @var Mage_Catalog_Block_Product_List_Related $myBlock */
                $productCollection = $myBlock->getItems();
                $listIdentifier = 'product_list_related';
                Mage::log(__METHOD__ . ": Related: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '.block-related'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
            else if ($myType == "catalog/product_list_upsell" && is_a($myBlock, "Mage_Catalog_Block_Product_List_Upsell"))
            {
                /** @var Mage_Catalog_Block_Product_List_Upsell $myBlock */
                $productCollection = $myBlock->getItemCollection();
                $listIdentifier = 'product_list_upsell';
                Mage::log(__METHOD__ . ": Upsell: " . count($productCollection), Zend_Log::DEBUG, 'intersales_overheat.log');
                foreach ($productCollection as $product)
                {
                    $helper->createProductList($listIdentifier, array('css_identifier' => '#upsell-product-table'));
                    $helper->addProduct($product, null, $listIdentifier);
                }
            }
        }
        return $observer;
    }

    public function checkoutCartProductAddAfter(Varien_Event_Observer $observer)
    {
        return $observer;
        // probably isnt needed anymore, because of qty change observer
        if ($observer && ($product = $observer->getProduct()) && ($quoteItem = $observer->getQuoteItem()))
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            Mage::log(__METHOD__ . ": Add to cart: " . $product->getId() . " - " . $quoteItem->getQtyToAdd(), Zend_Log::DEBUG, 'intersales_overheat.log');
            $helper->addProductToCollection($product, 'cart', array('quantity' => $quoteItem->getQtyToAdd()));
        }
        return $observer;
    }

    public function salesQuoteRemoveItem(Varien_Event_Observer $observer)
    {
        if ($observer && ($quoteItem = $observer->getQuoteItem()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            Mage::log(__METHOD__ . ": Remove from cart: " . $quoteItem->getProduct()->getId() . " - " . $quoteItem->getQty(), Zend_Log::DEBUG, 'intersales_overheat.log');
            $helper->removeProductFromCollection($quoteItem->getProduct(), 'cart', array('quantity' => $quoteItem->getQty()));
        }
        return $observer;
    }

    public function salesQuoteItemQtySetAfter(Varien_Event_Observer $observer)
    {
        if ($observer && ($item = $observer->getItem()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            Mage::log(__METHOD__ . ": Update cartitem: " . $item->getProduct()->getId() . " - " . $item->getQty() . " from " . $item->getOrigData('qty') . " (" . $item->dataHasChangedFor('qty') . ")", Zend_Log::DEBUG, 'intersales_overheat.log');
            if ($item->dataHasChangedFor('qty'))
            {
                $diff = $item->getQty() - $item->getOrigData('qty');
                if ($diff > 0) // increased
                {
                    $helper->addProductToCollection($item->getProduct(), 'cart', array('quantity' => $diff));
                }
                else if ($diff < 0) // decreased
                {
                    $helper->removeProductFromCollection($item->getProduct(), 'cart', array('quantity' => abs($diff)));
                }
            }
        }
        return $observer;
    }

    public function checkoutApplyCouponToProduct(Varien_Event_Observer $observer)
    {
        if ($observer && ($controller = $observer->getControllerAction()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            $couponCode = $controller->getRequest()->getParam('coupon_code');
            $remove = $controller->getRequest()->getParam('remove');
            if (!$remove && $couponCode)
            {
                Mage::log(__METHOD__ . ": Coupon added: " . $couponCode, Zend_Log::DEBUG, 'intersales_overheat.log');
                //$coupon = Mage::getModel('salesrule/coupon')->load($couponCode, 'code');
                $quote = Mage::getSingleton('checkout/session')->getQuote();
                $discountTotal = $quote->getSubtotal() - $quote->getSubtotalWithDiscount();
                $helper->addCoupon($couponCode, $discountTotal, true);
            }
        }
        return $observer;
    }

    public function checkoutOnepageControllerSuccessAction(Varien_Event_Observer $observer)
    {
        if ($observer && ($orderIds = $observer->getOrderIds()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            foreach ($orderIds as $orderId)
            {
                $order = Mage::getModel('sales/order')->load($orderId);
                if ($order && $order->getId())
                {
                    Mage::log(__METHOD__ . ": OrderSuccess: " . $orderId, Zend_Log::DEBUG, 'intersales_overheat.log');
                    $helper->checkoutSuccess($order);
                }
            }
        }
        return $observer;
    }

    public function newsletterSubscriberSaveCommitAfter(Varien_Event_Observer $observer)
    {
        Mage::log(__METHOD__ . ": a1", Zend_Log::DEBUG, 'intersales_overheat.log');
        if ($observer && ($event = $observer->getEvent()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            Mage::log(__METHOD__ . ": a", Zend_Log::DEBUG, 'intersales_overheat.log');
            /** @var Mage_Newsletter_Model_Subscriber $subscriber */
            $subscriber = $event->getDataObject();
            if ($subscriber && $subscriber->getId())
            {
                Mage::log(__METHOD__ . ": b", Zend_Log::DEBUG, 'intersales_overheat.log');
                $data = $subscriber->getData();
                $statusChange = $subscriber->getIsStatusChanged();

                if ($data['subscriber_status'] == "1" && $statusChange == true)
                {
                    Mage::log(__METHOD__ . ": c", Zend_Log::DEBUG, 'intersales_overheat.log');
                    /** @var InterSales_Overheat_Helper_Data $helper */
                    $helper = Mage::helper('intersales_overheat');
                    Mage::log(__METHOD__ . ": Newsletter added for: " . $subscriber->getEmail(), Zend_Log::DEBUG, 'intersales_overheat.log');
                    $helper->newsletterSubscribe($subscriber);
                }
            }
        }
        return $observer;
    }

    public function controllerActionPostdispatchContactsIndexPost(Varien_Event_Observer $observer)
    {
        if ($observer && ($controller = $observer->getControllerAction()) && Mage::helper('intersales_overheat')->hasTrackingCode() && Mage::helper('intersales_overheat')->isActive())
        {
            /** @var InterSales_Overheat_Helper_Data $helper */
            $helper = Mage::helper('intersales_overheat');
            $comment = $controller->getRequest()->getPost('comment');
            $email = $controller->getRequest()->getPost('email');
            Mage::log(__METHOD__ . ": Support Email: " . $comment . " - " . $email, Zend_Log::DEBUG, 'intersales_overheat.log');
            $helper->supportEmail($comment, array('email' => $email));
        }
        return $observer;
    }
}