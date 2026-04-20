<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 */
//inputs common params for fields: Ваше имя, Компания/Должность, Email, Номер телефона
//array key = field number from bitrix form
$inputsArray = [
			'new_field_85377' => [
				'label_for' => 'medicine_name',
				'input_notif' => 'Поле должно содержать не менее 3-х символов'
			],
			'new_field_18143' => [
				'label_for' => 'medicine_company',
				'input_notif' => 'Поле должно содержать не менее 3-х символов'
			],
			'new_field_3318' => [
				'label_for' => 'medicine_email',
				'input_notif' => 'Неверный формат почты'
			],
			'new_field_40658' => [
				'label_for' => 'medicine_phone',
				'input_notif' => ''
			]
		];

//params for field: Сообщение
$messageField = 'new_field_20802'; //field number from bitrix form
$messageLabel = 'medicine_message';
?>

<div class="contact-form">
	<!--FORM HEADER-->
	<div class="contact-form__head">
        <div class="contact-form__head-title">
			<?= $arResult["FORM_TITLE"] ?? '' ?>
		</div>

        <div class="contact-form__head-text">
			<?= $arResult["FORM_DESCRIPTION"] ?? '' ?>
        </div>
    </div>

	<!-- FIELDS ERRORS -->
    <?if ($arResult["isFormErrors"] == "Y" && $arResult["FORM_ERRORS_TEXT"])
        echo $arResult["FORM_ERRORS_TEXT"];
    ?>

	<!--FORM FROM BITRIX-->
	<?=$arResult["FORM_HEADER"]?>
	<div class="contact-form__form">

		<!--MAIN CYCLE FOR INPUTS FIELDS-->
		
		<!--INPUTS BLOCK-->
		<div class="contact-form__form-inputs">
			<?foreach ($arResult["QUESTIONS"] as $FIELD_SID => $arQuestion):
				// FOR HIDDEN FIELDS
				if ($arQuestion['STRUCTURE'][0]['FIELD_TYPE'] == 'hidden') {
					echo $arQuestion["HTML_CODE"];
					continue;
				}

				//check if message block
				if($FIELD_SID === $messageField) continue;

				//render inputs
				if($arQuestion['STRUCTURE'][0]['ACTIVE'] === 'Y'):
			?>
					<div class="input contact-form__input">
						<label class="input__label" for="<?= $inputsArray[$FIELD_SID]['label_for'] ?>">
							<!--FIELD TITLE-->
							<div class="input__label-text">
								<?= $arQuestion["CAPTION"]?><?if ($arQuestion["REQUIRED"] == "Y"):?><?=$arResult["REQUIRED_SIGN"];?><?endif; ?>
							</div>

							<!--HTML FIELD FROM BITRIX-->
							<div class="input__input" id="<?= $inputsArray[$FIELD_SID]['label_for'] ?>">
								<?= $arQuestion["HTML_CODE"] ?>
							</div>

							<!--NOTIFICATION BLOCK-->
							<div class="input__notification">
								<?= $inputsArray[$FIELD_SID]['input_notif'] ?>
							</div>
						</label>
					</div>
				<?endif;?>
			<?endforeach;?>
		</div>

		<!--MESSAGE BLOCK-->
		<?if($arResult["QUESTIONS"][$messageField]['STRUCTURE'][0]['ACTIVE'] === 'Y'):?>
		<div class="contact-form__form-message">
            <div class="input">
				<label class="input__label" for="<?= $messageLabel ?>">
					<!--FIELD TITLE-->
					<div class="input__label-text">
						<?= $arResult["QUESTIONS"][$messageField]["CAPTION"] ?><?if ($arResult["QUESTIONS"][$messageField]["REQUIRED"] == "Y"):?><?= $arResult["QUESTIONS"][$messageField]["REQUIRED_SIGN"] ?><?endif; ?>
					</div>
					
					<!--HTML FIELD FROM BITRIX-->
					<div class="input__input" id="<?= $messageLabel ?>">
						<?= $arResult["QUESTIONS"][$messageField]["HTML_CODE"] ?>
					</div>

					<!--NOTIFICATION BLOCK-->
					<div class="input__notification"></div>
            	</label>
			</div>
        </div>
		<?endif;?>

		<!--BUTTON BLOCK-->
		<div class="contact-form__bottom">
            <div class="contact-form__bottom-policy">
				Нажимая «Отправить», Вы подтверждаете, что ознакомлены, полностью согласны и принимаете условия «Согласия на обработку персональных данных».
            </div>
            <button type="submit" name="web_form_submit" class="form-button contact-form__bottom-button" data-success="Отправлено"
                    data-error="Ошибка отправки" value="<?=htmlspecialcharsbx(trim($arResult["arForm"]["BUTTON"]) == '' ? GetMessage("FORM_ADD") : $arResult["arForm"]["BUTTON"]);?>">
                <div class="form-button__title">Оставить заявку</div>
            </button>
        </div>
		
	<!--END OF BITRIX FORM-->
	</div>
	<?=$arResult["FORM_FOOTER"]?>

</div>

