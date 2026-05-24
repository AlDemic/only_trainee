<?php

use \Bitrix\Main\Localization\Loc;
\Bitrix\Main\Loader::includeModule('fileman');

class CIBlockPropertyCPropAlex
{
    private static $showedCss = false;

    public static function GetUserTypeDescription()
    {
        return array(
            'PROPERTY_TYPE' => 'S', //
            'USER_TYPE' => 'C',  //кастомное поле в битрикс
            'DESCRIPTION' => Loc::getMessage('IEX_CPROP_DESC'),
            'GetPropertyFieldHtml' => array(__CLASS__,  'GetPropertyFieldHtml'), //отрисовка параметров в админке где элемент
            'ConvertToDB' => array(__CLASS__, 'ConvertToDB'), //сохранение в бд
            'ConvertFromDB' => array(__CLASS__,  'ConvertFromDB'), // получение с бд
            'GetSettingsHTML' => array(__CLASS__, 'GetSettingsHTML'),  //вид настроек параметров в параметрах самого инфоблоков(какие поля добавить и хранить и т.д.)
            'PrepareSettings' => array(__CLASS__, 'PrepareUserSettings'), //настройки перед сохранением
            'GetLength' => array(__CLASS__, 'GetLength'), //длина значения (чисто битрикс: индексация, аоиск)
            'GetPublicViewHTML' => array(__CLASS__, 'GetPublicViewHTML')  //как показывать в публичной части
        );
    }

    public static function GetPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        $hideText = Loc::getMessage('IEX_CPROP_HIDE_TEXT');
        $clearText = Loc::getMessage('IEX_CPROP_CLEAR_TEXT');

        self::showCss();

