<?php


namespace Zkood\CouponsSelling\Model;

use \Zkood\CouponsSelling\Model\CouponFactory;
use \Zkood\CouponsSelling\Api\CouponsRepositoryInterface;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Phrase;

class CouponsRepository implements CouponsRepositoryInterface
{
    /**
     * @var \Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Zkood\CouponsSelling\Model\CouponFactory
     */
    private $couponFactory;

    /**
     * @var \Zkood\CouponsSelling\Model\ResourceModel\Coupon
     */
    private $couponResourceModel;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    private $productRepository;
    private $request;
    private $priceCurrency;
    private $userContext;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Zkood\CouponsSelling\Service\SellerOptionService
     */
    private $sellerOptionService;

    /**
     * CouponsRepository constructor.
     * @param \Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory
     * @param \Zkood\CouponsSelling\Model\CouponFactory $couponFactory
     * @param \Zkood\CouponsSelling\Model\ResourceModel\Coupon $couponResourceModel
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Authorization\Model\CompositeUserContext $userContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Zkood\CouponsSelling\Service\SellerOptionService $sellerOptionService
     */
    public function __construct(
        \Zkood\CouponsSelling\Model\ResourceModel\Coupon\CollectionFactory $collectionFactory,
        \Zkood\CouponsSelling\Model\CouponFactory $couponFactory,
        \Zkood\CouponsSelling\Model\ResourceModel\Coupon $couponResourceModel,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Authorization\Model\CompositeUserContext $userContext,
      \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
    \Zkood\CouponsSelling\Service\SellerOptionService $sellerOptionService
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->couponFactory = $couponFactory;
        $this->couponResourceModel=$couponResourceModel;
        $this->productRepository = $productRepository;
        $this->request=$request;
        $this->priceCurrency=$priceCurrency;
        $this->userContext =$userContext;
        $this->customerRepository = $customerRepository;
        $this->sellerOptionService = $sellerOptionService;
    }

