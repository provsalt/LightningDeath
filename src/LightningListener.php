<?php

namespace provsalt\lightningdeath;

use pocketmine\block\Block;
use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\network\mcpe\NetworkBroadcastUtils;
use pocketmine\network\mcpe\protocol\types\entity\PropertySyncData;
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
            $light2 = new AddActorPacket();
            $light2->actorUniqueId = Entity::nextRuntimeId();
            $light2->actorRuntimeId = 1;
            $light2->position = $player->getPosition()->asVector3();
            $light2->type = "minecraft:lightning_bolt";
            $light2->yaw = $player->getLocation()->getYaw();
            $light2->syncedProperties = new PropertySyncData([], []);

			$block = $player->getWorld()->getBlock($player->getPosition()->floor()->down());
			$particle = new BlockBreakParticle($block);

			$player->getWorld()->addParticle($pos, $particle, $player->getWorld()->getPlayers());
			$sound2 = PlaySoundPacket::create("ambient.weather.thunder", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1);

            NetworkBroadcastUtils::broadcastPackets($player->getWorld()->getPlayers(), [$light2, $sound2]);
		}
	}

	/**
	 * @return Loader
	 */
	public function getOwner(): Loader{
		return $this->owner;
	}
}
