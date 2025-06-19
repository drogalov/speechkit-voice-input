const searchInput = document.getElementById('search');
const voiceBtn = document.getElementById('voice-btn');

let mediaRecorder;
let audioChunks = [];

async function recognizeSpeech(blob) {
    try {
        const response = await fetch('/php/speech-proxy.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'audio/ogg;codecs=opus'
            },
            body: blob
        });

        const result = await response.json();

        if (result.result) {
            searchInput.value = result.result;
        } else {
            console.error(result);
            alert('Ошибка распознавания речи.');
        }
    } catch (error) {
        console.error('Ошибка запроса:', error);
        alert('Не удалось отправить аудио на сервер.');
    }
}

async function startRecording() {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });

    let mimeType = '';

    if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
        mimeType = 'audio/webm;codecs=opus';
    } else if (MediaRecorder.isTypeSupported('audio/webm')) {
        mimeType = 'audio/webm';
    } else {
        alert('Ваш браузер не поддерживает запись аудио.');
        return;
    }

    mediaRecorder = new MediaRecorder(stream, { mimeType });
    audioChunks = [];

    mediaRecorder.ondataavailable = (event) => {
        audioChunks.push(event.data);
    };

    mediaRecorder.onstop = () => {
        const audioBlob = new Blob(audioChunks, { type: mimeType });
        recognizeSpeech(audioBlob);
    };

    mediaRecorder.start();
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }
}

voiceBtn.addEventListener('mousedown', startRecording);
voiceBtn.addEventListener('mouseup', stopRecording);
voiceBtn.addEventListener('touchstart', (e) => {
    e.preventDefault();
    startRecording();
});
voiceBtn.addEventListener('touchend', stopRecording);
