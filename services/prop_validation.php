<?php 

class PropValidation {
    public function propValidate($propArray, $arProps) {
        foreach ($propArray as $key => &$value) {
            $value = trim($value);
            $value = str_replace('\n', '', $value);

            if (stripos($value, '•') !== false) {
                $value = explode('•', $value);
                array_splice($value, 0, 1);

                foreach ($value as &$str) {
                    $str = trim($str);
                }
            } elseif ($arProps[$key]) {
                $arSimilar = [];
                foreach ($arProps[$key] as $propKey => $propVal) {
                    if ($key == 'OFFICE') {
                        $value = strtolower($value);
                        if ($value == 'центральный офис') {
                            $value .= 'свеза ' . $propArray['LOCATION'];
                        } elseif ($value == 'лесозаготовка') {
                            $value = 'свеза ресурс ' . $value;
                        } elseif ($value == 'свеза тюмень') {
                            $value = 'свеза тюмени';
                        }
                        $arSimilar[similar_text($value, $propKey)] = $propVal;
                    }

                    if (stripos($propKey, $value) !== false) {
                        $value = $propVal;
                        break;
                    }

                    if (similar_text($propKey, $value) > 50) {
                        $value = $propVal;
                    }
                }

                if ($key == 'OFFICE' && !is_numeric($value)) {
                    ksort($arSimilar);
                    $value = array_pop($arSimilar);
                }
            }
        }

        if ($propArray['SALARY_VALUE'] == '-') {
            $propArray['SALARY_VALUE'] = '';
        } elseif ($propArray['SALARY_VALUE'] == 'по договоренности') {
            $propArray['SALARY_VALUE'] = '';
            $propArray['SALARY_TYPE'] = $arProps['SALARY_TYPE']['договорная'];
        } else {
            $arSalary = explode(' ', $propArray['SALARY_VALUE']);
            if ($arSalary[0] == 'от' || $arSalary[0] == 'до') {
                $propArray['SALARY_TYPE'] = $arProps['SALARY_TYPE'][$arSalary[0]];
                array_splice($arSalary, 0, 1);
                $propArray['SALARY_VALUE'] = implode(' ', $arSalary);
            } else {
                $propArray['SALARY_TYPE'] = $arProps['SALARY_TYPE']['='];
            }
        }

        //return property array with enum ID
        return $propArray;
    }
}


