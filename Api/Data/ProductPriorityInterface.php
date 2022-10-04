<?php

namespace RunAsRoot\ProductPriorities\Api\Data;

interface ProductPriorityInterface
{
    const KEY_ENTITY_ID = 'entity_id';
    const KEY_NAME = 'name';
    const KEY_CELL_COLOR = 'cell_color';
    const KEY_PROPORTION_VALUE = 'proportion_value';

    /**
     * Get entity ID
     *
     * @return int|null
     */
    public function getEntityId();

    /**
     * Set entity ID
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Get priority name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Set priority name
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name);

    /**
     * Get priority name
     *
     * @return string|null
     */
    public function getCellColor();

    /**
     * Set priority cell color
     *
     * @param string $cellColor
     * @return $this
     */
    public function setCellColor(string $cellColor);

    /**
     * Get priority proportion value
     *
     * @return int|null
     */
    public function getProportionValue();

    /**
     * Set priority proportion value
     *
     * @param int $proportionValue
     * @return $this
     */
    public function setProportionValue(int $proportionValue);
}