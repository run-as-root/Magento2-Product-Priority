<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Model\ProductPriority;
use RunAsRoot\ProductPriorities\Model\ProductPriorityFactory;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\AbstractPriority;

class Save extends AbstractPriority implements HttpPostActionInterface
{
    private DataPersistorInterface $dataPersistor;

    private ProductPriorityFactory $productPriorityFactory;

    public function __construct(
        Context $context,
        ProductPriorityRepositoryInterface $productPriorityRepository,
        ProductPriorityFactory $productPriorityFactory,
        DataPersistorInterface $dataPersistor
    )
    {
        parent::__construct($context, $productPriorityRepository);

        $this->productPriorityFactory = $productPriorityFactory;
        $this->dataPersistor = $dataPersistor;
    }

    public function execute(): Redirect
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            if (empty($data['entity_id'])) {
                $data['entity_id'] = null;
            }

            /** @var ProductPriority $model */
            $model = $this->productPriorityFactory->create();

            $id = (int)$this->getRequest()->getParam('entity_id');
            if ($id) {
                try {
                    $model = $this->productPriorityRepository->get($id);
                } catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage($e->getMessage());
                    return $resultRedirect->setPath('*/*/priorities');
                }
            }

            $model->setData($data);

            try {
                $this->productPriorityRepository->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the priority.'));
                $this->dataPersistor->clear('product_priority');
                return $this->processReturn($model, $data, $resultRedirect);
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the entity.'));
            }

            $this->dataPersistor->set('product_priority', $data);
            return $resultRedirect->setPath('*/*/priority_edit', ['entity_id' => $id]);
        }
        return $resultRedirect->setPath('*/*/priorities');
    }

    private function processReturn(ProductPriority $model, array $data, ResultInterface $resultRedirect): ResultInterface
    {
        $redirect = $data['back'] ?? 'close';

        if ($redirect ==='continue') {
            $resultRedirect->setPath('*/*/priority_edit', ['entity_id' => $model->getEntityId()]);
        } else if ($redirect === 'close') {
            $resultRedirect->setPath('*/*/priorities');
        }
        return $resultRedirect;
    }
}