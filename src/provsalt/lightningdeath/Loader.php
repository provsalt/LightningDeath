<?php

namespace provsalt\lightningdeath;

use pocketmine\plugin\PluginBase;
use JackMD\UpdateNotifier\UpdateNotifier;

class Loader extends PluginBase {

    public function onEnable() :void{
        $this->getServer()->getPluginManager()->registerEvents(new LightningListener($this), $this);
        if ($this->getConfig()->get("version") !== 1){
            $this->getLogger()->critical("Please regenerate your config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
        UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
    }
}
