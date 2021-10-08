<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-11
 * Time: 10:43
 */
namespace SM\PaymentExpress\Repositories;

use Exception;
use Magento\Framework\HTTP\ClientFactory;
use Magento\Framework\Simplexml\ElementFactory;
use SM\Core\Api\Data\PaymentExpress;
use SM\XRetail\Helper\DataConfig;
use SM\XRetail\Repositories\Contract\ServiceAbstract;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use SM\PaymentExpress\Model\PaymentExpressHistoryFactory;
use Magento\Framework\DataObject;
use SM\PaymentExpress\Model\ResourceModel\PaymentExpress\CollectionFactory;

/**
 * Class PaymentExpressManagement
 *
 * @package SM\PaymentExpress\Repositories
 */
class PaymentExpressManagement extends ServiceAbstract
{

    /**
     * @var array Endpoints
     */
    private $endpoints
        = [
            'uat' => 'https://uat.paymentexpress.com/pxmi3/pos.aspx',
            'pro' => 'https://sec.paymentexpress.com/pxmi3/pos.aspx',
        ];
    /**
     * @var \SM\PaymentExpress\Model\PaymentExpressHistoryFactory
     */
    protected $paymentHistoryFactory;
    /**
     * @var \SM\PaymentExpress\Model\ResourceModel\PaymentExpress\CollectionFactory
     */
    protected $paymentHistoryCollectionFactory;

    /**
     * @var \Magento\Framework\HTTP\ClientFactory
     */
    private $httpClientFactory;

    /**
     * @var \Magento\Framework\Simplexml\ElementFactory
     */
    private $xmlElFactory;

    /**
     * PaymentExpressManagement constructor.
     *
     * @param \Magento\Framework\App\RequestInterface                                 $requestInterface
     * @param \SM\XRetail\Helper\DataConfig                                           $dataConfig
     * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager
     * @param \SM\PaymentExpress\Model\PaymentExpressHistoryFactory                   $paymentExpressHistoryFactory
     * @param \SM\PaymentExpress\Model\ResourceModel\PaymentExpress\CollectionFactory $paymentHistoryCollectionFactory
     * @param \Magento\Framework\HTTP\ClientFactory                                   $httpClientFactory
     * @param \Magento\Framework\Simplexml\ElementFactory                             $xmlElFactory
     */
    public function __construct(
        RequestInterface $requestInterface,
        DataConfig $dataConfig,
        StoreManagerInterface $storeManager,
        PaymentExpressHistoryFactory $paymentExpressHistoryFactory,
        CollectionFactory $paymentHistoryCollectionFactory,
        ClientFactory $httpClientFactory,
        ElementFactory $xmlElFactory
    ) {
        $this->paymentHistoryFactory           = $paymentExpressHistoryFactory;
        $this->paymentHistoryCollectionFactory = $paymentHistoryCollectionFactory;
        $this->httpClientFactory               = $httpClientFactory;
        $this->xmlElFactory                    = $xmlElFactory;
        parent::__construct($requestInterface, $dataConfig, $storeManager);
    }

    /**
     * @return array
     */
    public function initTransaction()
    {
        $paymentExpressData = $this->getRequestData()['paymentExpressData'];

        $xmlRequest = $this->xmlElFactory->create(
            ['data' => '<Scr/>']
        );
        $xmlRequest->addAttribute('action', 'doScrHIT');
        $xmlRequest->addAttribute('user', $paymentExpressData['hit_username']);
        $xmlRequest->addAttribute('key', $paymentExpressData['hit_key']);

        $xmlRequest->addChild('Amount', $paymentExpressData['amount']);
        $xmlRequest->addChild('Cur', $paymentExpressData['currency']);
        $xmlRequest->addChild('TxnType', 'Purchase');
        $xmlRequest->addChild('Station', $paymentExpressData['station_id']);
        $xmlRequest->addChild('TxnRef', $paymentExpressData['txnref']);
        $xmlRequest->addChild('DeviceId', $paymentExpressData['device_id']);
        $xmlRequest->addChild('PosName', 'ConnectPOS');
        $xmlRequest->addChild('PosVersion', 'Pos V1');
        $xmlRequest->addChild('VendorId', 'PXVendor');
        $xmlRequest->addChild('MRef', 'My Reference');

        return $this->execPaymentRequest($xmlRequest, $paymentExpressData['endpoint']);
    }

