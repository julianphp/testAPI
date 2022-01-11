<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>


# <p style="center">Servicio API Diagnostico Pacientes</p> 

Este mini-proyecto consiste en la creacion de una API sobre Laravel 8 y PHP 7.4/8.1, sobre las que realiza operaciones 
sobre pacientes y diagnosticos, en el cual, para realizar las operaciones, sera necesario contar con un Token de
autorizacion.

## Uso de la API
<p>El desarrollo de la API, se ha realizado mediente la libreria de laravel/passport, la cual nos proporciona un amplio
soporte para el uso de <b>OAuth2</b>.</p>
<p>El servicio cuenta con distintas operaciones a relizar, principalmente sobre los pacientes y sus diagnosticos.</p>
<p>Para la realizacion del proyecto se ha usado <b>Postman</b>.
<p>Primero sera necesario contar con un usuario registrado en la plataforma.</p>
<p>Luego sera necesario hacer login que nos devolvera nuestro <b>Bearer Token</b>, en cual sera necesario enviar mediante el <b>"header"</b>>
con la "key" de <b>Authorization</b> y el "value" con <b>"Bearer ~NuestroToken~"</b> para autenticarse en cada operacion. </p>
<p>Adicionalmente, en cada peticion, podemos indicar el lenguaje en el cual mostrar los distintos mensajes, con la key <b>lang</b>
y el idioma preferido.</p>
<p>En nuestro caso de uso, la URL para las peticiones parte a partir de <b>example.app/public/api</b>.</p>

## Parametros necesarios en Headers
- ['Accept' => 'application/json'] - Para la correcta transmision de datos.
- ['Authorization' => 'Bearer ~Nuestro Token~ '] - Necesario para identificarse ante el servidor. Se obtiene al hacer login.
- ['lang' => 'es'|'en'] - Idioma en el que mostrar los mensajes.
## Peticion API disponibles
- [ URL, TYPE POST|GET, (PARAMS), return, Details]
- [**"register"**, **POST**, ('email => string','password => string|min:4'), Permite el registro en la aplicacion.]
- [**"login"**, **POST**, ('email','password'), Return => Bearer Token]
- **Requiere Bearer Token** // localhost/public/api/patient/delete
  - [**"logout"**, **GET**, ('Bearer Token'), Cierra la session y revoca el token.]
  - **"patient"** GROUP ROUTES
    - [**"new"**, **POST**, ('fullname => string|max:255'','personalidentification' => string|DNI|NIE), Return => Los datos del paciente, Da de alta un nuevo paciente]
    - [**"edit"**, **POST**, ('fullname','personalidentification'), Return => Los datos del paciente editados. Edita el Nombre del paciente.]
    - [**"details"**, **POST**, ('personalidentification'), Return => Los datos del paciente solicitado. Consulta los detalles de un paciente dado.]
    - [**"delete"**, **POST**, ('personalidentification', 'force' => 0|1), Borra un paciente, en el caso de que tenga diagnosticos asociados, se debera hacer uso de la opcion force para borrar todo.]
    - [**"listAll"**, **GET**, (), Listado con todos los pacientes, nombre e identificacion.]
  -**"diagnosis"**  GROUP ROUTES // example.app/public/api/diagnosis/patientListAll
    - [**"new"**, **POST**, ('diagnosis => string|max:2000'','personalidentification' => string|DNI|NIE), Return => Los datos del paciente y el diagnostico, Crea un nuevo diagnostico sobre un paciente dado.]
    - [**"patientListAll"**, **POST**, ('personalidentification', Return => Nombre y diagnosticos del paciente, Muestra los diagnosticos de un paciente.]

## Returns
<p>Cada vez que se devuelva una respuesta del servidor, siempre lo hara acompaño del parametro <b>error</b>, con un <b>true|false</b> indicando
si la llamada se ha llevado correctamente a cabo, o por el contrario, estara acompañado de un mensaje de error en <b>msg</b>.</p>
<p>
Tambien es posible que devuelva un mensaje de error distinto al llevar a cabo la validacion de los parametros, indicando cual es erroneo.
</p>

## Test
Se ha llevado a cabo la realizacion de test para el testeo de las funciones descritas anteriormente. Se pueden ejecutar en
**"tests/Feature"** el archivo llamado **"PatientDiagnosisTest"**.
> **Aviso!** Es posible que durante la ejecucion de los test, se requiera la escritura en **"storage/logs/laravel-xxxx-xx-xx.log"**
> e indique que no es posible escribir en el, para ello, sera necesario elimar el archivo a mano, ya que difiere de los permisos
> de Apache2.

## Base de Datos 
Se ha realizado sobre MariaDB 10.6. Se ha usado la codificacion "utf8_unicode_ci".
<p>Para guardar el historial de ediciones/creaciones sobre las tablas patients y diagnosis, se ha optado en realizarlo sobre la propia Base de Datos, y 
llevando el registro manualmente en los controladores.
El motivo es para facilitar la consulta de los datos a posteriori, ademas de guardar mas informacion en el caso de ser necesario y el usuario que lo realizo.

Se han creado las siguientes tablas:
</br>
- **users** ('id' PK int(11) UNSIGNED AU, 'email' varchar(255),'password', varchar(256), 'created_at' timestamp,'updated_at' timestamp). 
  - Tabla que almacena los usuario registrados. 
- **patients**('id' PK int(11) UNSIGNED AU,'fullName' varchar(255),'personalIdentification' varchar(9) index,'created_at' timestamp,'updated_at' timestamp).
  - Tabla que almacena los pacientes. Si un paciente tiene diagnosticos asociados, no se podra borrar, no se ha realizado un "ON DELETE CASCADE", para controlarlo en el controlador.
- **patients_history_log** ('id' int(11) UNSIGNED AU, 'patId' int(11) UNSIGNED, 'editBy' int(11) UNSIGNED, 'oldFullName' varchar(255), 'oldPersonalIdentification' varchar(9),'created_at' timestamp, 'updated_at' timestramp)
  - Tabla que almacena el historia de ediciones sobre la tabla **patients**. No se han vinculado con FK sobre la tabla patients, por si en el futuro se quiere consultar el registro de cambios y quien lo realizo.
- **diagnosis** ('id' PK int(11) UNSIGNED AU, 'idPatient' FK patients(id),'description' varchar(2000), 'date' timestamp, 'created_at' timestamp, 'updated_at' timestamp )
  - Tabla que almacena los diagnosticos de los pacientes.
- **diagnosis_history_log** ('id' PK int(11) UNSIGNED AU, 'idReg' int(11) UNSIGNED, 'editBy' int(11) UNSIGNED, 'oldDescription' varchar(2000), 'oldDate' tiemstamp, 'created_at' timestamp, 'updated_at' timestramp)
  - Tabla que almacena el historial de ediciones sobre la tabla **diagnosis**. No se ha vinculado con FK sobre la tabla diagnosis, para que en caso de borrar la informacion, se guarde el registro de cambios en caso de que fuera necesario consultar a posterior y quien lo edito.
- **oauth_access_tokens**,**oauth_auth_codes**,**oauth_clients**,**oauth_personal_access_clients**,**oauth_refresh_tokens**
  - Tablas generadas automaticamente al usar el paquete de laravel/passport y necesarias para la autenticacion y almacenamiento de los Tokens.
</p>
### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[CMS Max](https://www.cmsmax.com/)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**
- **[Romega Software](https://romegasoftware.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
