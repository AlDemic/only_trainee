<?php 

class CsvReader {
    public function readCsv($fileName) {
        //globals
        $row = 1; //start from
        $filePath = __DIR__ . "/../csv/" . $fileName; //selected file

        if (($handle = fopen($filePath, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if ($row === 1) {
                    $row++;
                    continue;
                }

                //return line
                yield $data;
            }

            //close stream
            fclose($handle);
        }
        
    }
}


