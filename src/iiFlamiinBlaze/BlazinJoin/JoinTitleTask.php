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
use pocketmine\scheduler\PluginTask;

class JoinTitleTask extends PluginTask{

    /** @var BlazinJoin $main */
    private $main;
    /** @var Player $player */
    private $player;

    public function __construct(BlazinJoin $main, Player $player){
        $this->main = $main;
        $this->player = $player;
        parent::__construct($main);
    }

    public function onRun(int $tick) : void{
        $config = $this->main->getConfig();
        $title = str_replace("&", "§", $config->get("title"));
        $title = str_replace("%p", $this->player->getName(), $title);
        $subtitle = str_replace("&", "§", $config->get("subtitle"));
        $this->player->addTitle($title, $subtitle);
    }
}