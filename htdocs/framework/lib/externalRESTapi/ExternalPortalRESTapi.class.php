<?php


class ExternalPortalRESTapi {
    
    /**
     * 
     * @param unknown $method
     * @param unknown $path
     * @param unknown $data
     * @param unknown $user
     * @param unknown $password
     * @return NULL|mixed
     */
    
    public static function getCurlContext($method, $path, $data, $user = NULL, $password = NULL) {
        
//         echo($path . "<br />");
        
        $query = json_encode($data);
        
        $process = curl_init(DB::getGlobalSettings()->externalPortalRESTApiHost . "/" . $path);
        
        curl_setopt($process, CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/x-www-form-urlencoded',
                'ds2020-apirequest: true'
            )
        );
        
        curl_setopt($process, CURLOPT_HEADER, 0);
        
        if($user == NULL) curl_setopt($process, CURLOPT_USERPWD, DB::getGlobalSettings()->externalPortalRESTApiAuth['user'] . ":" . DB::getGlobalSettings()->externalPortalRESTApiAuth['password']);
        else curl_setopt($process, CURLOPT_USERPWD, $user . ":" . $password);
        
        curl_setopt($process, CURLOPT_TIMEOUT, 30);
        
        if($method == "POST") curl_setopt($process, CURLOPT_POST, true);
        else if($method == "PUT") curl_setopt($process, CURLOPT_CUSTOMREQUEST, "PUT");
        else if($method == "DELETE") curl_setopt($process, CURLOPT_CUSTOMREQUEST, "DELETE");
        else if($method == "GET") curl_setopt($process, CURLOPT_CUSTOMREQUEST, "GET");
        else if($method == "PATCH") curl_setopt($process, CURLOPT_CUSTOMREQUEST, "PATCH");
        
        curl_setopt($process, CURLOPT_POSTFIELDS, $query);
        curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($process, CURLOPT_RETURNTRANSFER, true);
        
        $return = curl_exec($process);
        
        $statusCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
                      
        curl_close($process);

        
        return [
            'statusCode' => $statusCode,
            'data' => json_decode($return)
        ];
    }
}