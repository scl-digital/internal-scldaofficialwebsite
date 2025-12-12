<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

class PayPalClient {
    public static function client() {
        global $pdo;
        // read the latest company settings for PayPal keys
        $stmt = $pdo->query("SELECT * FROM company_settings ORDER BY id DESC LIMIT 1");
        $settings = $stmt->fetch();

        $clientId = $settings['paypal_client_id'] ?? getenv('CLIENT_ID');
        $clientSecret = $settings['paypal_client_secret'] ?? getenv('CLIENT_SECRET');
        $mode = $settings['paypal_mode'] ?? 'sandbox';

        // Local sandbox fallback for development/testing only.
        // WARNING: Do NOT commit real credentials into source control for production.
        // If no credentials are available from DB or environment, and we're running
        // on localhost, use the demo sandbox credentials for quick testing.
        if ((!$clientId || !$clientSecret)) {
            $isLocal = (isset($_SERVER['SERVER_NAME']) && (strpos($_SERVER['SERVER_NAME'], 'localhost') !== false || $_SERVER['SERVER_NAME'] === '127.0.0.1'))
                       || (php_sapi_name() === 'cli' && (getenv('LOCAL_DEVELOPMENT') === '1'));

            if ($isLocal) {
                // Demo sandbox credentials (replace with your own sandbox keys). Intended for local testing only.
                $clientId = 'AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm';
                $clientSecret = 'AR8HvXVovw6J8Z1NOnhn_GdJxj6ZUEoYMZuNhdMypq06NbZrWekzO6P0M7rq3QUhTJrwE7Ou-5Mkjwbm';
                $mode = 'sandbox';
            } else {
                throw new \Exception('PayPal credentials are not configured');
            }
        }

        if ($mode === 'live') {
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }
        return new PayPalHttpClient($environment);
    }
}

?>
