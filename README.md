# Recetas Colaborativas

Aplicacion web en Laravel para que usuarios publiquen recetas, agreguen ingredientes, comenten variaciones y valoren recetas de otros miembros. El proyecto cumple con autenticacion Breeze, roles, CRUD, API REST, migraciones, seeders y vistas Blade.


## Usuarios De Prueba

Todos usan la contrasena `password`.

| Rol | Email | Acceso |
| --- | --- | --- |
| admin | admin@recetas.test | Administra categorias, recetas y comentarios |
| usuario | ana@recetas.test | Crea recetas, comenta y valora |
| usuario | luis@recetas.test | Crea recetas, comenta y valora |


## Reglas Del

- Una receta siempre pertenece a un usuario y una categoria.
- Una receta debe tener al menos un ingrediente.
- Cada usuario puede valorar una receta una sola vez; si vuelve a valorar, se actualiza su calificacion.
- Las categorias con recetas asociadas no se eliminan para conservar integridad.
- Usuarios normales solo modifican sus propias recetas y comentarios.
- Admin puede administrar categorias y moderar recetas/comentarios.

## Problemas Encontrados Y Solucion

- Las migraciones de `recipes` e `ingredients` se generaron con el mismo segundo de timestamp; Laravel intento crear ingredientes antes que recetas. Se renombro la migracion de ingredientes para ejecutar despues.
- Los tests de Breeze fallaban antes del build porque faltaba el manifest de Vite. Se ejecuto `npm run build`.
- El test base necesitaba base migrada porque `/` consulta recetas. Se agrego `RefreshDatabase`.

## Conclusiones

El proyecto integra un flujo completo de colaboracion: usuarios crean contenido, la comunidad aporta comentarios y valoraciones, y el administrador conserva control sobre categorias y moderacion. La API permite probar el CRUD desde Postman con respuestas JSON y autenticacion por token.
