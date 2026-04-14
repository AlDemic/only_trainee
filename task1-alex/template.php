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

<!--news list-->
<div id="barba-wrapper">
	<div class="article-list">
		<!--news from infoblock-->
		<?foreach($arResult["ITEMS"] as $arItem):?>
			<?
				//admin actions: delete/edit news
				$this->AddEditAction(
					$arItem['ID'],
					$arItem['EDIT_LINK'],
					CIBlock::GetArrayByID(
						$arItem["IBLOCK_ID"],
						"ELEMENT_EDIT"
					)
				);
				$this->AddDeleteAction(
					$arItem['ID'],
					$arItem['DELETE_LINK'],
					CIBlock::GetArrayByID(
						$arItem["IBLOCK_ID"],
						"ELEMENT_DELETE"),
					array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM'))
				);
			?>
			<a class="article-item article-list__item" href="<? echo $arItem["DETAIL_PAGE_URL"]; ?>" data-anim="anim-3">
				<!--use preview pic of news as background-->
        		<div class="article-item__background">
					<img 
						src="<? echo $arItem["PREVIEW_PICTURE"]["SRC"]; ?>" 
						alt="<? echo $arItem["PREVIEW_PICTURE"]["ALT"]; ?>"
					/>
				</div>

				<!--NEWS CONTENT BLOCK-->
        		<div class="article-item__wrapper">
					<!--NEWS TITLE-->
            		<div class="article-item__title">
						<? echo $arItem["NAME"]; ?>
					</div>

					<!--NEWS TEXT PREVIEW-->
            		<div class="article-item__content">
						<? echo $arItem["PREVIEW_TEXT"]; ?>
					</div>
				</div>
    		</a>
		<?endforeach?>
	</div>
</div>