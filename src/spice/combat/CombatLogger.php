<?php

namespace spice\combat;

use pocketmine\plugin\PluginBase;
use spice\combat\kit\KitManager;
use spice\combat\session\CombatManager;

class CombatLogger extends PluginBase
{
	private static CombatLogger $instance;
	private CombatManager $combatManager;
	private KitManager $kitManager;

	public function onEnable(): void
	{
		self::$instance = $this;
		$this->kitManager = new KitManager($this);
		$this->getServer()->getPluginManager()->registerEvents(new CombatListener($this), $this);
		$this->getScheduler()->scheduleRepeatingTask(
			new CombatHeartbeatTask($this->combatManager = new CombatManager($this)), 20
		);
		$this->getServer()->getCommandMap()->register("CombatLogger3", new CombatCommand($this));
	}

	public function onDisable(): void
	{
		$this->kitManager->saveKits();
	}

	/**
	 * @return CombatManager
	 */
	public function getCombatManager(): CombatManager
	{
		return $this->combatManager;
	}

	/**
	 * @return KitManager
	 */
	public function getKitManager(): KitManager
	{
		return $this->kitManager;
	}

	/**
	 * @return CombatLogger
	 */
	public static function getInstance(): CombatLogger
	{
		return self::$instance;
	}
}