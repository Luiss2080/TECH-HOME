<?php
return [
    // Alertas por email
    'alerts' => [
        'enabled' => true,
        'email' => "admin@tech-home.com",
        'threshold' => [
            'failed_logins' => 10,      // > 10 fallos en 1 hora
            'suspicious_ips' => 5,      // > 5 eventos de 1 IP en 1 hora
            'critical_events' => 1      // Cualquier evento CRITICAL
        ]
    ],
    
    // Configuración de rate limiting por IP
    'ip_blacklist' => [
        'enabled' => true,
        'auto_block_threshold' => 50,  // Bloquear IP después de 50 eventos
        'block_duration_hours' => 24
    ],
    
    // Limpieza automática
    'cleanup' => [
        'security_logs_days' => 90,
        'audit_logs_days' => 365,
        'rate_limit_logs_days' => 7
    ]
];
