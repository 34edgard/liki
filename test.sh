#!/bin/bash
URL="http://0.0.0.0:8080/public/"
TOTAL=10000
CONC=1000

echo "🚀 Estresando a Liki en $URL..."
echo "Peticiones: $TOTAL | Concurrencia: $CONC"
echo "----------------------------------------------------------"

# Usamos xargs para la concurrencia y capturamos solo los números
results=$(seq $TOTAL | xargs -I {} -P $CONC curl -o /dev/null -s -w "%{time_total}\n" "$URL" | tr ',' '.')

# Procesamos los resultados con un poco de magia de awk (que es muy estable)
echo "$results" | awk '
  BEGIN { min=999; max=0; sum=0; count=0 }
  {
    if ($1 < min) min=$1;
    if ($1 > max) max=$1;
    sum += $1;
    count++;
  }
  END {
    printf "✅ Total peticiones: %d\n", count
    printf "⚡ Promedio:         %.4f seg\n", sum/count
    printf "🚀 Más rápida:       %.4f seg\n", min
    printf "🐢 Más lenta:        %.4f seg\n", max
  }
'
echo "----------------------------------------------------------"