<?

$apiKey = 'ваш_api_key';
$folderId = 'ваш_folder_id';


// Временный файл webm
$tmpInput = tempnam(sys_get_temp_dir(), 'webm_') . '.webm';
$tmpOutput = tempnam(sys_get_temp_dir(), 'ogg_') . '.ogg';

// Сохраняем webm во временный файл
file_put_contents($tmpInput, file_get_contents('php://input'));

// Конвертируем webm -> ogg (opus)
exec("ffmpeg -y -i {$tmpInput} -c:a libopus -b:a 48k {$tmpOutput}");

// Отправляем ogg в Yandex
$oggData = file_get_contents($tmpOutput);

$ch = curl_init("https://stt.api.cloud.yandex.net/speech/v1/stt:recognize?folderId={$folderId}");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $oggData,
    CURLOPT_HTTPHEADER => [
        "Authorization: Api-Key {$apiKey}",
        "Content-Type: audio/ogg;codecs=opus"
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Удаляем временные файлы
unlink($tmpInput);
unlink($tmpOutput);

http_response_code($httpCode);
header('Content-Type: application/json');
echo $response;