    /**
     * Retrieve list by page type, term, store, etc
     *
     * @return bool|\Zkood\CouponsSelling\Api\Data\CouponInterface[]
     */
    public function getCustomerList()
    {
        $customerId = 0 ;
        try {
            $customer =  $this->getCustomerFromToken();
            if($customer)
            {
                $customerId = $customer->getId();
            }
            //$collection = $this->collectionFactory->create();
            $collection = $this->couponFactory->create()->getCollection()
                ->addFieldToFilter('customer_id', ['eq' => $customerId]);

            /** @var \Zkood\CouponsSelling\Api\Data\CouponInterface[] $coupons */
            $coupons = [
                'coupons' => []
            ];
            foreach ($collection as $item) {
                $coupons['coupons'][] = $this->getItemData($item);
            }


            $result = [
                'coupons' => $coupons,
                'count'=> $collection->count(),
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            // to be changed later with better approach
            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();

        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Retrieve Coupon Details By Id
     *
     * @param  int $id
     * @return string

     */
    public function getById($id){
        //return $this->getList('entity_id',$id);
        return $this->couponFactory->create()->load($id);
    }

    /**
     * An endpoint to Redeem a coupon by coupon code.
     *
     * @return string
     */
    public function redeemCoupon(){

        /* Verify the request and token */
        //$this->verifyPostRequest();

        /*The endpoint should check the customer role, only the seller can use it.*/
        /*$customer = $this->getCustomerFromToken();

        if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode('Seller')->getId()) {

            throw new AuthorizationException(
                new Phrase(
                    'Selected Customer is not a seller'
                )
            );
        }*/

        /* 1- The endpoint will receive the coupon code and check its availability
           2- if available then mark it as redeemed and return an appropriate response with success status
           3-  otherwise it will return the failure message and error status.*/

        /*$couponCode = $this->request->getParam('coupon_code');

        if($entity = $this->checkAvailability($couponCode))
        {
            $entity->setIsRedeemed(1);
            $result = [
                'status'=>'Success',
                'coupon' => $entity,
                'count'=> $entity->count(),
                'total_number' => $entity->getSize(),
                'current_page' => $entity->getCurPage(),
                'last_page' => $entity->getLastPageNumber(),
            ];

            // to be changed later with better approach
            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();
        }
        else{
            $result = [
                'status'=>'Failed',
                'error'=>'entity does not exist',
                'coupon' => $entity,
                'count'=> $entity->count(),
                'total_number' => $entity->getSize(),
                'current_page' => $entity->getCurPage(),
                'last_page' => $entity->getLastPageNumber(),
            ];
            // to be changed later with better approach
            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();

        }*/
        $result = $this->processRequest();
        // to be changed later with better approach
        header("Content-Type: application/json; charset=utf-8");
        print_r(json_encode($result), false);
        die();

    }

    public function sellerCoupons(){
        try {
            $this->verifyGetRequest();
            $customer =  $this->getCustomerFromToken();
            if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode('Seller')->getId()) {
                return $result =[
                    'error' => 1,
                    'status' => 401,
                    'message' => 'Not Authorized'
                ];
            }
            $collection = $this->couponFactory->create()->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('seller_id', $customer->getId())
                ->addFieldToFilter('is_redeemed', 1)
                ->load();

            /** @var \Zkood\CouponsSelling\Api\Data\CouponInterface[] $coupons */
            $coupons = [
                'redeemedCoupons' => []
            ];
            $records = [];
            foreach ($collection as $item) {
                $records[] = $this->getItemData($item);
            }
            $coupons['redeemedCoupons']['records']= $records;
            $coupons['redeemedCoupons']['count'] = $collection->count();
            $coupons['redeemedCoupons']['total_number'] = $collection->getSize();
            $coupons['redeemedCoupons']['current_page'] = $collection->getCurPage();
            $coupons['redeemedCoupons']['last_page'] = $collection->getLastPageNumber();

            $result = [
                'coupons' => $coupons,
                'availableCouponsCount'=>$this->getAvailableCoupons()->count(),
                'expiredCouponsCount'=>$this->getExpiredCoupons()->count(),
                'totalCouponsCount'=>$this->getTotalCoupons()->count(),
            ];

            // to be changed later with better approach
            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();

        } catch (\Exception $e) {
            return false;
        }

    }

    /**
     * @param $item
     * @return array
     */
    public function getItemData($item)
    {
        $data = $item->getData();
        $customerId = $item->getData('customer_id');
        if(!$customerId)
        {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested customer doesn\'t exist'));
        }
        $customer = $this->customerRepository->getById($customerId);
        $data['customer_name']=$customer->getFirstName().' '.$customer->getLastName();
        $data['customer_email']=$customer->getEmail();
        return $data;
    }

    /**
 * @param $productId
 * @return mixed
 */
    /*public function getProductPrice($productId)
    {
        $product = $this->productRepository->getById($productId);
        return $product->getFinalPrice();
    }*/
    /**
     * @param $productId
     * @return mixed
     */
    /*ublic function getProductName($productId)
    {
        $product = $this->productRepository->getById($productId);
        return $product->getName();
    }*/
    public function getCurrentCurrencySymbol()
    {
        return $this->priceCurrency->getCurrencySymbol();
    }

        public function getAuthToken(){

        $token = false;

        $headers = [];
        foreach ($_SERVER as $name => $value)
        {
            if (substr($name, 0, 5) == 'HTTP_')
            {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        $authorizationBearer = '';

        if(isset($headers['Authorization'])) {
            $authorizationBearer = $headers['Authorization'];
        } else if(isset($headers['authorization'])) {
            $authorizationBearer = $headers['authorization'];
        } else {
            $authorizationBearer = "";
        }

        $authorizationBearerArr = explode(' ', $authorizationBearer);
        if(
            isset($authorizationBearerArr[0]) &&
            trim($authorizationBearerArr[0]) == 'Bearer' &&
            isset($authorizationBearerArr[1])
        ){
            $token = $authorizationBearerArr[1];
        }

        return $token;
    }

    /**
     * @throws NoSuchEntityException
     * @return mixed
     */
    public function getCustomerFromToken()
    {

        $customerId = $this->userContext->getUserId();
        if(!$customerId)
        {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested customer doesn\'t exist'));
        }
        $customer = $this->customerRepository->getById($customerId);
        return $customer;
    }

