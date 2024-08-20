<img src="https://res.cloudinary.com/bytefury/image/upload/v1574149856/Crater/craterframe.png" alt="app image">
<html>
<body>

<div class="center">

<h1>Corebill CraterApp proyecto actualizado Laravel 11</h1>

<h2>Requisitos</h2>
<p>Asegúrate de cumplir con los siguientes requisitos antes de comenzar:</p>

<p>
  <img src="https://img.shields.io/badge/PHP-8.1%2B-blue?logo=php&logoColor=white" class="icon" alt="PHP Logo"> <strong>PHP 8.1 o superior</strong>
</p>
<p>
  <img src="https://img.shields.io/badge/Composer-2.0%2B-blue?logo=composer&logoColor=white" class="icon" alt="Composer Logo"> <strong>Composer</strong>
</p>
<p>
  <img src="https://img.shields.io/badge/Node.js-16%2B-brightgreen?logo=node.js&logoColor=white" class="icon" alt="Node.js Logo"> <strong>Node.js</strong>
</p>
<p>
  <img src="https://img.shields.io/badge/MySQL-5.7%2B-blue?logo=mysql&logoColor=white" class="icon" alt="MySQL Logo"> <strong>MySQL</strong>
</p>

<h2>Instalación</h2>
<p>Sigue estos pasos para configurar el proyecto:</p>

<ol>
  <li><strong>Instala las dependencias de Node.js</strong>
    <pre><code>npm install --legacy-peer-deps
# o
yarn install --legacy-peer-deps</code></pre>
  </li>
  <li><strong>Actualiza las dependencias de Composer</strong>
    <pre><code>composer update --with-all-dependencies --ignore-platform-req=ext-http</code></pre>
  </li>
  <li><strong>Configura el archivo <code>.env</code></strong>
    <p>Verifica que el archivo <code>.env</code> tenga las credenciales correctas y el puerto MySQL adecuado (por defecto <code>3308</code>, aunque usualmente es <code>3306</code>).</p>
  </li>
  <li><strong>Asigna todos los privilegios a la base de datos <code>crater</code> en MySQL</strong>
    <p>Asegúrate de que la base de datos <code>crater</code> tenga todos los privilegios necesarios.</p>
  </li>
  <li><strong>Migra y siembra la base de datos</strong>
    <pre><code>php artisan migrate:fresh --seed</code></pre>
  </li>
  <li><strong>Genera la clave de aplicación</strong>
    <pre><code>php artisan key:generate</code></pre>
  </li>
  <li><strong>Siembra la base de datos con datos de demo</strong>
    <pre><code>php artisan db:seed --class=DemoSeeder</code></pre>
  </li>
  <li><strong>Inicia el entorno de desarrollo</strong>
    <pre><code>yarn dev
# o
npm run dev</code></pre>
  </li>
  <li><strong>Inicia el proceso de vigilancia de archivos</strong>
    <pre><code>yarn watch</code></pre>
  </li>
  <li><strong>Inicia el servidor de desarrollo</strong>
    <pre><code>php artisan serve</code></pre>
  </li>
</ol>

<h2>Notas Adicionales</h2>
<p>- Asegúrate de tener todas las extensiones PHP necesarias instaladas.</p>
<p>- Si encuentras problemas, revisa los logs de Laravel y las configuraciones de PHP y MySQL.</p>

<p>¡Feliz codificación!</p>

</div>

</body>
</html>

