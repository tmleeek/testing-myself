<?php
class Wsnyc_Homepagebanner_Block_Adminhtml_Banner_Edit_Form
	extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the inner form wrapper
	 */
	protected function _prepareForm() 
	{
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data'
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
	}

//    protected function _prepareLayout()
//    {
//        $return = parent::_prepareLayout();
//        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
//            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
//        }
//        return $return;
//    }
}
