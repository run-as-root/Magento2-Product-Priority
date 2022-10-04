<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Model;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority as ResourceModel;
use RunAsRoot\ProductPriorities\Model\ProductPriority;
use RunAsRoot\ProductPriorities\Model\ProductPriorityFactory;

class ProductPriorityRepository implements ProductPriorityRepositoryInterface
{
    /**
     * @var ProductPriority[]
     */
    protected array $instances = [];

    private ResourceModel $resource;

    private ProductPriorityFactory $productPriorityFactory;

    public function __construct(
        ResourceModel $resource,
        ProductPriorityFactory $productPriorityFactory
    ) {
        $this->resource = $resource;
        $this->productPriorityFactory = $productPriorityFactory;
    }

    public function save(ProductPriorityInterface $productPriority): ProductPriorityInterface
    {
        try {
            $this->resource->save($productPriority);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __(
                    'Could not save priority: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$productPriority->getId()]);
        return $this->get($productPriority->getId());
    }

    public function get(int $entityId): ProductPriorityInterface
    {
        if (!isset($this->instances[$entityId])) {
            /** @var ProductPriority $productPriority */
            $productPriority = $this->productPriorityFactory->create();
            $this->resource->load($productPriority, $entityId);

            if (!$productPriority->getId()) {
                throw NoSuchEntityException::singleField('entity_id', $entityId);
            }
            $this->instances[$entityId] = $productPriority;
        }
        return $this->instances[$entityId];
    }

    public function delete(ProductPriorityInterface $productPriority): bool
    {
        try {
            $entityId = $productPriority->getId();
            $this->resource->delete($productPriority);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __(
                    'Cannot delete priority with id %1',
                    $productPriority->getId()
                ),
                $e
            );
        }
        unset($this->instances[$entityId]);
        return true;
    }

    public function deleteById(int $entityId): bool
    {
        $productPriority = $this->get($entityId);
        return $this->delete($productPriority);
    }
}