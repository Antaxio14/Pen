<?php
declare(strict_types=1);
namespace jasonwynn10\Pen;

use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\block\Air;
use pocketmine\block\Block;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Pen extends Durable {
	private $maxDurability;

	public function __construct(int $uses) {
		parent::__construct(self::SADDLE, 0, "Pen");
		$this->maxDurability = $uses;
		$this->name = "Pen";
		$this->setCustomName("Pen");
		$this->setLore(["Writes Enchantments, Lore, and Names onto items"]);
	}

	/**
	 * @param Player $player
	 * @param Block $blockReplace
	 * @param Block $blockClicked
	 * @param int $face
	 * @param Vector3 $clickVector
	 *
	 * @return bool
	 */
	public function onActivate(Player $player, Block $blockReplace, Block $blockClicked, int $face, Vector3 $clickVector) : bool {
		if($player->hasPermission("pen.use")) {
			/** @var Item[] $arr */
			$items = [];
			/** @var int[] $slots */
			$slots = [];
	}
}
