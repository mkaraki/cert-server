<?php
$auth = array(
    // Username 'server-1'
    'server-1' => array(
        // Accepted CA name
        'accepted_ca' => array(
            'test01',
            'test02'
        ),
        // Password Hash
        'password' => '$2y$10$CbTHYgK6SXd86F.gAaea8.3HnOpyUaE2Zlvt0BqNXiBubleTUZge2'
    )
);

// Administrators
$admins = array(
    'admin' => array(
        'password' => '$2y$10$YfkghONsTTYWIAS7GOBu/.Ihym86ZmQZLZM2RLkpP4DmBZslefrY.' // test
    )
);

// Force certificate expire days
$forced_expire_days = array(
    // Cert name => days
    'test01' => '365',
);
