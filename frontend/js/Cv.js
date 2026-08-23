class Cv {
  ctx;
  canvas;
  sty;
  imgs ={};
  status ={};
  
  
   resizeCanvas() {
  this.canvas.width = window.innerWidth * 0.8;
  this.canvas.height = window.innerHeight * 0.8;
}

initResizeCanvas(){
window.addEventListener('resize', ()=>this.resizeCanvas());

}
  
  
  
  
  setStatus(id,status){
    this.status[id]= status
  }
  getStatus(id){
    return this.status[id]
  }
  loadSty(sty){
   this.sty = sty
  }
  constructor(id,d = '2d') {
    this.canvas = document.getElementById(id);
   if (!this.canvas) {
  console.error('❌ Canvas no encontrado:', id);
  return;
}
  this.ctx = this.canvas.getContext(d);
  
  this.states = {
  styles: {}, // Estados de estilos
  objects: {}, // Estados de objetos dibujados
  images: {}, // Estados de imágenes
  custom: {} // Propiedades personalizadas
};

// Configuración de caché
this.cache = {
  enabled: true,
  maxSize: 100,
  hitCount: 0,
  missCount: 0
};
  }
  
  
  

    
    
    
    
    

    
  // ===== SISTEMA DE ESTADOS MEJORADO =====
  
  // Guardar estado de un estilo
  saveStyleState(name, props) {
    this.states.styles[name] = {
      props: { ...props },
      timestamp: Date.now(),
      usage: 0
    };
    return this;
  }
  
  // Guardar estado de un objeto dibujado
  saveObjectState(id, data) {
    this.states.objects[id] = {
      type: data.type || 'rect',
      styles: data.styles || {},
      position: data.position || { x: 0, y: 0 },
      size: data.size || { w: 0, h: 0 },
      properties: data.properties || {},
      timestamp: Date.now(),
      usage: 0
    };
    return this;
  }
  
  // Guardar estado de una imagen
  saveImageState(id, data) {
    this.states.images[id] = {
      src: data.src,
      position: data.position || { x: 0, y: 0 },
      size: data.size || { w: 100, h: 100 },
      crop: data.crop || null,
      properties: data.properties || {},
      timestamp: Date.now(),
      usage: 0
    };
    return this;
  }
  
  // Guardar propiedades personalizadas
  saveCustomState(id, data) {
    this.states.custom[id] = {
      data: { ...data },
      timestamp: Date.now()
    };
    return this;
  }
  
  // Obtener estado
  getState(type, id) {
    if (this.states[type] && this.states[type][id]) {
      this.states[type][id].usage++;
      this.cache.hitCount++;
      return this.states[type][id];
    }
    this.cache.missCount++;
    return null;
  }
  
  // Verificar si existe estado
  hasState(type, id) {
    return !!(this.states[type] && this.states[type][id]);
  }
  
  // Eliminar estado
  clearState(type, id) {
    if (this.states[type] && this.states[type][id]) {
      delete this.states[type][id];
    }
    return this;
  }
  
  // Limpiar todos los estados
  clearAllStates() {
    this.states = {
      styles: {},
      objects: {},
      images: {},
      custom: {}
    };
    return this;
  }
  
  // Estadísticas de caché
  getCacheStats() {
    const total = this.cache.hitCount + this.cache.missCount;
    return {
      hits: this.cache.hitCount,
      misses: this.cache.missCount,
      hitRate: total > 0 ? (this.cache.hitCount / total * 100).toFixed(2) + '%' : '0%',
      total: total,
      enabled: this.cache.enabled
    };
  }

    
    
    
  
  
  
  
  
  loadimgs(imgs){
    
    
    for (let prop in imgs) {
      let img = new Image();
      // Tab to edit
     //this.imgs[prop] = 
     
     img.src = imgs[prop]
     this.imgs[prop] = img
     //console.log(this.imgs)
    }
    
  }
  
  
  

  // ===== ESTILOS MEJORADOS CON ESTADO =====
  style(name) {
    // Verificar si existe en caché
    const cachedState = this.getState('styles', name);
    
    if (cachedState && this.cache.enabled) {
      // Usar estado cacheado
     // console.log(`🔄 Usando estado cacheado para "${name}"`);
      this._applyStyle(cachedState.props);
      return this;
    }
    
    // Si no está en caché, procesar normalmente
    const style = this.sty[name];
    if (!style) {
      console.warn(`⚠️ Estilo "${name}" no encontrado`);
      return this;
    }
    
    // Guardar en estado
    this.saveStyleState(name, style);
    
    // Aplicar
    this._applyStyle(style);
    
    return this;
  }
  
  _applyStyle(style) {
    for (let prop in style) {
      if (Array.isArray(style[prop])) {
        this.ctx[prop](...style[prop]);
      } else {
        this.ctx[prop] = style[prop];
      }
    }
  }
  
  // ===== IMÁGENES MEJORADAS CON ESTADO =====
  dImg(img, sx, sy, 
  sw = null, sh = null, 
  dx = null, dy = null,
  dw = null, dh = null) {
    
    // Crear ID único para esta imagen con sus parámetros
    const stateId = `${img}_${sx}_${sy}_${sw}_${sh}_${dx}_${dy}_${dw}_${dh}`;
    
    // Verificar caché
    const cachedState = this.getState('images', stateId);
    
    if (cachedState && this.cache.enabled) {
      //console.log(`🔄 Usando imagen cacheada: "${img}"`);
      // Dibujar desde caché (ya tenemos los datos)
      
  // Tab to edit
  this.ctx.drawImage(
  this.imgs[img],
  cachedState.crop.sx, cachedState.crop.sy,
  cachedState.crop.sw, cachedState.crop.sh,
  cachedState.position.x, cachedState.position.y,
  cachedState.size.w, cachedState.size.h
);


      
      return this;
    }
    
    // Si no está en caché, dibujar normalmente
    const image = this.imgs[img];
    if (!image) {
      console.warn(`⚠️ Imagen "${img}" no encontrada`);
      return this;
    }
    
    this.ctx.drawImage(image, sx, sy, sw, sh, dx, dy, dw, dh);
    
    // Guardar en caché
    this.saveImageState(stateId, {
      src: img,
      position: { x: dx, y: dy },
      size: { w: dw, h: dh },
      crop: { sx, sy, sw, sh },
      properties: { timestamp: Date.now() }
    });
    
    return this;
  }
  
  // ===== RECTÁNGULO CON ESTADO =====
  rect(x, y, w, h, id = null) {
    const stateId = id || `rect_${x}_${y}_${w}_${h}`;
    
    // Verificar caché
    const cachedState = this.getState('objects', stateId);
    
    if (cachedState && this.cache.enabled) {
      console.log(`🔄 Usando rect cacheado: "${stateId}"`);
      this.ctx.fillRect(
        cachedState.position.x,
        cachedState.position.y,
        cachedState.size.w,
        cachedState.size.h
      );
      return this;
    }
    
    // Dibujar
    this.ctx.fillRect(x, y, w, h);
    
    // Guardar en caché
    this.saveObjectState(stateId, {
      type: 'rect',
      position: { x, y },
      size: { w, h },
      properties: { timestamp: Date.now() }
    });
    
    return this;
  }

  // ... código existente ...
  
  // ===== SISTEMA DE MODIFICADORES =====
  modifiers = {
    // Modificadores numéricos
    add: (current, value) => current + value,
    subtract: (current, value) => current - value,
    multiply: (current, value) => current * value,
    divide: (current, value) => current / value,
    set: (current, value) => value,
    increment: (current) => current + 1,
    decrement: (current) => current - 1,
    
    // Modificadores de texto
    append: (current, value) => current + value,
    prepend: (current, value) => value + current,
    
    // Modificadores de arrays
    push: (current, value) => [...current, value],
    pop: (current) => current.slice(0, -1),
    remove: (current, value) => current.filter(v => v !== value),
    
    // Modificadores de objetos
    merge: (current, value) => ({ ...current, ...value }),
    toggle: (current) => !current,
    
    // Modificadores personalizados
    clamp: (current, min, max) => Math.max(min, Math.min(max, current)),
    round: (current) => Math.round(current),
    floor: (current) => Math.floor(current),
    ceil: (current) => Math.ceil(current),
    abs: (current) => Math.abs(current),
    sign: (current) => current > 0 ? 1 : current < 0 ? -1 : 0
  };
  
  // ===== APLICAR MODIFICADOR =====
  modify(stateId, property, modifier, ...args) {
    // Obtener estado actual
    const state = this.getState('objects', stateId);
    if (!state) {
      console.warn(`⚠️ Estado "${stateId}" no encontrado`);
      return this;
    }
    
    // Obtener valor actual
    const currentValue = this._getNestedValue(state, property);
    
    // Aplicar modificador
    const modifierFn = this.modifiers[modifier];
    if (!modifierFn) {
      console.warn(`⚠️ Modificador "${modifier}" no encontrado`);
      return this;
    }
    
    const newValue = modifierFn(currentValue, ...args);
    
    // Actualizar estado
    this._setNestedValue(state, property, newValue);
    
    // Registrar cambio
    this._logModification(stateId, property, modifier, currentValue, newValue);
    
    return this;
  }
  
  // ===== MÉTODOS DE UTILIDAD PARA NESTED PROPERTIES =====
  _getNestedValue(obj, path) {
    return path.split('.').reduce((current, key) => current?.[key], obj);
  }
  
  _setNestedValue(obj, path, value) {
    const keys = path.split('.');
    const lastKey = keys.pop();
    const target = keys.reduce((current, key) => current[key], obj);
    if (target) target[lastKey] = value;
    return this;
  }
  
  // ===== REGISTRO DE MODIFICACIONES =====
  modificationLog = [];
  loggingEnabled = true;
  
  _logModification(stateId, property, modifier, oldValue, newValue) {
    if (!this.loggingEnabled) return;
    
    this.modificationLog.push({
      timestamp: Date.now(),
      stateId,
      property,
      modifier,
      oldValue,
      newValue,
      stack: new Error().stack
    });
    
    // Emitir evento de cambio
    this.emit('stateChange', {
      stateId,
      property,
      modifier,
      oldValue,
      newValue
    });
  }
  
  circle(x,y,r){
    this.ctx.beginPath();
    this.ctx.arc(x, y, r, 0, Math.PI * 2);
    this.ctx.fill();
    
  }
  
  
  line(x,y,bx,by){
    this.ctx.beginPath();
this.ctx.moveTo(x, y);
this.ctx.lineTo(bx, by);
this.ctx.stroke();
  }
  
  
  
  // 1. Sistema de Animación
animate(fn, fps = 60) {
  this.loop = setInterval(() => {
    this.clearCanvas();
    fn(this);
  }, 1000 / fps);
  return this;
}
clearCanvas(x=0,y=0){
  this.ctx.clearRect(x, y, this.canvas.width, this.canvas.height);
    
}
// 2. Capas
layer(name, zIndex = 0) {
  this.layers[name] = { zIndex, elements: [] };
  return this;
}

// 3. Redibujo automático con modify()
modifyAndRedraw(stateId, property, modifier, ...args) {
  this.modify(stateId, property, modifier, ...args);
  this.redrawAll(); // Implementar
  return this;
}

// 4. Sistema de Eventos
on(event, callback) {
  
  this.canvas.addEventListener(event , callback);
  return this;
}


  text(text, x, y, size = 16,font ='Arial') {
        this.ctx.font = `${size}px ${font}`;
        this.ctx.fillText(text, x, y);
        return this;
    }


triangle(x1, y1, x2, y2, x3, y3) {
  this.ctx.beginPath();
  this.ctx.moveTo(x1, y1);
  this.ctx.lineTo(x2, y2);
  this.ctx.lineTo(x3, y3);
  this.ctx.closePath();
  this.ctx.fill();
  return this;
}

}