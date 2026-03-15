<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pequeño Postman · Probador de rutas concurrente</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        body {
            background: #f1f5f9;
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 16px;
        }
        .card {
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 35px -8px rgba(0,0,0,0.2);
            padding: 28px 32px;
            transition: all 0.2s;
        }
        h1 {
            font-size: 2rem;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        h1 small {
            font-size: 0.9rem;
            font-weight: 400;
            color: #475569;
            background: #e9eef3;
            padding: 4px 12px;
            border-radius: 40px;
        }
        .section {
            background: #f8fafc;
            border-radius: 20px;
            padding: 22px 24px;
            margin-bottom: 28px;
            border: 1px solid #dbe0e8;
        }
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 18px;
            align-items: flex-end;
        }
        .form-group {
            flex: 1 1 200px;
            min-width: 0;
        }
        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #334155;
            margin-bottom: 6px;
        }
        input, select, textarea, button {
            border-radius: 14px;
            border: 1px solid #cbd5e1;
            padding: 12px 16px;
            font-size: 0.95rem;
            background: white;
            width: 100%;
            transition: 0.15s;
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.2);
        }
        textarea {
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            resize: vertical;
            min-height: 120px;
        }
        .button-group {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: center;
            margin-top: 16px;
        }
        .btn {
            background: white;
            border: 1px solid #94a3b8;
            font-weight: 500;
            cursor: pointer;
            flex: 0 1 auto;
            width: auto;
            padding: 12px 28px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .btn-primary {
            background: #1e3a8a;
            border-color: #1e3a8a;
            color: white;
            box-shadow: 0 8px 16px -6px #1e3a8a80;
        }
        .btn-primary:hover {
            background: #2563eb;
            border-color: #2563eb;
        }
        .btn-secondary {
            background: #334155;
            border-color: #1e293b;
            color: white;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
        }
        .btn-outline:hover {
            background: #f1f5f9;
        }
        .inline-number {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .inline-number input {
            width: 100px;
            text-align: center;
        }
        .response-area {
            background: #0b1120;
            color: #e2e8f0;
            border-radius: 20px;
            padding: 20px;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
            font-size: 0.9rem;
            border: 1px solid #334155;
            overflow: auto;
            max-height: 350px;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .badge {
            background: #334155;
            color: #f1f5f9;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-top: 16px;
            margin-bottom: 8px;
        }
        .warning-note {
            background: #fffbeb;
            border-left: 5px solid #f59e0b;
            padding: 12px 18px;
            border-radius: 16px;
            font-size: 0.9rem;
            color: #92400e;
            margin-bottom: 20px;
        }
        hr {
            border: none;
            border-top: 2px dashed #cbd5e1;
            margin: 24px 0;
        }
    </style>
</head>
<body>
<div class="card">
    <h1>
        🧪 Pequeño Postman
        <small>prueba rutas & concurrencia</small>
    </h1>

    <div class="warning-note">
        ⚠️ Asegúrate de que el servidor destino permita CORS o esté en el mismo origen. 
        Los datos en el textarea se envían como raw (application/json) para POST/PUT/PATCH.
    </div>

    <!-- SECCIÓN PRINCIPAL: RUTA, MÉTODO Y DATOS -->
    <div class="section">
        <div class="form-row">
            <div class="form-group" style="flex: 3;">
                <label>📍 Ruta completa (URL)</label>
                <input type="url" id="urlInput" placeholder="https://httpbin.org/post" value="https://httpbin.org/post">
            </div>
            <div class="form-group" style="flex: 1;">
                <label>⚡ Método HTTP</label>
                <select id="methodSelect">
                    <option value="GET">GET</option>
                    <option value="POST" selected>POST</option>
                    <option value="PUT">PUT</option>
                    <option value="PATCH">PATCH</option>
                    <option value="DELETE">DELETE</option>
                    <option value="HEAD">HEAD</option>
                    <option value="OPTIONS">OPTIONS</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>📦 Datos a enviar (cuerpo / raw JSON)</label>
            <textarea id="dataTextarea" placeholder='{ "ejemplo": "valor" }'>{ "nombre": "prueba", "activo": true }</textarea>
        </div>

        <div class="button-group">
            <button class="btn btn-primary" id="singleRequestBtn">▶ Enviar petición individual</button>
            <button class="btn btn-outline" id="clearOutputBtn">🗑️ Limpiar respuesta</button>
        </div>
    </div>

    <!-- SECCIÓN CARGA: CONCURRENCIA Y REPETICIONES -->
    <div class="section">
        <div style="display: flex; flex-wrap: wrap; gap: 28px; align-items: center; margin-bottom: 16px;">
            <div class="inline-number">
                <span class="badge">🔁 Peticiones totales</span>
                <input type="number" id="totalRequests" min="1" max="500" value="5">
            </div>
            <div class="inline-number">
                <span class="badge">👥 Usuarios concurrentes</span>
                <input type="number" id="concurrencyInput" min="1" max="50" value="2">
            </div>
            <button class="btn btn-secondary" id="loadTestBtn">🚀 Iniciar prueba de carga</button>
        </div>
        <small style="color: #475569;">Las peticiones usarán la misma URL, método y datos definidos arriba.</small>
    </div>

    <!-- RESPUESTAS DEL SERVIDOR -->
    <div class="flex-between">
        <h3 style="margin: 0 0 8px 0;">📬 Respuesta del servidor</h3>
        <span id="responseMeta" style="font-size:0.85rem; color:#64748b;">(listo)</span>
    </div>
    <div id="responseOutput" class="response-area">👆 Las respuestas aparecerán aquí...</div>
</div>

<script>
    (function() {
        // Elementos del DOM
        const urlInput = document.getElementById('urlInput');
        const methodSelect = document.getElementById('methodSelect');
        const dataTextarea = document.getElementById('dataTextarea');
        const singleBtn = document.getElementById('singleRequestBtn');
        const clearBtn = document.getElementById('clearOutputBtn');
        const loadTestBtn = document.getElementById('loadTestBtn');
        const totalRequestsInput = document.getElementById('totalRequests');
        const concurrencyInput = document.getElementById('concurrencyInput');
        const responseOutput = document.getElementById('responseOutput');
        const responseMeta = document.getElementById('responseMeta');

        // ---------- FUNCIÓN PARA REALIZAR UNA PETICIÓN (devuelve texto formateado) ----------
        async function performRequest(url, method, bodyData) {
            const fetchOptions = {
                method: method,
                headers: {
                    'Accept': 'application/json, text/plain, */*'
                },
                credentials: 'omit', // no envía cookies por defecto
                mode: 'cors'          // explícito, fallará si no hay CORS pero lo manejamos
            };

            // Añadir cuerpo sólo para métodos que lo permitan (NO para GET/HEAD)
            const methodsWithBody = ['POST', 'PUT', 'PATCH', 'DELETE']; // DELETE a veces tiene cuerpo, lo permitimos
            if (methodsWithBody.includes(method) && bodyData.trim() !== '') {
                fetchOptions.headers['Content-Type'] = 'application/json';
                fetchOptions.body = bodyData;  // se envía tal cual (debe ser JSON válido)
            } else if (method === 'GET' || method === 'HEAD') {
                // Para GET/HEAD no se envía body; si hay datos ignoramos.
                // No hacemos nada con body.
            } else {
                // Otros métodos (OPTIONS, etc.) sin cuerpo por simplicidad
            }

            const start = performance.now();
            try {
                const response = await fetch(url, fetchOptions);
                const end = performance.now();
                const timeMs = (end - start).toFixed(2);

                // Leer el cuerpo de la respuesta (texto)
                let responseText;
                const contentType = response.headers.get('content-type') || '';
                if (contentType.includes('application/json')) {
                    try {
                        const json = await response.json();
                        responseText = JSON.stringify(json, null, 2);
                    } catch {
                        responseText = await response.text();
                    }
                } else {
                    responseText = await response.text();
                }

                // Cabeceras resumidas (opcional)
                const headersSummary = `Status: ${response.status} ${response.statusText} | Tiempo: ${timeMs}ms | Tamaño: ${responseText.length} chars`;

                return {
                    success: true,
                    status: response.status,
                    headers: headersSummary,
                    body: responseText,
                    url: url,
                    method: method,
                    time: timeMs
                };
            } catch (error) {
                const end = performance.now();
                return {
                    success: false,
                    error: error.message,
                    url: url,
                    method: method,
                    time: (end - start).toFixed(2)
                };
            }
        }

        // ---------- MOSTRAR RESPUESTA (individual o colectiva) ----------
        function displaySingleResponse(result, requestIndex = null) {
            let headerText = '';
            if (requestIndex !== null) {
                headerText = `━━━━━━━━  PETICIÓN #${requestIndex + 1}  ━━━━━━━━━\n`;
            }
            if (result.success) {
                responseOutput.innerText += 
                    (headerText ? headerText + '\n' : '') +
                    `✅ ${result.method} ${result.url}\n${result.headers}\n\n${result.body}\n\n`;
            } else {
                responseOutput.innerText += 
                    (headerText ? headerText + '\n' : '') +
                    `❌ ERROR — ${result.method} ${result.url}\n   ⚠️ ${result.error}\n   Tiempo: ${result.time}ms\n\n`;
            }
            // Auto-scroll al final
            responseOutput.scrollTop = responseOutput.scrollHeight;
        }

        // Limpiar output
        function clearOutput() {
            responseOutput.innerText = '👆 Las respuestas aparecerán aquí...';
            responseMeta.innerText = '(listo)';
        }

        // ---------- CONTROL DE CONCURRENCIA ----------
        async function runConcurrentRequests(taskList, concurrency) {
            const total = taskList.length;
            const results = new Array(total);
            let index = 0;
            let completed = 0;

            // Actualizar meta cada vez que termina una
            function updateMeta() {
                responseMeta.innerText = `⏳ ${completed}/${total} completadas`;
            }
            updateMeta();

            return new Promise((resolve) => {
                function launch() {
                    while (index < total && concurrency > 0) {
                        const currentIdx = index++;
                        const task = taskList[currentIdx];
                        concurrency--;

                        task().then(res => {
                            results[currentIdx] = res;
                            completed++;
                            concurrency++;
                            updateMeta();
                            displaySingleResponse(res, currentIdx); // mostrar en tiempo real
                            launch(); // lanzar siguiente si hay
                        }).catch(err => {
                            // Si ocurre error inesperado en la tarea (no debería porque ya capturamos en perform)
                            results[currentIdx] = { success: false, error: err.message };
                            completed++;
                            concurrency++;
                            updateMeta();
                            displaySingleResponse(results[currentIdx], currentIdx);
                            launch();
                        });

                        // Si aún no hemos llegado al límite y quedan tareas, seguimos lanzando (while)
                    }
                    // Si ya no quedan tareas y todas completadas, resolvemos
                    if (completed === total) {
                        responseMeta.innerText = `✅ Finalizadas ${total} peticiones`;
                        resolve(results);
                    }
                }
                launch();
            });
        }

        // ---------- CONFIGURAR PETICIÓN ÚNICA ----------
        singleBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = urlInput.value.trim();
            if (!url) {
                alert('Introduce una URL válida');
                return;
            }
            const method = methodSelect.value;
            const data = dataTextarea.value;

            // Si el output está vacío o es el placeholder, limpiar antes para la primera respuesta
            if (responseOutput.innerText === '👆 Las respuestas aparecerán aquí...') {
                responseOutput.innerText = '';
            }

            responseMeta.innerText = '⏳ Enviando petición...';
            const result = await performRequest(url, method, data);
            displaySingleResponse(result);
            responseMeta.innerText = `📨 Respuesta individual (${result.success ? result.status : 'error'})`;
        });

        // ---------- PRUEBA DE CARGA (concurrente) ----------
        loadTestBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const url = urlInput.value.trim();
            if (!url) {
                alert('Introduce una URL válida');
                return;
            }
            const method = methodSelect.value;
            const data = dataTextarea.value;
            const total = parseInt(totalRequestsInput.value, 10);
            const concurrency = parseInt(concurrencyInput.value, 10);

            if (total < 1 || concurrency < 1) {
                alert('Número de peticiones y concurrentes deben ser al menos 1');
                return;
            }

            // Preparar output: añadir separador
            if (responseOutput.innerText === '👆 Las respuestas aparecerán aquí...') {
                responseOutput.innerText = '';
            }
            responseOutput.innerText += `\n═══════ INICIO PRUEBA DE CARGA (${total} peticiones, ${concurrency} concurrentes) ═══════\n\n`;
            responseMeta.innerText = `⏳ 0/${total} preparando...`;

            // Crear array de tareas (funciones que devuelven promesa de performRequest)
            const tasks = [];
            for (let i = 0; i < total; i++) {
                tasks.push(() => performRequest(url, method, data));
            }

            try {
                await runConcurrentRequests(tasks, concurrency);
                responseOutput.innerText += `═══════ FIN DE LA PRUEBA ═══════\n\n`;
            } catch (err) {
                responseOutput.innerText += `\n❌ Error inesperado en la prueba: ${err.message}\n`;
                responseMeta.innerText = '⚠️ Error en prueba';
            }
        });

        // ---------- BOTÓN LIMPIAR ----------
        clearBtn.addEventListener('click', (e) => {
            e.preventDefault();
            clearOutput();
        });

        // Pequeña ayuda: Si el método es GET/HEAD, advertir que el textarea no se envía (pero no bloqueamos)
        methodSelect.addEventListener('change', () => {
            const method = methodSelect.value;
            if (method === 'GET' || method === 'HEAD') {
                responseMeta.innerText = 'ℹ️ GET/HEAD ignoran el cuerpo (datos no se envían)';
            } else {
                responseMeta.innerText = '';
            }
        });

        // Inicial
        clearOutput();
    })();
</script>
</body>
</html>