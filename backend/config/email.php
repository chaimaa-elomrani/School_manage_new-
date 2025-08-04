<?php

return [
    'smtp_host' => $_ENV['SMTP_HOST'] ?? 'smtp.gmail.com',
    'smtp_port' => $_ENV['SMTP_PORT'] ?? 587,
    'username' => $_ENV['SMTP_USERNAME'] ?? 'chaimaelomrani6@gmail',
    'password' => $_ENV['SMTP_PASSWORD'] ?? 'ryesdmadagrpmfuo',
    'from_email' => $_ENV['FROM_EMAIL'] ?? 'noreply@school.com',
    'from_name' => $_ENV['FROM_NAME'] ?? 'School Management System',
    'smtp_secure' => $_ENV['SMTP_SECURE'] ?? 'tls',
];


