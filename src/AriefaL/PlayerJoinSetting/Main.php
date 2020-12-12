<?php

namespace AriefaL\PlayerJoinSetting;

use pocketmine\Server;
use pocketmine\Player;

use pocketmine\plugin\PluginBase;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerExhaustEvent;

use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    public function onEnable(){
        $this->saveResource("settings.yml");
        $this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML);
        
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getServer()->getLogger()->info("§6[§fEnabling Plugin AJoinSetPlayer§6]");
    }
    

    public function onJoin (PlayerJoinEvent $event) {
		$player = $event->getPlayer();
		
		// This Code to Setting Welcome Message and Welcome Popup for player
		if($this->settings->getNested("Welcome-Message.Enable") == true) {
			// Message
			$player->sendMessage(str_replace(["{player}", "&"], [$player->getName(), "§"], $this->settings->getNested("Welcome-Message.Message")));
		}
		if($this->settings->getNested("Welcome-Popup.Enable") == true) {
			// Popup
			$player->sendPopup(str_replace(["{player}", "&"], [$player->getName(), "§"], $this->settings->getNested("Welcome-Popup.Message")));
		}
		
		// Here is Setting Player Join to Clear Inventory Player
		if($this->settings->getNested("Player-Join.ClearInv") == true) {
            $player->getArmorInventory()->clearAll();
			$player->getInventory()->clearAll();
        }
		
		// And Here to Setting Gamemode to Player
		if($this->settings->getNested("Player-Join.Gamemode") == true) {
            $player->setGamemode($this->settings->getNested("Player-Join.setGamemode"));
        }else{
			$player->setGamemode($this->getServer()->getDefaultGamemode());
		}
		
		// For Setting to Hunger and Heal this for player join in server
		if($this->settings->getNested("Player-Join.Hunger-Heal.Enable") == true) {
			$player->setHealth($this->settings->getNested("Player-Join.Hunger-Heal.Heal"));
			$player->setFood($this->settings->getNested("Player-Join.Hunger-Heal.Hunger"));
		}
    }
	
	public function noFallDamage(EntityDamageEvent $event){
		$player = $event->getEntity();
		if ($event->getCause() === EntityDamageEvent::CAUSE_FALL) {
			if($player->getAllowFlight() == true){
				$event->setCancelled(true);
			}else{
				$event->setBaseDamage(3.0);
				$event->setCancelled($this->settings->getNested("Player-Join.NoFallDamage"));
			}
		}
    }
	
	public function onExhaust(PlayerExhaustEvent $event) {
		$player = $event->getPlayer();
        if(!$player instanceof Player){
			$player->setHealth($this->settings->getNested("Player-Join.Hunger-Heal.Heal"));
			$player->setFood($this->settings->getNested("Player-Join.Hunger-Heal.Hunger"));
        }
		$event->setCancelled($this->settings->getNested("Player-Join.NoHunger"));
    }
	
	public function onDisable(){
        $this->getServer()->getLogger()->info("§6[§cDisabling Plugin AJoinSetPlayer§6]");
	}
}