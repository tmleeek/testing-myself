<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_QandA_Edit_Form extends Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswerscategory_Edit_Form {

    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        return parent::_prepareLayout();
    }


    protected function _prepareForm() {
        $model = Mage::registry('wsnyc_questionsanswers');

        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('category_id' => $this->getRequest()->getParam('category_id'))),
            'method' => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => Mage::helper('wsnyc_questionsanswers')->__('Category Information'),
            'class' => 'fieldset-wide',
        ));

        if ($model->getId()) {
            $fieldset->addField('category_id', 'hidden', array(
                'name' => 'category_id',
            ));
        }

        $fieldset->addField('parent_id', 'select', array(
            'name' => 'parent_id',
            'label' => Mage::helper('wsnyc_questionsanswers')->__('Category Parent'),
            'title' => Mage::helper('wsnyc_questionsanswers')->__('Category Parent'),
            'values' => Mage::getModel('wsnyc_questionsanswers/source_category')->toOptionArray(),
            'required' => true,
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('wsnyc_questionsanswers')->__('Name'),
            'title' => Mage::helper('wsnyc_questionsanswers')->__('Name'),
            'required' => true,
        ));

        $fieldset->addField('seosubfooter_show', 'select', array(
            'name' => 'seosubfooter_show',
            'label' => Mage::helper('seosubfooter')->__('Show SEO Subfooter'),
            'title' => Mage::helper('seosubfooter')->__('Show SEO Subfooter'),
            'required' => true,
            'values' => array(1 => Mage::helper('seosubfooter')->__('Yes'), 0 => Mage::helper('seosubfooter')->__('No'))
        ));
        
        $fieldset->addField('image', 'image', array(
            'label' => Mage::helper('wsnyc_questionsanswers')->__('Index Category Image'),
            'required' => false,
            'name' => 'image',
            'note' => '240px wide'
        ));

        $fieldset->addField('wide_image', 'image', array(
            'label' => Mage::helper('wsnyc_questionsanswers')->__('Wide Category Image'),
            'required' => false,
            'name' => 'wide_image',
            'note' => '960px wide'
        ));

        $fieldset->addField('seosubfooter_text', 'editor', array(
            'name' => 'seosubfooter_text',
            'label' => Mage::helper('cms')->__('SEO Subfooter Text'),
            'title' => Mage::helper('cms')->__('SEO Subfooter Text'),
            'style' => 'height:26em;',
            'config'    => Mage::getSingleton('cms/wysiwyg_config')->getConfig(),
        ));



        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return $this;
    }

}
