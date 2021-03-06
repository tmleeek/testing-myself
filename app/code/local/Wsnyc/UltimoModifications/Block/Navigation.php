<?php
/**
 * Catalog navigation
 */

class Wsnyc_UltimoModifications_Block_Navigation extends Infortis_Ultimo_Block_Navigation
{

    /**
     * Render category to html
     *
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @param boolean Whether ot not this item is first, affects list item class
     * @param boolean Whether ot not this item is outermost, affects list item class
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @param boolean Whether ot not to add on* attributes to list item
     * @return string
     */
    protected function _renderCategoryMenuItemHtml($category, $level = 0, $isLast = false, $isFirst = false,
        $isOutermost = false, $outermostItemClass = '', $childrenWrapClass = '', $noEventAttributes = false, $staticBlock = false)
    {
  

        if (!$category->getIsActive()) {
            return '';
        }
 
        $html = array();

        // get all children
        if (Mage::helper('catalog/category_flat')->isEnabled()) {
            $children = (array)$category->getChildrenNodes();
            $childrenCount = count($children);
        } else {
            $children = $category->getChildren();
            $childrenCount = $children->count();
        }
        $hasChildren = ($children && $childrenCount);

        // select active children
        $activeChildren = array();
        foreach ($children as $child) {
            if ($child->getIsActive()) {
                $activeChildren[] = $child;
            }
        }
        $activeChildrenCount = count($activeChildren);
        $hasActiveChildren = ($activeChildrenCount > 0);

        // prepare list item html classes
        $classes = array();
        $classes[] = 'level' . $level;
        $classes[] = 'nav-' . $this->_getItemPosition($level);
        if ($this->isCategoryActive($category)) {
            $classes[] = 'active';
        }
        $linkClass = '';
        if ($isOutermost && $outermostItemClass) {
            $classes[] = $outermostItemClass;
            $linkClass = ' class="'.$outermostItemClass.'"';
        }
        if ($isFirst) {
            $classes[] = 'first';
        }
        if ($isLast) {
            $classes[] = 'last';
        }
        if ($hasActiveChildren) {
            $classes[] = 'parent';
        }
		
		//NEW: add special class if level == 1 and menu is not an accordion.
		if ($this->_isAccordion == FALSE && $level == 1) {
			$classes[] = 'item';
		}
		
        // prepare list item attributes
        $attributes = array();
        if (count($classes) > 0) {
            $attributes['class'] = implode(' ', $classes);
        }
        if ($hasActiveChildren && !$noEventAttributes) {
             $attributes['onmouseover'] = 'toggleMenu(this,1)';
             $attributes['onmouseout'] = 'toggleMenu(this,0)';
        }

        // assemble list item with attributes
        $htmlLi = '<li';
        foreach ($attributes as $attrName => $attrValue) {
            $htmlLi .= ' ' . $attrName . '="' . str_replace('"', '\"', $attrValue) . '"';
        }
        $htmlLi .= '>';
        $html[] = $htmlLi;

        $html[] = '<a href="'.$this->getCategoryUrl($category).'"'.$linkClass.'>';
        $html[] = '<span>' . $this->escapeHtml($category->getName()) . '</span>';
        $html[] = '</a>';

        // render children if no static block
        if($staticBlock) {  
            $html[] = '<ul class="level' . $level . '">';
            if(($staticBlock == 'block_header_nav_home') ||
               ($staticBlock ==  'block_header_nav_fabric'))
            {
                 $task = 'task';
                 $taskstart = 1;
                 $type = 'material_type2';
                 $typename = 'MATERIAL';

                 if($staticBlock == 'block_header_nav_home')
                 {
                     $task = 'task';
                     $taskstart = 11;
                     $type = 'fabric_type2';
                     $typename = 'FABRIC';
                 }

                $html[] = '<div class="nested-container"><div class="grid12-3">';
                $html[] = '<h4>SHOP BY CATEGORY</h4><ul class="childcategories">';
                $count = 0;
                foreach ($activeChildren as $child)
                {
                    if($count < 8)
                    {
                        $html[] = '<li><a href="'.$this->getCategoryUrl($child). '">';
                        $html[] = $this->escapeHtml($child->getName()) . '</a></li>';
                    }
                    $count++;
                }  

  
                $html[] = '</ul></div>';
                $html[] = '<div class="grid12-3"><h4>SHOP BY TASK</h4>';
                $html[] = '<ul class="childcategories">';
                $taskList = $this->getOptionsList($task, $taskstart);
                foreach ($taskList as $item)
                {
                    $html[] = '<li><a href="'.$this->getCategoryFilterUrl($category, $task, $item['option_id'],  $item['url_key']) . '">';
                    $html[] = $this->escapeHtml($item['value']) . '</a></li>';
                }  


                $html[] = '</ul></div>';
                $html[] = '<div class="grid12-3"><h4>SHOP BY ' . $typename . ' TYPE</h4>';
                $html[] = '<ul class="childcategories">';
                $taskList = $this->getOptionsList($type);
                foreach ($taskList as $item)
                {


                    $html[] = '<li><a href="'.$this->getCategoryFilterUrl($category, $type, $item['option_id'], $item['url_key']) . '">';
                    $html[] = $this->escapeHtml($item['value']) . '</a></li>';
                }  


                $html[] = '</ul></div>';
                $html[] = '<div class="grid12-3">';
                $block = Mage::getModel('cms/block')->setStoreId(Mage::app()->getStore()->getId())->load('block_header_nav_whatsnew');
                if ($block->getIsActive()) {
                    $html[] = '<div class="custom-block">' . $this->getChildHtml('block_header_nav_whatsnew') . '</div>';
                }
                $html[] = '<div class="custom-block">' . $this->getChildHtml('block_header_nav_featured') . '</div>';
                $html[] = '</div></div>';


            }
            else
            {

               $html[] = $this->getChildHtml($staticBlock);

            }
            $html[] = '</ul>';
        } else {
            $htmlChildren = '';
            $j = 0;
            foreach ($activeChildren as $child) {
                $htmlChildren .= $this->_renderCategoryMenuItemHtml(
                    $child,
                    ($level + 1),
                    ($j == $activeChildrenCount - 1),
                    ($j == 0),
                    false,
                    $outermostItemClass,
                    $childrenWrapClass,
                    $noEventAttributes
                );
                $j++;
            }
            if (!empty($htmlChildren)) {

                            //NEW: add opener if menu is used as accordion.
                            if ($this->_isAccordion == TRUE)
                                    $html[] = '<span class="opener">&nbsp;</span>';

                if ($childrenWrapClass) {
                    $html[] = '<div class="' . $childrenWrapClass . '">';
                }
                $html[] = '<ul class="level' . $level . '">';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
                if ($childrenWrapClass) {
                    $html[] = '</div>';
                }
            }
        }

        $html[] = '</li>';

        $html = implode("\n", $html);
        return $html;
    }

