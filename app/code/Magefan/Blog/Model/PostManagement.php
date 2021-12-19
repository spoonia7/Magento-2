<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\Blog\Model;

use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Post management model
 */
class PostManagement extends AbstractManagement
{
    /**
     * @var \Magefan\Blog\Model\PostFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $jsonFactory;
    /**
     * @var \Magefan\Blog\Model\CategoryFactory
     */
    private $categoryFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Magefan\Blog\Model\PostFactory $postFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Magefan\Blog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magefan\Blog\Model\PostFactory $postFactory,
        JsonFactory $jsonFactory,
        \Magefan\Blog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_itemFactory = $postFactory;
        $this->jsonFactory = $jsonFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Retrieve list of post by page type, term, store, etc
     *
     * @param  string $type
     * @param  string $term
     * @param  int $storeId
     * @param  int $page
     * @param  int $limit
     * @return bool|\Magento\Framework\Controller\Result\Json
     */
    public function getList($storeId, $page, $limit, $type = '', $term = '')
    {
        try {
            $collection = $this->_itemFactory->create()->getCollection();
            $collection
                ->addActiveFilter()
                ->addStoreFilter($storeId)
                ->setOrder('publish_time', 'DESC')
                ->setCurPage($page)
                ->setPageSize($limit);

            $type = strtolower($type);

            switch ($type) {
                case 'archive':
                    $term = explode('-', $term);
                    if (count($term) < 2) {
                        return false;
                    }
                    list($year, $month) = $term;
                    $year = (int) $year;
                    $month = (int) $month;

                    if ($year < 1970) {
                        return false;
                    }
                    if ($month < 1 || $month > 12) {
                        return false;
                    }

                    $collection->addArchiveFilter($year, $month);
                    break;
                case 'author':
                    $collection->addAuthorFilter($term);
                    break;
                case 'category':
                    $collection->addCategoryFilter($term);
                    break;
                case 'search':
                    $collection->addSearchFilter($term);
                    break;
                case 'tag':
                    $collection->addTagFilter($term);
                    break;
            }

            $posts = [];
            foreach ($collection as $item) {
                $posts[] = $this->getDynamicData($item);
            }

            $result = [
                'posts' => $posts,
                'total_number' => $collection->getSize(),
                'current_page' => $collection->getCurPage(),
                'last_page' => $collection->getLastPageNumber(),
            ];
            //$result[['posts']['categories']] = 'title';

            // to be changed later with a better approach.
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
    protected function getDynamicData($item)
    {
        $data = $item->getData();
        $ids = $item->getData('categories');


        $collection = $this->categoryFactory->create()->getCollection();
        $collection
            ->addActiveFilter()
            ->addFieldToFilter('category_id', array(
                    'in' => $ids)
            );

        $categories = [];
        foreach ($collection as $cat) {
            $categories[] = $cat->getData('title');
        }
        $data['categories'] = $categories ;

        $keys = [
            'og_image',
            'og_type',
            'og_description',
            'og_title',
            'meta_description',
            'meta_title',
            'short_filtered_content',
            'filtered_content',
            'first_image',
            'featured_image',
            'post_url',
            'media_gallery'
        ];

        foreach ($keys as $key) {
            $method = 'get' . str_replace(
                    '_',
                    '',
                    ucwords($key, '_')
                );
            $data[$key] = $item->$method();
        }

        return $data;
    }
}
