
    <!-- Botón flotante y panel expandible -->
    <div class="floating-btn-container body">
        <button class="floating-btn" id="floatingBtn">
            <i class="fas fa-plus"></i>
        </button>
        
        <div class="expanded-panel" id="expandedPanel">
            <div class="panel-header">
                <div class="panel-title">Herramientas de Desarrollo</div>
            </div>
            
            <div class="tabs-container">
                <div class="tab active" data-tab="editor">
                    <i class="fas fa-code"></i> Editor
                </div>
                <div class="tab" data-tab="testing">
                    <i class="fas fa-vial"></i> Testing
                </div>
                <div class="tab" data-tab="database">
                    <i class="fas fa-database"></i> Bases de Datos
                </div>
                <div class="tab" data-tab="chat">
                    <i class="fas fa-robot"></i> Chat con IA
                </div>
            </div>
            
            <div class="tab-content">
                <!-- Editor Tab -->
                <div 
                hx-get="/pheditor.php"
                hx-trigger="load"
                class="tab-pane active" id="editor">
                    

                </div>
                
                <!-- Testing Tab -->
                <div
                hx-get="/testing/rutas"
                hx-trigger="load"
                
                 class="tab-pane" id="testing">
                    
                </div>
                
                <!-- Database Tab -->
                <div 
                hx-get="/bdSQLWeb"
                hx-trigger="load"
                class="tab-pane" id="database">
                    
                </div>
                
                <!-- Chat Tab -->
                <div class="tab-pane" id="chat">
                    <h3>Chat con IA</h3>
                    <p>Pregunta lo que quieras sobre programación, debugging o mejores prácticas.</p>
                    
                    <div class="chat-container" id="chat-container">
                        <div class="chat-messages" id="chat-messages">
                          ....
                        </div>
                        
                        <div class="chat-input" id="">
                            <input type="text" id="chatInput" placeholder="Escribe tu mensaje aquí...">
                            <button id="sendChatBtn">
                                <i class="fas fa-paper-plane"></i> Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
