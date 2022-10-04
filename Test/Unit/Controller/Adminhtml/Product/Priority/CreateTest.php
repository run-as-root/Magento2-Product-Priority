<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Controller\Adminhtml\Product\Priority;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use RunAsRoot\ProductPriorities\Controller\Adminhtml\Product\Priority\Create;
use PHPUnit\Framework\TestCase;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\Result\Forward;

class CreateTest extends TestCase
{
    protected ForwardFactory $resultForwardFactoryMock;

    private Create $sut;

    private Redirect $redirectMock;

    private MessageManagerInterface $messageManagerMock;

    protected function setUp(): void
    {
        $this->resultForwardFactoryMock = $this->createMock(ForwardFactory::class);
        $contextMock = $this->createMock(Context::class);

        $this->sut = new Create($contextMock, $this->resultForwardFactoryMock);
    }

    public function testExecute(): void
    {
        $resultForwardMock = $this->createMock(Forward::class);
        $this->resultForwardFactoryMock
            ->method('create')
            ->willReturn($resultForwardMock);

        $resultForwardMock
            ->method('forward')
            ->with('priority_edit')
            ->willReturnSelf();

        self::assertInstanceOf(Forward::class, $this->sut->execute());
    }
}
