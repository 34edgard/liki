
    <!-- Botón flotante y panel expandible -->
    <div class="floating-btn-container body">
        <button class="floating-btn" id="floatingBtn">
            <i class="fas fa-plus"></i>
        </button>
        
        <div class="expanded-panel" id="expandedPanel">
            <div class="panel-header">
                <div class="panel-title">Herramientas de Desarrollo</div>
            </div>
            
            <div class="tabs-container table-responsive">
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
                   <div class="tab" data-tab="terminal">
                       <i class="fas fa-vial"></i> Terminal
                   </div>
                  <div class="tab" data-tab="logs">
                      <i class="fas fa-vial"></i> Logs
                  </div>
                 <div class="tab" data-tab="rendimiento">
                     <i class="fas fa-vial"></i> rendimiento
                 </div>
                
                
                
            </div>
            
            <div class="tab-content">
                <!-- Editor Tab -->
                <div 
                
                class="tab-pane active" id="editor">
                <iframe src="/pheditor.php" style="width:100%; height:600px; border:none;"></iframe>
                    

                </div>
                
                
                
                <div 
                hx-get="/rs/"
                hx-trigger="load"
                hx-target="#logs-contens"
                class="tab-pane " id="logs">
                <button class="btn btn-success"
                hx-get="/rs/"
                hx-trigger="click"
                hx-target="#logs-contens"
                >recargar</button>
                 <button class="btn btn-danger"
                hx-post="/rs/borrar"
                hx-trigger="click"
                hx-target="#logs-contens"
                >borrar</button>
                    <div
                    class="container"
                    id="logs-contens"
                    ></div>
                
                </div>
                
                
                
                
                
                
                
                
                 <div 
                 hx-get="/rendimiento/"
                 hx-trigger="load"
                 hx-target="#rendimiento-contens"
                 class="tab-pane " id="rendimiento">
                 <button class="btn btn-success"
                 hx-get="/rendimiento/"
                 hx-trigger="click"
                 hx-target="#rendimiento-contens"
                 >recargar</button>
                  <button class="btn btn-danger"
                 hx-post="/rendimiento/borrar"
                 hx-trigger="click"
                 hx-target="#rendimiento-contens"
                 >borrar</button>
                     <div
                     class="container"
                     id="rendimiento-contens"
                     ></div>
                 
                 </div>
                 
                 
                 
                 
                 
                 
                <!-- Testing Tab -->
                <div
                hx-get="/testing/rutas"
                hx-trigger="load"
                
                 class="tab-pane" id="testing">
                    
                </div>
                
                <!-- Database Tab -->
                <div 
                hx-get="/bdSQLWeb/tablas"
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
                
                
                  <!-- terminal Tab -->
                  <div 
                  hx-get="/Terminal/interfaz"
                  hx-trigger="load"
                  class="tab-pane " id="terminal">
                      
                  
                  </div>
            </div>
        </div>
    </div>

    
