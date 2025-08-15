# SpeechKit Voice Input

Голосовой ввод текста с помощью **Yandex SpeechKit API**.  
Простой и лёгкий плагин на JS + PHP для интеграции голосового ввода в любые формы (поиск, комментарии, заявки и т.д.).


## Возможности

- Запись речи по нажатию кнопки
- Отправка аудио на сервер
- Распознавание через Yandex SpeechKit
- Поддержка браузеров с `MediaRecorder` API
- Простой `demo.html` для проверки


## Структура проекта

```
speechkit-voice-input/
├── demo/
│   └── index.html             # Пример формы с голосовым вводом
├── js/
│   └── speechkit-voice-input.js  # Скрипт для записи и отправки
├── php/
│   └── speech-proxy.php       # Серверный обработчик для SpeechKit
└── .gitignore
```


## Требования

- В браузере: поддержка `MediaRecorder`
- На сервере:
  - PHP ≥ 7.2
  - ffmpeg установлен в системе
  - API-ключ и folderId от Яндекс Облака


## Установка

1. Распакуйте архив проекта.
2. Подключите скрипт и разметку:

```html
<input type="text" id="search" placeholder="Говорите..." />
<button id="voice-btn">🎤</button>
<script src="/js/speechkit-voice-input.js"></script>
```

3. Настройте `php/speech-proxy.php`:
   - Вставьте ваш `Api-Key` и `folderId`

4. Убедитесь, что сервер умеет выполнять `ffmpeg` с поддержкой кодека `libopus` и принимать POST-запросы с `webm`.


## Пример использования

Смотри `demo/index.html` — можно вставить на любую страницу.


## Автор

Сергей Дрогалов [@drogalov](https://github.com/drogalov)
