<script src="/sw.js"></script>
 
<script>
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/sw-2.js')
            .then(() => alert('Liki PWA: Lista para modo offline'));
    }
</script>