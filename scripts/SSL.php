<?php

set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, $severity, $severity, $file, $line);
});

class SSL
{
    public function __construct($hostName = null, $expiresWithin = null)
    {
        if ($hostName == null or $expiresWithin == null) {
            throw new Exception("Hostname or expiresWithin is null.");
        } else {
            $certificate = $this->getCertificate($hostName);
            $array = json_encode(['hostname' => $hostName, 'validFrom' => $certificate->validFrom, 'validTo' => $certificate->validTo, 'expiresWithin' => $this->validateCertificate($certificate, $expiresWithin), 'error' => $certificate->error]);
            echo $array;
        }
    }

    private function getCertificate($hostName)
    {
        try {
            $client = stream_socket_client("ssl://{$hostName}:443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, stream_context_create(["ssl" => ["capture_peer_cert" => true]]));
            $certificate = stream_context_get_params($client);
            $certificateInfo = (object) openssl_x509_parse($certificate['options']['ssl']['peer_certificate']);

            $validFrom = DateTime::createFromFormat('ymdHise', $certificateInfo->validFrom)->format('Y-m-d');
            $validTo = DateTime::createFromFormat('ymdHise', $certificateInfo->validTo)->format('Y-m-d');

            return (object) ['validFrom' => $validFrom, 'validTo' => $validTo, 'error' => null];
        } catch (Exception $error) {
            return (object) ['validFrom' => '1970-01-01', 'validTo' => '1970-01-01', 'error' => $error->getMessage()];
        }
    }

    private function validateCertificate($certificate, $expiresWithin)
    {
        $curDate = date ('Y-m-d');
        $expiresWithin = date('Y-m-d', strtotime($curDate . "+ {$expiresWithin} day"));
        
        if ($expiresWithin >= $certificate->validTo) 
            return true;
        
        return false;
    }
}

$hostName = (isset($argv[1]) ? $argv[1] : null);
$expiresWithin = (isset($argv[2]) ? $argv[2] : null);

new SSL($hostName, $expiresWithin);
