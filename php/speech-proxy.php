<?php

$apiKey = 'ваш_api_key';
$folderId = 'ваш_folder_id';

$tmpDir = sys_get_temp_dir();
$tmpInput = tempnam($tmpDir, 'webm_') . '.webm';
$tmpOutput = tempnam($tmpDir, 'ogg_') . '.ogg';

try {
    // Сохраняем входящий WebM в файл
    $inputData = file_get_contents('php://input');
    if (!$inputData) {
        throw new RuntimeException('Не удалось прочитать входной поток.');
    }

    file_put_contents($tmpInput, $inputData);

    // Конвертация через ffmpeg
    $command = sprintf(
        'ffmpeg -y -i %s -c:a libopus -b:a 48k %s 2>&1',
        escapeshellarg($tmpInput),
        escapeshellarg($tmpOutput)
    );
    exec($command, $output, $resultCode);

    if ($resultCode !== 0 || !file_exists($tmpOutput)) {
        throw new RuntimeException('Ошибка при конвертации ffmpeg: ' . implode("\n", $output));
    }

    // Отправка в Yandex SpeechKit
    $oggData = file_get_contents($tmpOutput);

    $ch = curl_init("https://stt.api.cloud.yandex.net/speech/v1/stt:recognize?folderId={$folderId}");
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $oggData,
        CURLOPT_HTTPHEADER => [
            "Authorization: Api-Key {$apiKey}",
            "Content-Type: audio/ogg;codecs=opus",
        ],
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        throw new RuntimeException('Ошибка CURL: ' . $curlError);
    }

    http_response_code($httpCode);
    header('Content-Type: application/json');
    echo $response;

} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => $e->getMessage(),
    ]);
} finally {
    if (file_exists($tmpInput)) {
        unlink($tmpInput);
    }
    if (file_exists($tmpOutput)) {
        unlink($tmpOutput);
    }
}
