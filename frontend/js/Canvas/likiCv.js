class LikiCv {
  constructor(id,d='2d') {
    this.canvas = document.getElementById(id);
if (!this.canvas) {
  throw new Error(`❌ Canvas con id "${id}" no encontrado`);
}
this.ctx = this.canvas.getContext(d);


this.styles = {};
this.imgs = {};
this.objects = {};

  }
  
  
  
  
  animate(fn, fps = 60) {
  if (this.loop) clearInterval(this.loop);
  this.loop = setInterval(() => {
    this.clearCanvas();
    try {
      fn(this);
    } catch (e) {
      console.error('Error en función de animación:', e);
    }
    //this.execColEvens();
  }, 1000 / fps);
  return this;
}

stopAnimation() {
  if (this.loop) {
    clearInterval(this.loop);
    this.loop = null;
  }
  return this;
}
  loadStyles(styles){
    this.styles = styles
  }
  loadImgs(){
    
  }
  
  object(id,object){
    this.objects[id] = object
  }
  setObjects(objects) {
  this.objects = objects
}
  getObjects(){
    return this.objects;
  }
  getObject(id) {
  return this.objects[id];
}
  getObjectState(id) {
  return this.objects[id].state;
}
  
  
  style(styles = ''){
 let style =  styles.split(' ');
 for (let prop of style) {
   // Tab to edit
   for (let id in this.styles[prop]) {
     // Tab to init
     this.ctx[id] =this.styles[prop][id]
     
   }
   
 }
    return this;
  }
  
  draw(id){
  let objects =  this.getObjects()
  let type = objects[id].type
  let state =objects[id].state
  
  this[type](
    state.position.x,
    state.position.y,
    state.size.w,
    state.size.h
  )
  
  console.log(type)
  }
  drawAll(){
    
  }
  clearCanvas(x = 0, y = 0) {
    this.ctx.clearRect(x, y, this.canvas.width, this.canvas.height);
    return this;
  }


  resizeCanvas() {
  if (!this.canvas) return;
  this.canvas.width = window.innerWidth * 0.8;
  this.canvas.height = window.innerHeight * 0.8;
}
  
  rect(x, y, w, h) {
    try {
   this.ctx.fillRect( x,y,w,h);
      return this;
    } catch (e) {
      console.error('Error en rect:', e);
      return this;
    }
  }

  circle(x, y, r) {
    try {
      this.ctx.beginPath();
      this.ctx.arc(x, y, r, 0, Math.PI * 2);
      this.ctx.fill();
      return this;
    } catch (e) {
      console.error('Error en circle:', e);
      return this;
    }
  }

  line(x1, y1, x2, y2) {
    try {
      this.ctx.beginPath();
      this.ctx.moveTo(x1, y1);
      this.ctx.lineTo(x2, y2);
      this.ctx.stroke();
      return this;
    } catch (e) {
      console.error('Error en line:', e);
      return this;
    }
  }

  text(text, x=0, y=0, size = 16, font = 'Arial') {
    
    try {
      this.ctx.font = `${size}px ${font}`;
      this.ctx.fillText(text, x, y);
      return this;
    } catch (e) {
      console.error('Error en text:', e);
      return this;
    }
  }

  triangle(x1, y1, x2, y2, x3, y3) {
    try {
      this.ctx.beginPath();
      this.ctx.moveTo(x1, y1);
      this.ctx.lineTo(x2, y2);
      this.ctx.lineTo(x3, y3);
      this.ctx.closePath();
      this.ctx.fill();
      return this;
    } catch (e) {
      console.error('Error en triangle:', e);
      return this;
    }
  }
  
  
  error(callback,messes){
    try {
  callback();
} catch (e) {
  console.error(`Error en ${messes}:`, e);
}
  }

  img(url,
  sx=0, sy=0, 
  sw=100, sh=100, 
  dx=50, dy=50, 
  dw=100, dh=100){
    
    
    this.error(()=>{
      
    const imge = new Image();
    imge.src = url;
    
  this.ctx.drawImage(imge, 
  sx, sy, 
  sw, sh, 
  dx, dy, 
  dw, dh);
},'imagen')

  
}

}