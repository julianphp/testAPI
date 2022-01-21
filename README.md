<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


# <p style="center">Servicio API Diagnostico Pacientes</p> 

Este mini-proyecto consiste en la creación de una API sobre Laravel 8 y PHP 7.4/8.1, sobre las que realiza operaciones 
sobre pacientes y diagnósticos, en el cual, para realizar las operaciones, será necesario contar con un Token de
autorización.

## Indice
- [Despliegue](#Despliegue)
- [Headers](#Parametros-necesarios-en-Headers)
- [Uso de la API](#Uso-de-la-API)
- [Returns](#Returns)
- [Test](#Test)
- [Base de Datos](#Base-de-Datos)

<br>
Se ha optado por realizarlo sobre Laravel usando de referencia un modelo "MVC", dada la facilidad para el despliegue de un servidor 
funciona, tanto web como API como en este caso. Además de integrar un control de "routing" para una fácil gestión de las rutas, herramientas de 
test unitarios, "eloquent ORM" para las consultas a la Bade de Datos, usos de "middleware" para aplicar diferentes reglas, entre otras. 

## Despliegue
Para el despliegue se va a realizar sobre Ubuntu 20.04 y Apache2. Se hace uso de PHP 8.1, Composer 2.x y MariaDB 10.X con PHPMyAdmin.
Se han ejecutado las llamadas con **Postman**.
<br>
Aunque siempre podremos realizar un despliegue fácil y rápido con **php artisan serve**, realiza un despliegue local para pre-producción el cual
nos será suficiente para demostrar el servicio.
<br>
Para instalar composer, visite: [Tutorial](https://www.digitalocean.com/community/tutorials/how-to-install-and-use-composer-on-ubuntu-20-04-es)
<br>
Para instalar MariaDB, visite: [Tutorial](https://computingforgeeks.com/how-to-install-mariadb-on-ubuntu-focal-fossa/)
<br>
Empezamos clonado este mismo repositorio en donde deseemos. Deberemos de tener instalado previamente la herramienta "git".
Abrimos nuestra terminal.
<br>
<code> git clone https://github.com/julianphp/testAPI </code>
<br>
Entramos dentro de la carpeta "testAPI", será realizar la instalación de las dependencias y librerías con:
<br>
<code> composer install </code>
<br>
Configuramos el archivo ".env", para ello renombramos el archivo ".env.example" a ".env" para usarlo de base. En este archivo será 
necesario, configurar el acceso a la BD, para en los parámetros siguientes ajustamos según la instalación.
> DB_HOST= IP de donde se este ejecutando MariaDB, si es la misma maquina: 127.0.0.1
> 
> DB_PORT= Puerto del MariaDb, por defecto 3036
> 
> DB_DATABASE= Nombre de la base de datos
> 
> DB_USER= usuario para acceder a MariaDB
> 
> DB_PASS= contraseña para acceder a MariaDB, dejar en blanco si no requiere.


Generamos las keys necesarias para Laravel y Oauth2, situados en nuestro proyecto.
<br>
<code> php artisan key:generate </code>
<br>
<code> php artisan passport:install --force </code>
<br>

Para finalizar, ejecutamos el servidor con

<code> php artisan serve </code>

Podemos comprobar que funciona si entramos en la url que nos muestra y ver el inicio.
> **Recuerda!** Recuerda que nuestra base para ejecutar las peticiones será tal que http://127.0.0.1:8000/api/

## Uso de la API
<p>El desarrollo de la API, se ha realizado mediante la librería de laravel/passport, que nos proporciona un amplio
soporte para el uso de <b>OAuth2</b>.</p>, además de una posibilidad a futuro de mayor modificación y control sobre las autorizaciones.
<p>El servicio cuenta con distintas operaciones a realizar, principalmente sobre los pacientes y sus diagnósticos.</p>
<p>Para la realización del proyecto se ha usado <b>Postman</b>.
<p>Primero será necesario contar con un usuario registrado en la plataforma.</p>
<p>Luego será necesario hacer login que nos devolverá nuestro <b>Bearer Token</b>, en cuál será necesario enviar mediante el <b>"header"</b>>
con la "key" de <b>Authorization</b> y el "value" con <b>"Bearer ~NuestroToken~"</b> para autenticarse en cada operación. </p>
<p>Adicionalmente, en cada petición, podemos indicar el lenguaje en el cual mostrar los distintos mensajes, con la key <b>lang</b>
y el idioma preferido.</p>


## Parametros necesarios en Headers
- ['Accept' => 'application/json'] - Para la correcta transmisión de datos.
- ['Authorization' => 'Bearer ~Nuestro Token~ '] - Necesario para identificarse ante el servidor. Se obtiene al hacer login.
- ['lang' => 'es'|'en'] - Idioma en el que mostrar los mensajes.
## Petición API disponibles
- [ URL, TYPE POST|GET, (PARAMS), return, Details]
- [**"register"**, **POST**, ('email => string','password => string|min:4'), Permite el registro en la aplicación.]
- [**"login"**, **POST**, ('email','password'), Return => Bearer Token]
- **Requiere Bearer Token** // localhost/api/patient/delete
  - [**"logout"**, **GET**, ('Bearer Token'), Cierra la session y revoca el token.]
  - **"patient"** GROUP ROUTES
    - [**"new"**, **POST**, ('fullname => string|max:255'','personalidentification' => string|DNI|NIE), Return => Los datos del paciente, Da de alta un nuevo paciente]
    - [**"edit"**, **POST**, ('fullname','personalidentification'), Return => Los datos del paciente editados. Edita el Nombre del paciente.]
    - [**"details"**, **POST**, ('personalidentification'), Return => Los datos del paciente solicitado. Consulta los detalles de un paciente dado.]
    - [**"delete"**, **POST**, ('personalidentification', 'force' => 0|1), Borra un paciente, en el caso de que tenga diagnósticos asociados, se debera hacer uso de la opción force para borrar todo.]
    - [**"listAll"**, **GET**, (), Listado con todos los pacientes, nombre e identificación.]
  -**"diagnosis"**  GROUP ROUTES // example.app/api/diagnosis/patientListAll
    - [**"new"**, **POST**, ('diagnosis => string|max:2000'','personalidentification' => string|DNI|NIE), Return => Los datos del paciente y el diagnostico, Crea un nuevo diagnostico sobre un paciente dado.]
    - [**"patientListAll"**, **POST**, ('personalidentification', Return => Nombre y diagnósticos del paciente, Muestra los diagnósticos de un paciente.]

## Returns
<p>Cada vez que se devuelva una respuesta del servidor, siempre lo hará acompaño del parámetro <b>error</b>, con un <b>true|false</b> indicando
si la llamada se ha ejecutado correctamente a cabo, o por el contrario, estará acompañado de un mensaje de error o un array en <b>msg</b>.</p>
<p>

## Test
Se ha llevado a cabo la realización de test para el testeo de las funciones descritas anteriormente. Se pueden encontrar en
**"tests/Feature"** el archivo llamado **"PatientDiagnosisTest"**. 
<br> 
Podemos ejecutar los test, en nuestra carpeta del proyecto con **"php artisan test"**.
> **Aviso!** Es posible que durante la ejecución de los test, se requiera la escritura en **"storage/logs/laravel-xxxx-xx-xx.log"**
> e indique que no es posible escribir en él, para ello, será necesario elimar el archivo a mano, ya que difiere de los permisos
> de Apache2.

## Base de Datos 
Se ha realizado sobre MariaDB 10.6. <br>
Para las consultas en la parte de PHP, se van a llevar a cabo mediante el uso de "Eloquent ORM", con el uso de modelos y relaciones, 
ya que nos permite realizar las consultas más básica y cotidianas de manera más fácil y rápida, pero siempre con la oportunidad de poder realizar nuestras 
propias consultas en caso de ser necesario con el Facade "DB".
<br>
Se ha usado la codificación "utf8_unicode_ci". La DB se encuentra en **"/storage/db/db_service.sql"**, necesitará crear una nueva DB para poder importarla.
<p>
Para guardar el historial de ediciones/creaciones sobre las tablas patients y diagnosis, se ha optado en realizarlo sobre la propia Base de Datos, y 
llevando el registro manualmente en los controladores,
 el motivo es para facilitar la consulta de los datos a posteriori, además de guardar más información en el caso de ser necesario y el usuario que lo realizo.

Se han creado las siguientes tablas:

- **users** ('id' PK int(11) UNSIGNED AU, 'email' varchar(255),'password', varchar(256), 'created_at' timestamp,'updated_at' timestamp). 
  - Tabla que almacena los usuarios registrados. 
- **patients**('id' PK int(11) UNSIGNED AU,'fullName' varchar(255),'personalIdentification' varchar(9) index,'created_at' timestamp,'updated_at' timestamp).
  - Tabla que almacena los pacientes. Si un paciente tiene diagnósticos asociados, no se podrá borrar, no se ha realizado un "ON DELETE CASCADE", para controlarlo en el controlador.
- **patients_history_log** ('id' int(11) UNSIGNED AU, 'patId' int(11) UNSIGNED, 'editBy' int(11) UNSIGNED, 'oldFullName' varchar(255), 'oldPersonalIdentification' varchar(9),'created_at' timestamp, 'updated_at' timestamp)
  - Tabla que almacena él historia de ediciones sobre la tabla **patients**. No se han vinculado con FK sobre la tabla patients, por si en el futuro se quiere consultar el registro de cambios y quien lo realizo.
- **diagnosis** ('id' PK int(11) UNSIGNED AU, 'idPatient' FK patients(id),'description' varchar(2000), 'date' timestamp, 'created_at' timestamp, 'updated_at' timestamp )
  - Tabla que almacena los diagnósticos de los pacientes.
- **diagnosis_history_log** ('id' PK int(11) UNSIGNED AU, 'idReg' int(11) UNSIGNED, 'editBy' int(11) UNSIGNED, 'oldDescription' varchar(2000), 'oldDate' timestamp, 'created_at' timestamp, 'updated_at' timestamp)
  - Tabla que almacena el historial de ediciones sobre la tabla **diagnosis**. No se ha vinculado con FK sobre la tabla diagnosis, para que en caso de borrar la información, se guarde el registro de cambios en caso de que fuera necesario consultar a posterior y quien lo edito.
- **oauth_access_tokens**,**oauth_auth_codes**,**oauth_clients**,**oauth_personal_access_clients**,**oauth_refresh_tokens**
  - Tablas generadas automáticamente al usar el paquete de laravel/passport y necesarias para la autenticación y almacenamiento de los Tokens.

**Diseño de la Base de Datos**
<br>
![Image](https://imgur.com/si1c93f.png "Diagrama DB")

</p>

