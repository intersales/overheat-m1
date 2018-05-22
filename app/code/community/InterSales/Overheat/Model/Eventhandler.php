<?php
/**
 * DESCRIPTION
 *
 * @category   InterSales
 * @package    InterSales_overheat
 * @author     Robert Meyer <rm@intersales.de>
 */

class InterSales_Overheat_Model_Eventhandler extends Mage_Core_Model_Abstract
{
    const CACHE_TAG = 'intersales_overheat_eventhandler';
    protected $_cacheTag = 'intersales_overheat_eventhandler';

    protected function _construct()
    {
        $this->_init('intersales_overheat/eventhandler');
    }

    // TODO: recursion is not necessary as only 1 level of OR'ed expressions make sense
    protected function recursiveMatchSelectors($blockInfo, $selectors)
    {
        $rslt = true;

        if (is_array($selectors) && !empty($selectors))
        {
            foreach ($selectors as $key => $selector)
            {
                if (is_numeric($key)) // OR correlation - we are at a root
                {
                    $rslt = self::recursiveMatchSelectors($blockInfo, $selector);
                    if ($rslt)
                    {
                        return true;
                    }
                }
                else // we are at a leaf
                {
                    if (is_array($selector)) // AND correlation
                    {
                        foreach ($selector as $singleSelector)
                        {
                            if (!isset($blockInfo[$key]) || !preg_match($singleSelector, $blockInfo[$key]))
                            {
                                return false;
                            }
                        }
                    }
                    else // simple correlation
                    {
                        if (!isset($blockInfo[$key]) || !preg_match($selector, $blockInfo[$key]))
                        {
                            return false;
                        }
                    }
                }
            }

        }

        return $rslt;
    }

    public function doesMatchBlock($block)
    {

        $rslt = true;

        $blockSelectorString = $this->getBlockSelector();
        if ($blockSelectorString)
        {
            $blockInfo = array();
            $blockInfo['name'] = $block->getNameInLayout();
            $blockInfo['class'] = get_class($block);
            $blockInfo['template'] = $block->getTemplate();
            $blockInfo['type'] = $block->getType();
            $parentBlock = $block->getParentBlock();
            if ($parentBlock)
            {
                $blockInfo['parent_name'] = $parentBlock->getNameInLayout();
                $blockInfo['parent_class'] = get_class($parentBlock);
                $blockInfo['parent_template'] = $parentBlock->getTemplate();
                $blockInfo['parent_type'] = $parentBlock->getType();
            }

            $blockSelectors = Mage::helper('core')->jsonDecode($blockSelectorString);
            $rslt = self::recursiveMatchSelectors($blockInfo, $blockSelectors);
        }

        return $rslt;
    }

    protected function matchNode($node, $key, $value)
    {
        $rslt = false;

        foreach ($node->attributes as $curAttribute)
        {
            if ($curAttribute->name() == $key)
            {
                if (preg_match($value, $curAttribute->value()))
                {
                    $rslt = true;
                }
                break;
            }
        }

        return $rslt;
    }

    // TODO: recursion is not necessary as only 1 level of OR'ed expressions make sense
    protected function recursiveMatchElements($domDocument, $domXPath, $selectors)
    {
        /** @var DOMXPath $domXPath */
        $rslt = array();

        if (is_array($selectors) && !empty($selectors))
        {
            $isLeaf = true;
            foreach ($selectors as $key => $selector)
            {
                if (is_numeric($key)) // OR correlation - we are at a root
                {
                    $isLeaf = false;
                    /** @var array $tempRslt */
                    $tempRslt = self::recursiveMatchElements($domDocument, $domXPath, $selector);
                    $rslt = array_merge($rslt, $tempRslt);
                }
            }
            if ($isLeaf)
            {
                $queryString = '';
                if (is_array($selectors['xpath'])) // AND correlation
                {
                    implode(' and ', $selectors['xpath']);
                }
                else
                {
                    $queryString = $selectors['xpath'];
                }

                $domNodeList = $domXPath->query($queryString);
                if (isset($selectors['attributes'])) // check attributes
                {
                    foreach ($domNodeList as $node) // loop through all nodes selected by xpath and check with attribute selectors
                    {
                        $doesAttributesMatch = false; // bool if some attribute rule applies
                        $isLeaf = true;
                        foreach ($selectors['attributes'] as $key => $attribute)
                        {
                            if (is_numeric($key)) // OR correlation - we are at a root
                            {
                                $isLeaf = false;
                                $broke = false;

                                foreach ($attribute as $innerKey => $attributeValue)
                                {
                                    if (!self::matchNode($node, $key, $attribute))
                                    {
                                        $broke = true;
                                        break;
                                    }
                                }

                                if (!$broke) // All matched
                                {
                                    $doesAttributesMatch = true;
                                    break;
                                }
                            }
                        }
                        if ($isLeaf)
                        {
                            $doesAttributesMatch = true;
                            foreach ($selectors['attributes'] as $key => $attribute)
                            {
                                if (!self::matchNode($node, $key, $attribute))
                                {
                                    $doesAttributesMatch = false;
                                    break;
                                }
                            }
                        }
                        if ($doesAttributesMatch)
                        {
                            array_push($rslt, $node);
                        }
                    }
                }
                else // if no attributes set just take all xpath results
                {
                    foreach ($domNodeList as $node)
                    {
                        array_push($rslt, $node);
                    }
                }
            }

        }

        return $rslt;
    }

    public function matchElements($domDocument, $domXPath)
    {
        return self::recursiveMatchSelectors($domDocument, $domXPath, Mage::helper('core')->jsonDecode($this->getCssSelector()));
    }
}