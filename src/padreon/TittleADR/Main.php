<?php
/*
 *                      _                               
 *  _ __     __ _    __| |  _ __    ___    ___    _ __  
 * | '_ \   / _` |  / _` | | '__|  / _ \  / _ \  | '_ \ 
 * | |_) | | (_| | | (_| | | |    |  __/ | (_) | | | | |
 * | .__/   \__,_|  \__,_| |_|     \___|  \___/  |_| |_|
 * |_|                                                  
 *
 * Created by PhpStorm.
 * Date: 16/06/2019
 * Time: 13.56

*/

namespace padreon\TittleADR;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\utils\Config;
use pocketmine\command\ConsoleCommandSender;
use pocketmine\utils\TextFormat;
use Frago9876543210\Specter\Specter;
use jojoe77777\FormAPI\SimpleForm;



class Main extends PluginBase {
	
    public $myConfig;

    /**
     *
     */
    public function onEnable()
    {
        @mkdir($this->getDataFolder());
        $this->saveResource("config.yml");
        $this->myConfig = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        /** @var jumlah tag yg di load $te */
        $te = count($this->myConfig->getAll());
        $this->getServer()->getLogger()->info("$te tags");
    }

    /**
     *
     */
    public function onDisable()
    {
        $this->getServer()->getLogger()->info("plugin disable");
    }


    /**
     * @param CommandSender $sender
     * @param Command $cmd
     * @param string $label
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
        $player = $sender;
        switch ($cmd->getName()) {
            case 'settag':
                if ($sender->hasPermission("set.player.tags"))
                {
                    if (count($args) == 2)
                    {
                        $target = $this->getServer()->getPlayer($args[0]);
                        if ($target instanceof Player)
                        {
                            if (!$target->isOnline())
                            {
                                $sender->sendMessage("Player not online");
                            }
                            $prefix = $this->getServer()->getPluginManager()->getPlugin("PureChat");
                            $id = $args[1];
                            $conf = $this->myConfig->get($id);
                            $permis = $conf[0];
                            $tag = $this->translateColors("$conf[1]");
                            $prefix->setPrefix($tag, $target);
                            $sender->sendMessage("Tag changed" . $target->getName());
                            $nick = $target->getName();
                            $cmd = "setuperm $nick $permis";
                            $this->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
                        }
                        else {
                            $sender->sendMessage("must be player");
                        }
                    }
                    else {
                        $sender->sendMessage("/settag nick id");
                    }
                }

                break;
            case 'tags':
                Specter::getInstance()->createPlayer(new SpecterInfo("fake"));
                break;
        }
        return true;
    }
	
	public function translateColors($message) 
	{
	$message = str_replace("§0", TextFormat::BLACK, $message);
	$message = str_replace("§1", TextFormat::DARK_BLUE, $message);
	$message = str_replace("§2", TextFormat::DARK_GREEN, $message);
	$message = str_replace("§3", TextFormat::DARK_AQUA, $message);
	$message = str_replace("§4", TextFormat::DARK_RED, $message);
	$message = str_replace("§5", TextFormat::DARK_PURPLE, $message);
	$message = str_replace("§6", TextFormat::GOLD, $message);
	$message = str_replace("§7", TextFormat::GRAY, $message);
	$message = str_replace("§8", TextFormat::DARK_GRAY, $message);
	$message = str_replace("§9", TextFormat::BLUE, $message);
	$message = str_replace("§a", TextFormat::GREEN, $message);
	$message = str_replace("§b", TextFormat::AQUA, $message);
	$message = str_replace("§c", TextFormat::RED, $message);
	$message = str_replace("§d", TextFormat::LIGHT_PURPLE, $message);
	$message = str_replace("§e", TextFormat::YELLOW, $message);
	$message = str_replace("§f", TextFormat::WHITE, $message);
	$message = str_replace("§k", TextFormat::OBFUSCATED, $message);
	$message = str_replace("§l", TextFormat::BOLD, $message);
	$message = str_replace("§m", TextFormat::STRIKETHROUGH, $message);
	$message = str_replace("§n", TextFormat::UNDERLINE, $message);
	$message = str_replace("§o", TextFormat::ITALIC, $message);
	$message = str_replace("§r", TextFormat::RESET, $message);
	return $message;
	}
	
    public function openForm(Player $player) {
        $form = new SimpleForm(function (Player $player, int $data = null) {
            if($data !== null) {
                $streeng = "$data";
                $conf = $this->myConfig->get($streeng);
                $permis = $conf[0];
                $tag = $conf[1];

                if ($player->hasPermission($permis))
                {
                    $suffix = $this->getServer()->getPluginManager()->getPlugin("PureChat");
                    $suffix->setSuffix($player, $tag);
                    $player->sendMessage(TextFormat::GREEN . "Tag changed to $conf[1]");
                }
                else{
                    $player->sendMessage(TextFormat::RED . "You don't have permission to use $conf[1]" . TextFormat::RED . " Tag");
                }

            }
        });
		$title = "§7- Tags -";
		$content = "§7Select a tag below to use it. §cLOCKED §7tags can be found at §dCrate Tags or Buycraft!";
        $form->setTitle($this->translateColors($title));
        $form->setContent($this->translateColors($content));
        $conf = $this->myConfig->getAll();
        $lock = TextFormat::RED . 'LOCKED';
        $avaible = TextFormat::GREEN . 'AVAILABLE';
        foreach ($conf as $id => $tag)
        {
            if ($player->hasPermission($tag[0]))
            {
                $form->addButton($this->translateColors("$tag[1]") . "\n" . $avaible);
            }
            else {
                $form->addButton($tag[1] . "\n" . $lock);
            }
        }
        $form->sendToPlayer($player);
        return $form;
    }
}
