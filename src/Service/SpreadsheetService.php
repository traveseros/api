<?php

namespace App\Service;

use Google\Client;
use Google\Service;

class SpreadsheetService
{
    //composer require google/apiclient:^2.15.0
    private Client $client;
    private Service $service;

    //const CORTA_FILE_ID = '1Bx4RhQ0Jj-2pVrTI_nz5sMXJsLFZ1L7Uex7L2VOXJrk';
    const CORTA_FILE_ID = '1IOOCDHAPtYA7qawWHyGlYv6v4VRul0uK8oD2xna-f9Y';
    const CORTA_SHEET = 'CORTA';
    //const LARGA_FILE_ID = '1xCObpWe0x570pwMrCB2dk8UsMVj4-SP9SxcIk1vBeWs';
    const LARGA_FILE_ID = '1IOOCDHAPtYA7qawWHyGlYv6v4VRul0uK8oD2xna-f9Y';
    const LARGA_SHEET = 'LARGA';
    //const FAMILIAR_FILE_ID = '1JTu4cH2ge8PElvn8tpTq8uVKhPmjE48YQDhBU5GFgng';
    const FAMILIAR_FILE_ID = '1IOOCDHAPtYA7qawWHyGlYv6v4VRul0uK8oD2xna-f9Y';
    const FAMILIAR_SHEET = 'FAMILIAR';

    public function __construct(){
        $this->client = new \Google_Client();
        $this->client->setApplicationName('traveseros');
        $this->client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $this->client->setAccessType('offline');
        $path = __DIR__ . '/data/credentials.json';
        try {
            $this->client->setAuthConfig($path);
            $this->service = new \Google_Service_Sheets($this->client);
        }catch (\Exception $e){
            throw $e;
        }
    }

    public function get(int $travesíaId): array
    {
        switch ($travesíaId){
            case 1:
                $range = self::LARGA_SHEET;
                $file = self::LARGA_FILE_ID;
                break;
            case 2:
                $range = self::CORTA_SHEET;
                $file = self::CORTA_FILE_ID;
                break;
            case 3:
                $range = self::FAMILIAR_SHEET;
                $file = self::FAMILIAR_FILE_ID;
                break;
        }
        $spreadsheet = $this->service->spreadsheets_values->get($file, $range);
        return $spreadsheet->getValues();
    }

}