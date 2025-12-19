const apiKey = "AIzaSyDykleGESQ0IG5Lz6QHXuXN4Olzi5sXRvE";
  let memoria = [];
    const chatbox = document.getElementById('chat-container');
  const userInput = document.getElementById('chatInput');
  const sendButton = document.getElementById('sendChatBtn');
  const chatMessages = document.getElementById('chat-messages');
  const contexto = '';

  

  sendButton.addEventListener('click', () => {
    const message = userInput.value;
    if (message.trim() !== '') {
      // Add user message to chat

      memoria.push(`user: ${message}`);
      addChatMessage('user', message);

      userInput.value = '';
      //alert(JSON.stringify(memoria))
      // Placeholder for chatbot response - REPLACE THIS!
      fetch('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' + apiKey, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          "contents": [{
            "parts": [{
              "text": contexto + JSON.stringify(memoria)
            }]
          }]
        })
      })
      .then(response => response.json())
      .then(data => {
        //    console.log(data)
        const botResponse = data.candidates[0].content.parts[0].text; // candidates Extract the bot's response
        memoria.push(`bot: ${botResponse}`);
        addChatMessage('bot', botResponse);

      })
      .catch(error => {
        addChatMessage("Error:", "An error occurred. Please try again later.");
        console.error("Error:", error);
      });





    }
  });

  function addChatMessage(sender,
    message) {
    const messageElement = document.createElement('div');
    messageElement.classList.add("message-"+sender);
    message = reemplazarSimboloConEtiquetas(message,
      '*',
      '<small>',
      '</small>');


    messageElement.innerHTML = `${sender}: ${message}`;
    chatMessages.appendChild(messageElement);
    // alert(chatMessages.innerHTML)
    chatMessages.scrollTop = chatMessages.scrollHeight; // Auto-scroll to bottom
  }

  //let message = "Este es un mensaje con *asteriscos*.";





  function reemplazarSimboloConEtiquetas(texto,
    simbolo,
    etiquetaApertura,
    etiquetaCierre) {
    let contador = 0;
    let nuevoTexto = "";
    let indice = texto.indexOf(simbolo);


    while (indice !== -1) {
      contador++;
      if (contador % 2 !== 0) {
        // Si es impar (primera, tercera, etc. ocurrencia)
        nuevoTexto += texto.substring(0, indice) + etiquetaApertura;

      } else {
        // Si es par (segunda, cuarta, etc. ocurrencia)
        nuevoTexto += texto.substring(0, indice) + etiquetaCierre;

      }
      texto = texto.substring(indice + simbolo.length);
      indice = texto.indexOf(simbolo);
    }
    nuevoTexto += texto; // Agregar el resto del texto después de la última ocurrencia del símbolo

    return nuevoTexto;
  }


  

  chatButton.addEventListener('drag',e => {
    butonContenedor.style.top = e.clientY+'px';
    butonContenedor.style.left = e.clientX + 'px';
  });
