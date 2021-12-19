<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */
namespace Yosto\CustomerAttribute\Api\Data;

/**
 * Interface EavAttibuteInterface
 * @package Yosto\CustomerAttribute\Api\Data
 */
interface EavAttibuteInterface  extends \Magento\Eav\Api\Data\AttributeInterface
{

    const IS_USED_IN_GRID = 'is_used_in_grid';

    const IS_VISIBLE_IN_GRID = 'is_visible_in_grid';

    const IS_FILTERABLE_IN_GRID = 'is_filterable_in_grid';

    const IS_SEARCHABLE_IN_GRID = 'is_searchable_in_grid';

    const SORT_ORDER = 'sort_order';

    const IS_VISIBLE = 'is_visible';

    const SCOPE_STORE_TEXT = 'store';

    const SCOPE_GLOBAL_TEXT = 'global';

    const SCOPE_WEBSITE_TEXT = 'website';

    const USED_IN_FORMS  = 'used_in_forms';

    /**
     * Whether it is used in customer grid
     *
     * @return bool|null
     */
    public function getIsUsedInGrid();

    /**
     * Whether it is visible in customer grid
     *
     * @return bool|null
     */
    public function getIsVisibleInGrid();

    /**
     * Whether it is filterable in customer grid
     *
     * @return bool|null
     */
    public function getIsFilterableInGrid();

    /**
     * Set whether it is used in grid filter
     *
     * @param bool $isFilterableInGrid
     * @return $this
     */
    public function setIsFilterableInGrid($isFilterableInGrid);

    /**
     * Get sort order
     *
     * @return int|null
     */
    public function getSortOrder();

    /**
     * Set position
     *
     * @param int $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder);

    /**
     * Whether the attribute can be used in Grid search
     *
     * @return bool|null
     */
    public function getIsSearchableInGrid();

    /**
     * Whether the attribute can be used in Grid Search
     *
     * @param bool $isSearchableInGrid
     * @return $this
     */
    public function setIsSearchableInGrid($isSearchableInGrid);


    /**
     * Whether attribute is visible on frontend.
     *
     * @return bool|null
     */
    public function getIsVisible();

    /**
     * Set whether attribute is visible on frontend.
     *
     * @param bool $isVisible
     * @return $this
     */
    public function setIsVisible($isVisible);

    /**
     * Retrieve attribute scope
     *
     * @return string|null
     */
    public function getScope();

    /**
     * Set attribute scope
     *
     * @param string $scope
     * @return $this
     */
    public function setScope($scope);

}