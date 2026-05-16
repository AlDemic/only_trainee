<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Localization\Loc;
/**
* @var array $arParams
* @var array $arResult
* @var CMain $APPLICATION
* @var CBitrixComponent $component
* @var CBitrixComponentTemplate $this
*/
?>
<div class="my-user-card">  
    <div class="my-user-card__info">
        <?foreach($arResult['ITEMS'] as $ibId => $value):?>
                <h2>Инфоблок ID: <?= $ibId ?></h2>
                    <?foreach($value as $rec):?>
                        <p class="my-user-card__email">
                            <span><?= $rec['ID'] ?></span>
                            <span><?= $rec['NAME'] ?></span>
                            <span><?= $rec['DATE_ACTIVE_FROM'] ?></span>
                        </p>
                    <?endforeach;?>
        <?endforeach;?>
    </div>
</div>
