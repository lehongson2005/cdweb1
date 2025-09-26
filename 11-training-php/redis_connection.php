<?php
$redis = new Redis();       
try {
    // Trong container PHP, dùng service name "redis" + cổng mặc định 6379
    $redis->connect('redis', 6379);

    echo " Redis connected successfully<br>";

    // Test thử
    $redis->set("test_key", "Hello from Redis!");
    echo "Test value: " . $redis->get("test_key");

} catch (Exception $e) {
    echo " Redis connection failed: " . $e->getMessage();
}
