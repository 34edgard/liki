esta es una comparación entre liki y laravel 12 
liki es un framenword php minimalistas que pesa 1.13mb tambien hay que destacar que 
en las pruevas solo se cargaron la pagina de inicio de cada framenword liki cargo solo su pagina de login
y laravel su pagina de bienvenida 

🚀 Estresando laravel en http://localhost:8000...            Peticiones: 100 | Concurrencia: 10
----------------------------------------------------------
✅ Total peticiones: 100
⚡ Promedio:         3.1675 seg
🚀 Más rápida:       0.3814 seg
🐢 Más lenta:        3.6345 seg
----------------------------------------------------------
Esos son los resultados de laravel en el servidor de php -S




🚀 Estresando a Liki en http://localhost:8080...
Peticiones: 100 | Concurrencia: 10
----------------------------------------------------------
✅ Total peticiones: 100
⚡ Promedio:         0.0779 seg
🚀 Más rápida:       0.0359 seg
🐢 Más lenta:        0.1198 seg
----------------------------------------------------------
Esos son los resultados de liki en el servidor de php -S con la misma concurrencia que laravel 


🚀 Estresando a Laravel en http://localhost:8000...
Peticiones: 100 | Concurrencia: 100
----------------------------------------------------------
✅ Total peticiones: 100
⚡ Promedio:         19.7671 seg
🚀 Más rápida:       1.6183 seg
🐢 Más lenta:        38.2101 seg
----------------------------------------------------------
Esos son los resultados de laravel en el servidor de php -S con 100c
❯ sh test
🚀 Estresando a Liki en http://localhost:8080...
Peticiones: 100 | Concurrencia: 100
----------------------------------------------------------
✅ Total peticiones: 100
⚡ Promedio:         0.5469 seg
🚀 Más rápida:       0.0872 seg
🐢 Más lenta:        0.6421 seg
----------------------------------------------------------
Estos son los resultados de liki en el servidor de php -S con 100c


🚀 Estresando a Laravel en http://localhost:8000...
Peticiones: 1000 | Concurrencia: 1000
----------------------------------------------------------
✅ Total peticiones: 1000
⚡ Promedio:         116.1347 seg
🚀 Más rápida:       6.0537 seg
🐢 Más lenta:        147.5302 seg
----------------------------------------------------------
Esos son los resultados de laravel con mil de concurrencia 


🚀 Estresando a Liki en http://localhost:8080...
Peticiones: 1000 | Concurrencia: 1000
----------------------------------------------------------
✅ Total peticiones: 1000
⚡ Promedio:         6.9862 seg
🚀 Más rápida:       0.1075 seg
🐢 Más lenta:        19.6675 seg
----------------------------------------------------------
Esos son los resultados de liki