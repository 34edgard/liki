<?php

namespace Liki\Routing;

class ControlInterfaz{
public static function cambiarPagina(string $pagina)
{
  echo "<script>
  navigation.navigate('/$pagina');
  </script>";
}

public static function scriptAlert(string $mensage){
 echo "<script >alert('{$mensage}')</script>";
        
}

}