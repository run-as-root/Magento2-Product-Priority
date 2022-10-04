<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Ui\Component\MassAction\Filter;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;

class MassDelete extends Action implements HttpPostActionInterface
{
    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'RunAsRoot_ProductPriorities::product_priorities';

    private CollectionFactory $collectionFactory;

    private ProductPriorityRepositoryInterface $productPriorityRepository;

    protected Filter $filter;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ProductPriorityRepositoryInterface $productPriorityRepository
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->productPriorityRepository = $productPriorityRepository;
        parent::__construct($context);
    }

    public function execute(): Redirect
    {
        /** @var Redirect $redirect */
        $redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if (!$this->getRequest()->isPost()) {
            $this->messageManager->addErrorMessage(__('Page not found'));
            return $redirect->setPath('run_as_root/product/priorities');
        }
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $productPriorityDeleted = 0;
        /** @var ProductPriorityInterface $productPriority */
        foreach ($collection->getItems() as $productPriority) {
            try {
                $this->productPriorityRepository->delete($productPriority);
                $productPriorityDeleted++;
            } catch (CouldNotDeleteException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }

        if ($productPriorityDeleted) {
            $this->messageManager->addSuccessMessage(
                __('A total of %1 record(s) have been deleted.', $productPriorityDeleted)
            );
        }
        return $redirect->setPath('run_as_root/product/priorities');
    }
}