    /**
     * @return array
     */
    public function checkStatus()
    {
        $paymentExpressData = $this->getRequestData()['paymentExpressData'];

        $xmlRequest = $this->xmlElFactory->create(
            ['data' => '<Scr/>']
        );
        $xmlRequest->addAttribute('action', 'doScrHIT');
        $xmlRequest->addAttribute('user', $paymentExpressData['hit_username']);
        $xmlRequest->addAttribute('key', $paymentExpressData['hit_key']);

        $xmlRequest->addChild('TxnType', 'Status');
        $xmlRequest->addChild('Station', $paymentExpressData['station_id']);
        $xmlRequest->addChild('TxnRef', $paymentExpressData['txnref']);

        return $this->execPaymentRequest($xmlRequest, $paymentExpressData['endpoint']);
    }

    /**
     * @return array
     */
    public function doButton()
    {
        $paymentExpressData = $this->getRequestData()['paymentExpressData'];
        $xmlRequest = $this->xmlElFactory->create(
            ['data' => '<Scr/>']
        );
        $xmlRequest->addAttribute('action', 'doScrHIT');
        $xmlRequest->addAttribute('user', $paymentExpressData['hit_username']);
        $xmlRequest->addAttribute('key', $paymentExpressData['hit_key']);

        $xmlRequest->addChild('TxnType', 'UI');
        $xmlRequest->addChild('Station', $paymentExpressData['station_id']);
        $xmlRequest->addChild('TxnRef', $paymentExpressData['txnref']);
        $xmlRequest->addChild('UiType', 'Bn');
        $xmlRequest->addChild('Name', $paymentExpressData['name']);
        $xmlRequest->addChild('Val', $paymentExpressData['val']);

        return $this->execPaymentRequest($xmlRequest, $paymentExpressData['endpoint']);
    }

    /**
     * @return array
     */
    public function doRefund()
    {
        $paymentExpressData = $this->getRequestData()['paymentExpressData'];

        $xmlRequest = $this->xmlElFactory->create(
            ['data' => '<Scr/>']
        );
        $xmlRequest->addAttribute('action', 'doScrHIT');
        $xmlRequest->addAttribute('user', $paymentExpressData['hit_username']);
        $xmlRequest->addAttribute('key', $paymentExpressData['hit_key']);

        $xmlRequest->addChild('Amount', $paymentExpressData['amount']);
        $xmlRequest->addChild('Cur', $paymentExpressData['currency']);
        $xmlRequest->addChild('TxnType', 'Refund');
        $xmlRequest->addChild('Station', $paymentExpressData['station_id']);
        $xmlRequest->addChild('TxnRef', $paymentExpressData['txnref']);
        $xmlRequest->addChild('DpsTxnRef', $paymentExpressData['DpsTxnRef']);
        $xmlRequest->addChild('DeviceId', $paymentExpressData['device_id']);
        $xmlRequest->addChild('PosName', 'ConnectPOS');
        $xmlRequest->addChild('PosVersion', 'Pos V1');
        $xmlRequest->addChild('VendorId', 'PXVendor');
        $xmlRequest->addChild('MRef', 'My Reference');

        return $this->execPaymentRequest($xmlRequest, $paymentExpressData['endpoint']);
    }

