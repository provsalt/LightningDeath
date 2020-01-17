<?php

declare(strict_types=1);

namespace provsalt\lightningdeath;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\Player;

class Main extends PluginBase implements Listener {
    public function onEnable()
    {
        Server::getInstance()->getPluginManager()->registerEvents($this, $this);
        //TODO Add config
    }
    public function onDeath(PlayerDeathEvent $event) {
        $this->Lightning($event->getPlayer());
    }
    public function Lightning(Player $player) {
        $light = $player->getLevel();
        $light = new AddActorPacket();
        $light->type = 93;
        $light->entityRuntimeId = Entity::$entityCount++;
        $light->metadata = array();
        $light->motion = null;
        $light->yaw = $player->getYaw();
        $light->pitch = $player->getPitch();
        $light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
        foreach ($player->getLevel()->getPlayers() as $players) {
            $players->dataPacket($light);
        }
    }
}
