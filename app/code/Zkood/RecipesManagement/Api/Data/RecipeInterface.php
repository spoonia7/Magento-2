<?php


namespace Zkood\RecipesManagement\Api\Data;

/**
 * Interface RecipeInterface
 * @package Zkood\RecipesManagement\Api\Data
 */
interface RecipeInterface
{

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setEntityId($value);

    /**
     * @return int
     */
    public function getCustomerId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setCustomerId($value);

    /**
     * @return string
     */
    public function getCustomerName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerName($value);

    /**
     * @return string
     */
    public function getCustomerEmail();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerEmail($value);

    /**
     * @return string
     */
    public function getRecipeImage();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setRecipeImage($value);

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setNotes($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);
}
