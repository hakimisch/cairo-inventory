<?php

return [
    'superadmin' => [
        'priority' => 100,
        'label'    => 'Super Admin',
        'can'      => ['manage_users', 'verify', 'crud_assets', 'force_delete', 'view_reports', 'view_admin'],
    ],
    'admin' => [
        'priority' => 80,
        'label'    => 'Pentadbir',
        'can'      => ['verify', 'crud_assets', 'view_reports', 'view_admin'],
    ],
    'manager' => [
        'priority' => 60,
        'label'    => 'Pengurus',
        'can'      => ['view_reports'],
    ],
    'auditor' => [
        'priority' => 40,
        'label'    => 'Auditor',
        'can'      => ['view_reports'],
    ],
    'user' => [
        'priority' => 20,
        'label'    => 'Pengguna',
        'can'      => ['verify'],
    ],
];
