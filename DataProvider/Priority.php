<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\DataProvider;

use Magento\Framework\DB\Select;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Reports\Model\Item as ReportItem;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\ConfigProvider\General as ConfigProvider;
use RunAsRoot\ProductPriorities\Model\ResourceModel\Report\Bestsellers\CollectionFactory as BestSellersCollectionFactory;
use RunAsRoot\ProductPriorities\Model\ResourceModel\Report\Bestsellers\Collection as BestSellersCollection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\Collection as PriorityCollection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory as PriorityCollectionFactory;

class Priority
{
    const ORDER_FIELD = 'proportion_value';

    private StoreManagerInterface $storeManager;

    private BestSellersCollectionFactory $bestSellersCollectionFactory;

    private ConfigProvider $configProvider;

    private PriorityCollectionFactory $priorityCollectionFactory;

    private ?array $loadedPriorities = null;

    public function __construct(
        StoreManagerInterface        $storeManager,
        BestSellersCollectionFactory $bestSellersCollectionFactory,
        ConfigProvider               $configProvider,
        PriorityCollectionFactory    $priorityCollectionFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->bestSellersCollectionFactory = $bestSellersCollectionFactory;
        $this->configProvider = $configProvider;
        $this->priorityCollectionFactory = $priorityCollectionFactory;
    }

    public function getData(): array
    {
        $bestSellers = $this->prepareCollection();
        /** @var ReportItem $topBestSeller */
        $topBestSeller = $bestSellers->getFirstItem();
        if (!($maxQtyOrdered = $topBestSeller->getData('qty_ordered'))) {
            return [];
        }

        $result = [];
        /** @var ReportItem $bestSeller */
        foreach ($bestSellers->getItems() as $bestSeller) {
            $result[$bestSeller->getData('product_id')] = $this->getPriorityByProportion(
                (int)($bestSeller->getData('qty_ordered') * 100 / $maxQtyOrdered)
            );
        }

        return $result;
    }

    private function prepareCollection(): BestSellersCollection
    {
        $storeIds = array_keys($this->storeManager->getStores(true));
        /** @var BestSellersCollection $bestSellers */
        $collection = $this->bestSellersCollectionFactory->create();
        $collection
            ->setDateRange($this->configProvider->getFromDate())
            ->addStoreFilter($storeIds);
        return $collection;
    }

    private function getPriorityByProportion(int $proportionValue): ?array
    {
        foreach ($this->getPriorities() as $priority) {
            if ($priority['proportion_value'] > $proportionValue) {
                continue;
            }
            return $priority;
        }
        return null;
    }

    private function getPriorities(): array
    {
        if (is_null($this->loadedPriorities)) {
            $this->loadedPriorities = [];
            /** @var PriorityCollection $collection */
            $collection = $this->priorityCollectionFactory->create()
                ->setOrder(self::ORDER_FIELD, Select::SQL_DESC);
            /** @var ProductPriorityInterface $item */
            foreach ($collection->getItems() as $item) {
                $this->loadedPriorities[$item->getName()] = $item->toArray();
            }
        }
        return $this->loadedPriorities;
    }
}