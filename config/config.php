<?php
$link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$u = "$_SERVER[HTTP_HOST]";
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    $_SERVER['HTTPS'] = 'on';
    $http = "https://";
  } else {
    $http = "http://";
  }
define('URL',$http.$u.'/svsysback/');// ip local:puerto
define('TOKEN_MOVIL', 'NLJwd=twVjJZ5!caOx!Cuh2XfjbLmcKXBr3R0F07DF8U?bDN1/i9omfIALwsVTZSGR0EhiOeNipl5pk5=s1rxL8RvF6pDxxVlTBmzOL2QCp0qGlPbSv=gs8tKGREhxGds29RXwbAU56nx5K6rotNeCXigeTNUFR5E-Bq!0T?LqoIyqvHkg6S13kv-fxm3e=piDz3k2jhrOuHFOVx-DzwC8I/?F3lPSRuvj0V/!oO2YAgqHGA3p-Kt3YQnpOWM7!6');
define('TOKEN_WEB', 'My0Ua8GDgEMPbpTZhiOEwjrzy5s4r9GFBOO7RWgwDA1kP2ZixULX0GpVHh99erfm');

// define('HOST', 'tcp:10.5.1.245');
// define('DB', 'svsys');
// define('USER', 'root');
// define('PASSWORD', 'Bruno2001');
// define('CHARSET', 'utf8mb4');

// define('HOST', '85.10.205.173:3306');
// define('DB', 'svsys_jorge123');
// define('USER', 'jorge_123');
// define('PASSWORD', 'Jorge123*');
// define('CHARSET', 'utf8mb4');


define('HOST', '127.0.0.1');
define('DB', 'svsys');
define('USER', 'root');
define('PASSWORD', '');
define('CHARSET', 'utf8mb4');
