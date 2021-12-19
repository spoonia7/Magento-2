<?php

namespace Zkood\RecipesManagement\Ui\DataProvider\Recipe;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Zkood\RecipesManagement\Model\ResourceModel\Recipe\CollectionFactory;

class Listing extends AbstractDataProvider
{
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
    }
}
