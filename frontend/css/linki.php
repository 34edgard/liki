.linki-container {
    position: fixed;
    bottom: 180px;
    right: 20px;
    z-index: 9999;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    font-family: sans-serif;
}

.linki-avatar img {
    width: 60px;
    border-radius: 1rem;
    filter: drop-shadow(0 0 10px rgba(255, 0, 255, 0.5));
    cursor: pointer;
    /* Animación de flotado */
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-15px) rotate(5deg); }
}

.linki-bubble {
    background: #2d0050;
    color: #fff;
    padding: 10px 15px;
    border-radius: 15px 15px 0 15px;
    margin-bottom: 10px;
    font-size: 13px;
    border: 1px solid #ff00ff;
    box-shadow: 0 0 15px rgba(255, 0, 255, 0.3);
    max-width: 200px;
    opacity: 0;
    transition: opacity 0.5s;
}

.linki-container:hover .linki-bubble {
    opacity: 1;
}