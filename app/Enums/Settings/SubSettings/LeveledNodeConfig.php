<?php

namespace App\Enums\Settings\SubSettings;

use App\Enums\Settings\SettingNode;
use App\Enums\Settings\LevelUnit;
use App\Enums\Settings\LevelState;
use App\Enums\Settings\LevelAction;

/**
 * Class LeveledNodeConfig. Leveled Setting Config
 * @package App\Enums\Settings\SubSettings
 */
class LeveledNodeConfig
{
    private LevelState $_state;
    private LevelUnit $_unit;
    private string $_value;
    private LevelAction $_action;

    public function __construct(LevelState $state, LevelUnit $unit, string $value, LevelAction $action)
    {
        $this->setState($state);
        $this->setUnit($unit);
        $this->setValue($value);
        $this->setAction($action);
    }

    #region Getters & Setters
    /**
     * @return LevelState
     */
    public function getState(): LevelState
    {
        return $this->_state;
    }

    /**
     * @param LevelState $state
     */
    public function setState(LevelState $state): void
    {
        $this->_state = $state;
    }

    /**
     * @return LevelUnit
     */
    public function getUnit(): LevelUnit
    {
        return $this->_unit;
    }

    /**
     * @param LevelUnit $unit
     */
    public function setUnit(LevelUnit $unit): void
    {
        $this->_unit = $unit;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->_value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->_value = $value;
    }

    /**
     * @return LevelAction
     */
    public function getAction(): LevelAction
    {
        return $this->_action;
    }

    /**
     * @param LevelAction $action
     */
    public function setAction(LevelAction $action): void
    {
        $this->_action = $action;
    }
    #endregion
}
