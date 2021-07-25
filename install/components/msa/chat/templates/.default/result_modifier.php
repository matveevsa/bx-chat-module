<?php

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

foreach ($arResult['MESSAGES'] as &$message) {
    $message['USER_PHOTO_SRC'] = $message['USER_PHOTO_SRC'] ?? '/local/components/msa/chat/templates/.default/images/default_avatar.png';
}
