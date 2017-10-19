<?php

include_once("../framework/lib/data/absenzen/Absenz.class.php");
include_once("../framework/lib/data/absenzen/AbsenzBefreiung.class.php");
include_once("../framework/lib/data/absenzen/AbsenzBeurlaubung.class.php");
include_once("../framework/lib/data/absenzen/AbsenzSchuelerInfo.class.php");
include_once("../framework/lib/system/DateFunctions.class.php");

class RestGetAbsenzenTageProMonat extends AbstractRest {
    public function execute($input, $request) {
        $schueler = schueler::getByAsvID($request[1]);
        
        if($schueler == null) {
            $this->statusCode = 404;
            return [
                'error' => 1,
                'errorText' => 'Schüler nicht gefunden.'
            ];
        }
        
        $absenzen = Absenz::getAbsenzenForSchueler($schueler);
        
        $stats = [
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
            5 => 0,
            6 => 0,
            7 => 0,
            8 => 0,
            9 => 0,
            10 => 0,
            11 => 0,
            12 => 0
        ];
                
        for($i = 0; $i < sizeof($absenzen); $i++) {
            $stats = $absenzen[$i]->getDaysToStats($stats);
        }
        
        
        
        return $stats;
    }

    public function getAllowedMethod() {
        return 'GET';
    }


    public function needsSystemAuth() {
        return true;
    }
}
