package me.provsalt.lightningdeath;

import cn.nukkit.plugin.PluginBase;
import me.provsalt.lightningdeath.events.Death;

public class Loader extends PluginBase {
    @Override
    public void onEnable(){
        saveDefaultConfig();
        getServer().getPluginManager().registerEvents(new Death(this), this);
    }
}
