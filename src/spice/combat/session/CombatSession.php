<?php

namespace spice\combat\session;

use pocketmine\player\Player;
use pocketmine\Server;
use spice\combat\CombatLogger;
use spice\combat\kit\Kit;

class CombatSession
{
	/** @var bool */
	private bool $tagged = false;
	/** @var int */
	private int $tagTime = 10;
	/** @var Player */
	private Player $player;
	/** @var string|null */
	private ?string $oponent = null;

	/**
	 * CombatSession constructor.
	 * @param Player $Player
	 */
	public function __construct(Player $Player)
	{
		$this->player = $Player;
	}

	public function tick()
	{
		if ($this->tagged && $this->tagTime >= 0) {
			$this->tagTime--;
		}
		if ($this->oponent !== null) {
			$oponent = Server::getInstance()->getPlayerByPrefix($this->oponent);
			if (!$oponent instanceof Player || !$oponent->isOnline()) {
				$this->reward();
				return;
			}
		}
		if ($this->tagTime === 0) {
			$this->setUntagged();
		}
	}

	public function setTagged(Player $opponent)
	{
		if ($this->tagged) {
			$this->tagTime = 10;
			return;
		}
		$this->oponent = $opponent->getName();
		$this->tagged = true;
		$this->player->sendMessage("§eNow you are in combat with §a" . $opponent->getName());
	}

	public function setUntagged()
	{
		$this->oponent = null;
		$this->tagged = false;
		$this->tagTime = 10;
		$this->player->sendMessage("§eYou are out of combat.");
	}

	public function reward()
	{
		$this->player->setHealth(20.0);
		$kit = CombatLogger::getInstance()->getKitManager()->getRandomKit();
		if ($kit instanceof Kit) {
			$kit->applyTo($this->player);
		}
		$this->setUntagged();
	}

	public function isTagged(): bool
	{
		return $this->tagged;
	}

	/**
	 * @return Player
	 */
	public function getPlayer(): Player
	{
		return $this->player;
	}

	/**
	 * @return string|null
	 */
	public function getOponent(): ?string
	{
		return $this->oponent;
	}
}