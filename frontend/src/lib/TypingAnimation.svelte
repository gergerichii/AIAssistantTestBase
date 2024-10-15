<script lang="ts">
    import { onDestroy } from 'svelte';

    let dots = '';
    let interval: NodeJS.Timeout;

    onDestroy(() => {
        clearInterval(interval);
    });

    interval = setInterval(() => {
        dots = dots.length < 3 ? dots + '.' : '';
    }, 250);
</script>

<style>
    .typing-animation {
        font-style: italic;
        color: #999;
        font-size: 2em; /* Увеличение шрифта в 2 раза */
    }
</style>

<div class="typing-animation">
    Оператор набирает текст{dots}
</div>