<?php

/**
 * Portal Api
 * @author Christian
 *
 */
class PortalApi {
    private function __construct() {}

    public static function getAktuelleStunde() {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetCurrentStunde',[]);

        if($result['statusCode'] == 200) {
            return $result['data']->stunde;
        }
        else {
            return 0;
        }
    }

    private static $maxStunde = -1;
    
    public static function getAnzahlStunden() {
        if(self::$maxStunde >= 0) return self::$maxStunde;
        
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetMaxStunde',[]);

        if($result['statusCode'] == 200) {
            self::$maxStunde = $result['data']->stunde;
            return $result['data']->stunde;
        }
        else {
            return 0;
        }
    }

    private static $unixTimeErsteStunde = -1;
    
    public static function getUnixTimeErsteStundeBeginnHeute() {
        
        if(self::$unixTimeErsteStunde >= 0) return self::$unixTimeErsteStunde;
        
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetUnixTimeErsteStundeHeute',[]);

        if($result['statusCode'] == 200) {
            self::$unixTimeErsteStunde = $result['data']->unixtime;
            return $result['data']->unixtime;
        }
        else {
            return 0;
        }
    }

    public static function getLeistungsnachweise($schuelerAsvID, $startDatum, $endDatum) {
        $request = 'GetLeistungsnachweise/' . $schuelerAsvID . "/" . $startDatum . "/" . $endDatum;
        
        $result = ExternalPortalRESTapi::getCurlContext('GET', $request,[]);
        
        // print_r($result);die();
        
        if($result['statusCode'] == 200) {
            $data = $result['data'];
            
            $lnws = [];
            
            for($i = 0; $i < sizeof($data); $i++) {
                $lnws[] = new Leistungsnachweis($i,$data[$i]);
            }
            
            return $lnws;
            
        }
        else {
            return [];
        }

    }
    
    private static $ferienCache = [];

    public static function isFerien($datum) {        
        if(self::$ferienCache[$datum] != '') {
            if(self::$ferienCache[$datum] == 'f') return true;
            else return false;
        }
        
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'IsFerien/' . $datum ,[]);
        
        if($result['statusCode'] == 200) {
            if($result['data']->isFerien) self::$ferienCache[$datum] = 'f';
            else $result['data']->isFerien = 'x';
            
            return $result['data']->isFerien;
        }
        else {
            return false;
        }
    }

    /**
     * Liest die Benutzerinformationen für den Benutzernamen $username aus
     * @param String $username
     */
    public static function getUserinformationen($username, $password) {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetUserInfo',[], $username,$password);
        
        if($result === null) return null;       // Login Falsch

        if($result['statusCode'] == 200) {
            $result = $result['data'];

            return [
                'vorname' => $result->firstName,
                'nachname' => $result->lastName,
                'anzeigeName' => $result->displayName,
                'isAdmin' => $result->isAdmin,
                'isLehrer'=> $result->isLehrer,
                'isEltern' => $result->isEltern,
                'isSchueler' => $result->isSchueler,
                'isSekretariat' => $result->isSekretariat,
                'lehrerAsvID' => $result->lehrerAsvID,
                'schuelerAsvID' => $result->schuelerAsvID,
                'elternSchuelerAsvIDs'=> $result->elternSchuelerAsvIDs
            ];
        }
        else {
            return null;
        }
    }
    
    public static function getAllSchueler() {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetAllSchueler' ,[]);
        
        if($result['statusCode'] == 200) {
            return $result['data'];
        }
        else {
            return [];
        }
    }
    
    public static function getAllLehrer() {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetAllLehrer' ,[]);
        
        if($result['statusCode'] == 200) {
            return $result['data'];
        }
        else {
            return [];
        }
    }

    public static function getAllFaecher() {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetAllFaecher' ,[]);
        
        if($result['statusCode'] == 200) {
            return $result['data'];
        }
        else {
            return [];
        }
    }
    
    public static function sendMessage($username, $betreff, $nachricht, $senderUsername = null) {
        $result = ExternalPortalRESTapi::getCurlContext('POST', 'NachrichtSenden' ,[
            'empfaenger' => $username,
            'betreff' => $betreff,
            'nachricht' => $nachricht,
            'absender' => $senderUsername
        ]);
        
        return $result['statusCode'] == 200;
    }
    
    public static function getRespizienzLeistungsnachweise($lehrerAsvID) {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetRespizienzLeistungsnachweise/' . $lehrerAsvID ,[]);
        
        return $result['data'];
    }
    
    public static function getRespizienzLeistungsnachweiseFachbetreuer($lehrerAsvID) {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetRespizienzLeistungsnachweiseFachbetreuer/' . $lehrerAsvID ,[]);
        
        return $result['data'];
    }
    
    public static function getRespizienzLeistungsnachweiseSchulleitung($lehrerAsvID) {
        $result = ExternalPortalRESTapi::getCurlContext('GET', 'GetRespizienzLeistungsnachweiseSchulleitung/' . $lehrerAsvID ,[]);
        
        return $result['data'];
    }
}
