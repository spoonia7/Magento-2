<?php

namespace Zkood\RecipesManagement\Model;

use \Zkood\RecipesManagement\Api\Magento;
use \Zkood\RecipesManagement\Api\RecipesManagementInterface;
use \Zkood\RecipesManagement\Model\RecipeFactory;
use \Magento\Framework\Api\Search\SearchCriteriaBuilder;
use \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Exception\InputException;
use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Exception\AuthorizationException;
use Magento\Framework\Phrase;
class RecipesManagement implements RecipesManagementInterface
{
    const PAGE_SIZE=1;
    const LIMIT = 5;

    /**
     * @var int
     */
    private $customer_id;

    /**
     * @var string
     */
    private $customer_name;

    /**
     * @var string
     */
    private $customer_email;

    /**
     * @var \Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \Magento\Framework\Api\Search\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;
    /**
     * @var \Zkood\RecipesManagement\Model\RecipeFactory
     */
    private $recipeFactory;

    /**
     * @var \Zkood\RecipesManagement\Model\ResourceModel\Recipe
     */
    private $recipeResourceModel;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Authorization\Model\CompositeUserContext
     */
    private $userContext;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Zkood\RecipesManagement\Helper\Helper
     */
    private $helper;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var Magento\MediaStorage\Model\File\UploaderFactory
     */
    private $uploaderFactory;

    /**
     * @var Magento\Framework\Filesystem
     */
    private $fileSystem;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $timeZone;


