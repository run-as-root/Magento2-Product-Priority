<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use RunAsRoot\ProductPriorities\ConfigProvider\General as ConfigProvider;
use RunAsRoot\ProductPriorities\DataProvider\Priority as DataProvider;

class ProductPriority extends Column
{
    public const FIELD_NAME = 'product_priority';

    public const DEFAULT_VALUE = [
        'name' => '-',
        'cell_color' => 'transparent'
    ];

    private ConfigProvider $configProvider;

    private DataProvider $dataProvider;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ConfigProvider $configProvider,
        DataProvider $dataProvider,
        array $components = [],
        array $data = []
    )
    {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->configProvider = $configProvider;
        $this->dataProvider = $dataProvider;
    }

    public function prepareDataSource(array $dataSource): array
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items']) || !$this->configProvider->isModuleEnabled()) {
            return $dataSource;
        }

        $data = $this->dataProvider->getData();

        foreach ($dataSource['data']['items'] as &$item) {
            $item[self::FIELD_NAME] = $data[$item['entity_id']] ?? self::DEFAULT_VALUE;
        }

        return $dataSource;
    }

    public function prepare()
    {
        parent::prepare();

        if (!$this->configProvider->isModuleEnabled()) {
            $config = $this->getData('config');
            $config['componentDisabled'] = true;
            $this->setData('config', $config);
        }
    }
}