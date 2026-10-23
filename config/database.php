<?php

return [
    'host'       => '127.0.0.1',
    'port'       => 3306,
    'dbname'     => 'crud_demo',
    'user'       => 'root',
    'password'   => '',
    'charset'    => 'utf8mb4',

    // Pool
    'pool_size'  => 5,     // máximo de conexiones simultáneas por proceso
    'persistent' => true,  // PDO::ATTR_PERSISTENT: reutiliza conexiones entre peticiones
];
