<style>


.app-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    overflow: hidden;
}



/* Panel de control */
.control-panel {
    background: #f8f9fa;
    padding: 15px 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    border-bottom: 1px solid #dee2e6;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #5a6268;
}

.btn-error {
    background: #dc3545;
    color: white;
}

.btn-error:hover {
    background: #c82333;
}

.btn-warning {
    background: #ffc107;
    color: #212529;
}

.btn-warning:hover {
    background: #e0a800;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 5px;
    cursor: pointer;
}

.error-count {
    margin-left: auto;
    background: #2c3e50;
    color: white;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 14px;
}

/* Contenedor de errores */
.error-container {
    height: 400px;
    overflow-y: auto;
    background: #1e1e1e;
    color: #d4d4d4;
    font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
    padding: 15px;
}

.empty-state {
    text-align: center;
    color: #666;
    padding: 40px;
    font-style: italic;
}

/* Estilos para cada error */
.error-item {
    margin-bottom: 15px;
    padding: 12px;
    border-radius: 6px;
    background: #2d2d2d;
    border-left: 4px solid;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.error-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 8px;
    padding-bottom: 8px;
    border-bottom: 1px solid #404040;
}

.error-type {
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 3px;
    font-size: 12px;
    text-transform: uppercase;
}

.error-type.error { background: #dc3545; color: white; }
.error-type.warning { background: #ffc107; color: #212529; }

.error-time {
    color: #888;
    font-size: 11px;
}

.error-message {
    color: #fff;
    margin-bottom: 5px;
    font-weight: 500;
    word-break: break-word;
}

.error-stack {
    font-size: 12px;
    color: #888;
    padding: 8px;
    background: #1e1e1e;
    border-radius: 4px;
    overflow-x: auto;
    white-space: pre-wrap;
    font-family: inherit;
}

.error-location {
    font-size: 11px;
    color: #569cd6;
    margin-top: 5px;
}

/* Botones de prueba */
.test-buttons {
    padding: 20px;
    background: #f8f9fa;
    border-top: 1px solid #dee2e6;
}

.test-buttons h3 {
    margin-bottom: 15px;
    color: #495057;
    font-size: 16px;
}

.test-buttons .btn {
    margin-right: 10px;
    margin-bottom: 10px;
}

/* Responsive */
@media (max-width: 600px) {
    .control-panel {
        flex-wrap: wrap;
    }
    
    .error-count {
        margin-left: 0;
        width: 100%;
        text-align: center;
    }
    
    .test-buttons .btn {
        width: 100%;
        margin-right: 0;
    }
}

</style>
    <div class="app-container">
        <h1>Visualizador de Errores de Consola</h1>
        
        <!-- Panel de control -->
        <div class="control-panel">
            <button id="btn-limpiar" class="btn btn-secondary">Limpiar Errores</button>
            <label class="checkbox-label">
                <input type="checkbox" id="checkbox-auto-scroll" checked> Auto-scroll
            </label>
            <span id="contador-errores" class="error-count">0 errores</span>
        </div>

        <!-- Contenedor de errores -->
        <div id="error-container" class="error-container">
            <div class="empty-state">No hay errores para mostrar</div>
        </div>

        <!-- Botones para probar errores -->
        
    </div>
<script>
// Clase para manejar la visualización de errores
class ErrorVisualizer {
    constructor() {
        this.container = document.getElementById('error-container');
        this.contador = document.getElementById('contador-errores');
        this.checkboxAutoScroll = document.getElementById('checkbox-auto-scroll');
        this.btnLimpiar = document.getElementById('btn-limpiar');
        this.errores = [];
        
        this.init();
    }

    init() {
        // Capturar errores globales
        window.addEventListener('error', (event) => {
            this.agregarError({
                tipo: 'error',
                mensaje: event.message,
                archivo: event.filename,
                linea: event.lineno,
                columna: event.colno,
                stack: event.error?.stack || 'No disponible',
                timestamp: new Date()
            });
        });

        // Capturar promesas rechazadas
        window.addEventListener('unhandledrejection', (event) => {
            this.agregarError({
                tipo: 'error',
                mensaje: `Promesa rechazada: ${event.reason}`,
                stack: event.reason?.stack || 'No disponible',
                timestamp: new Date()
            });
        });

        // Interceptar console.error
        const originalConsoleError = console.error;
        console.error = (...args) => {
            this.agregarError({
                tipo: 'error',
                mensaje: args.map(arg => this.formatearArgumento(arg)).join(' '),
                timestamp: new Date()
            });
            originalConsoleError.apply(console, args);
        };

        // Interceptar console.warn
        const originalConsoleWarn = console.warn;
        console.warn = (...args) => {
            this.agregarError({
                tipo: 'warning',
                mensaje: args.map(arg => this.formatearArgumento(arg)).join(' '),
                timestamp: new Date()
            });
            originalConsoleWarn.apply(console, args);
        };

        // Event listeners para los botones
        this.btnLimpiar.addEventListener('click', () => this.limpiarErrores());
    }

    formatearArgumento(arg) {
        if (arg instanceof Error) {
            return `${arg.name}: ${arg.message}`;
        }
        if (typeof arg === 'object') {
            try {
                return JSON.stringify(arg, null, 2);
            } catch {
                return String(arg);
            }
        }
        return String(arg);
    }

    agregarError(error) {
        this.errores.push(error);
        this.actualizarContador();
        this.mostrarError(error);
        
        if (this.checkboxAutoScroll.checked) {
            this.scrollAlFinal();
        }
    }

    mostrarError(error) {
        // Remover mensaje de empty state si existe
        const emptyState = this.container.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        const errorElement = document.createElement('div');
        errorElement.className = 'error-item';
        
        const tiempo = error.timestamp.toLocaleTimeString();
        const tipoClase = error.tipo === 'error' ? 'error' : 'warning';
        
        errorElement.innerHTML = `
            <div class="error-header">
                <span class="error-type ${tipoClase}">${error.tipo.toUpperCase()}</span>
                <span class="error-time">${tiempo}</span>
            </div>
            <div class="error-message">${this.escapeHTML(error.mensaje)}</div>
            ${error.stack && error.stack !== 'No disponible' ? 
                `<div class="error-stack">${this.escapeHTML(error.stack)}</div>` : ''}
            ${error.archivo ? 
                `<div class="error-location">${error.archivo}:${error.linea}:${error.columna}</div>` : ''}
        `;

        this.container.appendChild(errorElement);
    }

    escapeHTML(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    actualizarContador() {
        const erroresCount = this.errores.filter(e => e.tipo === 'error').length;
        const warningsCount = this.errores.filter(e => e.tipo === 'warning').length;
        
        this.contador.textContent = `${erroresCount} errores, ${warningsCount} warnings`;
        
        if (erroresCount > 0) {
            this.contador.style.background = '#dc3545';
        } else if (warningsCount > 0) {
            this.contador.style.background = '#ffc107';
            this.contador.style.color = '#212529';
        } else {
            this.contador.style.background = '#2c3e50';
            this.contador.style.color = 'white';
        }
    }

    limpiarErrores() {
        this.errores = [];
        this.container.innerHTML = '<div class="empty-state">No hay errores para mostrar</div>';
        this.actualizarContador();
        this.contador.style.background = '#2c3e50';
        this.contador.style.color = 'white';
    }

    scrollAlFinal() {
        this.container.scrollTop = this.container.scrollHeight;
    }
}

// Funciones para probar diferentes tipos de errores


// Inicializar el visualizador cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    
    // Ejemplo de error asíncrono
    
});
</script>