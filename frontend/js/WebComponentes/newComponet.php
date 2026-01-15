// Definimos una clase que extiende HTMLElement
class <?= $nombre ?> extends HTMLElement {
  constructor() {
    super();
    
    // Creamos un shadow DOM
    this.attachShadow({ mode: 'open' });

    // Añadimos contenido al shadow DOM
    const div = document.createElement('div');
    div.textContent = '¡Hola, soy un componente web!';

    // Añadimos estilos
    const estilo = document.createElement('style');
    estilo.textContent = `
      div {
        font-size: 20px;
        color: blue;
      }
    `;
div.innerHTML ='<button class="btn btn-primary">sex</button>'
    // Agregamos los estilos y el div al shadow DOM
    this.shadowRoot.appendChild(estilo);
    this.shadowRoot.appendChild(div);
  }
}
//alert('sexoo')
// Registramos el nuevo elemento personalizado
customElements.define("<?= $nombreComponente ?>", <?= $nombre ?>);