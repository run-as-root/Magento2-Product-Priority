<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Controller\Adminhtml\Product;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use PHPUnit\Framework\TestCase;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\MassDelete;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\Collection;
use RunAsRoot\ProductPriorities\Model\ResourceModel\ProductPriority\CollectionFactory;

class MassDeleteTest extends TestCase
{
    private RequestInterface $requestMock;

    private Context $contextMock;

    private CollectionFactory $collectionFactoryMock;

    private ProductPriorityRepositoryInterface $productPriorityRepositoryMock;

    private Filter $filterMock;

    private MassDelete $sut;

    private Redirect $redirectMock;

    private MessageManagerInterface $messageManagerMock;

    protected function setUp(): void
    {
        $this->contextMock = $this->createMock(Context::class);
        $this->filterMock = $this->createMock(Filter::class);
        $this->collectionFactoryMock = $this->createMock(CollectionFactory::class);
        $this->productPriorityRepositoryMock = $this->createMock(ProductPriorityRepositoryInterface::class);

        $this->requestMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $resultFactoryMock = $this->getMockBuilder(\Magento\Framework\Controller\ResultFactory::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->contextMock->expects($this->any())
            ->method('getResultFactory')
            ->willReturn($resultFactoryMock);

        $this->redirectMock = $this->createMock(Redirect::class);
        $resultFactoryMock
            ->method('create')
            ->with(ResultFactory::TYPE_REDIRECT)
            ->willReturn($this->redirectMock);

        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);
        $this->contextMock
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);

        $this->sut = new MassDelete(
            $this->contextMock,
            $this->filterMock,
            $this->collectionFactoryMock,
            $this->productPriorityRepositoryMock
        );
    }

    public function testExecuteNotPostMethod(): void
    {
        $this->requestMock
            ->method('isPost')
            ->willReturn(false);

        $this->messageManagerMock
            ->method('addErrorMessage')
            ->with(__('Page not found'))
            ->willReturnSelf();

        $this->redirectMock
            ->method('setPath')
            ->with('run_as_root/product/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testExecute(): void
    {
        $this->requestMock
            ->method('isPost')
            ->willReturn(true);

        $collectionMock = $this->createMock(Collection::class);
        $this->collectionFactoryMock
            ->method('create')
            ->willReturn($collectionMock);
        $this->filterMock
            ->method('getCollection')
            ->with($collectionMock)
            ->willReturn($collectionMock);

        $priorityMock1 = $this->createMock(ProductPriorityInterface::class);
        $priorityMock2 = $this->createMock(ProductPriorityInterface::class);
        $collectionMock
            ->method('getItems')
            ->willReturn([$priorityMock1, $priorityMock2]);

        $this->productPriorityRepositoryMock
            ->expects($this->at(0))
            ->method('delete')
            ->with($priorityMock1)
            ->willReturn(true);


        $errorMessage = __('Cannot delete priority with id 5');
        $this->productPriorityRepositoryMock
            ->expects($this->at(1))
            ->method('delete')
            ->with($priorityMock2)
            ->willThrowException(new CouldNotDeleteException($errorMessage));

        $this->messageManagerMock
            ->method('addErrorMessage')
            ->with($errorMessage)
            ->willReturnSelf();

        $successMessage = __('A total of %1 record(s) have been deleted.', 1);
        $this->messageManagerMock
            ->method('addSuccessMessage')
            ->with($successMessage)
            ->willReturnSelf();

        $this->redirectMock
            ->method('setPath')
            ->with('run_as_root/product/priorities')
            ->willReturnSelf();


        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }
}
