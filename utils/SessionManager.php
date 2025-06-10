<?php

class SessionManager {
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function destroySession() {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public static function set($key, $value) {
        self::startSession();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::startSession();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function remove($key) {
        self::startSession();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function clear() {
        self::startSession();
        $_SESSION = array();
    }

    public static function isAuthenticated() {
        self::startSession();
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
} 