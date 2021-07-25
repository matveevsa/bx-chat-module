<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use Bitrix\Main\Localization\Loc;

if (!$USER->IsAuthorized()) {
    echo Loc::getMessage('MSA_CHAT_NOT_AUTHORIZED');
    return;
}

?>

<h1 class="chat-title"><?=Loc::getMessage('MSA_CHAT_TITLE')?></h1>

<div class="chat-wrapper">
    <div class="chat-container">
        <div class="chat-messages">
            <?php
            foreach ($arResult['MESSAGES'] as $message) { ?>
                <div class="chat-message message">
                    <div class="message-wrapper">
                        <p class="message__date">
                            <?= $message['UF_DATE'] ?>
                        </p>
                        <div class="message__author">
                            <img src="<?=$message['USER_PHOTO_SRC']?>" width="50" height="50">
                            <p><?= $message['USER_FULL_NAME'] ?></p>
                        </div>
                        <p class="message__text">
                            <?= $message['UF_MESSAGE'] ?>
                        </p>
                    </div>
                    <div class="message-buttons">
                        <button class="message-edit">
                            <?=Loc::getMessage('MSA_CHAT_BUTTON_EDIT')?>
                        </button>
                        <button class="message-reply">
                            <?=Loc::getMessage('MSA_CHAT_BUTTON_REPLY')?>
                        </button>
                        <button class="message-quote">
                            <?=Loc::getMessage('MSA_CHAT_BUTTON_QUOTE')?>
                        </button>
                    </div>
                </div>
            <?php } ?>
        </div>
        <form class="chat-form form" data-form="chat">
            <input name="userId" type="text" hidden value="<?=$USER->GetID()?>">
            <textarea class="message-textarea" name="message" id="message-text" rows="7"></textarea>
            <button class="form-submit">
                <?= Loc::getMessage('MSA_CHAT_SUBMIT') ?>
            </button>
        </form>
    </div>
</div>

<script>
//TODO: вынести в файл script.js сделать конструктор и разбить по методам
    let messageTemplate = `
    <div class="chat-message message">
        <div class="message-wrapper">
            <p class="message__date">
                #UF_DATE#
            </p>
            <div class="message__author">
                <img src="#USER_PHOTO#" width="50" height="50">
                <p>#USER_FULL_NAME#</p>
            </div>
            <p class="message__text">
                #UF_MESSAGE#
            </p>
        </div>
        <div class="message-buttons">
            <button class="message-edit">
                <?=Loc::getMessage('MSA_CHAT_BUTTON_EDIT')?>
            </button>
            <button class="message-reply">
                <?=Loc::getMessage('MSA_CHAT_BUTTON_REPLY')?>
            </button>
            <button class="message-quote">
                <?=Loc::getMessage('MSA_CHAT_BUTTON_QUOTE')?>
            </button>
        </div>
    </div>
    `
    const form = document.querySelector('[data-form="chat"]');
    const messagesContainer = document.querySelector('.chat-messages');

    form.addEventListener('submit', (evt) => {
        evt.preventDefault();
        const formData = new FormData(evt.target);

        BX.ajax.runComponentAction('msa:chat', 'addMessage', {
            mode: 'class',
            data: formData
        }).then((res) => {
            let message = messageTemplate;

            const placeholders = {
                '#UF_DATE#': res.data.UF_DATE,
                '#USER_PHOTO#': res.data.USER_PHOTO_SRC,
                '#USER_FULL_NAME#': res.data.USER_FULL_NAME,
                '#UF_MESSAGE#': res.data.UF_MESSAGE,
            };
            const keys = Object.keys(placeholders);

            for (let i = 0; i < keys.length; i += 1) {
                message = message.split(keys[i]).join(placeholders[keys[i]]);
            }
            var div = document.createElement('div');
            div.innerHTML = message.trim();

            messagesContainer.append(div);
            form.elements.message.value = '';
        }).catch((e) => console.log(e));

    });

</script>