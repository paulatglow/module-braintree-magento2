<?php

namespace Magento\Braintree\Model\ResourceModel;

use Magento\Braintree\Api\CreditPriceRepositoryInterface;
use Magento\Braintree\Api\Data\CreditPriceDataInterface;

/**
 * Class CreditPriceRepository
 * @package Magento\Braintree\Model\ResourceModel
 * @author Aidan Threadgold <aidan@gene.co.uk>
 */
class CreditPriceRepository implements CreditPriceRepositoryInterface
{
    /**
     * @var CreditPrice\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * CreditPriceRepository constructor.
     * @param CreditPrice\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Braintree\Model\ResourceModel\CreditPrice\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function save(CreditPriceDataInterface $entity)
    {
        return $entity->getResource()->save($entity);
    }

    /**
     * @inheritdoc
     */
    public function getByProductId($productId)
    {
        /** @var CreditPrice\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->setOrder('term', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
        return $collection->getItems();
    }

    /**
     * @inheritdoc
     */
    public function getCheapestByProductId($productId)
    {
        /** @var CreditPrice\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->setOrder('monthly_payment', \Magento\Framework\Data\Collection\AbstractDb::SORT_ORDER_ASC);
        $collection->setPageSize(1);

        return $collection->getFirstItem();
    }

    /**
     * @inheritdoc
     */
    public function deleteByProductId($productId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        return $collection->walk('delete');
    }
}
