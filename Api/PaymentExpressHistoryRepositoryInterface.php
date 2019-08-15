<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-15
 * Time: 15:22
 */

namespace SM\PaymentExpress\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface;

/**
 * Interface PaymentExpressHistoryRepositoryInterface
 *
 * @package SM\PaymentExpress\Api
 */
interface PaymentExpressHistoryRepositoryInterface
{

    public function save(PaymentExpressHistoryInterface $page);

    public function getById($id);

    public function getList(SearchCriteriaInterface $criteria);

    public function delete(PaymentExpressHistoryInterface $page);

    public function deleteById($id);
}
