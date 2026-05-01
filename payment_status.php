<?php
echo file_exists("status.txt") ? file_get_contents("status.txt") : "PENDING";
?>