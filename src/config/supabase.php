
<?php 
class SupabaseConfig {
    private string $supabaseUrl;
    private string $supabaseKey;

    public function __construct() {
        // Load environment variables from .env file
        $this->loadEnv();
        
        // Get credentials from environment variables
        $this->supabaseUrl = rtrim(getenv('SUPABASE_URL'), '/');
        $this->supabaseKey = getenv('SUPABASE_KEY');
        
        // Validate credentials are set
        if (empty($this->supabaseUrl) || empty($this->supabaseKey)) {
            throw new Exception('Supabase credentials not properly configured');
        }
    }

    private function loadEnv() {
        // Load .env file from the root directory
        $envFile = __DIR__ . '/../../.env';
        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        // Parse .env file
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($name, $value) = explode('=', $line, 2);
                $name = trim($name);
                $value = trim($value);
                
                // Set environment variable
                putenv("$name=$value");
            }
        }
    }

    public function query($endpoint, $method = 'GET', $data = null, $additionalHeaders = []) {
        $url = $this->supabaseUrl . $endpoint;
        error_log("Making request to: " . $url);
        
        $ch = curl_init($url);
        
        $headers = [
            'apikey: ' . $this->supabaseKey,
            'Authorization: Bearer ' . $this->supabaseKey
        ];
        
        if ($method !== 'GET' && $data) {
            $headers[] = 'Content-Type: application/json';
        }
        
        if (!empty($additionalHeaders)) {
            $headers = array_merge($headers, $additionalHeaders);
        }
        
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_FOLLOWLOCATION => true
        ];

        if ($method !== 'GET') {
            $options[CURLOPT_CUSTOMREQUEST] = $method;
            if ($data) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        curl_setopt_array($ch, $options);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        error_log("Response code: " . $httpCode);
        error_log("Response body: " . $response);
        
        if (curl_errno($ch)) {
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        
        curl_close($ch);

        // Handle empty successful responses
        if ($httpCode >= 200 && $httpCode < 300) {
            if (empty($response)) {
                return ['success' => true];
            }
        }
        
        // Try to decode JSON response
        $decodedResponse = json_decode($response, true);
        if ($decodedResponse === null && !empty($response)) {
            throw new Exception('Invalid JSON response: ' . $response);
        }
        
        if ($httpCode >= 400) {
            throw new Exception('Supabase API Error: HTTP ' . $httpCode . ' - ' . $response);
        }
        
        return $decodedResponse ?? ['success' => true];
    }
}
?>