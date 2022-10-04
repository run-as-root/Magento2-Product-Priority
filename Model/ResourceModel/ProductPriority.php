<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductPriority extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('run_as_root_product_priorities', 'entity_id');
    }
}