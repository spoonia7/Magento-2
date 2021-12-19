<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_MobikulCore
 * @author    Webkul <support@webkul.com>
 * @copyright 2010-2019 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\MobikulCore\Api\Data;

/**
 * Interface DeviceTokenInterface
 */
interface DeviceTokenInterface
{
    const ID = "id";
    const TOKEN = "token";
    const CUSTOMER_ID = "customer_id";

    /**
     * Function getId
     *
     * @return integer
     */
    public function getId();

    /**
     * Function setId
     *
     * @param integer $id id
     */
    public function setId($id);

    /**
     * Function getToken
     *
     * @return string
     */
    public function getToken();

    /**
     * Function setToken
     *
     * @param string $token token
     */
    public function setToken($token);

    /**
     * Function getCustomerId
     *
     * @return integer
     */
    public function getCustomerId();

    /**
     * Function setCustomerId
     *
     * @param integer $customerId customerId
     */
    public function setCustomerId($customerId);
}
