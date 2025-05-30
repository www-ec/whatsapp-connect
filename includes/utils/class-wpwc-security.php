<?php
if (!defined('ABSPATH')) {
    exit;
}

class WPWC_Security {
    public function __construct() {
        // Add security filters if needed
    }

    public static function sanitize_phone($phone) {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    public static function sanitize_message($message) {
        return sanitize_text_field($message);
    }
}