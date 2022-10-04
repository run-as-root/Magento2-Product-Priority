<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Controller\Adminhtml\Product;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\HttpGetActionInterface;

class Priorities extends Action implements HttpGetActionInterface
{
    private PageFactory $pageFactory;

    public function __construct(
        Context $context,
        PageFactory $rawFactory
    ) {
        $this->pageFactory = $rawFactory;

        parent::__construct($context);
    }

    public function execute(): Page
    {
        $resultPage = $this->pageFactory->create();
        $resultPage->setActiveMenu('RunAsRoot_ProductPriorities::product_priorities');
        $resultPage->getConfig()->getTitle()->prepend(__('Product Priorities'));

        return $resultPage;
    }
}