    /**
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     * @throws AuthorizationException
     * @return json | void
     */
    public function verifyPostRequest()
    {
        if ($this->request->getMethod() == "POST") {
            $customerToken = $this->getAuthToken();
            if (!$customerToken) {
                throw new AuthorizationException(
                    new Phrase(
                        'Consumer %consumer_id is not authorized to access %resources',
                        ['consumer_id' => '3', 'resources' => '4']
                    )
                );
            }
            $customer = $this->getCustomerFromToken();
            if (!$customer) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested customer doesn\'t exist'));
            }
            if (!$this->request->getParam('coupon_code')) {
                throw new \Exception(__("coupon code is not exist"));
            }
        }
        else
        {
            throw new \Exception(__("Invalid Request"));
        }
    }
    /**
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     * @throws AuthorizationException
     * @return json | void
     */
    public function verifyGetRequest()
    {
        if ($this->request->getMethod() == "GET") {
            $customerToken = $this->getAuthToken();
            if (!$customerToken) {
                throw new AuthorizationException(
                    new Phrase(
                        'Consumer  is not authorized to access this endpoint'
                    )
                );
            }
            $customer = $this->getCustomerFromToken();
            if (!$customer) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested customer doesn\'t exist'));
            }
        }
        else
        {
            throw new \Exception(__("Invalid Request"));
        }
    }

    /**
     * @param $couponCode
     * @return mixed
     */
    public function checkAvailability($couponCode)
    {
        $customer = $this->getCustomerFromToken();
         $entity =  $this->collectionFactory->create()
                ->addFieldToSelect('*')
                ->addFieldToFilter('seller_id', $customer->getId())
                ->addFieldToFilter('coupon_code', ['eq' => $couponCode])
                ->addFieldToFilter('valid_to', ['gt' => date("Y-m-d H:i:s")])
                ->addFieldToFilter('is_redeemed', 0)
                ->load()
                ->getFirstItem();
         if(!$entity) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Requested entity doesn\'t exist'));
        }
         return $entity;
    }

    /**
     * @return array
     */
    public function processRequest(){

        $customer = $this->getCustomerFromToken();

        if ($customer->getGroupId() != $this->sellerOptionService->getGroupByCode('Seller')->getId()) {
            return $result =[
                'error' => 1,
                'status' => 401,
                'message' => 'Not Authorized'
            ];
        }
        $coupon = $this->couponFactory->create()->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('coupon_code', ['eq' =>$this->request->getParam('coupon_code')])
            ->load()
            ->getFirstItem();

        if (is_null($coupon->getData('coupon_code'))) {
            return $result = [
                'error' => 1,
                'status' => 200,
                'message' => 'Invalid Coupon Code!'
            ];
        }

        if ($coupon->getData('is_redeemed')) {
            return $result = [
                'error' => 1,
                'status' => 200,
                'message' => 'Coupon Already Used'
            ];
        }

        if ($coupon->getData('valid_to') < date("Y-m-d H:i:s")) {
            return $result = [
                'error' => 1,
                'status' => 200,
                'message' => 'Coupon Expired'
            ];
        }

        if ($coupon->getData('seller_id') != $customer->getId()) {
            return $result= [
                'error' => 1,
                'status' => 200,
                'message' => 'Invalid Coupon Code.'
            ];
        }


        //$coupon->setData('is_redeemed', 1);
        $coupon->setIsRedeemed('is_redeemed', 1);
        $coupon->save();

        return $result = [
            'error' => 0,
            'status' => 200,
            'message' => 'Coupon Redeemed!'
        ];
    }
    public function getExpiredCoupons()
    {
        $customer = $this->getCustomerFromToken();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('valid_to', ['lt' => date("Y-m-d H:i:s")])
            ->addFieldToFilter('is_redeemed', 0)
            ->load();
    }

    public function getAvailableCoupons()
    {
        $customer =  $customer = $this->getCustomerFromToken();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->addFieldToFilter('valid_to', ['gt' => date("Y-m-d H:i:s")])
            ->addFieldToFilter('is_redeemed', 0)
            ->load();
    }

    public function getTotalCoupons()
    {
        $customer =  $customer = $this->getCustomerFromToken();
        return $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('seller_id', $customer->getId())
            ->load();
    }
}
