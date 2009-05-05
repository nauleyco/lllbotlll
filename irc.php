<?php
require_once("Net/SmartIRC.php");
require_once("config.php");
require_once("mybot.php");
require_once("mySmartIRC.php");

$bot = new mybot();
$irc = new mySmartIRC();
$irc->init(IRC_ENCODING, SOURCE_ENCODING);

$irc->registerActionhandler(SMARTIRC_TYPE_CHANNEL|SMARTIRC_TYPE_QUERY, 
    '^' . BOT_NICKNAME . ':', $bot, 'bot');

$irc->connect(IRC_SERVER, IRC_PORT);
$irc->login(BOT_NICKNAME, BOT_NAME);
$irc->join(IRC_CHANNEL, IRC_CHANNEL_PASS);

$irc->listen();
?>