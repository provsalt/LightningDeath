<?php

namespace provsalt\lightningdeath;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\level\particle\DestroyBlockParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\Player;
use pocketmine\Server;

class LightningListener implements Listener {

	/**
	 * @var Loader
	 */
	private $owner;

	public function __construct(Loader $owner){
		$this->owner = $owner;
	}


	public function onDeath(PlayerDeathEvent $event) :bool{
		if ($event->getPlayer()->hasPermission("lightningdeath.bypass")){
			return false;
		}
		$this->Lightning($event->getPlayer());
		return true;
	}
	public function Lightning(Player $player) :void{
		if(in_array($player->getLevel()->getFolderName(), $this->getOwner()->getConfig()->get("worlds"))){
			$light = new AddActorPacket();
			$light->type = "minecraft:lightning_bolt";
			$light->entityRuntimeId = Entity::$entityCount++;
			$light->metadata = [];
			$light->motion = null;
			$light->yaw = $player->getYaw();
			$light->pitch = $player->getPitch();
			$light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
			Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $light);
			$block = $player->getLevel()->getBlock($player->getPosition()->floor()->down());
			$particle = new DestroyBlockParticle(new Vector3($player->getX(), $player->getY(), $player->getZ()), $block);
			$player->getLevel()->addParticle($particle);
			$sound = new PlaySoundPacket();
			$sound->soundName = "ambient.weather.thunder";
			$sound->x = $player->getX();
			$sound->y = $player->getY();
			$sound->z = $player->getZ();
			$sound->volume = 1;
			$sound->pitch = 1;
			Server::getInstance()->broadcastPacket($player->getLevel()->getPlayers(), $sound);
		}
	}

	/**
	 * @return Loader
	 */
	public function getOwner(): Loader{
		return $this->owner;
	}
}