<?php

namespace provsalt\lightningdeath;

use pocketmine\entity\Entity;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\network\mcpe\protocol\AddActorPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {
    public $cfg;
    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        if ($this->cfg->get("version") !== 1){
            $this->getLogger()->critical("Please regenerate your config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    public function onDeath(PlayerDeathEvent $event) {
        $this->Lightning($event->getPlayer());
    }
    public function Lightning($player) {
        $inworld = false;
        foreach ($this->cfg->get("worlds") as $worlds){
            if ($player->getLevel() === $this->getServer()->getLevelByName($worlds)){
                $inworld = true;
                break;
            }
        }
        if($inworld) {
            $light = $player->getLevel();
            $light = new AddActorPacket();
            $light->type = 93;
            $light->entityRuntimeId = Entity::$entityCount++;
            $light->metadata = array();
            $light->motion = null;
            $light->yaw = $player->getYaw();
            $light->pitch = $player->getPitch();
            $light->position = new Vector3($player->getX(), $player->getY(), $player->getZ());
            $this->getServer()->broadcastPacket($player->getLevel()->getPlayers(), $light);
            // Sends lightning packet

        }
    }
}
