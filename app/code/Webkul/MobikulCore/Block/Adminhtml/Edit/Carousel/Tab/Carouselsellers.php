<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Block\Adminhtml\Edit\Carousel\Tab;

use Magento\Backend\Block\Template\Context;

/**
 * Class Carouselsellers
 */
class Carouselsellers extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $type;
    protected $status;
    protected $visibility;
    protected $moduleManager;
    protected $sellerFactory;

    public function __construct(
        Context $context,
        \Magento\Catalog\Model\Product\Type $type,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Product\Visibility $visibility,
        \Webkul\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $status,
        array $data = []
    ) {
        $this->type = $type;
        $this->status = $status;
        $this->visibility = $visibility;
        $this->moduleManager = $moduleManager;
        $this->sellerFactory = $sellerFactory;
        $this->storeManager = $context->getStoreManager();
        parent::__construct($context, $backendHelper, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setUseAjax(true);
        $this->setDefaultDir("ASC");
        $this->setDefaultSort("seller_id");
        $this->setId("carousel_seller_grid");
        $this->setSaveParametersInSession(true);
    }

    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam("store", 0);
        return $this->storeManager->getStore($storeId);
    }

    protected function _prepareCollection()
    {
        $storeID       = $this->storeManager->getStore()->getStoreId(); 
        $store = $this->_getStore();
        $collection = $this->sellerFactory->create()->getCollection()->addFieldToFilter('store_id', ['eq' => $storeID]);
        $joinConditions = 'main_table.seller_id = customer_grid_flat.entity_id';
        $collection->getSelect()->join(
            ['customer_grid_flat'],
            $joinConditions,
            [
                'seller_name' => 'name',
                'seller_email' => 'email'
            ]
            );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            "triggers",
            [
                "type" => "checkbox",
                "align" => "center",
                "header" => __("Select"),
                "index" => "seller_id",
                "sortable" => false
            ]
        );
        $this->addColumn(
            "seller_id",
            [
                "type" => "number",
                "align" => "center",
                "width" => "30px",
                "index" => "seller_id",
                "header" => __("Seller ID")
            ]
        );

        $this->addColumn(
            "filename",
            [
                "type" => "image",
                "align" => "center",
                "index" => "logo_pic",
                "header" => __("Seller Logo"),
                "escape" => true,
                "filter" => false,
                "renderer" => "Webkul\MobikulCore\Block\Adminhtml\SellerThumbnail",
                "sortable" => false
            ]
        );
        
        $this->addColumn(
            "name",
            [
                "index" => "seller_name",
                "align" => "left",
                "header" => __("Seller Name")
            ]
        );

        $this->addColumn(
            "email",
            [
                "index" => "seller_email",
                "align" => "left",
                "header" => __("Seller Email")
            ]
        );

        $this->addColumn(
            "status",
            [
                "header" => __("Status"),
                "index" => "is_seller",
                "type" => "options",
                "options" => $this->status->getOptionArray()
            ]
        );

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl("*/*/sellerGridData", ["_current"=>true]);
    }
}
