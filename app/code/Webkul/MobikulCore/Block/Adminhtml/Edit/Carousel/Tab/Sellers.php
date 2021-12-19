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

namespace Webkul\MobikulCore\Block\Adminhtml\Edit\Carousel\Tab;

/**
 * Class Sellers
 */
class Sellers extends \Magento\Backend\Block\Template
{
    /**
     * $_request
     */
    protected $request;

    /**
     * $carouselRepository
     */
    protected $carouselRepository;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Backend\Block\Template\Context $context,
        \Webkul\MobikulCore\Api\CarouselRepositoryInterface $carouselRepository,
        array $data = []
    ) {
        $this->request = $request;
        $this->carouselRepository = $carouselRepository;
        parent::__construct($context, $data);
    }

    protected function _prepareLayout()
    {
        $this->setTemplate("carousel/sellers.phtml");
    }

    public function getCarouselSellersJson()
    {
        $carouselImages = "";
        $carouselId = $this->request->getParam("id");
        $carousel = $this->carouselRepository->getById($carouselId);
        $carouselSellers = $carousel->getSellerIds();
        return $carouselSellers;
    }
}
