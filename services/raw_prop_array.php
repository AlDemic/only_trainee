<?php 

class RawPropArray {
    public function rawProp($row) {
        return [
            //for iblock creation
            'fields' => [
                'NAME' => $row[3],
                'ACTIVE' => !empty($row[14]) ? 'Y' : 'N',
            ],
            'props' => [
                'ACTIVITY' => $row[9],
                'FIELD' => $row[11],
                'OFFICE' => $row[1],
                'LOCATION' => $row[2],
                'REQUIRE' => $row[4],
                'DUTY' => $row[5],
                'CONDITIONS' => $row[6],
                'EMAIL' => $row[12],
                'DATE' => date('d.m.Y'),
                'TYPE' => $row[8],
                'SALARY_TYPE' => '',
                'SALARY_VALUE' => $row[7],
                'SCHEDULE' => $row[10],
            ]
        ];
    }
}


