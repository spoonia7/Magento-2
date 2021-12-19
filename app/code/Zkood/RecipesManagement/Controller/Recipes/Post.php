<?php

namespace Zkood\RecipesManagement\Controller\Recipes;

use Exception;
use Magento\Customer\Controller\AbstractAccount;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Zkood\RecipesManagement\Model\RecipeFactory;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\Filesystem;

class Post extends AbstractAccount
{
    private $recipeFactory;
    private $uploaderFactory;
    private $fileSystem;

    public function __construct(
        RecipeFactory $recipeFactory,
        UploaderFactory $uploaderFactory,
        Filesystem $fileSystem,
        Context $context)
    {
        parent::__construct($context);
        $this->recipeFactory = $recipeFactory;
        $this->fileSystem = $fileSystem;
        $this->uploaderFactory = $uploaderFactory;
    }

    public function execute()
    {
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('customer/recipes');
        $recipe = $this->createRecipe();
        try {
            $recipe->save();
            $this->messageManager->addSuccessMessage(__("Recipe Submitted Successfully!"));
        } catch (Exception $e) {
            $this->messageManager->addErrorMessage(__($e->getMessage()));
        }

        return $redirect;
    }

    protected function createRecipe()
    {
        $recipe = $this->recipeFactory->create();
        $file = $this->getRequest()->getFiles()['recipe_image'];
        if (isset($file)) {
            $uploader = $this->uploaderFactory->create(['fileId' => 'recipe_image']);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setAllowCreateFolders(true);
            $uploader->setFilesDispersion(true);
            $mediaDirectory = $this->fileSystem->getDirectoryRead(DirectoryList::MEDIA);
            try {
                $imageName = $this->getImageName($file);
                $result = $uploader->save($mediaDirectory->getAbsolutePath("zkood/recipes/"), $imageName);
                $recipe->setRecipeImage($result['file']);
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        $recipe->setCustomerName($this->getRequest()->getParam('customer_name'));
        $recipe->setCustomerEmail($this->getRequest()->getParam('customer_email'));
        $recipe->setCustomerId($this->getRequest()->getParam('customer_id'));
        $recipe->setNotes($this->getRequest()->getParam('notes'));

        return $recipe;
    }

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

    private function getImageName($file): string
    {
        $fileName = explode(".", $file['name']);
        $ext = '.' . end($fileName);
        return str_replace(' ', '_', $this->getRequest()->getParam('customer_name')) .
            '_' . $this->generateRandomString(9) . $ext;
    }
}
