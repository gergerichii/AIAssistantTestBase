<script lang="ts">
    import { onMount, afterUpdate } from 'svelte'; // Импортируем onMount и afterUpdate из Svelte

    const API_URL = 'http://assistant.local/api/chat_bot'; // Константа для URL API

    let message: string = '';
    let messages: Array<{ type: string; text: string }> = [];

    // Функция для отправки запроса на сервер
    async function sendMessage() {
        if (message.trim() !== '') {
            // Отображение сообщения пользователя
            addMessage('user', message);

            // Отправка запроса на сервер
            const data = await fetchMessageFromServer(message);

            // Добавление ответа от бота
            addMessage('bot', data.reply);
            message = '';
        }
    }

    // Функция для добавления сообщения в список
    function addMessage(type: string, text: string) {
        messages = [...messages, { type, text }];
    }

    // Функция для отправки сообщения на сервер и получения ответа
    async function fetchMessageFromServer(msg: string) {
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: msg }),
        });

        if (response.ok) {
            return await response.json();
        } else {
            console.error('Ошибка при отправке сообщения на сервер');
            return { reply: 'Ошибка' };
        }
    }

    // Обработчик нажатия клавиши Enter
    function handleKeyPress(event: KeyboardEvent) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    }

    // Метод, который запускается при первом открытии окна с компонентом
    async function onComponentMount() {
        const data = await fetchMessageFromServer('@handshake');
        addMessage('bot', data.reply);
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
    button {
        margin-left: 5px;
        padding: 5px 10px;
        border: none;
        border-radius: 4px;
        background-color: #007bff;
        color: white;
        cursor: pointer;
    }
</style>

<div>
    <div class="chat-container">
        {#each messages as { type, text } }
            <div class="message {type}">{text}</div>
        {/each}
    </div>

    <div class="input-container">
        <input type="text" bind:value={message} placeholder="Введите сообщение..." on:keypress={handleKeyPress} />
        <button on:click={sendMessage}>Отправить</button>
    </div>
</div>