        if(!empty($arProperty['USER_TYPE_SETTINGS'])){
            $arFields = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);
        }
        else{
            return '<span>'.Loc::getMessage('IEX_CPROP_ERROR_INCORRECT_SETTINGS').'</span>';
        }

        $result = '';
        $result .= '<div class="mf-gray"><a class="cl mf-toggle">'.$hideText.'</a>';
        if($arProperty['MULTIPLE'] === 'Y'){
            $result .= ' | <a class="cl mf-delete">'.$clearText.'</a></div>';
        }
        $result .= '<table class="mf-fields-list active">';


        foreach ($arFields as $code => $arItem){
            if($arItem['TYPE'] === 'string'){
                $result .= self::showString($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
            else if($arItem['TYPE'] === 'html'){
                $result .= self::showHTML($code, $arItem['TITLE'], $value, $strHTMLControlName);
            }
        }

        $result .= '</table>';

        return $result;
    }

    public static function GetPublicViewHTML($arProperty, $value, $strHTMLControlName)
    {
        return $value;
    }

    public static function GetSettingsHTML($arProperty, $strHTMLControlName, &$arPropertyFields)
    {
        $btnAdd = Loc::getMessage('IEX_CPROP_SETTING_BTN_ADD');
        $settingsTitle =  Loc::getMessage('IEX_CPROP_SETTINGS_TITLE');

        $arPropertyFields = array(
            'USER_TYPE_SETTINGS_TITLE' => $settingsTitle,
            'HIDE' => array('ROW_COUNT', 'COL_COUNT', 'DEFAULT_VALUE', 'SEARCHABLE', 'SMART_FILTER', 'WITH_DESCRIPTION', 'FILTRABLE', 'MULTIPLE_CNT', 'IS_REQUIRED'),
            'SET' => array(
                'MULTIPLE_CNT' => 1,
                'SMART_FILTER' => 'N',
                'FILTRABLE' => 'N',
            ),
        );

        self::showJsForSetting($strHTMLControlName["NAME"]);
        self::showCssForSetting();

        $result = '<tr><td colspan="2" align="center">
            <table id="many-fields-table" class="many-fields-table internal">        
                <tr valign="top" class="heading mf-setting-title">
                   <td>XML_ID</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TITLE').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_SORT').'</td>
                   <td>'.Loc::getMessage('IEX_CPROP_SETTING_FIELD_TYPE').'</td>
                </tr>';


        $arSetting = self::prepareSetting($arProperty['USER_TYPE_SETTINGS']);

        if(!empty($arSetting)){
            foreach ($arSetting as $code => $arItem) {
                $result .= '
                       <tr valign="top">
                           <td><input type="text" class="inp-code" size="20" value="'.$code.'"></td>
                           <td><input type="text" class="inp-title" size="35" name="'.$strHTMLControlName["NAME"].'['.$code.'_TITLE]" value="'.$arItem['TITLE'].'"></td>
                           <td><input type="text" class="inp-sort" size="5" name="'.$strHTMLControlName["NAME"].'['.$code.'_SORT]" value="'.$arItem['SORT'].'"></td>
                           <td>
                                <select class="inp-type" name="'.$strHTMLControlName["NAME"].'['.$code.'_TYPE]">
                                    '.self::getOptionList($arItem['TYPE']).'
                                </select>                        
                           </td>
                       </tr>';
            }
        }

        $result .= '
               <tr valign="top">
                    <td><input type="text" class="inp-code" size="20"></td>
                    <td><input type="text" class="inp-title" size="35"></td>
                    <td><input type="text" class="inp-sort" size="5" value="500"></td>
                    <td>
                        <select class="inp-type"> '.self::getOptionList().'</select>                        
                    </td>
               </tr>
             </table>   
                
                <tr>
                    <td colspan="2" style="text-align: center;">
                        <input type="button" value="'.$btnAdd.'" onclick="addNewRows()">
                    </td>
                </tr>
                </td></tr>';

        return $result;
    }

    public static function PrepareUserSettings($arProperty)
    {
        $result = [];
        if(!empty($arProperty['USER_TYPE_SETTINGS'])){
            foreach ($arProperty['USER_TYPE_SETTINGS'] as $code => $value) {
                $result[$code] = $value;
            }
        }
        return $result;
    }

    public static function GetLength($arProperty, $arValue)
    {
        $result = false;
        foreach($arValue['VALUE'] as $code => $value){
            if(!empty($value)){
                $result = true;
                break;
            }
        }

        return $result;
    }

    public static function ConvertToDB($arProperty, $arValue)
    {
        $isEmpty = true;
        foreach ($arValue['VALUE'] as $v){
            if(!empty($v)){
                $isEmpty = false;
                break;
            }
        }

        if($isEmpty === false){
            $arResult['VALUE'] = json_encode($arValue['VALUE']);
        }
        else{
            $arResult = ['VALUE' => '', 'DESCRIPTION' => ''];
        }

        return $arResult;
    }

    public static function ConvertFromDB($arProperty, $arValue)
    {
        $return = array();

        if(!empty($arValue['VALUE'])){
            $arData = json_decode($arValue['VALUE'], true);

            foreach ($arData as $code => $value){
                $return['VALUE'][$code] = $value;
            }

        }
        return $return;
    }

    //Internals

    private static function showString($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';
        $result .= '<tr>
                    <td align="right">'.$title.': </td>
                    <td><input type="text" value="'.$v.'" name="'.$strHTMLControlName['VALUE'].'['.$code.']"/></td>
                </tr>';

        return $result;
    }

    /*
        Редактор хранит значение в iframe на стороне клиента, при отправке имя становится PROP_VALUEcode,
        ConvertToDB ждёт PROP_VALUE[code] -> поэтому делаем отдельное поле и после отправки формы вытягиваем значение с textarea редактора и кладём в input
    */ 
    private static function showHTML($code, $title, $arValue, $strHTMLControlName)
    {
        $result = '';

        $v = !empty($arValue['VALUE'][$code]) ? $arValue['VALUE'][$code] : '';

        $name = $strHTMLControlName['VALUE'] . '[' . $code . ']';

        $nameEditor = preg_replace("/[^a-zA-Z0-9_:\.]/is", "", $name);

        $html = '<input type="hidden" name="'.$name.'" id="hid_'.$nameEditor.'" value="'.$v.'">';

        ob_start();
		
		CFileMan::AddHTMLEditorFrame(
			$nameEditor,
			$v,
			$nameEditor . "_TYPE",
			strlen($v) ? "html" : "text",
			[
				'height' => 80,
            ]
		);
		
		$html .= ob_get_contents();
		ob_end_clean();

        //вытягиваем значение
        $html .= '
            <script>
                document.querySelector("form").addEventListener("submit", function() {
                    var nameEditor = "'.$nameEditor.'";
                    var textarea = document.querySelector("textarea[name=\'" + nameEditor + "\']");

                    if(textarea) {
                        document.getElementById("hid_" + nameEditor).value = textarea.value;
                    }
                });
            </script>
        ';

        $result .= '<tr>
                        <td align="right">'.$title.': </td>
                        <td>'.$html.'</td>
                    </tr>';

        return $result;
    }

    private static function showCss()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .cl {cursor: pointer;}
                .mf-gray {color: #797777;}
                .mf-fields-list {display: none; padding-top: 10px; margin-bottom: 10px!important; margin-left: -300px!important; border-bottom: 1px #e0e8ea solid!important;}
                .mf-fields-list.active {display: block;}
                .mf-fields-list td {padding-bottom: 5px;}
                .mf-fields-list td:first-child {width: 300px; color: #616060;}
                .mf-fields-list td:last-child {padding-left: 5px;}
                .mf-fields-list input[type="text"] {width: 350px!important;}
                .mf-fields-list textarea {min-width: 350px; max-width: 650px; color: #000;}
                .mf-fields-list img {max-height: 150px; margin: 5px 0;}
                .mf-img-table {background-color: #e0e8e9; color: #616060; width: 100%;}
                .mf-fields-list input[type="text"].adm-input-calendar {width: 170px!important;}
                .mf-file-name {word-break: break-word; padding: 5px 5px 0 0; color: #101010;}
                .mf-fields-list input[type="text"].mf-inp-bind-elem {width: unset!important;}
            </style>
            <?
        }
    }

    private static function showJsForSetting($inputName)
    {
        ?>
        <script>
            (function () {
                function run() {
                    window.addNewRows = function () {
                        const html =
                            '<tr valign="top">' +
                            '<td><input type="text" class="inp-code" size="20"></td>' +
                            '<td><input type="text" class="inp-title" size="35"></td>' +
                            '<td><input type="text" class="inp-sort" size="5" value="500"></td>' +
                            '<td><select class="inp-type"><?=CUtil::JSEscape(self::getOptionList())?></select></td>' +
                            '</tr>';
                        jQuery("#many-fields-table").append(html);
                    };

                    jQuery(document).on('change', '.inp-code', function () {
                        const code = jQuery(this).val();
                        const row = jQuery(this).closest('tr');

                        if (code.length <= 0) {
                            row.find('input.inp-title').removeAttr('name');
                            row.find('input.inp-sort').removeAttr('name');
                            row.find('select.inp-type').removeAttr('name');
                        } else {
                            row.find('input.inp-title').attr('name', '<?=$inputName?>[' + code + '_TITLE]');
                            row.find('input.inp-sort').attr('name', '<?=$inputName?>[' + code + '_SORT]');
                            row.find('select.inp-type').attr('name', '<?=$inputName?>[' + code + '_TYPE]');
                        }
                    });

                    jQuery(document).on('input', '.inp-sort', function () {
                        const num = jQuery(this).val();
                        jQuery(this).val(num.replace(/[^0-9]/g, ''));
                    });
                }

                if (typeof jQuery === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js';
                    script.onload = run;
                    document.head.appendChild(script);
                } else {
                    run();
                }
            })();
        </script>
        <?php
    }

    private static function showCssForSetting()
    {
        if(!self::$showedCss) {
            self::$showedCss = true;
            ?>
            <style>
                .many-fields-table {margin: 0 auto; /*display: inline;*/}
                .mf-setting-title td {text-align: center!important; border-bottom: unset!important;}
                .many-fields-table td {text-align: center;}
                .many-fields-table > input, .many-fields-table > select{width: 90%!important;}
                .inp-sort{text-align: center;}
                .inp-type{min-width: 125px;}
            </style>
            <?
        }
    }

    private static function prepareSetting($arSetting)
    {
        $arResult = [];

        foreach ($arSetting as $key => $value){
            if(strstr($key, '_TITLE') !== false) {
                $code = str_replace('_TITLE', '', $key);
                $arResult[$code]['TITLE'] = $value;
            }
            else if(strstr($key, '_SORT') !== false) {
                $code = str_replace('_SORT', '', $key);
                $arResult[$code]['SORT'] = $value;
            }
            else if(strstr($key, '_TYPE') !== false) {
                $code = str_replace('_TYPE', '', $key);
                $arResult[$code]['TYPE'] = $value;
            }
        }

        if(!function_exists('cmp')){
            function cmp($a, $b)
            {
                if ($a['SORT'] == $b['SORT']) {
                    return 0;
                }
                return ($a['SORT'] < $b['SORT']) ? -1 : 1;
            }
        }

        uasort($arResult, 'cmp');

        return $arResult;
    }

    private static function getOptionList($selected = 'string')
    {
        $result = '';
        $arOption = [
            'string' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_STRING'),
            'html' => Loc::getMessage('IEX_CPROP_FIELD_TYPE_HTML'),
        ];

        foreach ($arOption as $code => $name){
            $s = '';
            if($code === $selected){
                $s = 'selected';
            }

            $result .= '<option value="'.$code.'" '.$s.'>'.$name.'</option>';
        }

        return $result;
    }
}