function cambiarTitulo(titulo) {
            const tituloElement = document.getElementById('titulo');
            
            const  parrafoElement = document.getElementById('parrafo');
            
            if (tituloElement) {
                tituloElement.textContent = titulo[0];
                parrafoElement.textContent = titulo[1];
            } else {
                console.error("Elemento con ID 'titulo' no encontrado.");
            }
        }
        
        
function visibleOn(id){
    document.getElementById(id).hidden=false;
}    

function visibleOff(id){
    document.getElementById(id).hidden=true;
}



function inserValue(inputExtractValue,inputInsertValue){
    let i = document.getElementById('parentesco').value;
  document.getElementById(inputInsertValue+i).value =  document.getElementById(inputExtractValue).value;
}




function añadirValorPorDefecto(id,valor){
    // Obtener el elemento select por su ID
    const selectElement = document.getElementById(id);
    
    // Establecer el valor que quieres que esté seleccionado
    selectElement.value = valor;
    
}

