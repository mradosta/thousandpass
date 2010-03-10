<div class="inner_container_border">

	<div class="inner_container">

	<div class="left"></div>

	<div class="right">

		<div class="text_and_logo">
			<?php
				echo $html->tag('span', __('Sign Up at', true));
				echo $html->image('logo_black.png');
			?>
		</div>

		<h3><?php __('We order your home page') ?></h3>

			<?php
				echo $form->create('User', array('action' => 'register'));
				echo $form->input('username', array('between' => __('Pick a username', true)));

				// Force the FormHelper to render a password field, and change the label.
				echo $form->input('passwrd', array('type' => 'password', 'label' => __('Password', true)));
				echo $form->input('email', array('between' => __('We need to send you a confirmation email', true)));
			?>
		<?php echo $form->end('Add');?>
		</div>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('List SitesUsers', true), array('action' => 'index'));?></li>
				<li><?php echo $html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
				<li><?php echo $html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
				<li><?php echo $html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
				<li><?php echo $html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
			</ul>
		</div>

	</div> <!--right-->
</div> <!--inner_container_border-->


<?php

	echo '<h2>'.__('Sign Up', true).'</h2>';

	echo $form->create('User', array('action' => 'register'));
	echo $form->input('username', array('between' => __('Pick a username', true)));

	// Force the FormHelper to render a password field, and change the label.
	echo $form->input('passwrd', array('type' => 'password', 'label' => __('Password', true)));
	echo $form->input('email', array('between' => __('We need to send you a confirmation email', true)));


	$policy = 'Al registrarse como usuario, usted acepta estar sujeto a las siguientes las condiciones y limitaciones de uso, y también hacer las siguientes representaciones:


- Usted utiliza el servicio sujeto a los Términos y Condiciones de Uso que actualmente están publicadas en nuestro sitio aquí; 
- Usted no utilizará el Servicio para manejar, de cualquier manera o forma, cualquier información sobre salud y seguridad públicas o cualquier tipo de datos que 
pérdida, robo, destrucción o inaccesibilidad, aunque sólo sea temporal o parcial, podría causar lesiones, epidemia, catástrofe, muerte o destrucción; 
- Usted está de acuerdo que sólo está autorizado a visitar, ver y mantener una copia de las páginas de este Sitio para su uso personal, y que se no duplicar, descargar, publicar, modificar, o distribuir el material de este sitio para cualquier uso comercial, o para cualquier otro propósito que como se describe en los términos; 
- Usted no utilizará el Servicio para transmitir o publicar tercero contenidos, datos o información que 
(a) están sujetos a derechos de autor la protección, a menos que tal uso o publicación se permite en virtud de los las leyes de copyright; 
(B) son obsceno, blasfemo, difamatorio, amenazante, o contra el orden público, la moral y normas vinculantes;
 (C) contener el comercio y / o secretos industriales, marcas de servicio o infringir marcas, patentes, derechos de autor y el software, o que, en general, el incumplimiento las obligaciones de confidencialidad o violen derechos de propiedad tangible o intelectual de los derechos de los demás datos anteriormente citados estarán sujetas a la reglamentación pertinente, como resultado, es su responsabilidad garantizar la necesaria el cumplimiento
- Usted es el único controlador, de los datos personales recogidos a través del uso del Servicio, y es su responsabilidad garantizar el cumplimiento necesario y a adoptar todas las medidas de seguridad necesarias en las actuales y futuras legislación; 
- Usted se atendrá a las leyes de su país de residencia, o de los reglamentos, convenios, tratados y acuerdos internacionales  aplicables a la materia. 

