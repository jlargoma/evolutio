<?php
namespace App\Services;

      
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LogsService{
  
  var $logger;
  
  public function __construct($folder,$channel) {
    $this->logger = new Logger($channel);
    $this->logger->pushHandler(
            new StreamHandler(storage_path('logs/'.$folder.'.log'), Logger::DEBUG)
            );
  }
  
  public function info($text,$context = null) {
    if ($context)  $this->logger->info($text,[$context]);
    else   $this->logger->info($text);
  }
  public function warning($text,$context = null) {
    if ($context)  $this->logger->warning($text,[$context]);
    else   $this->logger->warning($text);
  }
  public function error($text,$context = null) {
    if ($context)  $this->logger->error($text,[$context]);
    else   $this->logger->error($text);
  }
  
}


