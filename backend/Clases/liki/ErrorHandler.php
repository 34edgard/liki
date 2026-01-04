<?php

namespace Liki;

use Liki\Plantillas\Flow;


class ErrorHandler {  
    private static $instance = null;  
    private $responseType;  
    private $logEnabled;  
    private $logPath;  
    private $showDetails;  
    private $logLevel;  
    private $language;  
      
    // Códigos de error del sistema  
    const AUTH_INVALID_CREDENTIALS = 1001;  
    const AUTH_USER_NOT_FOUND = 1002;  
    const AUTH_EMAIL_NOT_FOUND = 1003;  
    const DB_CONNECTION_ERROR = 2001;  
    const DB_QUERY_ERROR = 2002;  
    const VALIDATION_ERROR = 3001;  
    const ROUTE_NOT_FOUND = 4001;  
    const PERMISSION_DENIED = 4003;  
    const SERVER_ERROR = 5000;  
    
    private function __construct() {  
        $this->responseType = defined('ERROR_RESPONSE_TYPE') ? ERROR_RESPONSE_TYPE : 'json';  
        $this->logEnabled = defined('ERROR_LOG_ENABLED') ? ERROR_LOG_ENABLED : true;  
        $this->logPath = defined('ERROR_LOG_PATH') ? ERROR_LOG_PATH : __DIR__ . '/../../logs/errors.log';  
        $this->showDetails = defined('ERROR_SHOW_DETAILS') ? ERROR_SHOW_DETAILS : false;  
        $this->logLevel = defined('ERROR_LOG_LEVEL') ? ERROR_LOG_LEVEL : 'ERROR';  
        $this->language = defined('ERROR_LANGUAGE') ? ERROR_LANGUAGE : 'es';  
    }  
      
    public static function getInstance() {  
        if (self::$instance === null) {  
            self::$instance = new self();  
        }  
        return self::$instance;  
    }  
    public function handle($errorCode, $message, $details = [], $httpCode = 500) {  
        // Log del error  
        if ($this->logEnabled) {  
            $this->logError($errorCode, $message, $details);  
        }  
          
        // Preparar respuesta  
        $response = $this->prepareResponse($errorCode, $message, $details);  
          
        // Enviar respuesta según tipo configurado  
        $this->sendResponse($response, $httpCode);  
    }  
      
    private function prepareResponse($errorCode, $message, $details) {  
        $response = [  
            'success' => false,  
            'error_code' => $errorCode,  
            'message' => $this->translateMessage($message),  
            'timestamp' => date('Y-m-d H:i:s')  
        ];  
          
        if ($this->showDetails && !empty($details)) {  
            $response['details'] = $details;  
        }  
          
        return $response;  
    }  
      
    private function sendResponse($response, $httpCode) {  
        if (defined('ERROR_HTTP_CODES') && ERROR_HTTP_CODES) {  
            http_response_code($httpCode);  
        }  
          
        switch ($this->responseType) {  
            case 'json':  
                header('Content-Type: application/json');  
                echo json_encode($response);  
            //    Flow::json($response,$response['error_code']);
                break;  
                  
            case 'html':  
                Flow::html('errores/error', $response);  
                break;  
                  
            case 'xml':  
                header('Content-Type: application/xml');  
                echo $this->arrayToXml($response);  
                break;  
        }  
          
        exit;  
    }  
    
    
    
    
    private function logError($errorCode, $message, $details) {  
        $logMessage = sprintf(  
            "[%s] Code: %d | Message: %s | Details: %s | User: %s | IP: %s\n",  
            date('Y-m-d H:i:s'),  
            $errorCode,  
            $message,  
            json_encode($details),  
            $_SESSION['cedula'] ?? 'guest',  
            $_SERVER['REMOTE_ADDR'] ?? 'unknown'  
        );  
          
        error_log($logMessage, 3, $this->logPath);  
    }  
      
    private function translateMessage($message) {  
        // Implementar traducciones según ERROR_LANGUAGE  
        return $message;  
    }  
}