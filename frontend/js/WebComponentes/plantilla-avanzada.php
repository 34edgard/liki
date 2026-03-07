<?php
use Liki\Plantillas\Flow;
/**
 * Plantilla avanzada para Web Components dinámicos con Liki.
 * 
 * @var array $datos Contiene la configuración para generar el componente.
 * esperadas:
 *  - nombre_clase: (string) Nombre para la clase JS del componente.
 *  - nombre_etiqueta: (string) Nombre para la etiqueta HTML (debe contener un guion).
 *  - html_path: (string) Ruta al archivo PHP en `frontend/Html/` que renderizará el HTML del componente.
 *  - css_path: (string) Ruta al archivo CSS en `frontend/css/` que contiene los estilos.
 *  - observed_attributes: (array) Lista de atributos HTML que el componente observará.
 */

// --- 1. Define variables con valores por defecto usando el operador ?? ---
$nombreClase = $datos['nombre_clase'] ?? 'MiComponente';
$nombreEtiqueta = $datos['nombre_etiqueta'] ?? 'mi-componente';
$htmlPath = $datos['html_path'] ?? 'componentes/componente-vacio';
$cssPath = $datos['css_path'] ?? 'componentes/componente-vacio';
$observedAttributes = $datos['observed_attributes'] ?? [];

?>

class <?= $nombreClase ?> extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });

        // --- 2. Carga el HTML y CSS desde archivos separados usando Flow ---
        const wrapper = document.createElement('div');
        // Flow::html renderizará el PHP y lo inyectará como un string aquí.
        // Se pasan los datos para que el HTML también sea dinámico.
        wrapper.innerHTML = `<?php Flow::html($htmlPath, $datos); ?>`;

        const style = document.createElement('style');
        // Asumimos una función Flow::css análoga a Flow::html
        style.textContent = `<?php Flow::css($cssPath); ?>`;

        this.shadowRoot.appendChild(style);
        this.shadowRoot.appendChild(wrapper);
    }

    // --- 3. Define los atributos a observar para manejar propiedades ---
    static get observedAttributes() {
        return <?= json_encode($observedAttributes) ?>;
    }

    // --- 4. Reacciona a los cambios en los atributos observados ---
    attributeChangedCallback(name, oldValue, newValue) {
        // Ejemplo: Busca un elemento dentro del Shadow DOM con un `data-bind`
        // que coincida con el nombre del atributo y actualiza su contenido.
        const element = this.shadowRoot.querySelector(`[data-bind="${name}"]`);
        if (element) {
            element.textContent = newValue;
        }
    }

    connectedCallback() {
        // Se ejecuta cuando el componente se añade al DOM.
        // Aquí puedes inicializar listeners de eventos.
    }
}

// --- 5. Registra el componente web para que pueda ser usado en el HTML ---
customElements.define("<?= $nombreEtiqueta ?>", <?= $nombreClase ?>);
