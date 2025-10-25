<?php
namespace MobiCity\Services\SpTrans;

session_start();

use Exception;
use MobiCity\Core\Log;

class SpTransService
{
    public $options;
    private $cookieFile;
    private $baseUrl;
    private $secretKey;

    public function __construct()
    {
        $this->baseUrl = get_env('SPTRANS_BASE_URI');
        $this->secretKey = get_env('SPTRANS_SECRET_KEY');
        $this->options = [];
        $this->cookieFile = __DIR__.'/cookie.txt';

        if (!file_exists($this->cookieFile)) {
            file_put_contents($this->cookieFile, '');
        }
    }
    
    private function checkCookieIsInvalid()
    {
        if (empty($_SESSION['sptrans_auth_time'])) {
            return true;
        }

        if (!file_exists($this->cookieFile)) {
            return true;
        }

        return (time() - $_SESSION['sptrans_auth_time']) > 1800;
    }

    public function auth()
    {
        $ch = curl_init("{$this->baseUrl}/Login/Autenticar?token={$this->secretKey}");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_COOKIEJAR      => $this->cookieFile,
            CURLOPT_COOKIEFILE     => $this->cookieFile,
            CURLOPT_HEADER         => false
        ]);
        
        $responseData = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception("Erro na autenticação: $error");
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerRaw = substr($responseData, 0, $headerSize);
        $body = substr($responseData, $headerSize);

        curl_close($ch);

        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headerRaw, $matches);
        $cookie = $matches[1] ?? [];
        
        if (!empty($cookie)) {
            file_put_contents($this->cookieFile, $cookie);
            $_SESSION['sptrans_auth_time'] = time();            
        }
        
        return trim($body) === true;
    }

    private function request($path, $requestParams = [])
    {
        if ($this->checkCookieIsInvalid()) {
            $this->auth();
        }

        $url = "{$this->baseUrl}{$path}";
        if (!empty($requestParams)) {
            $url .= "?" . http_build_query($requestParams);
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_COOKIEJAR      => $this->cookieFile,
            CURLOPT_COOKIEFILE     => $this->cookieFile,
            CURLOPT_HEADER         => false
        ]);

        $responseData = curl_exec($ch);
        $error = curl_error($ch);

        if ($error) {
            throw new Exception("Erro na requisição: $error");
        }
        
        return json_decode($responseData, true);
    }

    public function getLines($term)
    {
        return $this->request(
            '/Linha/Buscar',
            ['termosBusca' => $term]
        );
    }

    public function getLinePosition($term)
    {
        return $this->request(
            '/Posicao/Linha',
            ['codigoLinha' => $term]
        );
    }

    public static function getLineShapeId($lines): array
    {
        $map = self::mapCsvData('trips.txt');
        
        foreach ($lines as $key => $line) {
            $direction_id = (string)$line['sl'] == 1 ? 0 : 1;
            $trip_id = "{$line['lt']}-{$line['tl']}-{$direction_id}";            
            $line['sid'] = (int)$map[$trip_id];
            $lines[$key] = $line;
        }

        return $lines;
    }

    private static function mapCsvData($file)
    {
        $handle = fopen(__DIR__."/Gtfs/".$file, 'r');
        $header = fgetcsv($handle, separator: ',', escape: "\\");
        $map = [];

        while (($items = fgetcsv($handle, separator: ',', escape: "\\")) !== false) {
            $data = array_combine($header, $items);
            if (isset($data['trip_id'], $data['shape_id'])) {
                $map[$data['trip_id']] = $data['shape_id'];
            }
        }

        fclose($handle);
        return $map;
    }
}