<?php

namespace spice\combat\session;

use pocketmine\player\Player;
use spice\combat\CombatLogger;

class CombatManager
{
	/** @var CombatSession[] */
	private array $sessions = [];

	/** @var CombatLogger */
	private CombatLogger $plugin;

	/**
	 * CombatManager constructor.
	 * @param CombatLogger $plugin
	 */
	public function __construct(CombatLogger $plugin)
	{
		$this->plugin = $plugin;
	}

	public function registerSession(Player $player)
	{
		$this->sessions[$player->getName()] = new CombatSession($player);
	}

	public function unregisterSession(Player $player){
		unset($this->sessions[$player->getName()]);
	}

	/**
	 * @param Player $player
	 * @return CombatSession|null
	 */
	public function getSession(Player $player): ?CombatSession
	{
		return $this->sessions[$player->getName()] ?? null;
	}

	/**
	 * @return CombatSession[]
	 */
	public function getSessions(): array
	{
		return $this->sessions;
	}


}