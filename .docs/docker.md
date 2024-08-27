# Sail Use

- Para que se armen/creen los contenedores se puede utilizar el comando `sail build` o directamente `sail up`. 
- Para iniciar los contenedores y que queden en un terminal trabajando se utiliza `sail up`, si se quiere que trabaje en segundo plano basta con agregarle -d (detach) al comando para que quede asi `sail up -d`.
- Para detenerlo, si se usa el primer comando basta con presionar Control + c, si se dejó en segundo plano se puede utilizar el comando `sail stop`.
- Si se quiere hacer un rebuild completo (quizas no reconoce cambios en env o se necesitan agregar nuevas extensiones a php u otr motivo), se puede ejecutar el siguiente codigo.
```
docker compose down -v

sail build --no-cache

sail up
```
- Para correr cualquier comando artisan basta con usar `sail artisan` por ejemplo `sail artisan queue:work`
- Igualmente para correr php directamente dentro del contenedor seria de la siguiente forma `sail php`.
- Lo mismo para composer `sail composer require laravel/sanctum`.
- Npm `sail npm run dev`.
- Correr test `sail test`
- Cli/terminal/shell `sail shell` y para abrir con permisos elevados `sail root-shell`

## Notas
- Recordar que todos los comandos deben ser realizados en el root del proyecto.
- Se asume que se esta trabajando con un alias para sail `sail = ./vendor/bin/sail` si no se puede o se sabe como configurarlo o sencillamente da error el escribir sail solo, entonces se puede usar `./vendor/bin/sail` en lugar de `sail` en todos los comandos, como ejemplo `sail up` quedaría como `./vendor/bin/sail up`.
