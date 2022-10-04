<?php

namespace RunAsRoot\ProductPriorities\Api;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;

interface ProductPriorityRepositoryInterface
{
    /**
     * @param ProductPriorityInterface $productPriority
     * @return ProductPriorityInterface
     * @throws CouldNotSaveException
     */
    public function save(ProductPriorityInterface $productPriority): ProductPriorityInterface;

    /**
     * @param int $entityId
     * @return ProductPriorityInterface
     * @throws NoSuchEntityException
     */
    public function get(int $entityId): ProductPriorityInterface;

    /**
     * @param ProductPriorityInterface $productPriority
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ProductPriorityInterface $productPriority): bool;

    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool;
}