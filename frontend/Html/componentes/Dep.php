
    <!-- Botón flotante y panel expandible -->
    <div class="floating-btn-container body">
        <button class="floating-btn" id="floatingBtn">
            <i class="fas fa-plus"></i>
        </button>
        
        <div class="expanded-panel" id="expandedPanel">
            <div class="panel-header">
                <div class="panel-title nav-link active" 
                
                >Herramientas de Desarrollo</div>
            </div>
            
            <div class="nav nav-tabs ">
                <button type="button" class="nav-link active"
                data-bs-toggle="tab" 
                data-bs-target="#editor" 
                role="tab"
                 data-tab="editor">
                    <i class="fas fa-code"></i> Editor
                </button>
                <button type="button" class="nav-link"
                data-bs-toggle="tab" 
                data-bs-target="#testing" 
                role="tab"
                 data-tab="testing">
                    <i class="fas fa-vial"></i> Testing
                </button>
                <button type="button" class="nav-link"
                data-bs-toggle="tab" 
                data-bs-target="#database" 
                role="tab"
                 data-tab="database">
                    <i class="fas fa-database"></i> Bases de Datos
                </button>
                  <button type="button" class="nav-link"
                data-bs-toggle="tab" 
                data-bs-target="#pages" 
                role="tab"
                 data-tab="pages">
                      <i class="fas fa-database"></i> constructor de interfas
                  </button>
                <button type="button" class="nav-link"
                data-bs-toggle="tab" 
                data-bs-target="#chat" 
                role="tab"
                 data-tab="chat">
                    <i class="fas fa-robot"></i> Chat con IA
                </button>
                   <button type="button" class="nav-link" 
                data-bs-toggle="tab" 
                data-bs-target="#terminal" 
                role="tab"
                
                data-tab="terminal">
                       <i class="fas fa-vial"></i> Terminal
                   </button>
                  <button type="button" class="nav-link" 
                data-bs-toggle="tab" 
                data-bs-target="#logs" 
                role="tab"
                data-tab="logs">
                      <i class="fas fa-vial"></i> Logs
                  </button>
                 <button type="button" class="nav-link" 
                data-bs-toggle="tab" 
                data-bs-target="#rendimiento" 
                role="tab"
                data-tab="rendimiento">
                     <i class="fas fa-vial"></i> rendimiento
                 </button>
                
                
                
            </div>
            
            <div class="tab-content">
                <!-- Editor Tab -->
                <div 
                
                class="tab-pane active" id="editor" role="tabpanel">
                <iframe src="/pheditor.php" style="width:100%; height:600px; border:none;"></iframe>
                    

                </div>
                
                
                
                <div 
                hx-get="/rs/"
                hx-trigger="load"
                hx-target="#logs-contens"
                class="tab-pane " id="logs" role="tabpanel">
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
                 class="tab-pane " id="rendimiento" role="tabpanel">
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
                role="tabpanel"
                 class="tab-pane" id="testing">
                    
                </div>
                
                <!-- Database Tab -->
                <div 
                hx-get="/bdSQLWeb/tablas"
                hx-trigger="load"
                class="tab-pane" id="database" role="tabpanel">
                    
                </div>
                 <!-- paginas disponibles -->
                 <div 
                 hx-get="/pages"
                 hx-trigger="load"
                 class="tab-pane" id="pages" role="tabpanel">
                     
                 </div>
                
                <!-- Chat Tab -->
                <div class="tab-pane" id="chat" role="tabpanel">
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
                  class="tab-pane " id="terminal" role="tabpanel">
                      
                  
                  </div>
            </div>
        </div>
    </div>

    
