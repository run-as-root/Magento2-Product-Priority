<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Controller\Adminhtml\Product\Priority;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority\Save;
use PHPUnit\Framework\TestCase;
use RunAsRoot\ProductPriorities\Model\ProductPriorityFactory;
use RunAsRoot\ProductPriorities\Model\ProductPriority as Model;

class SaveTest extends TestCase
{
    private ProductPriorityRepositoryInterface $productPriorityRepositoryMock;

    private Redirect $redirectMock;

    private MessageManagerInterface $messageManagerMock;

    private RequestInterface $requestMock;

    private DataPersistorInterface $dataPersistorMock;

    private Model $productPriorityModelMock;

    private Save $sut;

    private int $entityId = 1;

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
            ->setMethods(['getParam', 'getPostValue'])
            ->getMockForAbstractClass();

        $contextMock->expects($this->any())
            ->method('getRequest')
            ->willReturn($this->requestMock);

        $productPriorityFactoryMock = $this->createMock(ProductPriorityFactory::class);
        $this->dataPersistorMock = $this->createMock(DataPersistorInterface::class);

        $this->productPriorityModelMock = $this->createMock(Model::class);
        $productPriorityFactoryMock
            ->method('create')
            ->willReturn($this->productPriorityModelMock);

        $this->sut = new Save(
            $contextMock,
            $this->productPriorityRepositoryMock,
            $productPriorityFactoryMock,
            $this->dataPersistorMock
        );
    }

    public function testSaveActionWithoutPostValue(): void
    {
        $this->requestMock
            ->expects($this->any())
            ->method('getPostValue')
            ->willReturn(false);

        $this->redirectMock
            ->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testSaveAction(): void
    {
        $postData = [
            'name' => 'A',
            'proportion_value' => 90,
            'cell_color' => 'rgb(255,0,0)'
        ];

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['entity_id', null, $this->entityId],
                    ['back', null, false],
                ]
            );

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->entityId)
            ->willReturn($this->productPriorityModelMock);

        $this->productPriorityModelMock->expects($this->once())
            ->method('setData');

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->productPriorityModelMock);


        $this->dataPersistorMock->expects($this->any())
            ->method('clear')
            ->with('product_priority');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You saved the priority.'));

        $this->redirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testSaveActionNoId(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn(['entity_id' => 1]);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['entity_id', null, 1],
                    ['back', null, 'close']
                ]
            );

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->entityId)
            ->willThrowException(new NoSuchEntityException(__('Error message')));

        $this->messageManagerMock->expects($this->once())
            ->method('addErrorMessage')
            ->with(__('Error message'));

        $this->redirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/priorities')
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testSaveActionAndContinue(): void
    {
        $postData = [
            'name' => 'A',
            'proportion_value' => 90,
            'cell_color' => 'rgb(255,0,0)',
            'back' => 'continue'
        ];

        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn($postData);
        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['entity_id', null, $this->entityId],
                    ['back', null, 'continue'],
                ]
            );

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->entityId)
            ->willReturn($this->productPriorityModelMock);

        $this->productPriorityModelMock->expects($this->once())
            ->method('setData');

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->productPriorityModelMock);


        $this->dataPersistorMock->expects($this->any())
            ->method('clear')
            ->with('product_priority');

        $this->messageManagerMock->expects($this->once())
            ->method('addSuccessMessage')
            ->with(__('You saved the priority.'));

        $this->redirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/priority_edit', ['entity_id' => $this->entityId])
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }

    public function testSaveActionThrowsException(): void
    {
        $this->requestMock->expects($this->any())
            ->method('getPostValue')
            ->willReturn(['entity_id' => $this->entityId]);

        $this->requestMock->expects($this->atLeastOnce())
            ->method('getParam')
            ->willReturnMap(
                [
                    ['entity_id', null, $this->entityId],
                    ['back', null, true]
                ]
            );

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('get')
            ->with($this->entityId)
            ->willReturn($this->productPriorityModelMock);

        $this->productPriorityModelMock->expects($this->once())
            ->method('setData');

        $this->productPriorityRepositoryMock->expects($this->once())
            ->method('save')
            ->with($this->productPriorityModelMock)
            ->willThrowException(new \Exception('Error message.'));

        $this->messageManagerMock->expects($this->never())
            ->method('addSuccessMessage');
        $this->messageManagerMock->expects($this->once())
            ->method('addExceptionMessage');

        $this->dataPersistorMock->expects($this->any())
            ->method('set')
            ->with(
                'product_priority',
                [
                    'entity_id' => $this->entityId
                ]
            );

        $this->redirectMock->expects($this->atLeastOnce())
            ->method('setPath')
            ->with('*/*/priority_edit', ['entity_id' => $this->entityId])
            ->willReturnSelf();

        self::assertInstanceOf(Redirect::class, $this->sut->execute());
    }
}
