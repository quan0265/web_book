<?php 

if (!function_exists('sendDiscord')) {
    function sendDiscord($message, $avatarUrl = null) {
        $webhookUrl = "https://discord.com/api/webhooks/1410086294171684874/YXbgvegF_M8fccxb2p7ZW4dS1kZHTF2N52w3nbhguppbzvjp27j0onhQzc54biFqEI2t";
        $username = "Doraemon";

        if (empty($webhookUrl) || empty($message)) {
            return false;
        }

        $data = [
            "content" => $message,
            "username" => $username
        ];

        if ($avatarUrl) {
            $data["avatar_url"] = $avatarUrl;
        }

        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 300);
    }
}



?>