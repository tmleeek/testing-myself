<?php

class Magestore_Ajaxcart_Block_Product_Bundle extends Magestore_Ajaxcart_Block_Product_View
{
	public function getStartFormHtml(){
		if ($bundleBlock = $this->getLayout()->getBlock('product.info.bundle')){
			$html  = '<div style="display:none">';
			$html .= $bundleBlock->toHtml();
			$html .= '</div>';
			return $html;
		}
		return parent::getStartFormHtml();
	}
}