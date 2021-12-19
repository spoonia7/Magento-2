<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Model\Attribute\Backend;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Class Image
 * @package Yosto\MpVendorAttributeManager\Model\VendorAttribute\Attribute\Backend
 */
class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_uploaderFactory;

    /**
     * @var string
     */
    protected $_type = 'image';

    /**
     * Filesystem facade.
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * File Uploader factory.
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;
    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $request;
    /**
     * @var \Yosto\CustomerAttribute\Helper\Data
     */
    protected $_currentHelper;

    /**
     * Construct.
     *
     * @param \Psr\Log\LoggerInterface                         $logger
     * @param \Magento\Framework\Filesystem                    $filesystem
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Yosto\CustomerAttribute\Helper\Data $currentHelper
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Yosto\CustomerAttribute\Helper\Data $currentHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    ) {
        $this->_filesystem = $filesystem;
        $this->_coreRegistry = $registry;
        $this->request = $request;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger = $logger;
        $this->_currentHelper = $currentHelper;
    }
     /**
      * Save uploaded file and set its name to category
      *
      * @param \Magento\Framework\DataObject $object
      * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
      */
    public function afterSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $value = $this->request->getPostValue();
        $savedValue = '';
        if (isset($value['customer'][$attributeCode])) {
            $savedValue = $value['customer'][$attributeCode];
        }
        if (isset($value[$attributeCode]['delete']) && $value[$attributeCode]['delete'] == 1) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }

        $path = $this->_filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            'customer/attribute/files/' . $this->_type
        );
        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            return $this;
        }
        $allowedExtensions = explode(',', $this->_currentHelper->getAllowedExtensions('allowed_' .$this->_type .'_extension'));

        try {
            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_fileUploaderFactory->create(['fileId' => $this->getAttribute()->getName()]);
            $uploader->setAllowedExtensions($allowedExtensions);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $uploader->setAllowCreateFolders(true);
            $result = $uploader->save($path);
            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
        } catch (\Exception $e) {
             // if no image was set - save previous image value
            if ($savedValue != '') {
                $object->setData($this->getAttribute()->getName(), $savedValue);
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            }
            return $this;
        }
        
        return $this;
    }
}
