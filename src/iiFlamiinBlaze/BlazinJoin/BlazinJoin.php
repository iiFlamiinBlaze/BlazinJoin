<?php
/**
 *  ____  _            _______ _          _____
 * |  _ \| |          |__   __| |        |  __ \
 * | |_) | | __ _ _______| |  | |__   ___| |  | | _____   __
 * |  _ <| |/ _` |_  / _ \ |  | '_ \ / _ \ |  | |/ _ \ \ / /
 * | |_) | | (_| |/ /  __/ |  | | | |  __/ |__| |  __/\ V /
 * |____/|_|\__,_/___\___|_|  |_| |_|\___|_____/ \___| \_/
 *
 * Copyright (C) 2018 iiFlamiinBlaze
 *
 * iiFlamiinBlaze's plugins are licensed under MIT license!
 * Made by iiFlamiinBlaze for the PocketMine-MP Community!
 *
 * @author iiFlamiinBlaze
 * Twitter: https://twitter.com/iiFlamiinBlaze
 * GitHub: https://github.com/iiFlamiinBlaze
 * Discord: https://discord.gg/znEsFsG
 */
declare(strict_types=1);

namespace iiFlamiinBlaze\BlazinJoin;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\player\PlayerJoinEvent;

class BlazinJoin extends PluginBase implements Listener{

	public const VERSION = "v1.1.5";
	public const PREFIX = TextFormat::AQUA . "BlazinJoin" . TextFormat::GOLD . " > ";

	/** @var self $instance */
	private static $instance;

	public function onEnable() : void{
		self::$instance = $this;
		@mkdir($this->getDataFolder());
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info("BlazinJoin " . self::VERSION . " by iiFlamiinBlaze enabled");
	}

	public function onJoin(PlayerJoinEvent $event) : void{
		$player = $event->getPlayer();
		$this->getScheduler()->scheduleDelayedTask(new JoinTask($player), 30);
		$event->setJoinMessage(str_replace(["&", "{player}"], ["§", $player->getName()], strval($this->getConfig()->get("join-message"))));
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
		if($command->getName() === "blazinjoin"){
			if(empty($args[0])){
				$sender->sendMessage(self::PREFIX . TextFormat::GRAY . "Usage: /blazinjoin <info | set> <title | subtitle | message | curse | joinmessage> <message>");
				return false;
			}
			if(!$sender instanceof Player){
				$sender->sendMessage(self::PREFIX . TextFormat::RED . "Use this command in-game");
				return false;
			}
			if(!$sender->hasPermission("blazinjoin.command")){
				$config = $this->getConfig();
				$message = str_replace("&", "§", $config->get("no-permission"));
				$message = str_replace("{player}", $sender->getName(), $message);
				$message = str_replace("{line}", "\n", $message);
				$sender->sendMessage($message);
				return false;
			}
			switch($args[0]){
				case "info":
					$sender->sendMessage(TextFormat::DARK_GRAY . "-=========BlazinJoin " . self::VERSION . " =========-");
					$sender->sendMessage(TextFormat::GREEN . "Author: iiFlamiinBlaze");
					$sender->sendMessage(TextFormat::GREEN . "GitHub: https://github.com/iiFlamiinBlaze");
					$sender->sendMessage(TextFormat::GREEN . "Support: https://discord.gg/znEsFsG");
					$sender->sendMessage(TextFormat::GREEN . "Description: Allows you to customize multiple things when a player joins your server");
					$sender->sendMessage(TextFormat::DARK_GRAY . "-===============================-");
					break;
				case "set":
					switch($args[1]){
						case "title":
							if(is_string($args[2])){
								$config = $this->getConfig();
								$config->set("title", $args[2]);
								$config->save();
								$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set a new title in BlazinJoin config");
							}else{
								$sender->sendMessage(self::PREFIX . TextFormat::RED . "You have to set the title to a string.");
								return false;
							}
							break;
						case "subtitle":
							if(is_string($args[2])){
								$config = $this->getConfig();
								$config->set("subtitle", $args[2]);
								$config->save();
								$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set a new subtitle in BlazinJoin config");
							}else{
								$sender->sendMessage(self::PREFIX . TextFormat::RED . "You have to set the subtitle to a string.");
								return false;
							}
							break;
						case "message":
							if(is_string($args[2])){
								$config = $this->getConfig();
								$config->set("message", $args[2]);
								$config->save();
								$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set a new message in BlazinJoin config");
							}else{
								$sender->sendMessage(self::PREFIX . TextFormat::RED . "You have to set the message to a string.");
								return false;
							}
							break;
						case "joinmessage":
							if(is_string($args[2])){
								$config = $this->getConfig();
								$config->set("join-message", $args[2]);
								$config->save();
								$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set a new join message in BlazinJoin config");
							}else{
								$sender->sendMessage(self::PREFIX . TextFormat::RED . "You have to set the join message to a string.");
								return false;
							}
							break;
						case "curse":
							switch($args[2]){
								case "enabled":
									$config = $this->getConfig();
									$config->set("guardian-curse", "enabled");
									$config->save();
									$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set the guardian curse to enabled in BlazinJoin config");
									break;
								case "disabled":
									$config = $this->getConfig();
									$config->set("guardian-curse", "disabled");
									$config->save();
									$sender->sendMessage(self::PREFIX . TextFormat::GREEN . "You have now set the guardian curse to disabled in BlazinJoin config");
									break;
								default:
									$sender->sendMessage(self::PREFIX . TextFormat::RED . "You must set the curse to enabled or disabled!");
									break;
							}
							break;
					}
					break;
			}
		}
		return true;
	}

	public static function getInstance() : self{
		return self::$instance;
	}
}