    /**
     * @param $xmlRequest
     * @param $mode
     *
     * @return array
     */
    private function execPaymentRequest($xmlRequest, $mode)
    {
        $debugData = [];
        try {
            $client = $this->httpClientFactory->create();
            $client->post($this->getPaymentExpressAcceptUrl($mode), $xmlRequest->asXML());
            $xmlResponse = $client->getBody();
            $response = $this->xmlElFactory->create(['data' => $xmlResponse]);
            return $response;
        } catch (\Exception $e) {
            $debugData['result'] = ['error' => $e->getMessage(), 'code' => $e->getCode()];
        }

        return $debugData;
    }

    /**
     * @param string $mode
     *
     * @return string
     */
    private function getPaymentExpressAcceptUrl($mode = 'pro')
    {
        $url = $this->endpoints[$mode];

        return $url;
    }

    /**
     * @return $this
     */
    public function savePaymentExpressHistory()
    {
        $paymentHistory = $this->paymentHistoryFactory->create();
        $paymentHistoryData = $this->getRequest()->getParam('paymentExpressData');
        try {
            $paymentHistory
                ->setData('hit_username', $paymentHistoryData['hit_username'])
                ->setData('hit_key', $paymentHistoryData['hit_key'])
                ->setData('device_id', $paymentHistoryData['device_id'])
                ->setData('station_id', $paymentHistoryData['station_id'])
                ->setData('dl1', $paymentHistoryData['dl1'])
                ->setData('dl2', $paymentHistoryData['dl2'])
                ->setData('txnref', $paymentHistoryData['txnref'])
                ->setData('message', $paymentHistoryData['message'])
                ->setData('create_at', $paymentHistoryData['created_at'])
                ->setData('updated_at', $paymentHistoryData['updated_at']);
            $paymentHistory->save();
        } catch (Exception $e) {

        }

        return $this;
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function getListPaymentExpressHistory()
    {
        return $this->load($this->getSearchCriteria())->getOutput();
    }

    /**
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return \SM\Core\Api\SearchResult
     * @throws \Exception
     */
    public function load(DataObject $searchCriteria)
    {
        if (is_null($searchCriteria) || !$searchCriteria) {
            $searchCriteria = $this->getSearchCriteria();
        }

        $collection = $this->getPaymentExpressHistoryCollection($searchCriteria);

        $items = [];
        if ($collection->getLastPageNumber() >= $searchCriteria->getData('currentPage')) {
            foreach ($collection as $item) {
                $i = new PaymentExpress();
                $items[] = $i->addData($item->getData());
            }
        }

        return $this->getSearchResult()
                    ->setSearchCriteria($searchCriteria)
                    ->setItems($items)
                    ->setLastPageNumber($collection->getLastPageNumber())
                    ->setTotalCount($collection->getSize());
    }

    /**
     * @param \Magento\Framework\DataObject $searchCriteria
     *
     * @return \SM\PaymentExpress\Model\ResourceModel\PaymentExpress\Collection
     */
    public function getPaymentExpressHistoryCollection(DataObject $searchCriteria)
    {
        /** @var \SM\PaymentExpress\Model\ResourceModel\PaymentExpress\Collection $collection */
        $collection = $this->paymentHistoryCollectionFactory->create()->setOrder('created_at', 'DESC');

        $collection->addFieldToFilter('device_id', $searchCriteria['device_id'])
                   ->addFieldToFilter('station_id', $searchCriteria['station_id'])
                   ->addFieldToFilter('hit_key', $searchCriteria['hit_key'])
                   ->addFieldToFilter('hit_username', $searchCriteria['hit_username']);
        if (is_nan($searchCriteria->getData('currentPage'))) {
            $collection->setCurPage(1);
        } else {
            $collection->setCurPage($searchCriteria->getData('currentPage'));
        }
        if (is_nan($searchCriteria->getData('pageSize'))) {
            $collection->setPageSize(
                DataConfig::PAGE_SIZE_LOAD_DATA
            );
        } else {
            $collection->setPageSize(
                $searchCriteria->getData('pageSize')
            );
        }

        return $collection;
    }

}
