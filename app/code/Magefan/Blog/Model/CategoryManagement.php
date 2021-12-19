<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Model;

/**
 * Category management model
 */
class CategoryManagement extends AbstractManagement
{
    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    protected $_itemFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magefan\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_itemFactory = $categoryFactory;
    }

     /**
      * Retrieve list of category by page type, term, store, etc
      *

      * @param  int $storeId
      * @param  int $page
      * @param  int $limit
      * @param  string $type
      * @param  string $term
      * @return string
      */
    public function getList($storeId, $page, $limit , $type = "", $term="")
    {
        try {
            $collection = $this->_itemFactory->create()->getCollection();
            $collection
                ->addActiveFilter()
                ->addStoreFilter($storeId)
                ->setCurPage($page)
                ->setPageSize($limit);
            if(!empty($type)) {

                $type = strtolower($type);

                switch ($type) {
                    case 'search':
                        $collection->addSearchFilter($term);
                        break;
                }
            }

            $categories = [];
            foreach ($collection as $item) {
                $categories[] = $this->getDynamicData($item);
            }

            $result = [
                'categories' => $categories,
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];

            // to be changed later with a better approach.
            header("Content-Type: application/json; charset=utf-8");
            print_r(json_encode($result), false);
            die();

//            return json_encode($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $item
     * @return array
     */
    protected function getDynamicData($item)
    {
        $data = $item->getData();

        $keys = [
            'meta_description',
            'meta_title',
            'category_url',
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace('_', '', ucwords($key, '_'));
            $data[$key] = $item->$method();
        }

        return $data;
    }
}
