<?php

namespace Zkood\RecipesManagement\Helper;

use \Magento\Framework\Api\ImageProcessorInterface;
use \Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use \Zkood\RecipesManagement\Model\RecipeFactory;

class Helper
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Api\ImageProcessorInterface
     */
    private $imageProcessor;
    /**
     * @var \Magento\Framework\Api\Data\ImageContentInterfaceFactory
     */
    private $imageContentFactory;
    /**
     * @var \Zkood\RecipesManagement\Model\RecipeFactory
     */
    private $recipeFactory;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * Info constructor.
     * @param \Magento\Framework\Api\ImageProcessorInterface $imageProcessor
     * @param \Magento\Framework\Api\Data\ImageContentInterfaceFactory $imageContentFactory
     * @param \Zkood\RecipesManagement\Model\RecipeFactory $recipeFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Api\ImageProcessorInterface $imageProcessor,
        \Magento\Framework\Api\Data\ImageContentInterfaceFactory $imageContentFactory,
        \Zkood\RecipesManagement\Model\RecipeFactory $recipeFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->imageProcessor = $imageProcessor;
        $this->imageContentFactory = $imageContentFactory;
        $this->recipeFactory = $recipeFactory;
        $this->logger = $logger;
    }

    /**
     * @return mixed
     */
    /*protected function isEnabled()
    {
        return $this->scopeConfig->getValue("mfblog/general/enabled");
    }*/

    /**
 * @param array $data
 * @return mixed
 */
    public function createRecipeFromBase46(array $data)
    {
        $recipe = $this->recipeFactory->create();
        $imageFiles = $data['imageFiles'];
        $imageName = $this->processImage($imageFiles);
        if (!empty($imageName)) {
            try {
                $recipe->setRecipeImage($imageName);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
        $recipe->setCustomerId($data['customer_id']);
        $recipe->setCustomerName($data['customer_name']);
        $recipe->setCustomerEmail($data['customer_email']);
        $recipe->setNotes($data['notes']);

        return $recipe;
    }
    /**
     * @param array $data
     * @return mixed
     */
    public function createRecipe(array $data)
    {
        $recipe = $this->recipeFactory->create();
        $imageFiles = $data['imageFiles'];
        $imageName = $this->processImage($imageFiles);
        if (!empty($imageName)) {
            try {
                $recipe->setRecipeImage($imageName);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
            }
        }
        $recipe->setCustomerId($data['customer_id']);
        $recipe->setCustomerName($data['customer_name']);
        $recipe->setCustomerEmail($data['customer_email']);
        $recipe->setNotes($data['notes']);

        return $recipe;
    }

    /**
     * @param array $files
     * @return string
     */
    public function processImage($files) {
        $fileName ='';
        if (count($files) > 0) {
            $destinationFolder = 'zkood/recipes/';
            foreach ($files as $file) {
                $imageContent = $this->imageContentFactory->create();
                $imageContent->setBase64EncodedData($file['base64_encoded_data']);
                $imageContent->setType($file['type']);
                $imageContent->setName($file['name']);
                $fileName = $this->imageProcessor->processImageContent($destinationFolder, $imageContent);
            }
        }
        return $fileName;

    }

    /**
     * @param int $length
     * @return string
     */
    private function generateRandomString($length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * @param $file
     * @return string
     */
    public function getImageName($file,$customer_name): string
    {
        $fileName = explode(".", $file['name']);
        $ext = '.' . end($fileName);
        return str_replace(' ', '_', $customer_name) .
            '_' . $this->generateRandomString(9) . $ext;
    }
}