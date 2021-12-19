<?php

namespace Zkood\RecipesManagement\Api;

interface RecipesManagementInterface
{

    /**
     * Retrieve list by page type, term, store, etc
     *
     * @return string

     */
    public function getList();
    /**
     * Retrieve Recipe Details By Id
     *
     * @param  int $id
     * @return string

     */
    public function getRecipeDetail($id);

    /**
     * Post New Recipe
     *
     * @return string
     */
    public function postNewRecipe();
}