    /**
     * Render category to html
     *
     * @deprecated deprecated after 1.4
     * @param Mage_Catalog_Model_Category $category
     * @param int Nesting level number
     * @param boolean Whether ot not this item is last, affects list item class
     * @return string
     */
    public function drawItem($category, $level = 0, $last = false)
    {
        return $this->_renderCategoryMenuItemHtml($category, $level, $last);
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        if (Mage::getSingleton('catalog/layer')) {
            return Mage::getSingleton('catalog/layer')->getCurrentCategory();
        }
        return false;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getCurrentCategoryPath()
    {
        if ($this->getCurrentCategory()) {
            return explode(',', $this->getCurrentCategory()->getPathInStore());
        }
        return array();
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Category $category
     * @return string
     */
    public function drawOpenCategoryItem($category) {
        $html = '';
        if (!$category->getIsActive()) {
            return $html;
        }

        $html.= '<li';

        if ($this->isCategoryActive($category)) {
            $html.= ' class="active"';
        }

        $html.= '>'."\n";
        $html.= '<a href="'.$this->getCategoryUrl($category).'"><span>'.$this->htmlEscape($category->getName()).'</span></a>'."\n";

        if (in_array($category->getId(), $this->getCurrentCategoryPath())){
            $children = $category->getChildren();
            $hasChildren = $children && $children->count();

            if ($hasChildren) {
                $htmlChildren = '';
                foreach ($children as $child) {
                    $htmlChildren.= $this->drawOpenCategoryItem($child);
                }

                if (!empty($htmlChildren)) {
                    $html.= '<ul>'."\n"
                            .$htmlChildren
                            .'</ul>';
                }
            }
        }
        $html.= '</li>'."\n";
        return $html;
    }

    /**
     * Render categories menu in HTML
     *
	 * @param bool Add opener if menu is used as accordion.
     * @param int Level number for list item class to start from
     * @param string Extra class of outermost list items
     * @param string If specified wraps children list in div with this class
     * @return string
     */
    public function renderCategoriesMenuHtml($isAccordion = FALSE, $level = 0, $outermostItemClass = '', $childrenWrapClass = '', $staticBlocks = array())
    {
        //NEW: save additional attribute
        $this->_isAccordion = $isAccordion;
		
        $activeCategories = array();
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return '';
        }                       
        
        $html = '';
        $j = 0;
        foreach ($activeCategories as $category) {                        
            
            $staticBlock = false;
            
            if(isset($staticBlocks[$category->getId()])) {
                $staticBlock = $staticBlocks[$category->getId()];                
            } 
            
            $html .= $this->_renderCategoryMenuItemHtml(
                $category,
                $level,
                ($j == $activeCategoriesCount - 1),
                ($j == 0),
                true,
                $outermostItemClass,
                $childrenWrapClass,
                true,
                $staticBlock
            );
            $j++;
        }

        return $html;
    }

    private function getOptionsList($optionname, $start = 1)
    {
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
     
        $query = "SELECT value, eav_attribute_option.option_id, ecommerceteam_sln_attribute_options.url_key  FROM  eav_attribute 
                    JOIN  eav_attribute_option  ON 
                         eav_attribute.attribute_id = eav_attribute_option.attribute_id
                    JOIN  eav_attribute_option_value 
                     ON  eav_attribute_option.option_id = eav_attribute_option_value.option_id
                    LEFT JOIN  ecommerceteam_sln_attribute_options
                     ON  ecommerceteam_sln_attribute_options.attribute_id = eav_attribute.attribute_id AND
                     ecommerceteam_sln_attribute_options.option_id = eav_attribute_option.option_id 
WHERE eav_attribute.attribute_code = '$optionname' AND eav_attribute_option.sort_order >= $start
    ORDER BY eav_attribute_option.sort_order ASC LIMIT 8";

       $results = $readConnection->fetchAll($query);

        return $results;
    }



}
