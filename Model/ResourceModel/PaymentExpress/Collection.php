<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-15
 * Time: 15:28
 */

namespace SM\PaymentExpress\Model\ResourceModel\PaymentExpress;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
/**
 * Class Collection
 *
 * @package SM\PaymentExpress\Model\ResourceModel\PaymentExpress
 */
class Collection extends AbstractCollection
{

    /**
     * Collection constructor
     */
    protected function _construct()
    {
        $this->_init('SM\PaymentExpress\Model\PaymentExpressHistory', 'SM\PaymentExpress\Model\ResourceModel\PaymentExpressHistory');
    }
}
