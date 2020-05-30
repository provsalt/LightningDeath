package me.provsalt.lightningdeath.events;

import cn.nukkit.Player;
import cn.nukkit.Server;
import cn.nukkit.entity.Entity;
import cn.nukkit.event.EventHandler;
import cn.nukkit.event.Listener;
import cn.nukkit.event.player.PlayerDeathEvent;
import cn.nukkit.level.Sound;
import cn.nukkit.math.Vector3;
import cn.nukkit.network.protocol.AddEntityPacket;
import cn.nukkit.utils.Config;
import me.provsalt.lightningdeath.Loader;

public class Death implements Listener {
    public Loader Main;

    public Death(Loader loader){
        Main = loader;
    }
    @EventHandler
    public void DeathEvent(PlayerDeathEvent event){
        Player player = event.getEntity();
        Config cfg = Main.getConfig();
        if (player.hasPermission("lightningdeath.bypass")){
            return;
        }
        boolean inWorld = false;
        for (String World : cfg.getStringList("worlds")) {
            if (player.getLevel().getName().equals(World)){
                inWorld = true;
                break;
            }
        }
        if (inWorld){
            AddEntityPacket light;
            light = new AddEntityPacket();
            light.type = 93;
            light.entityRuntimeId = Entity.entityCount++;
            light.yaw = (float) player.getYaw();
            light.pitch = (float) player.getPitch();
            light.x = player.getFloorX();
            light.y = player.getFloorY();
            light.z = player.getFloorZ();
            Server.broadcastPacket(player.getLevel().getPlayers().values(), light);
            player.level.addSound(new Vector3(player.getX(), player.getY(), player.getZ()), Sound.AMBIENT_WEATHER_THUNDER);
        }
    }
}