    /**
     * RecipesManagement constructor.
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor
     * @param \Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory $collectionFactory
     * @param \Zkood\RecipesManagement\Model\RecipeFactory $recipeFactory
     * @param ResourceModel\Recipe $recipeResourceModel
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Authorization\Model\CompositeUserContext $userContext
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Zkood\RecipesManagement\Helper\Helper $helper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface $collectionProcessor,
        \Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory $collectionFactory,
        \Zkood\RecipesManagement\Model\RecipeFactory $recipeFactory,
        \Zkood\RecipesManagement\Model\ResourceModel\Recipe $recipeResourceModel,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Authorization\Model\CompositeUserContext $userContext,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Zkood\RecipesManagement\Helper\Helper $helper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone
    )
    {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionProcessor = $collectionProcessor;
        $this->collectionFactory = $collectionFactory;
        $this->recipeFactory = $recipeFactory;
        $this->recipeResourceModel = $recipeResourceModel;
        $this->storeManager = $storeManager;
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->request = $request;
        $this->uploaderFactory = $uploaderFactory;
        $this->fileSystem = $filesystem;
        $this->timeZone = $timeZone;
    }

    /**
     * Retrieve list by page type, term, store, etc
     *
     * @return string
     */
    public function getList()
    {
        $customerEmail='';
        try {
            $this->verifyGetRequest();
            $customer = $this->getCustomerFromToken();
            if($customer)
            {
                $customerEmail = $customer->getEmail();
            }
            //get values of current page
            $page=($this->request->getParam('p'))? $this->request->getParam('p') : self::PAGE_SIZE ;
            //get values of current limit
            $pageSize=($this->request->getParam('limit'))? $this->request->getParam('limit') : self::LIMIT;

            $collection = $this->collectionFactory->create();
            $collection->addFieldToFilter('customer_email', ['eq' => $customerEmail]);
            $collection->setPageSize($pageSize);
            $collection->setCurPage($page);

            $recipes = [];
            foreach ($collection as $item) {
                $recipes[] = $this->getItemData($item);
            }
            $result = [
                'recipes' => $recipes,
                'count' => $collection->count(),
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Retrieve Recipe Details By Id
     *
     * @param  int $id
     * @return string
     */
    public function getRecipeDetail($id)
    {
        $customerEmail='';
        try {
            $this->verifyGetRequest();
            $customer = $this->getCustomerFromToken();
            if($customer)
            {
                $customerEmail = $customer->getEmail();
            }
            $collection = $this->collectionFactory->create()
                ->addFieldToFilter('customer_email', ['eq' => $customerEmail])
                ->addFieldToFilter('entity_id', ['eq' => $id])
                ->load();

            $recipes = [];
            foreach ($collection as $item) {
                $recipes[] = $this->getItemData($item);
            }
            $result = [
                'recipes' => $recipes,
                'count' => $collection->count(),
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Post New Recipe
     *
     * @return string
     */
    public function postNewRecipe()
    {
        /*$data= $this->request->getPostValue();
        $file = $this->request->getFiles()['recipe_image'];
        header("Content-Type: application/json; charset=utf-8");
        $allData =
            ["data"=>$data,
            "file"=>$file];
        print_r(json_encode($allData), false);
        die();*/
        $status = 'Failed';
        $this->verifyPostRequest();
        try {
            $recipe = $this->createRecipe();
            $recipe->save();
            $status = 'Success';
            $recipes[] = $this->getItemData($recipe);
            $result = [
                'recipe' => $recipes,
                'status' => $status
            ];

            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();
            $this->logger->debug(__("Recipe Submitted Successfully!"));
        } catch (Exception $e) {
            $this->logger->debug(__($e->getMessage()));
        }
        return false;
    }

    /**
     * @param $item
     * @return mixed
     */
    public function getItemData($item)
    {
        $media = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $keys = [
            'id',
            'customer_name',
            'customer_email',
            'recipe_image',
            'notes',
            'created_at'
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace(
                    '_',
                    '',
                    ucwords($key, '_')
                );
            if ($key == 'recipe_image') {
                $data[$key] = $media . 'zkood/recipes' . $item->$method();
            } else {
                $data[$key] = $item->$method();
            }
        }

        return $data;
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
     * @return mixed
     * @throws \Exception
     */
    protected function createRecipe()
    {
        $recipe = $this->recipeFactory->create();
        $file = $this->request->getFiles()['recipe_image'];
        if (isset($file)) {
            $uploader = $this->uploaderFactory->create(['fileId' => 'recipe_image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            try {
                $imageName = $this->helper->getImageName($file,$this->customer_name);
                $result = $uploader->save($mediaDirectory->getAbsolutePath("zkood/recipes/"), $imageName);
                $recipe->setRecipeImage($result['file']);
            } catch (\Exception $e) {
                throw new \Exception(__('could not name the file image'.$e->getMessage()));
            }
        }
        else{
            throw new \Exception(__('The data is not valid'));
        }
        $recipe->setCustomerName($this->customer_name);
        $recipe->setCustomerEmail($this->customer_email);
        $recipe->setCustomerId($this->customer_id);
        $recipe->setNotes($this->request->getParam('notes'));
        $recipe->setCreatedAt($this->timeZone->date()->format('Y-m-d H:i:s'));

        return $recipe;
    }

    /**
     * @return bool
     */
    private function getToken()
    {
        $token = false;

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        $authorizationBearer = '';

        if (isset($headers['Authorization'])) {
            $authorizationBearer = $headers['Authorization'];
        } else if (isset($headers['authorization'])) {
            $authorizationBearer = $headers['authorization'];
        } else {
            $authorizationBearer = "";
        }

        $authorizationBearerArr = explode(' ', $authorizationBearer);
        if (
            isset($authorizationBearerArr[0]) &&
            trim($authorizationBearerArr[0]) == 'Bearer' &&
            isset($authorizationBearerArr[1])
        ) {
            $token = $authorizationBearerArr[1];
        }

        return $token;
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
            $customerToken = $this->getToken();
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
            if($customer->getEmail() != $this->request->getParam('customer_email'))
            {
                throw new AuthorizationException(
                    new Phrase(
                        'Consumer %consumer_id is not authorized to access %resources',
                        ['consumer_id' => '3', 'resources' => '4']
                    )
                );
            }
            $this->customer_id = empty($this->request->getParam('customer_id')) ? $customer->getId() : $this->request->getParam('customer_id')  ;
            $this->customer_name = empty($this->request->getParam('customer_name')) ? $customer->getFirstname()." ".$customer->getLastname() : $this->request->getParam('customer_name')  ;
            $this->customer_email = empty($this->request->getParam('customer_email')) ? $customer->getEmail() : $this->request->getParam('customer_email') ;
        } else {
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
            $customerToken = $this->getToken();
            if (!$customerToken) {
                throw new AuthorizationException(
                    new Phrase(
                        'Consumer %consumer_id is not authorized to access %resources',
                        ['consumer_id' => '3', 'resources' => '4']
                    )
                );
            }
        } else {
            throw new \Exception(__("Invalid Request"));
        }
    }
}
