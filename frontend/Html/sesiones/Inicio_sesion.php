    <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="card p-4 p-md-5 shadow-lg custom-card">
                        <div class="card-body text-center">
                            <h2 class="mb-4 custom-heading">Bienvenidos a la APP Uruguay</h2>
                            <p class="mb-4 text-muted">Ingrese sus credenciales para continuar</p>
    
                            <form
                            hx-post="/iniciar/sesion"
                            hx-trigger="submit"
                            hx-target="#caja"
                            >
                                <div class="mb-3 text-start">
                                    <label for="emailInput" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control custom-input" id="emailInput" placeholder="Ingrese el Correo Electrónico" name="correo" required>
                                    <div class="invalid-feedback">
                                        Rellene este campo.
                                    </div>
                                </div>
                                <div class="mb-3 text-start">
                                    <label for="passwordInput" class="form-label" >Contraseña</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control custom-input" id="passwordInput" name="contraseña" placeholder="Ingrese la Contraseña" minlength="8" required>
                                        <button class="btn btn-outline-secondary custom-eye-btn" type="button" id="togglePassword">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13.134 13.134 0 1 1 13.653 0A13.134 13.134 0 0 1 1.173 8"/>
                                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
    
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                            Recordarme
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none custom-link">¿Olvidó su contraseña?</a>
                                </div>
    
                                <button type="submit" name="Inicio_secion" class="btn btn-success w-100 py-2 custom-btn">Ingresar</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      <div id="caja"></div>

