<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */
/** @var CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var CBitrixComponent $component */
$this->setFrameMode(true);
?>
<div class="article-card">
	<!--NEWS TITLE-->
	<?if((!isset($arParams["DISPLAY_NAME"]) || $arParams["DISPLAY_NAME"]!="N") && $arResult["NAME"]):?>
		<div class="article-card__title"><?=$arResult["NAME"]?></div>
	<?endif;?>

	<!--NEWS DATE-->
	<?if((!isset($arParams["DISPLAY_DATE"]) || $arParams["DISPLAY_DATE"]!="N") && $arResult["DISPLAY_ACTIVE_FROM"]):?>
		<div class="article-card__date"><?=$arResult["DISPLAY_ACTIVE_FROM"]?></div>
	<?endif;?>

    <div class="article-card__content">
		<!--NEWS PICTURE-->
		<?if((!isset($arParams["DISPLAY_PICTURE"]) || $arParams["DISPLAY_PICTURE"]!="N") && is_array($arResult["DETAIL_PICTURE"])):?>
		<div class="article-card__image sticky">	
			<img
				src="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>"
				alt="<?=$arResult["DETAIL_PICTURE"]["ALT"]?>"
				data-object-fit="cover"
			/>
		</div>
		<?endif?>

		<!--NEWS TEXT BLOCK-->
        <div class="article-card__text">
			<!--MAIN TEXT-->
            <div class="block-content" data-anim="anim-3">
				<?if($arResult["NAV_RESULT"]):?>
					<?if($arParams["DISPLAY_TOP_PAGER"]):?><?=$arResult["NAV_STRING"]?><br /><?endif;?>
					<?echo $arResult["NAV_TEXT"];?>
					<?if($arParams["DISPLAY_BOTTOM_PAGER"]):?><br /><?=$arResult["NAV_STRING"]?><?endif;?>
				<?elseif($arResult["DETAIL_TEXT"] <> ''):?>
					<?echo $arResult["DETAIL_TEXT"];?>
				<?else:?>
					<?echo $arResult["PREVIEW_TEXT"];?>
				<?endif?>
			</div>
			
			<!--BACK TO NEWS LIST HREF-->
            <a class="article-card__button" href="<?=$arResult["LIST_PAGE_URL"]?>">Назад к новостям</a>
		</div>
    </div>
</div>
