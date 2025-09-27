<?php
$redis = new Redis();       
try {
    // Trong container PHP, dùng service name "redis" + cổng mặc định 6379
    $redis->connect('redis', 6379);

   

   

} catch (Exception $e) {
    echo " Redis connection failed: " . $e->getMessage();
}
