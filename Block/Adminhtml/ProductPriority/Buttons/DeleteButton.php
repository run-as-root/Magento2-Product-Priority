<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Block\Adminhtml\ProductPriority\Buttons;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    public function getButtonData(): array
    {
        $data = [];
        if ($entityId = $this->getEntityId()) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl($entityId) . '\', {"data": {}})',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl(int $entityId): string
    {
        return $this->getUrl('*/*/priority_delete', ['entity_id' => $entityId]);
    }
}