Mantenimiento de Cuenta 
Usted es el único responsable de mantener y salvaguardar el acceso a la su cuenta. Si usted sospecha que su cuenta ha sido comprometida, es de su exclusiva responsabilidad de tomar medidas inmediatas para proteger su Entradas. La acción inmediata puede incluir, sin limitación, la baja en 1000Pass  su cuenta y cambiar su ID de usuario, contraseña inmediatamente, o ponerse en contacto con 1000Pass  para reportar un incidente.  info@1000pass.com
Una cuenta se considera inactiva si no se ha identificado correctamente a la cuenta por un período de seis meses. Las cuentas inactivas se eliminarán de nuestro sistema sin previo aviso. Tras la supresión de su cuenta, su almacena las entradas se pueden perder y puede ser permanentemente irrecuperable. 
Propiedad
Derechos de uso
A excepción de lo expresamente previsto en virtud del presente Acuerdo, 1000Pass  es propietario de todos los derechos, títulos e intereses sobre y para todos los derechos de autor, marcas comerciales, de servicios marcas, atentes, secretos comerciales u otra propiedad intelectual y los derechos de propiedad y para el sitio y de servicios, y en todos los medios conocido o desarrollado más tarde, en la mayor medida prevista en el marco del legislación aplicable en cualquiera o en todas las jurisdicciones en que el Servicio ha sido utilizados. Usted no deberá eliminar, ocultar o alterar cualquier aviso de copyright, la información, aviso legal, restricción u otro aviso en este sitio o cualquier otro parte del mismo. Usted no debe usar o permitir a terceros a utilizar el nombre, marcas, nombres comerciales, o imágenes de marca de 1000Pass, incluida la "palabra 1000Pass ," sin el consentimiento previo por escrito de 1000Pass , como determina a su sola discreción, para cada uso. 
Seguridad de sitios Web 
Está prohibido violar o intentar violar la seguridad de este Sitio, incluyendo, sin limitación, 
(i)acceder a datos no dirigida a usted o entrar en un servidor o cuenta de que no está autorizado a acceder, 
(ii) intentar explorar, escanear o probar la de la vulnerabilidad de un sistema o red o violar la seguridad o las medidas de autenticación sin la debida autorización,
(iii) el intento de interferir con el servicio a cualquier usuario, anfitrión o red, incluyendo, sin limitación, por medio de envío de un virus, o sobrecarga, "inundaciones", "spamming", "bombardeos de correo", o "ataques" al Sitio, 
(iv) el envío de correo electrónico no solicitados a cualquier usuario del sitio, incluyendo promociones y / o publicidad de los productos o servicios, o (v) falsificar cualquier paquete TCP / IP encabezado o cualquier parte de la información del encabezado en cualquier correo electrónico o anuncio generado en relación con el Servicio. Violaciónes de sistema o red de de seguridad puede resultar en responsabilidad civil o penal. 1000Pass  se investigar los incidentes que puedan implicar tales violaciónes, y puede que participar y cooperar con las autoridades policiales en la persecución de los usuarios que participan en ales iolaciónes. 1000Pass en términos comercialmente los esfuerzos razonables a no divulgar cualquier información que se comunica, o correo postal a, el derecho del sitio, pero se reserva el de revelar dicha información , en la medida necesaria para satisfacer cualquier ley, normativa, jurídica roceso o solicitud gubernamental aplicable, y el derecho a eliminar cualquier dato de todo o en parte. Descarga de software de Passpack.com 
En el caso de que usted necesita para descargar cualquier software de este Sitio para utilizar nuestros Servicio ( Software 1000Pass "), usted reconoce y acepta a los siguientes los términos ( "Licencia"): 
1) Cualquier Software 1000Pass  está licenciado para su uso limitado en el marco del términos de esta licencia y de este Acuerdo, y no es vendida, a usted por 1000Pass. 
2) Nos reservamos todos los derechos no concedidos expresamente a usted; 
3) Los derechos que aquí se están estrictamente limitados a 1000Pass y sus otorgantes de licencias de derechos de propiedad intelectual del Software 1000Pass y hacer no incluyen ninguna otra patente ni derechos de propiedad intelectual; 
4) Usted es dueño de los medios de comunicación en la que se utiliza el Software 1000Pass, pero 1000Pass y / o el licenciante 1000Pass (s) de mantener la propiedad del Software 1000Pass sí mismo. Los términos de esta Licencia a cualquier actualización del software de proporcionada por 1000Pass que sustituyen y / o completar el 1000Pass original Producto de software, a menos que dicha actualización se acompaña de un separado de licencia, en cuyo caso los términos de dicha licencia; 
5)  La licencia le permite instalar y utilizar el Software 1000Pass. Tú no puede hacer que el 1000Pass software disponible a través de una red donde se podría ser utilizado por varios equipos al mismo tiempo. Usted no puede hacer cualquier copia del Software 1000Pass salvo y únicamente en la medida en expresamente permitido en esta Licencia o por la legislación aplicable, y puede que no copiar, compilar, realizar ingeniería inversa, desmontar, modificar, o crear rebajos derivados del Software 1000Pass o parte de ella; 
6) 1000Pass EL SOFTWARE NO ESTÁ DESTINADO A SER UTILIZADO EN ACTIVIDADES DE CUALQUIER quipos u otros servicios de aplicación o EN QUE EL FALLO DE LA SOFTWARE PODRÍA CAUSAR LA MUERTE, DAÑOS PERSONALES O físicas graves o Los daños ambientales; 
7) Usted no puede alquilar, arrendar, prestar, o distribuir el Software a 1000Pass a nadie; 
8) Usted no puede utilizar ni tampoco exportar o reexportar el Software 1000Pass salvo los autorizados por las leyes de la jurisdicción en la que la 1000Pass Software se obtuvo. 
Cifrado de la Ley de Cumplimiento 
Usamos la tecnología de cifrado fuerte para ofrecer nuestro servicio, sin embargo, el uso de la tecnología de cifrado fuerte es ilegal en algunos países. Esto puede significar que usted no puede utilizar nuestro servicio en determinados países. No estamos responsable de proporcionar algún consejo o una advertencia con respecto a la cuestiones de Derecho de cifrado de cumplimiento. Es su responsabilidad cumplir con las leyes locales de cifrado, y aceptar las consecuencias de la violación de tales leyes. Nos reservamos el derecho a suspender el servicio de inmediato y sin previo aviso si se determina, a nuestro criterio, que el servicio está siendo utilizado en violación de las regulaciones locales que rigen el uso de las tecnologías de cifrado (aunque no tenemos la responsabilidad de tomar esa decisión), y sin perjuicio de cualquier las medidas adecuadas para recuperar las pérdidas que puede ser sostenida. 

