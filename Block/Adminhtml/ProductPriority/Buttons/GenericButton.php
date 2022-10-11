<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;

class GenericButton
{
    protected Context $context;

    protected ProductPriorityRepositoryInterface $productPriorityRepository;

    public function __construct(
        Context $context,
        ProductPriorityRepositoryInterface $productPriorityRepository
    ) {
        $this->context = $context;
        $this->productPriorityRepository = $productPriorityRepository;
    }

    public function getEntityId(): ?int
    {
        $entityId = (int)$this->context->getRequest()->getParam('entity_id');
        if (!$entityId) {
            return null;
        }
        try {
            return $this->productPriorityRepository->get($entityId)->getEntityId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    public function getUrl($route = '', $params = []): string
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}