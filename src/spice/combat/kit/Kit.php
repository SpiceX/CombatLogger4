<?php

namespace spice\combat\kit;

use pocketmine\item\Item;
use pocketmine\player\Player;

class Kit
{
	private string $name;
	private array $items;

	/**
	 * Kit constructor.
	 * @param string $name
	 * @param Item[] $items
	 */
	public function __construct(string $name, array $items)
	{

		$this->name = $name;
		$this->items = $items;
	}

	public function applyTo(Player $player)
	{
		foreach ($this->items as $item) {
			if ($player->getInventory()->canAddItem($item)) {
				$player->getInventory()->addItem($item);
			} else {
				$player->getWorld()->dropItem($player->getLocation(), $item);
			}
		}
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getItems(): array
	{
		return $this->items;
	}
}