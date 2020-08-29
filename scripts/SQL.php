<?php

set_error_handler(function ($severity, $message, $file, $line) {
    throw new \ErrorException($message, $severity, $severity, $file, $line);
});

class Query
{

    private $host   = "dns or ip to database";
    private $user   = "your username for sql";
    private $pass   = "your user sql password";
    private $db     = "zabbix";
    private $table  = "httptest";

    private $connection;

    public function __construct($hostName = null, $expiresWithin = null)
    {
        if ($this->host == "" || $this->user == "" || $this->pass == "" || $this->db == "" || $this->table == "") {
            throw new Exception("Missing database information.");
        } else {
            $this->connection = new mysqli($this->host, $this->user, $this->pass, $this->db);
            echo json_encode($this->getHttpSteps());
        }
    }

    private function getHttpSteps()
    {
        try {
            $sql = "SELECT name FROM " . $this->table;
            $result = $this->connection->query($sql);
            $urls = [];

            foreach($result as $item)
            {
              $url = str_replace('https://', '', $item['name']);
              $urls[] = ['{#URL}' => $url];
            }

            return (object) ['data' => $urls];
        } catch (Exception $error) {
            return (object) ['error' => $error->getMessage()];
        }
    }

    private function checkIfHttps($url)
    {
        if(preg_match('/^https:\/\//m', $url))
        {
          return true;
        } else {
          return false;
        }
    }
}

new Query();
