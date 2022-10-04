<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'run_as_root_product_priority_collection';
    protected $_eventObject = 'product_priority_collection';

    protected function _construct(): void
    {
        $this->_init(
            \RunAsRoot\ProductPriorities\Model\ProductPriority::class,
            \RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority::class
        );
    }
}