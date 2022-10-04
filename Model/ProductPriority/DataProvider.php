<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Model\ProductPriority;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\Collection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var Collection $collection
     */
    protected $collection;

    protected DataPersistorInterface $dataPersistor;

    protected array $loadedData = [];

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    )
    {
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
    }

    public function getData(): array
    {
        if (!empty($this->loadedData)) {
            return $this->loadedData;
        }
        /** @var ProductPriorityInterface $productPriority */
        foreach ($this->collection->getItems() as $productPriority) {
            $this->loadedData[$productPriority->getEntityId()] = $productPriority->getData();
        }

        $data = $this->dataPersistor->get('product_priority');
        if (!empty($data)) {
            $productPriority = $this->collection->getNewEmptyItem();
            $productPriority->setData($data);
            $this->loadedData[$productPriority->getEntityId()] = $productPriority->getData();
            $this->dataPersistor->clear('product_priority');
        }

        return $this->loadedData;
    }
}