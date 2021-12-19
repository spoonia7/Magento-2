<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Controller\Adminhtml\Featuredcategories;

/**
 * Class Categorytree
 */
class Categorytree extends \Webkul\MobikulCore\Controller\Adminhtml\Featuredcategories
{
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        try {
            $parentCategory = $this->categoryRepository->get($data["parentCategoryId"]);
            $parentChildren = $parentCategory->getChildren();
            $parentChildIds = explode(",", $parentChildren);
            $index = 0;
            foreach ($parentChildIds as $parentChildId) {
                $categoryData = $this->categoryRepository->get($parentChildId);
                if ($this->categoryResourceModel->getChildrenCount($parentChildId) > 0) {
                    $result[$index]["counting"] = 1;
                } else {
                    $result[$index]["counting"] = 0;
                }
                $result[$index]["id"] = $categoryData["entity_id"];
                $result[$index]["name"] = $categoryData->getName();
                $categories = [];
                $categoryIds = "";
                if (isset($data["categoryIds"])) {
                    $categories  = explode(",", $data["categoryIds"]);
                    $categoryIds = $data["categoryIds"];
                }
                if ($categoryIds && in_array($categoryData["entity_id"], $categories)) {
                    $result[$index]["check"] = 1;
                } else {
                    $result[$index]["check"] = 0;
                }
                ++$index;
            }
            $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
        } catch (\Exception $e) {
            $this->getResponse()->representJson($this->jsonHelper->jsonEncode("[]"));
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed("Webkul_MobikulCore::featuredcategories");
    }
}
