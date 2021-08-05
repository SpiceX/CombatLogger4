<?php

namespace spice\combat;

use pocketmine\scheduler\Task;
use spice\combat\session\CombatManager;

class CombatHeartbeatTask extends Task
{
	private CombatManager $manager;

	/**
	 * CombatHeartbeatTask constructor.
	 */
	public function __construct(CombatManager $manager)
	{
		$this->manager = $manager;
	}

	public function onRun(): void
	{
		foreach ($this->manager->getSessions() as $session){
			$session->tick();
		}
	}
}