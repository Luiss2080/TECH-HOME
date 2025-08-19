<?php

namespace App\Services;

use App\Services\Email\MailServiceInterface;

class MailServiceFactory
{
    /**
     * Crear instancia del servicio de email según configuración
     */
    public static function create(): MailServiceInterface
    {
        // Obtener solo el nombre de la clase desde el .env
        $serviceClassName = $_ENV['MAIL_SERVICE_CLASS'] ?? 'SimpleMailService';
        
        // Construir el namespace completo
        $fullClassName = 'App\\Services\\Email\\' . $serviceClassName;
        
        // Verificar que la clase existe
        if (!class_exists($fullClassName)) {
            throw new \Exception("La clase de servicio de email '{$fullClassName}' no existe.");
        }
        
        // Crear y retornar la instancia
        return new $fullClassName();
    }
    
    /**
     * Obtener el nombre de la clase configurada
     */
    public static function getServiceClass(): string
    {
        $serviceClassName = $_ENV['MAIL_SERVICE_CLASS'] ?? 'SimpleMailService';
        return 'App\\Services\\Email\\' . $serviceClassName;
    }
    
    /**
     * Verificar si el servicio está correctamente configurado
     */
    public static function isConfigured(): bool
    {
        $serviceClassName = $_ENV['MAIL_SERVICE_CLASS'] ?? 'SimpleMailService';
        $fullClassName = 'App\\Services\\Email\\' . $serviceClassName;
        return class_exists($fullClassName);
    }
}
