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
		$this->setLore(["Writes Enchantments and Lore onto items"]);
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
			$form = new SimpleForm(function($player, $data){});
			foreach($player->getInventory()->getContents(true) as $slot => $item) {
				if(!$item instanceof Pen and $item->getCount() === 1 and !$item instanceof Air) {
					$form->addButton($item->getName());
					$items[] = $item;
					$slots[] = $slot;
				}
				if(!$item instanceof Pen and $item->getCount() > 1 and !$item instanceof Air) {
					$form->addButton($item->getName() . " x".$item->getCount());
					$items[] = $item;
					$slots[] = $slot;
				}
			}
			$form->setCallable(function(Player $player, $data) use ($items, $slots) {
				if($data === null)
					return;
				/** @var Item $item */
				$item = clone $items[$data];
				$slot = $slots[$data];
				$form = new SimpleForm(function(){});
				$form->setTitle("Pen Selection");
				$form->addButton("Write Enchantment");
				$form->addButton("Write Lore");
				$form->setCallable(function(Player $player, $data) use($item, $slot) {
					if($data === null)
						return;
					if($data == 0) {
						$form = new CustomForm(function(){});
						$form->setTitle("Enchantments");
						$i = 1;
						foreach($item->getEnchantments() as $enchantment) {
							$form->addInput("Enchantment Slot ".$i++, "", $enchantment->getType()->getName());
						}
						$form->addInput("Enchantment Slot ".$i++);
						$form->addInput("Enchantment Slot ".$i++);
						$form->addInput("Enchantment Slot ".$i++);
						$form->addInput("Enchantment Slot ".$i);
						$form->setCallable(function(Player $player, $data) use($item, $slot) {
							if($data === null and !is_array($data))
								return;
							foreach($data as $string) {
								$parse = explode(" ", $string);
								if($parse === false or count($parse) > 2)
									continue;
								$ench = Enchantment::getEnchantmentByName($parse[0]);
								// TODO: roman numeral converter
								if($ench !== null)
									$item->addEnchantment(new EnchantmentInstance($ench, (int)($parse[1] ?? 1)));
								$player->getInventory()->setItem($slot, $item, false);
							}
							$player->getInventory()->sendContents($player);
							$this->applyDamage(1);
						});
						$player->sendForm($form);
					}else{
						$form = new CustomForm(function(){});
						$form->setTitle("Lore Text");
						$i = 1;
						foreach($item->getLore() as $line) {
							$form->addInput("Line ".$i++, "", $line);
						}
						$form->addInput("Line ".$i++);
						$form->addInput("Line ".$i++);
						$form->addInput("Line ".$i++);
						$form->addInput("Line ".$i);
						$form->setCallable(function(Player $player, $data) use ($item, $slot) {
							$output = array_filter($data, function($data) {
								return is_string($data) and !empty($data);
							});
							$item->setLore($output);
							$player->getInventory()->setItem($slot, $item);
							$this->applyDamage(1);
						});
						$player->sendForm($form);
					}
				});
				$player->sendForm($form);
			});
			$player->sendForm($form);
		}
		return true;
	}

	/**
	 * Returns the maximum amount of damage this item can take before it breaks.
	 *
	 * @return int
	 */
	public function getMaxDurability() : int {
		return $this->maxDurability;
	}
}