<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-15
 * Time: 15:28
 */

namespace SM\PaymentExpress\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class PaymentExpressHistory
 *
 * @package SM\PaymentExpress\Model\ResourceModel
 */
class PaymentExpressHistory extends AbstractDb
{

    /**
     * PaymentExpressHistory constructor
     */
    protected function _construct()
    {
        $this->_init('sm_payment_express_history', 'id');
    }
}
