<?php

namespace provsalt\lightningdeath;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use function array_shift;
use function array_map;
use function shuffle;
use function count;
use function array_unique;

class Loader extends PluginBase {
    private LightningListener $lightningListener;

    public function onEnable() :void{
        $this->getServer()->getPluginManager()->registerEvents($this->lightningListener = new LightningListener($this), $this);
        if ($this->getConfig()->get("version") !== 1){
            $this->getLogger()->critical("Please regenerate your config file!");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
    }
    
    public function onCommand(CommandSender $sender, Command $command, string $alias, array $args) : bool {
        [$world, $pos] = $sender instanceof Player ? [
            $sender->getWorld(),
            $sender->getPosition()
        ] : [$this->getServer()->getDefaultWorld(),
            $this->getServer()->getDefaultWorld()->getSpawnLocation()
        ];
        $nearestPlayers = $world->getPlayers();
        ushort($nearestPlayes, fn($playerA, $playerB) => $playerA->getPosition()->distance($pos) <=> $playerB->getPosition()->distance($pos));

        $randomPlayers = $this->getServer()->getOnlinePlayers();
        shuffle($randomPlayers);

        foreach ($args as &$arg) {
            if ($arg === "@a" || $arg === "@e") {
                $args = array_map(fn($player) => $player->getName(), $this->getServer()->getOnlinePlayers());
                break 2; // Break both the switch and foreach.
            }

            $arg = match ($arg) {
                "@p" => array_shift($nearestPlayers)?->getName(),
                "@r" => $arg = array_shift($randomPlayers)?->getName(),
                "@s" => $arg = $sender->getName(),
                default => $arg,
            };
        }

        foreach (array_unique($args) as $arg) {
            if ($arg === "CONSOLE") {
                // Dear Poggit reviewers: This message is a command output instead of log. Please show some mercy regarding rule B3... Your help is important for such plugin to be "fun".
                $sender->sendMessage(<<<EOT
                                     .eeeeeeeee
                                    .$$$$$$$$P"
                                   .$$$$$$$$P
                                  z$$$$$$$$P
                                 z$$$$$$$$"
                                z$$$$$$$$"
                               d$$$$$$$$"
                              d$$$$$$$$"
                            .d$$$$$$$P
                           .$$$$$$$$P
                          .$$$$$$$$$.........
                         .$$$$$$$$$$$$$$$$$$"
                        z$$$$$$$$$$$$$$$$$P"
                       -**********$$$$$$$P
                                 d$$$$$$"
                               .d$$$$$$"
                              .$$$$$$P"
                             z$$$$$$P
                            d$$$$$$"
                          .d$$$$$$"
                         .$$$$$$$"
                        z$$$$$$$beeeeee
                       d$$$$$$$$$$$$$*
                      ^""""""""$$$$$"
                              d$$$*
                             d$$$"
                            d$$*
                           d$P"
                         .$$"
                        .$P"
                       .$"
                      .P"
                     ."     Gilo94'
                    /"      www.asciiart.eu/nature/lightning
                    EOT);
                continue;
            } elseif ($arg === "") continue; // If @p or @r candidates run out.

            $target = $this->getServer()->getPlayerByPrefix($arg);
            if ($target === null) $sender->sendMessage("$arg who?");
            else $this->lightningListener->Lightning($target);
        }

        $doThat = count($args) === 1 ? "dose that" : "do those";
        $sender->sendMessage(TextFormat::BOLD . TextFormat::ITALIC . TextFormat::DARK_RED . "Little $doThat " . count($args) . " know what's gonna happen...");

        return true;
    }
}
