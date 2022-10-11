<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Model;

class ProductPriority
    extends \Magento\Framework\Model\AbstractModel
    implements
    \Magento\Framework\DataObject\IdentityInterface,
    \RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface
{
    public const CACHE_TAG = 'run_as_root_product_priority';

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getEntityId()];
    }

    public function getEntityId(): ?int
    {
        return (int)$this->getData(self::KEY_ENTITY_ID);
    }

    public function getName()
    {
        return $this->getData(self::KEY_NAME);
    }

    public function setName(string $name): ProductPriority
    {
        return $this->setData(self::KEY_NAME, $name);
    }

    public function getCellColor()
    {
        return $this->getData(self::KEY_CELL_COLOR);
    }

    public function setCellColor(string $cellColor): ProductPriority
    {
        $this->setData(self::KEY_CELL_COLOR, $cellColor);
    }

    public function getProportionValue()
    {
        return $this->getData(self::KEY_PROPORTION_VALUE);
    }

    public function setProportionValue(int $proportionValue): ProductPriority
    {
        return $this->setData(self::KEY_PROPORTION_VALUE, $proportionValue);
    }
}