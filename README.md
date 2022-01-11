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
<p>Cada vez que se devuelva una respuesta del servidor, siempre lo hara acompaño del parametro **error**, con un **true|false** indicando
si la llamada se ha llevado correctamente a cabo, o por el contrario, estara acompañado de un mensaje de error en **msg**.
Tambien es posible que devuelva un mensaje de error distinto al llevar a cabo la validacion de los parametros, indicando cual es erroneo.
</p>

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

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
