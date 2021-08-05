<?php

namespace spice\combat\kit;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\plugin\PluginException;
use spice\combat\CombatLogger;

class KitManager
{
	private static BigEndianNbtSerializer $nbtWriter;
	private CombatLogger $plugin;
	/** @var Kit[] */
	private array $kits = [];

	/**
	 * KitManager constructor.
	 */
	public function __construct(CombatLogger $plugin)
	{
		$this->plugin = $plugin;
		self::$nbtWriter = new BigEndianNbtSerializer();
		$this->init();
	}

	private function init()
	{
		@mkdir($this->plugin->getDataFolder() . 'kits');
		$this->loadKits();
	}

	/**
	 * @param string $name
	 * @param Item[] $items
	 */
	public function createKit(string $name, array $items)
	{
		$this->kits[$name] = new Kit($name, $items);
	}

	public function getKit(string $name): ?Kit
	{
		return $this->kits[$name] ?? null;
	}

	public function removeKit(string $name)
	{
		@unlink($this->plugin->getDataFolder() . 'kits' . DIRECTORY_SEPARATOR . $name . 'ckit');
		if (isset($this->kits[$name])) {
			unset($this->kits[$name]);
		}
	}

	public function loadKits()
	{
		foreach (glob($this->plugin->getDataFolder() . "kits" . DIRECTORY_SEPARATOR . "*.ckit") as $kitFile) {
			$file = @fopen($kitFile, 'rb');
			$items = self::decodeInventory(fread($file, filesize($kitFile)));
			$this->createKit(basename($kitFile), $items);
			@fclose($file);
		}
	}

	public function saveKits()
	{
		foreach ($this->kits as $name => $kit) {
			$kitPath = $this->plugin->getDataFolder() . 'kits' . DIRECTORY_SEPARATOR . $name . ".ckit";
			$kitFile = @fopen($kitPath, 'wb') or die("Unable to open kit file!");
			$encoded = self::encodeInventory($kit->getItems());
			@fwrite($kitFile, $encoded);
			@fclose($kitFile);
		}
	}

	/**
	 * @return Kit|null
	 */
	public function getRandomKit(): ?Kit
	{
		if (empty($this->kits)) return null;
		return $this->kits[array_rand($this->kits)];
	}

	/**
	 * @param Item[] $items
	 * @return string
	 */
	public static function encodeInventory(array $items): string
	{
		$serializedItems = [];
		foreach ($items as $item) {
			$serializedItems[] = $item->nbtSerialize();
		}
		$nbt = CompoundTag::create();
		$nbt->setTag("Items", new ListTag($serializedItems));
		return self::$nbtWriter->write(new TreeRoot($nbt));
	}

	/**
	 * @param string $compression
	 *
	 * @return Item[]
	 */
	public static function decodeInventory(string $compression): array
	{
		if (empty($compression)) {
			return [];
		}

		$tag = self::$nbtWriter->read($compression)->mustGetCompoundTag();
		if (!$tag instanceof CompoundTag) {
			throw new PluginException("Expected a CompoundTag, got " . get_class($tag));
		}
		$content = [];
		/** @var CompoundTag $item */
		foreach ($tag->getListTag("Items")->getValue() as $item) {
			$content[] = Item::nbtDeserialize($item);
		}
		return $content;
	}
}