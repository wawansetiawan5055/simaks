<?php
// File: app/models/AIModel.php

require_once __DIR__ . '/AppConfigModel.php';

class AIModel {
    // Primary and fallback models supported by Google Gemini API
    private static $models = [
        "gemini-3.6-flash",
        "gemini-flash-lite-latest"
    ];

    /**
     * Generate content using Google Gemini API with automatic fallback
     */
    public static function generate($pdo, $prompt, $system_instruction = "", $response_json = false) {
        $api_key = AppConfigModel::get($pdo, 'gemini_api_key');
        
        if (empty($api_key)) {
            return [
                'success' => false,
                'message' => 'API Key Gemini belum diatur di pengaturan sistem (Menu Pengaturan Aplikasi).'
            ];
        }

        // If system instruction is provided, prepend it to the prompt
        $final_prompt = $prompt;
        if (!empty($system_instruction)) {
            $final_prompt = "INSTRUCTION: " . $system_instruction . "\n\n" . $prompt;
        }

        $last_error = "";
        
        foreach (self::$models as $model) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . urlencode($api_key);
            
            $payload = [
                "contents" => [
                    [
                        "parts" => [
                            ["text" => $final_prompt]
                        ]
                    ]
                ]
            ];

            if ($response_json) {
                $payload["generationConfig"] = [
                    "responseMimeType" => "application/json"
                ];
            }

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
            // Disable SSL verification on Windows to prevent CA certificate path errors
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_error = curl_error($ch);
            curl_close($ch);

            if ($curl_error) {
                $last_error = "cURL Error: " . $curl_error;
                continue;
            }

            $res_data = json_decode($response, true);
            
            if ($http_code === 200 && isset($res_data['candidates'][0]['content']['parts'][0]['text'])) {
                $generated_text = $res_data['candidates'][0]['content']['parts'][0]['text'];
                // Clean markdown code blocks if the output was raw HTML wrapped in ```html
                $clean_text = preg_replace('/^```(?:html)?\s*/i', '', trim($generated_text));
                $clean_text = preg_replace('/\s*```$/', '', $clean_text);
                
                return [
                    'success' => true,
                    'text' => trim($clean_text),
                    'model' => $model
                ];
            } else {
                $msg = $res_data['error']['message'] ?? "HTTP Error ($http_code)";
                $last_error = "Model {$model} error: " . $msg;
            }
        }

        return [
            'success' => false,
            'message' => 'Gagal menghasilkan dokumen. ' . $last_error
        ];
    }
}
?>
