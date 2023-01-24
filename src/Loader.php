<?php

namespace provsalt\lightningdeath;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {
    private LightningListener $lightningListener;

    public function onEnable() :void{
        $this->getServer()->getPluginManager()->registerEvents($this->lightningListener = new LightningListener($this), $this);
        if ($this->getConfig()->get("version") !== 1){
            $this->getLogger()->critical("Please regenerate your config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    
    public function onCommands(CommandSender $sender, Command $command, string $alias, array $args) : bool {
        $doThat = count($args) === 1 : "dose that" : "do those";
        $sender->sendMessage("Little $doThat {count($args)} know what's gonna happen...");
        foreach ($args as $arg) {
            $target = $this->getServer()->getPlayerByPrefix($arg);
            if ($target === null) $sender->sendMessage("$arg who?");
            else $this->lightningListener->Lightning($target);
        }

        return true;
    }
}
