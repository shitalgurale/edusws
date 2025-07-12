<?php
file_put_contents('face_device_test.log', date('Y-m-d H:i:s') . " - HIT\n", FILE_APPEND);
file_put_contents('face_device_test.log', "HEADERS:\n" . print_r(getallheaders(), true), FILE_APPEND);
file_put_contents('face_device_test.log', "BODY:\n" . file_get_contents('php://input') . "\n\n", FILE_APPEND);

echo "OK RAW INDEX.PHP";
?>
