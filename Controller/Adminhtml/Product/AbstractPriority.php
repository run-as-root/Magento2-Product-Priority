<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;

abstract class AbstractPriority extends Action
{
    protected ProductPriorityRepositoryInterface $productPriorityRepository;

    public function __construct(
        Context $context,
        ProductPriorityRepositoryInterface $productPriorityRepository
    )
    {
        parent::__construct($context);
        $this->productPriorityRepository = $productPriorityRepository;
    }
}