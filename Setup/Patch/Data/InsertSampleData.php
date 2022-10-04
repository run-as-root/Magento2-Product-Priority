<?php

declare(strict_types=1);

namespace RunAsRoot\ProductPriorities\Setup\Patch\Data;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use RunAsRoot\ProductPriorities\Api\ProductPriorityRepositoryInterface;
use RunAsRoot\ProductPriorities\Api\Data\ProductPriorityInterface;
use RunAsRoot\ProductPriorities\Model\ProductPriorityFactory;

class InsertSampleData implements DataPatchInterface
{
    private ProductPriorityRepositoryInterface $productPriorityRepository;

    private ProductPriorityFactory $productPriorityFactory;

    public function __construct(
        ProductPriorityRepositoryInterface $productPriorityRepository,
        ProductPriorityFactory $productPriorityFactory
    )
    {
        $this->productPriorityRepository = $productPriorityRepository;
        $this->productPriorityFactory = $productPriorityFactory;
    }

    public function apply()
    {
        $sampleData = [
            [
                'name'              => 'A',
                'cell_color'        => 'rgb(0,128,0)',
                'proportion_value'  => 90
            ],
            [
                'name'              => 'B',
                'cell_color'        => 'rgb(9,211,56)',
                'proportion_value'  => 75
            ],
            [
                'name'              => 'C',
                'cell_color'        => 'rgb(115,255,144)',
                'proportion_value'  => 60
            ],
            [
                'name'              => 'D',
                'cell_color'        => 'rgb(255,248,115)',
                'proportion_value'  => 45
            ],
            [
                'name'              => 'E',
                'cell_color'        => 'rgb(255,165,0)',
                'proportion_value'  => 30
            ],
            [
                'name'              => 'F',
                'cell_color'        => 'rgb(255,0,0)',
                'proportion_value'  => 0
            ],
        ];

        foreach ($sampleData as $data) {
            /** @var ProductPriorityInterface $entity */
            $entity = $this->productPriorityFactory->create()->setData($data);
            try {
                $this->productPriorityRepository->save($entity);
            } catch (CouldNotSaveException $e) {
            }
        }
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}