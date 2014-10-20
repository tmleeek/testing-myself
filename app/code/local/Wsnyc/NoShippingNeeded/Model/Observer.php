<?php

class Wsnyc_NoShippingNeeded_Model_Observer {

    public function checkIfShippingMethodStepIsIgnored($observer) {
        if(!Mage::helper('noshippingneeded')->checkIfShippingMethodStepShouldBeIgnored()) {
            return;
        }
        $controller = $observer->getControllerAction();
        $responseBody = Mage::helper('core')->jsonDecode($controller->getResponse()->getBody());
        $responseBody['goto_section'] = 'payment';
        $method = 'noshippingneeded_noshippingneeded';
        $controller->getOnepage()->saveShippingMethod($method);
        $controller->getOnepage()->getQuote()->collectTotals()->save();
        $controller->getResponse()->setBody(Mage::helper('core')->jsonEncode($responseBody));

    }

}
