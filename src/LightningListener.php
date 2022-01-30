<?php

namespace provsalt\lightningdeath;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\player\Player;
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
		if(in_array($player->getWorld()->getFolderName(), $this->getOwner()->getConfig()->get("worlds"))){
                        $pos = $player->getPosition();
                        $light = new AddActorPacket();
			$light->type = "minecraft:lightning_bolt";
			$light->actorRuntimeId = 1;
			$light->metadata = [];
			$light->motion = null;
			$light->yaw = $player->getLocation()->getYaw();
			$light->pitch = $player->getLocation()->getPitch();
			$light->position = new Vector3($pos->getX(), $pos->getY(), $pos->getZ());
			$block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
			$particle = new BlockBreakParticle($block);
			$player->getWorld()->addParticle($pos->asVector3(), $particle, $player->getWorld()->getPlayers());
			$sound = new PlaySoundPacket();
			$sound->soundName = "ambient.weather.thunder";
                        $sound->x = $pos->getX();
                        $sound->y = $pos->getY();
			$sound->z = $pos->getZ();
			$sound->volume = 1;
			$sound->pitch = 1;
			Server::getInstance()->broadcastPackets($player->getWorld()->getPlayers(), [$light, $sound]);
		}
	}

	/**
	 * @return Loader
	 */
	public function getOwner(): Loader{
		return $this->owner;
	}
}
