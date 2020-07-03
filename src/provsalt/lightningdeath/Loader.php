<?php

namespace provsalt\lightningdeath;

use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase {

    public function onEnable() :void{
        $this->getServer()->getPluginManager()->registerEvents(new LightningListener($this), $this);
        $this->saveDefaultConfig();
        if ($this->getConfig()->get("version") !== 1){
            $this->getLogger()->critical("Please regenerate your config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
}
