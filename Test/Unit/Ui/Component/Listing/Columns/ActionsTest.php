<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Test\Unit\Ui\Component\Listing\Columns;

use RunAsRoot\ProductPriorities\Ui\Component\Listing\Columns\Actions;
use PHPUnit\Framework\TestCase;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

class ActionsTest extends TestCase
{
    private UrlInterface $urlBuilder;

    private Actions $sut;

    protected function setUp(): void
    {
        $context = $this->createMock(ContextInterface::class);
        $uiComponentFactory = $this->createMock(UiComponentFactory::class);
        $this->urlBuilder = $this->createMock(UrlInterface::class);

        $this->sut = new Actions(
            $context,
            $uiComponentFactory,
            $this->urlBuilder
        );
    }

    public function testPrepareDataSource(): void
    {
        $entityId = 1;
        $name = 'A';
        $items = [
            'data' => [
                'items' => [
                    [
                        'entity_id' => $entityId,
                        'name' => $name,
                    ],
                ],
            ],
        ];
        $expectedItems = [
            [
                'entity_id' => $entityId,
                'name' => $name,
                $name => [
                    'edit' => [
                        'href' => 'test/url/edit',
                        'label' => __('Edit'),
                    ],
                    'delete' => [
                        'href' => 'test/url/delete',
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete'),
                            'message' => __('Are you sure you want to delete record?'),
                        ],
                        'post' => true,
                    ]
                ]
            ]
        ];

        $this->urlBuilder->expects($this->exactly(2))
            ->method('getUrl')
            ->willReturnMap(
                [
                    [
                        Actions::URL_PATH_EDIT,
                        [
                            'entity_id' => $entityId,
                        ],
                        'test/url/edit',
                    ],
                    [
                        Actions::URL_PATH_DELETE,
                        [
                            'entity_id' => $entityId,
                        ],
                        'test/url/delete',
                    ],
                ]
            );

        $this->sut->setData('name', $name);
        $actual = $this->sut->prepareDataSource($items);
        self::assertEquals($expectedItems, $actual['data']['items']);
    }
}
