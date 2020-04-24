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

use pocketmine\item\Item;
use pocketmine\level\particle\Particle;
use pocketmine\network\mcpe\protocol\ActorEventPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;
use pocketmine\scheduler\Task;
use pocketmine\utils\TextFormat;

class JoinTask extends Task{

	/** @var Player $player */
	private $player;

	public function __construct(Player $player){
		$this->player = $player;
	}

	public function onRun(int $tick) : void{
		$config = BlazinJoin::getInstance()->getConfig();
		$this->player->addTitle(TextFormat::colorize(strval($config->get("title"))), TextFormat::colorize(strval($config->get("subtitle"))));
		if(!$this->player->hasPlayedBefore()){
			$message = TextFormat::colorize(str_replace(["{player}", "{line}"], [$this->player->getName(), "\n"], strval($config->get("new-player-message"))));
			$config->get("new-player-message-type") === "server" ? (BlazinJoin::getInstance()->getServer()->broadcastMessage($message)) : ($this->player->sendMessage($message));
		}
		if($config->get("guardian-curse") === "enabled"){
			$pk = new LevelEventPacket();
			$pk->evid = LevelEventPacket::EVENT_GUARDIAN_CURSE;
			$pk->data = 1;
			$pk->position = $this->player->asVector3();
			$this->player->dataPacket($pk);
		}
		if($config->get("totem-effect") === "enabled"){
			$item = $this->player->getInventory()->getItemInHand();
			$this->player->getInventory()->setItemInHand(Item::get(Item::TOTEM));
			$pk = new LevelEventPacket();
			$pk->position = $this->player->asVector3();
			$pk->evid = LevelEventPacket::EVENT_SOUND_TOTEM;
			$pk->data = 0;
			$this->player->sendDataPacket($pk);
			$pk = new LevelEventPacket;
			$pk->evid = LevelEventPacket::EVENT_ADD_PARTICLE_MASK | (Particle::TYPE_TOTEM & 0xFFF);
			$pk->position = $this->player->asVector3();
			$pk->data = 0;
			$this->player->sendDataPacket($pk);
			$pk = new ActorEventPacket();
			$pk->entityRuntimeId = $this->player->getId();
			$pk->event = ActorEventPacket::CONSUME_TOTEM;
			$pk->data = 0;
			$this->player->sendDataPacket($pk);
			$this->player->getInventory()->setItemInHand($item);
		}
		$this->player->sendMessage(TextFormat::colorize(str_replace(["{player}", "{line}"], [$this->player->getName(), "\n"], strval($config->get("message")))));
	}
}