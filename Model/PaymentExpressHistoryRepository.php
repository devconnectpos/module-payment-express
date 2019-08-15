<?php
/**
 * Created by Hung Nguyen - hungnh@smartosc.com
 * Date: 2019-07-15
 * Time: 15:20
 */


namespace SM\PaymentExpress\Model;

use Exception;
use SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface;
use SM\PaymentExpress\Api\PaymentExpressHistoryRepositoryInterface;
use SM\PaymentExpress\Model\ResourceModel\PaymentExpress\CollectionFactory;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchResultsInterfaceFactory;

/**
 * Class PaymentExpressHistoryRepository
 *
 * @package SM\PaymentExpress\Model
 */
class PaymentExpressHistoryRepository implements PaymentExpressHistoryRepositoryInterface
{

    protected $objectFactory;
    protected $collectionFactory;

    /**
     * PaymentExpressHistoryRepository constructor.
     *
     * @param \SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface              $objectFactory
     * @param \SM\PaymentExpress\Model\ResourceModel\PaymentExpress\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Api\SearchResultsInterfaceFactory                    $searchResultsFactory
     */
    public function __construct(
        PaymentExpressHistoryInterface $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->objectFactory        = $objectFactory;
        $this->collectionFactory    = $collectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param \SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface $object
     *
     * @return \SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(PaymentExpressHistoryInterface $object)
    {
        try {
            $object->save();
        } catch (Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }

        return $object;
    }

    /**
     * @param $id
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id)
    {
        $object = $this->objectFactory->create();
        $object->load($id);
        if (!$object->getId()) {
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $id));
        }

        return $object;
    }

    /**
     * @param \SM\PaymentExpress\Api\Data\PaymentExpressHistoryInterface $object
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(PaymentExpressHistoryInterface $object)
    {
        try {
            $object->delete();
        } catch (Exception $exception) {
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param $id
     *
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($id)
    {
        return $this->delete($this->getById($id));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     *
     * @return mixed
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->getCollectionFactory($criteria);

        $objects = [];
        foreach ($collection as $objectModel) {
            $objects[] = $objectModel;
        }
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($objects);

        return $searchResults;
    }

    /**
     * @param $criteria
     *
     * @return mixed
     */
    private function getCollectionFactory($criteria)
    {
        $collection = $this->collectionFactory->create();

        foreach ($criteria->getFilterGroups() as $filterGroup) {
            list($fields, $conditions) = $this->prepareFilterCollection($filterGroup);
            $collection = $this->addFilterCollection($collection, $fields, $conditions);
        }

        $collection = $this->addSortCollection($collection, $criteria);
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        return $collection;
    }

    /**
     * @param $collection
     * @param $criteria
     *
     * @return mixed
     */
    private function addSortCollection($collection, $criteria) {
        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var sortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        return $collection;
    }

    /**
     * @param $filterGroup
     *
     * @return array
     */
    private function prepareFilterCollection($filterGroup) {
        $fields     = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition    = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[]     = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        return [$fields, $conditions];
    }

    /**
     * @param $collection
     * @param $fields
     * @param $conditions
     *
     * @return mixed
     */
    private function addFilterCollection($collection, $fields, $conditions) {
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
        return $collection;
    }
}
