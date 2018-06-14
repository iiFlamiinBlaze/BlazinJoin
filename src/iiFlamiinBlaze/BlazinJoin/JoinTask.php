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

use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class JoinTask extends PluginTask{

	/** @var Player $player */
	private $player;

	public function __construct(BlazinJoin $main, Player $player){
		$this->player = $player;
		parent::__construct($main);
	}

	public function onRun(int $tick) : void{
		$config = BlazinJoin::getInstance()->getConfig();
		$title = str_replace("&", "§", $config->get("title"));
		$title = str_replace("{player}", $this->player->getName(), $title);
		$subtitle = str_replace("&", "§", $config->get("subtitle"));
		$this->player->addTitle($title, $subtitle);
		if($config->get("guardian-curse") === "enabled"){
			$pk = new LevelEventPacket();
			$pk->evid = LevelEventPacket::EVENT_GUARDIAN_CURSE;
			$pk->data = 1;
			$pk->position = $this->player->asVector3();
			$this->player->dataPacket($pk);
		}
		$message = str_replace("&", "§", $config->get("message"));
		$message = str_replace("{player}", $this->player->getName(), $message);
		$message = str_replace("{line}", "\n", $message);
		$this->player->sendMessage($message);
	}
}