<script lang="ts">
    import { onMount, afterUpdate } from 'svelte'; // Импортируем onMount и afterUpdate из Svelte
    import TypingAnimation from './TypingAnimation.svelte'; // Импортируем компонент анимации
    import { marked } from 'marked'; // Импортируем библиотеку marked для поддержки markdown

    const API_URL: string = 'http://assistant.local/api/chat_bot'; // Константа для URL API

    let message: string = '';
    let messages: Array<{ type: string; text: string }> = [];
    let currentBotConfig: string | null = null; // Значение по умолчанию для выпадающего списка
    let gptModelOptions: Array<{ id: string | null; name: string }> = [{ id: null, name: 'нет модели' }];
    let isDropdownDisabled: boolean = true;
    let isTyping: boolean = false; // Флаг для отображения анимации
    let isRequestPending: boolean = false; // Флаг для предотвращения двойного клика

    /**
     * Отправляет сообщение на сервер и обрабатывает ответ.
     * Если сообщение не пустое и выбрана модель, добавляет его в список сообщений и отправляет на сервер.
     */
    async function sendMessage() {
        if (message.trim() !== '' && currentBotConfig !== null && !isRequestPending) {
            isRequestPending = true; // Устанавливаем флаг, чтобы предотвратить повторный запрос

            // Отображение сообщения пользователя
            addMessage('user', message);

            await fetchMessageFromServer(message, currentBotConfig);
            message = '';
            isRequestPending = false; // Сбрасываем флаг после завершения запроса
        }
    }

    /**
     * Очищает чат и отправляет сообщение @clearContext на сервер.
     */
    async function clearContext() {
        if (!isRequestPending) {
            isRequestPending = true; // Устанавливаем флаг, чтобы предотвратить повторный запрос

            messages = [];
            await fetchMessageFromServer('@clearContext', currentBotConfig);
            isRequestPending = false; // Сбрасываем флаг после завершения запроса
        }
    }

    /**
     * Добавляет сообщение в список сообщений.
     *
     * @param type Тип сообщения (например, 'user' или 'bot').
     * @param text Текст сообщения.
     */
    function addMessage(type: string, text: string) {
        messages = [...messages, { type, text }];
    }

    /**
     * Отправляет сообщение на сервер и получает ответ.
     *
     * @param msg Сообщение для отправки.
     * @param model Текущая модель, используемая для обработки сообщения.
     */
    async function fetchMessageFromServer(msg: string, currentBotConfig: string | null) {
        isTyping = true;
        
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: msg, currentBotConfig: currentBotConfig }),
        });

        isTyping = false; // Отключаем анимацию после получения ответа

        if (response.ok) {
            const data = await response.json();
            addMessage('bot', data.reply);
            applyConfig(data.config);
        } else {
            console.error('Ошибка при отправке сообщения на сервер');
            addMessage('bot', 'Ошибка');
        }
    }

    /**
     * Применяет конфигурацию для обновления параметров модели.
     *
     * @param config Объект конфигурации, содержащий список моделей и текущую модель.
     */
    function applyConfig(config: any) {
        if (config && config.gptModelsList) {
            gptModelOptions = Object.entries(config.gptModelsList).map(([id, name]) => ({
                id,
                name: typeof name === 'string' ? name : 'неизвестная модель',
            }));
            isDropdownDisabled = gptModelOptions.length === 0;

            if (!isDropdownDisabled) {
                currentBotConfig = config.currentBotConfig || gptModelOptions[0].id;
            } else {
                gptModelOptions = [{ id: null, name: 'нет модели' }];
                currentBotConfig = null;
            }
        } else {
            gptModelOptions = [{ id: null, name: 'нет модели' }];
            currentBotConfig = null;
            isDropdownDisabled = true;
        }
    }

    /**
     * Обрабатывает нажатие клавиши Enter в поле ввода.
     * Если нажата клавиша Enter и выбрана модель, отправляет сообщение.
     *
     * @param event Событие нажатия клавиши.
     */
    function handleKeyPress(event: KeyboardEvent) {
        if (event.key === 'Enter' && currentBotConfig !== null && !isRequestPending) {
            sendMessage();
        }
    }

    /**
     * Метод, который запускается при первом открытии окна с компонентом.
     * Отправляет начальное сообщение на сервер для установления соединения.
     */
    async function onComponentMount() {
        await fetchMessageFromServer('@handshake', currentBotConfig);
    }

    // Вызов метода при монтировании компонента
    onMount(onComponentMount);

    // Прокрутка к последнему сообщению после обновления списка сообщений
    afterUpdate(() => {
        const chatContainer = document.querySelector('.chat-container');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });
</script>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px); /* Уменьшение высоты окна */
        border: 1px solid #ccc;
        padding: 10px;
        overflow-y: auto;
    }
    .message {
        padding: 5px 10px;
        margin-bottom: 5px;
        border-radius: 4px;
        font-size: 2em; /* Увеличение шрифта в 2 раза */
    }
    .message.user {
        align-self: flex-end;
        background-color: #ddf;
    }
    .message.bot {
        align-self: flex-start;
        background-color: #fdd;
    }
    .input-container {
        display: flex;
        width: 100%;
        height: 30px; /* Уменьшение высоты строки ввода */
    }
    input[type="text"] {
        flex-grow: 1;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }
    input[type="text"]:disabled {
        background-color: #f0f0f0;
        color: #999;
    }
    button {
        margin-left: 5px;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }
    button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }
    .dropdown {
        margin-left: 5px;
    }
</style>

<div>
    <div class="chat-container">
        {#each messages as { type, text } }
            <div class="message {type}">{(text)}</div>
        {/each}
        {#if isTyping}
            <TypingAnimation />
        {/if}
    </div>

    <div class="input-container">
        <input 
            type="text" 
            bind:value={message} 
            placeholder={isDropdownDisabled ? "Ввод сообщения заблокирован. Обновите страницу!" : "Введите сообщение..."} 
            on:keypress={handleKeyPress} 
            disabled={isDropdownDisabled}
        />
        <select bind:value={currentBotConfig} class="dropdown" disabled={isDropdownDisabled}>
            {#each gptModelOptions as { id, name } }
                <option value={id}>{name}</option>
            {/each}
        </select>
        <button on:click={sendMessage} disabled={currentBotConfig === null || isRequestPending}>Отправить</button>
        <button on:click={clearContext} disabled={currentBotConfig === null || isRequestPending}>Забыть контекст</button>
    </div>
</div>
