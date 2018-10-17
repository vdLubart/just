<?php

namespace Lubart\Just\Validators;

use GuzzleHttp\Client;

/**
 * Description of ReCaptcha
 *
 * @author Viacheslav Dymarchuk
 */
class Recaptcha
{
    protected $client;
    
    protected static $instance = null;

    protected static function getInsatnce(){
        if(is_null(self::$instance)){
            self::$instance = new Recaptcha;
        }
        
        return self::$instance;
    }


    public function __construct() {
        $this->client = new Client;
    }
    
    public function validate(
        $attribute, 
        $value, 
        $parameters, 
        $validator
    ){
        
        $response = $this->getClient()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params'=>
                [
                    'secret'=>env('RE_CAP_SECRET'),
                    'response'=>$value
                 ]
            ]
        );
    
        $body = json_decode((string)$response->getBody());
        return $body->success;
    }
    
    public static function setClient($client) {
        $instance = self::getInsatnce();
        
        return $instance->client = $client;
    }
    
    public static function getClient() {
        $instance = self::getInsatnce();
        
        return $instance->client;
    }
    
}