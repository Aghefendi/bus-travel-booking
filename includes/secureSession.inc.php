<?php

if (session_status() === PHP_SESSION_NONE) {

    ini_set("session.cookie_httponly", 1);
    ini_set("session.use_strict_mode", 1);
    ini_set("session.use_only_cookies", 1);

    session_start([
        "cookie_lifetime" => 0,
        "cookie_httponly" => true,
        "cookie_samesite" => "Strict",
        "cookie_secure" => false,
        "use_only_cookies" => true
    ]);
}
?>