<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advd_Helper_Email_Filter extends Mage_Core_Model_Email_Template_Filter
{
    public function filter($text)
    {
        $html = parent::filter($text);

        if (strpos($html, '<style>') !== false) {
            $cssToInline = Mage::helper('advd/email_cssToInline');

            $cssToInline->setHtml($html);
            $html = $cssToInline->emogrify();
        }

        return $html;
    }
}