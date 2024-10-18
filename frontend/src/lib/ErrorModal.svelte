<script lang="ts">
    import { createEventDispatcher } from 'svelte';

    export let errorMessage: string;

    const dispatch = createEventDispatcher();

    function handleClose() {
        window.removeEventListener('keydown', handleKeyPress);
        dispatch('close');
    }

    // Обработчик нажатия клавиши
    function handleKeyPress(event: KeyboardEvent) {
        if (event.key === 'Escape') {
            handleClose();
        }
    }

    // Добавляем обработчик события нажатия клавиш при монтировании компонента
    window.addEventListener('keydown', handleKeyPress);
</script>

<style>
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .iframe-container {
        width: calc(100% - 20px);
        height: calc(100% - 20px);
        margin: 10px;
        overflow: hidden;
        background-color: #f0f0f0; /* Изменено на светло серый цвет */
        border-radius: 8px;
        position: relative;
    }

    .error-content-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background-color: #ff5f5f; /* Розовая панелька */
        padding: 10px;
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }

    .close-button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.5em;
    }

    .error-content {
        text-align: center;
        font-size: 1.2em;
        margin: 0; /* Убираем отступы */
    }

    iframe {
        width: 100%;
        height: calc(100% - 50px); /* Учитываем высоту кнопки */
        border: none;
    }
</style>

<div class="modal">
    <div class="iframe-container">
        <div class="error-content-container">
            <div class="error-content">Содержимое ошибки</div>
            <button class="close-button" on:click={handleClose}>&times;</button>
        </div>
        <iframe title="Ошибка" srcdoc={errorMessage}></iframe>
    </div>
</div>
