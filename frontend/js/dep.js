// Elementos del DOM
const floatingBtn = document.getElementById('floatingBtn');
const expandedPanel = document.getElementById('expandedPanel');
const tabs = document.querySelectorAll('.tab');
const tabPanes = document.querySelectorAll('.tab-pane');
const chatInput = document.getElementById('chatInput');
const sendChatBtn = document.getElementById('sendChatBtn');
const chatMessages = document.querySelector('.chat-messages');

// Alternar visibilidad del panel
floatingBtn.addEventListener('click', function() {
    floatingBtn.classList.toggle('active');
    expandedPanel.classList.toggle('active');
});

// Cambiar entre tabs
tabs.forEach(tab => {
    tab.addEventListener('click', function() {
        // Remover clase activa de todos los tabs
        tabs.forEach(t => t.classList.remove('active'));
        // Agregar clase activa al tab clickeado
        this.classList.add('active');
        
        // Obtener el tab a mostrar
        const tabId = this.getAttribute('data-tab');
        
        // Ocultar todos los tab panes
        tabPanes.forEach(pane => pane.classList.remove('active'));
        
        // Mostrar el tab pane correspondiente
        document.getElementById(tabId).classList.add('active');
    });
});

// Enviar mensaje en el chat
function sendMessage() {
    const message = chatInput.value.trim();
    
    if (message) {
        // Agregar mensaje del usuario
        const userMessage = document.createElement('div');
        userMessage.className = 'message user';
        userMessage.innerHTML = `<strong>Tú:</strong> ${message}`;
        chatMessages.appendChild(userMessage);
        
        // Limpiar input
        chatInput.value = '';
        
        // Desplazar hacia abajo
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Simular respuesta de la IA después de un retraso
        setTimeout(() => {
            const aiMessage = document.createElement('div');
            aiMessage.className = 'message ai';
            aiMessage.innerHTML = `<strong>Asistente IA:</strong> He recibido tu pregunta sobre "${message}". ¿Podrías darme más detalles para poder ayudarte mejor?`;
            chatMessages.appendChild(aiMessage);
            
            // Desplazar hacia abajo
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }, 1000);
    }
}

// Eventos para el chat
sendChatBtn.addEventListener('click', sendMessage);

chatInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        sendMessage();
    }
});

// Cerrar el panel si se hace clic fuera de él
document.addEventListener('click', function(event) {
    const isClickInsidePanel = expandedPanel.contains(event.target);
    const isClickOnButton = floatingBtn.contains(event.target);
    
    if (!isClickInsidePanel && !isClickOnButton && expandedPanel.classList.contains('active')) {
        floatingBtn.classList.remove('active');
        expandedPanel.classList.remove('active');
    }
});

// Ejecutar prueba al hacer clic en el botón de testing
document.querySelectorAll('.test-item').forEach(item => {
    item.addEventListener('click', function() {
        const status = this.querySelector('.test-status');
        
        if (status.classList.contains('running')) {
            status.classList.remove('running');
            status.textContent = 'Pasada';
            status.classList.add('passed');
        } else if (status.classList.contains('failed')) {
            status.classList.remove('failed');
            status.textContent = 'Ejecutando...';
            status.classList.add('running');
            
            // Simular tiempo de ejecución
            setTimeout(() => {
                status.classList.remove('running');
                status.textContent = 'Pasada';
                status.classList.add('passed');
            }, 1500);
        }
    });
});