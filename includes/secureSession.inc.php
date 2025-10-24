<?php
// Only configure session if none exists
if (session_status() === PHP_SESSION_NONE) {
    // Set session ini settings before starting the session
    ini_set("session.cookie_httponly", 1);
    ini_set("session.use_strict_mode", 1);
    ini_set("session.use_only_cookies", 1);

    session_start([
        "cookie_lifetime" => 0,
        "cookie_httponly" => true,
        "cookie_samesite" => "Strict",
        "cookie_secure" => false, // change to true in production with HTTPS
        "use_only_cookies" => true
    ]);
}
?>