<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Controller\Adminhtml\Product\Priority;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Backend\App\Action\Context;
use PHPUnit\Framework\TestCase;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority\Delete;

class DeleteTest extends TestCase
{
    private ProductPriorityRepositoryInterface $productPriorityRepositoryMock;

    private Redirect $redirectMock;

    private MessageManagerInterface $messageManagerMock;

    private RequestInterface $requestMock;

    private Delete $sut;

    protected function setUp(): void
    {
        $contextMock = $this->createMock(Context::class);
        $this->productPriorityRepositoryMock = $this->createMock(
            ProductPriorityRepositoryInterface::class
        );

        $this->redirectMock = $this->createMock(Redirect::class);
        $resultFactoryMock = $this->createMock(RedirectFactory::class);

        $contextMock
            ->method('getResultRedirectFactory')
            ->willReturn($resultFactoryMock);

        $resultFactoryMock
            ->method('create')
            ->willReturn($this->redirectMock);

        $this->messageManagerMock = $this->createMock(MessageManagerInterface::class);
        $contextMock
            ->method('getMessageManager')
            ->willReturn($this->messageManagerMock);

        $this->requestMock = $this->getMockBuilder(RequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $this->sut = new Delete($contextMock, $this->productPriorityRepositoryMock);
    }

    public function testExecuteWithoutEntityId(): void
    {
        $this->requestMock
            ->method('getParam')
            ->willReturn(null);

        $errorMessage = __('We can\'t find a priority to delete.');
        $this->messageManagerMock
            ->method('addErrorMessage')
            ->with($errorMessage)
            ->willReturnSelf();

        $this->redirectMock
            ->method('setPath')
            ->with('*/*/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testExecuteWithException(): void
    {
        $entityId = 10;

        $this->requestMock
            ->method('getParam')
            ->willReturn($entityId);

        $errorMessage = __('Cannot delete priority with id 10');

        $this->productPriorityRepositoryMock
            ->method('deleteById')
            ->with($entityId)
            ->willThrowException(new CouldNotDeleteException($errorMessage));

        $this->messageManagerMock
            ->method('addErrorMessage')
            ->with($errorMessage)
            ->willReturnSelf();

        $this->redirectMock
            ->method('setPath')
            ->with('*/*/priority_edit', ['entity_id' => $entityId])
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testExecute(): void
    {
        $entityId = 10;

        $this->requestMock
            ->method('getParam')
            ->willReturn($entityId);

        $successMessage = __('You deleted the priority.');

        $this->productPriorityRepositoryMock
            ->method('deleteById')
            ->with($entityId)
            ->willReturn(true);

        $this->messageManagerMock
            ->method('addSuccessMessage')
            ->with($successMessage)
            ->willReturnSelf();

        $this->redirectMock
            ->method('setPath')
            ->with('*/*/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }
}
