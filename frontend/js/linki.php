const linkiMessages = [
    "¡Tu código es tan ligero que casi no lo siento! ✨",
    "¿Viste eso? Respondí antes de que terminaras el clic. ⚡",
    "No es por presumir, pero usamos menos RAM que una pestaña vacía. 😎",
    "¿Necesitas ayuda con una ruta? ¡Estoy listo! 🚀"
];

const linkiBird = document.getElementById('linki-bird');
const linkiMsg = document.getElementById('linki-msg');

// Cambiar mensaje aleatoriamente al hacer clic
linkiBird.addEventListener('click', () => {
    const randomMsg = linkiMessages[Math.floor(Math.random() * linkiMessages.length)];
    linkiMsg.innerText = randomMsg;
    linkiMsg.style.opacity = 1;
    
    // Animación de "aleteo rápido" al interactuar
    linkiBird.style.animation = "float 0.5s ease-in-out infinite";
    setTimeout(() => {
        linkiBird.style.animation = "float 3s ease-in-out infinite";
    }, 2000);
});