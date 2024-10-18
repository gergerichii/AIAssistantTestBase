<script lang="ts">
    import { onMount, afterUpdate } from 'svelte';
    import TypingAnimation from './TypingAnimation.svelte';
    import ErrorModal from './ErrorModal.svelte';

    const API_URL: string = 'http://assistant.local/api/chat_bot';

    let message: string = '';
    let messages: Array<{ type: string; text: string }> = [];
    let currentBotConfig: string | null = null;
    let gptModelOptions: Array<{ id: string | null; name: string }> = [{ id: null, name: 'нет модели' }];
    let isDropdownDisabled: boolean = true;
    let isTyping: boolean = false;
    let isRequestPending: boolean = false;
    let showErrorModal: boolean = false;
    let errorMessage: string = '';

    async function sendMessage() {
        if (message.trim() !== '' && currentBotConfig !== null && !isRequestPending) {
            isRequestPending = true;

            addMessage('user', message);

            await fetchMessageFromServer(message, currentBotConfig);
            message = '';
            isRequestPending = false;
        }
    }

    async function clearContext() {
        if (!isRequestPending) {
            isRequestPending = true;

            messages = [];
            await fetchMessageFromServer('@clearContext', currentBotConfig);
            isRequestPending = false;
        }
    }

    function addMessage(type: string, text: string) {
        messages = [...messages, { type, text }];
    }

    async function fetchMessageFromServer(msg: string, currentBotConfig: string | null) {
        isTyping = true;
        
        const response = await fetch(API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: msg, currentBotConfig: currentBotConfig }),
        });

        isTyping = false;

        if (response.ok) {
            const data = await response.json();
            addMessage('bot', data.reply);
            applyConfig(data.config);
        } else {
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('text/html')) {
                const errorText = await response.text();
                console.error('Ошибка при отправке сообщения на сервер:', errorText);
                addMessage('bot', `Ошибка: ${errorText}`);
            } else {
                errorMessage = await response.text();
                addMessage('error', `Ошибка`); // Добавляем кликабельное сообщение
            }
        }
    }

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

    function handleInputKeyPress(event: KeyboardEvent) {
        if (event.key === 'Enter' && currentBotConfig !== null && !isRequestPending) {
            sendMessage();
        }
    }

    async function onComponentMount() {
        await fetchMessageFromServer('@handshake', currentBotConfig);
    }

    onMount(onComponentMount);

    afterUpdate(() => {
        const chatContainer = document.querySelector('.chat-container');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }
    });

    function handleErrorClick() {
        showErrorModal = true;
    }

    function closeErrorModal() {
        showErrorModal = false;
    }
</script>

<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: calc(100vh - 70px);
        border: 1px solid #ccc;
        padding: 10px;
        overflow-y: auto;
    }
    .message {
        padding: 5px 10px;
        margin-bottom: 5px;
        border-radius: 4px;
        font-size: 2em;
    }
    .message.user {
        align-self: flex-end;
        background-color: #ddf;
    }
    .message.bot, .message.error {
        align-self: flex-start;
        background-color: #fdd;
    }
    .input-container {
        display: flex;
        width: 100%;
        height: 30px;
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
            {#if type === 'error'}
                <button style="background-color: red;" on:click={handleErrorClick}>
                    {(text)}
                </button>
            {:else}
                <div class="message {type}">
                    {(text)}
                </div>
            {/if}
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
            on:keypress={handleInputKeyPress} 
            disabled={isDropdownDisabled}
        />
        <select bind:value={currentBotConfig} class="dropdown" disabled={isDropdownDisabled}>
            {#each gptModelOptions as { id, name } }
                <option value={id}>{name}</option>
            {/each}
        </select>
        <button on:click={sendMessage} disabled={currentBotConfig === null || isRequestPending}>Отправить</button>
        <button on:click={clearContext} disabled={isRequestPending}>Забыть контекст</button>
    </div>
    {#if showErrorModal}
        <ErrorModal {errorMessage} on:close={closeErrorModal} />
    {/if}
</div>