GARANTÍA DE RENUNCIAS 
1000Pass NO GARANTIZA QUE EL SERVICIO SERÁ LIBRE DE ERRORES O DE INTERRUPCIONES, O QUE OFREZCA RESULTADOS ESPECÍFICOS POR EL USO DEL SERVICIO O CUALQUIER CONTENIDO, BÚSQUEDA, o enlace en TI. EL SERVICIO, Y EL SITIO CONTENIDO SE ENTREGAN "TAL COMO ESTÁ" Y "TAL COMO ESTÁ DISPONIBLE". 1000Pass NO PUEDE ASEGURAR QUE LOS ARCHIVOS CONTENIDOS O descargarse desde el sitio SEA LIBRE DE VIRUS, la contaminación o características destructivas. 1000Pass RENUNCIA A TODA GARANTÍA, EXPRESA O IMPLÍCITA, INCLUYENDO CUALQUIER GARANTÍAS DE COMERCIALIZACIÓN Y APTITUD PARA UN ROPÓSITO PARTICULAR. 1000Pass NO SERÁ RESPONSABLE POR NINGÚN DAÑO DE CUALQUIER TIPO DERIVADOS DE LA EL USO DEL SERVICIO O EL SITIO, INCLUYENDO, SIN LIMITACIÓN, DIRECTO, INDIRECTOS, INCIDENTALES, PUNITIVOS Y CONSECUENTES. 
1000Pass renuncia a toda responsabilidad por los actos, omisiones, y realización de los terceros usuarios, los usuarios 1000Pass, anunciantes y / o de los patrocinadores en el sitio, en relación con el Sitio, o está relacionado a su uso del Sitio y de nuestro servicio. 1000Pass no es responsable de los productos, servicios, acciones o omisiones de un tercero en conexión con o que se hace referencia en el sitio. Sin limitar el lo anterior, usted puede reportar la mala conducta de los usuarios y / o de terceros los anunciantes o servicio y / o proveedores de producto de referencia o incluidos en en el sitio para 1000Pass en info@passpack.com. 1000Pass puede investigar el reclamo y tomar las medidas adecuadas, a su discreción. 
LIMITACIÓN DE RESPONSABILIDAD 
Excepto en jurisdicciones donde tales estipulaciones son restringidas, EN NINGÚN CASO, 1000Pass SERÁ RESPONSABLE ANTE USTED POR CUALQUIER DAÑO INDIRECTO, CONSECUENTE, EJEMPLARES, INCIDENTALES, ESPECIALES O PUNITIVOS, INCLUYENDO LA PÉRDIDA DE BENEFICIOS, INCLUSO SI 1000Pass HA SIDO ADVERTIDO DE LA POSIBILIDAD DE DICHOS DAÑOS. 
Aceptación de riesgos, las indemnizaciones de Al acceder a nuestro Sitio y el uso de nuestro servicio, usted reconoce y acepta que está utilizando nuestro servicio a su propio riesgo. Además, reconoce de que la disponibilidad de acceso de servicio y tiempo de respuesta puede variar debido a, sin limitación, el estado de tráfico de red, el sistema del usuario el rendimiento, el rendimiento del sistema de terceras partes página web, el sistema de 1000Pass rendimiento, y muchos otros factores. Usted acepta indemnizar y mantener a 1000Pass, sus afiliados, oficiales, agentes, y otros socios y empleados de cualquier pérdida, responsabilidad, reclamo o demanda, incluyendo honorarios razonables de abogados, hechos por cualquier tercero debido a que surjan de su uso del Sitio y de vuestro Servicio, incluyendo su uso del Sitio para proporcionar un vínculo a otro sitio o para cargar contenido o de otro tipo información al sitio. Terminación; Daños y perjuicios 
Si usted viola los términos de este Acuerdo, se reserva el derecho a la 1000Pass considerar la resolución del contrato, sin perjuicio del pleno derecho de recompensa por los daños y de pago de las consideraciones para el servicio prestado. Usted está de acuerdo que los daños monetarios pueden no proporcionar una reparación suficiente para 1000Pass para violaciones de este Acuerdo, y usted acepta por mandato judicial o de otro tipo compensación equitativa por violaciones tal. 
Derecho de los consumidores de Retiro 
Un usuario (como persona física) que entran en el presente contrato para a fines no relacionados con su actividad profesional tiene derecho a el derecho de retractación sin penalización alguna y sin motivos concretos, que se ejerce por medio de correo electrónico a info@1000pass.com días laborables después de la celebración o de la información se comunica, el derecho de rescisión podrá ser ejercido hasta tres meses después de la fecha de celebración del contrato. a través de carta certificada con acuse de recibo de entrega dentro de los siguientes 48 horas. El derecho de desistimiento no podrá ejercerse si el servicio es activada antes de la fecha de expiración especificada en este apartado. Usted nos da autorización explícita para activar el servicio antes de la fecha de expiración especificada anteriormente. 
Varios 
Este Acuerdo representa el acuerdo completo entre usted e integrado y 1000Pass sobre el objeto del mismo. Los Términos y Condiciones del sitio y de la Política de Privacidad se incorporan por de referencia. Si cualquier disposición de este Acuerdo es mantenida por un tribunal de la jurisdicción competente para ser inválida, nula o inejecutable, tal disposición se considerará nula y sin valor, pero las demás disposiciones continuará en pleno vigor sin ser alterada o anulados en cualquier camino. 
La renuncia de 1000Pass de una violación o una violación de cualquier disposición de el presente Acuerdo no constituirá, ni se entenderá como una renuncia de cualquier posterior incumplimiento o violación de cualquier otra disposición de estas Condiciones de el presente Acuerdo. 
 El partes acuerdan que cualquier disputa que surja de o en relación a este Acuerdo se someterá a los tribunales con jurisdicción Cordoba Argentina 

En cualquier acción incurridos para hacer cumplir este Acuerdo de Servicio o defender siempre de acuerdo con el Acuerdo, la parte que prevalezca tendrá derecho a los honorarios razonables de abogado. 
El presente Acuerdo podrá ser enmendado, modificado o eliminado en cualquier momento, con o sin previo aviso a la discreción de 1000Pass. En el caso de hay un cambio material al presente Acuerdo, vamos a utilizar los esfuerzos comercialmente razonables para notificarle del cambio, siempre que tiene una dirección de correo electrónico viables en nuestro sistema. Sea o no la el cambio es el material se encuentra dentro de la discreción de 1000Pass. 
De conformidad con el Código Civil, que usted representa que usted ha estudiado cuidadosamente y considerado todas y cada una disposición de este Acuerdo y por el presente de forma explícita y específicamente aprobar cada disposición.

HE LEÍDO, ENTENDIDO Y ESTOY DE ACUERDO';

	echo $form->input('email', array('value' => $policy, 'type' => 'textbox'));

	echo $form->submit('Create Account');
	echo $form->end();
?>