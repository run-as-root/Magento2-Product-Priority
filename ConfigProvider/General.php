<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\ConfigProvider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class General
{
    private const CONFIG_PATH_MODULE_IS_ENABLED = 'run_as_root_product_priority/general/enabled';
    private const CONFIG_PATH_FROM_DATE = 'run_as_root_product_priority/general/from_date';

    private ScopeConfigInterface $config;

    public function __construct(ScopeConfigInterface $config)
    {
        $this->config = $config;
    }

    public function isModuleEnabled(int $storeId = 0): bool
    {
        return $this->config->isSetFlag(self::CONFIG_PATH_MODULE_IS_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getFromDate(int $storeId = 0): ?string
    {
        return $this->config->getValue(self::CONFIG_PATH_FROM_DATE, ScopeInterface::SCOPE_STORE, $storeId);
    }
}