<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\AbstractPriority;

class Delete extends AbstractPriority implements HttpPostActionInterface
{
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = (int)$this->getRequest()->getParam('entity_id');
        if ($id) {
            try {
                $this->productPriorityRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the priority.'));
                return $resultRedirect->setPath('*/*/priorities');
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                return $resultRedirect->setPath('*/*/priority_edit', ['entity_id' => $id]);
            }
        }
        $this->messageManager->addErrorMessage(__('We can\'t find a priority to delete.'));
        return $resultRedirect->setPath('*/*/priorities');
    }
}