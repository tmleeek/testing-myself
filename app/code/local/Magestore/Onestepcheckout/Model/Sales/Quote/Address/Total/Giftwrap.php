<?php
class Magestore_Onestepcheckout_Model_Sales_Quote_Address_Total_Giftwrap extends Mage_Sales_Model_Quote_Address_Total_Abstract {
	public function collect(Mage_Sales_Model_Quote_Address $address) {

        $_helper = Mage::helper('onestepcheckout');
		$active = $_helper->enableGiftWrap();
		if (!$active)
		{
			return;
		} 
        $session = Mage::getSingleton('checkout/session');
        $giftWrapType = $session->getData('onestepcheckout_giftwrap_type');
        $giftwrap = $session->getData('onestepcheckout_giftwrap');
        if($giftWrapType == 'holiday_') $giftwrap = $session->getData('onestepcheckout_holiday_giftwrap');
        Mage::getSingleton('core/session')->unsetData('giftWrapType');

        if(!$giftwrap){
            return $this;
        } else {
            Mage::getSingleton('core/session')->setData('giftWrapType' , $giftWrapType);
        }
		
		$items = $address->getAllItems();
		if (!count($items)) {
			return $this;
		}
        
		$giftwrapType = $_helper->getGiftwrapType();
		$giftwrapAmount = $_helper->getGiftwrapAmount();
        if($giftWrapType == 'holiday_')
        {
            $giftwrapType = $_helper->getHolidayGiftwrapType();
            $giftwrapAmount = $_helper->getHolidayGiftwrapAmount();
        }
     
        $wrapTotal = 0;


        if($giftwrapType == 1) {
            foreach ($items as $item){
				if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                // if ($item->getHasChildren() && $item->isShipSeparately()) {
                    // foreach ($item->getChildren() as $child) {
                        // if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            // $freeBoxes += $item->getQty() * $child->getQty();
                        // }
						// elseif ($item->getFreeShipping()) {
							// $freeBoxes += $item->getQty();
						// }
                    // }
                // } 
                $wrapTotal += $giftwrapAmount * ($item->getQty());
            }
        }
        else {
            $wrapTotal = $giftwrapAmount;
        }	
//        var_dump($wrapTotal);
//		$giftwrapAmount = Mage::helper('giftwrap')->giftwrapAmount(null,$address_id);		
		$session->setData('onestepcheckout_giftwrap_amount', $wrapTotal);
		$address->setOnestepcheckoutGiftwrapAmount($wrapTotal);		
		$address->setGrandTotal($address->getGrandTotal() + $address->getOnestepcheckoutGiftwrapAmount());
		$address->setBaseGrandTotal($address->getBaseGrandTotal() + $address->getOnestepcheckoutGiftwrapAmount());	
		return $this;
	}

	public function fetch(Mage_Sales_Model_Quote_Address $address) 
	{
        $session = Mage::getSingleton('checkout/session');
        $giftWrapType = $session->getData('onestepcheckout_giftwrap_type');
		$_helper = Mage::helper('onestepcheckout');
		$active = $_helper->enableGiftWrap();
		if (!$active)
		{
			return;
		} 
		$amount = $address->getOnestepcheckoutGiftwrapAmount();

		$title = Mage::helper('sales')->__('Gift Wrap');
        if($giftWrapType == 'holiday_') $title = Mage::helper('sales')->__('Holiday Gift Wrap');

		if ($amount!=0) {
			$address->addTotal(array(
					'code'=>$this->getCode(),
					'title'=>$title,
					'value'=>$amount
			));
		}
		return $this;
	}

//	public function getCheckoutSession() {
//		return Mage::getSingleton('checkout/session');
//	}
//
//	public function getQuote() {
//		return $this->getCheckoutSession()->getQuote();
//	}
//
//	public function getProductName($itemId) {
//		$item = $this->getQuote()->getItemById($itemId);
//		if($item){
//			return $item->getProduct()->getName();
//		}else{
//			return '';
//		}		
//	}
}
