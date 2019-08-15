<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-15
 * Time: 15:19
 */

namespace SM\PaymentExpress\Model;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class PaymentExpressHistory
 *
 * @package SM\PaymentExpress\Model
 */
class PaymentExpressHistory extends AbstractModel implements IdentityInterface
{

    const CACHE_TAG = 'sm_payment_express_history';

    /**
     * PaymentExpressHistory constructor
     */
    protected function _construct()
    {
        $this->_init('SM\PaymentExpress\Model\ResourceModel\PaymentExpressHistory');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
