-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-06-2025 a las 15:29:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `bolsa_trabajo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion_sistema`
--

CREATE TABLE `configuracion_sistema` (
  `id` int(11) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `valor` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `configuracion_sistema`
--

INSERT INTO `configuracion_sistema` (`id`, `clave`, `valor`) VALUES
(1, 'aprobar_registros', 'true'),
(2, 'aprobar_ofertas', 'true');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ofertas`
--

CREATE TABLE `ofertas` (
  `id_oferta` int(11) NOT NULL,
  `puesto` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `empresa` varchar(100) DEFAULT NULL,
  `ubicacion` varchar(150) DEFAULT NULL,
  `modalidad` enum('presencial','remoto','híbrido') DEFAULT 'presencial',
  `jornada` enum('completa','parcial') DEFAULT NULL,
  `horario` varchar(100) DEFAULT NULL,
  `experiencia_requerida` tinyint(3) UNSIGNED DEFAULT NULL,
  `enlace` varchar(255) DEFAULT NULL,
  `email_contacto` varchar(150) DEFAULT NULL,
  `telefono_contacto` varchar(50) DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activa` tinyint(1) DEFAULT 1,
  `estado_aprobacion` enum('pendiente','aprobado','rechazado') DEFAULT 'aprobado',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_modificacion` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `fecha_eliminacion` timestamp NULL DEFAULT NULL,
  `publicada_por` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ofertas`
--

INSERT INTO `ofertas` (`id_oferta`, `puesto`, `descripcion`, `empresa`, `ubicacion`, `modalidad`, `jornada`, `horario`, `experiencia_requerida`, `enlace`, `email_contacto`, `telefono_contacto`, `fecha_fin`, `activa`, `estado_aprobacion`, `fecha_creacion`, `fecha_modificacion`, `fecha_eliminacion`, `publicada_por`) VALUES
(1, 'Desarrollador Frontend', 'Buscamos desarrollador junior con conocimientos en HTML, CSS y JavaScript.....', 'TechNovaA', 'Palermo, CABA', 'remoto', NULL, 'Part-time', 0, 'https://ejemplo.com/postulacion/frontend', NULL, NULL, NULL, 1, 'aprobado', '2025-06-13 13:00:00', '2025-06-14 23:52:54', NULL, 1),
(2, 'Asistente de redes sociales', 'Ayuda en campañas de redes para ONG.', 'Conecta ONG', 'Belgrano, CABA', 'híbrido', NULL, '6hs semanales', NULL, NULL, NULL, NULL, NULL, 1, 'aprobado', '2025-06-12 12:30:00', '2025-06-14 23:52:54', NULL, 2),
(3, 'Data Entry', 'Carga de datos para estudio de mercado.', 'Estudio Uno', 'Villa Urquiza, CABA', 'presencial', NULL, 'Lunes a viernes de 10 a 14hs', NULL, NULL, NULL, NULL, NULL, 1, 'aprobado', '2025-06-10 17:45:00', '2025-06-14 23:52:54', NULL, 3),
(4, 'Tallerista de robótica', 'Dictado de talleres para estudiantes secundarios.', 'Escuela Técnica 12', 'San Telmo, CABA', 'presencial', NULL, 'Viernes de 15 a 17hs', 2, NULL, NULL, NULL, NULL, 1, 'aprobado', '2025-06-11 14:20:00', '2025-06-14 23:52:54', NULL, 4),
(5, 'Ayudante de laboratorio', 'Apoyo en tareas de preparación de prácticas.', 'Laboratorios ALFA', 'Barracas, CABA', 'híbrido', NULL, 'Horario a convenir', 1, NULL, NULL, NULL, NULL, 1, 'aprobado', '2025-06-09 19:10:00', '2025-06-14 23:52:54', NULL, 4),
(29, 'Desarrollador Node.js + AWS Semi Senior', 'Anuncio Desarrollador Node.js + AWS \r\n\r\n \r\n\r\nDesde C&S estamos buscando un desarrollador Node.js + AWS con 3+ años de experiencia para un banco.  \r\n\r\n \r\n\r\nAcerca de C&S: \r\n\r\n¡Hola! Somos C&S, una empresa de informática que posee una prevalencia de 40 años en el mercado. Nos dedicamos al desarrollo de software, consultoría y talent outsourcing. Priorizamos la calidad, la innovación, la transparencia y la colaboración entre partes. Fomentamos relaciones de confianza y honestidad y valoramos la pasión por la tecnología.   \r\n\r\n \r\n\r\nDescripción del puesto:  \r\n\r\n \r\n\r\nBuscamos un Desarrollador Semi Senior con experiencia en Node.js y AWS, capaz de diseñar, desarrollar e implementar soluciones escalables en un entorno de microservicios. Deberá aplicar principios de Clean Architecture y buenas prácticas como SOLID, además de contar con conocimientos en infraestructura de AWS, incluyendo SQS, SNS, Step Functions, Lambda y CloudFormation. Desarrollo Backend con Node.js. \r\n\r\n \r\n\r\nSkills mandatorios:  \r\n\r\nExperiencia en Javascript/TypeScript en entornos backend.  \r\nManejo avanzado de Node.JS \r\nConocimiento en programación asincrónica, eventos y procesamiento en paralelo.  \r\nImplementación de patrones como Repository Pattern, Dependency Injection, y Factory Pattern.  \r\nUso de Jest, Mocha o Chai para testing.  \r\nAquitectura y Principios de Diseño.  \r\nAplicacion de principios SOLID en el desarrollo de software.  \r\nConocimiento de Clean Arquitecture y separación de capas. (Domain, Use Case, Infratructure, Interfaces)  \r\nExperiencia en microservicios y APIs RESTful con prácticas de desacoplamiento.  \r\nManejo de event driven architecture y procesamiento de eventos en AWS.  \r\nAWS y Servidores Serverless.  \r\nAWS Lambda: Desarrollo de funciones serverless con Node.js , optimizacion de tiempos de ejecución y gestión de errores.  \r\nSQS Y SNS: Configuración y consumo de colas de mensajes (FIFOS/Standard), publicación y suscripción de eventos.  \r\nStep Functions: Orquestación de flujos de trabajo con tareas síncronas y asíncronas.  \r\nCloudFormation: Definición de infraestructura como código (IaC), templates YAML/JSON.  \r\nDynamoDB/RDS: Modelado de datos en base de datos NoSQL, y SQL en AWS.  \r\nMicroservicios y comunicación.  \r\nDiseño y desarrollo de microservicios desacoplados con patrones como event sourcing y CQRS.  \r\nImplementacion de mensajeria asincronica con SQS/SNS para comunicación entre servicios.  \r\nConocimiento en API Gateway y gestión de endpoints con JWT o IAM Roles.  \r\n \r\n\r\nModalidad: Hibrido \r\n\r\nHorario: 9 a 18hs. (Jornada Completa) \r\n\r\n \r\n\r\nBeneficios:  \r\n\r\nRevisiones salariales 3 veces por año. \r\nAcceso a la plataforma gratuita UDEMY. \r\nDescuentos en instituciones académicas y cursos de idiomas. \r\nPlataforma de capacitación online totalmente gratuita con certificación internacional. \r\nPosiciones hibridas y/o 100% remotas.  \r\nBono de compensación de gastos (internet/luz).  \r\nSe brinda licencia por paternidad extendida.  \r\nBono para escolaridad en caso de tener hijos menores. \r\nKit de bienvenida.  \r\nCuponera de descuentos en gastronomía, indumentaria y cursos académicos con certificación internacional. \r\nDia de cumpleaños libre y regalo.  \r\nRedirección de aportes para obra social.  \r\nReintegro por gastos en guardería.  \r\nBono por candidato de referidos.  \r\nEntrega de equipo de trabajo. ', 'CYS Informática', 'Cdad. Autónoma de Buenos Aires', 'híbrido', 'completa', 'Lunes a viernes de 9 a 18hs.', 4, '', 'contacto@puesto.com', '1548965359', NULL, 1, 'aprobado', '2025-05-18 13:03:19', '2025-06-12 13:44:36', NULL, 1),
(48, 'Desarrollador Backend Junior', '<p><strong>Empresa de tecnología educativa</strong> busca incorporar un <em>desarrollador backend</em> con conocimientos en Python y Django.</p><ul><li>Trabajo remoto</li><li>Horario flexible</li><li>Capacitación constante</li></ul>', 'EdTech Solutions', 'Buenos Aires', 'remoto', 'completa', 'Lunes a viernes de 9 a 18', 1, 'https://edtechsolutions.com/jobs/backend-jr', 'rrhh@edtechsolutions.com', '1123456789', '2024-12-31', 0, 'aprobado', '2024-10-01 13:00:00', '2025-06-14 21:12:45', NULL, 1),
(49, 'QA Tester Manual', '<p>Buscamos estudiantes avanzados para tareas de <strong>testing manual</strong> en proyectos ágiles. Se valorará experiencia en <u>JIRA</u> y nociones de metodologías Scrum.</p>', 'SoftTesting SRL', 'Rosario', 'híbrido', 'parcial', 'Lunes a viernes de 14 a 18', 0, '', 'seleccion@softtesting.com', '1134567890', '2025-01-15', 0, 'aprobado', '2024-11-05 18:00:00', '2025-06-14 21:12:45', NULL, 8),
(50, 'Asistente de Soporte Técnico', '<p>Se requiere perfil proactivo para <strong>atención a usuarios</strong> y soporte de primer nivel.</p><ul><li>Instalación de software</li><li>Mantenimiento preventivo</li><li>Documentación de incidencias</li></ul>', 'TechHelp', 'Córdoba', 'presencial', 'completa', '8 a 17 hs', 2, '', 'techhelp@empresa.com', '1145678901', '2025-02-01', 0, 'aprobado', '2024-09-10 12:15:00', '2025-06-14 21:12:45', NULL, 14),
(51, 'Analista Funcional Jr.', '<p>Buscamos <b>Analista Funcional Jr.</b> para colaborar en proyectos de software educativo.</p>\r\n    <ul>\r\n        <li>Conocimiento de UML</li>\r\n        <li>Redacción de casos de uso</li>\r\n        <li>Testing de requisitos</li>\r\n    </ul>', 'FormarTech', 'Buenos Aires', 'híbrido', 'completa', '9 a 17 hs', 1, '', 'seleccion@formartech.com', '1141122233', '2025-03-01', 0, 'aprobado', '2024-10-15 12:00:00', '2025-06-14 21:15:51', NULL, 10),
(52, 'Frontend Developer React', '<p>Incorporamos <em>frontend developer</em> con experiencia en <b>React</b>, <u>Vite</u> y consumo de APIs REST.</p>\r\n    <p>Trabajo en equipo y buenas prácticas de desarrollo son fundamentales.</p>', 'InnovarWeb', 'Remoto', 'remoto', 'completa', 'Flexible', 2, 'https://innovarweb.com/careers/frontend', 'hr@innovarweb.com', '1178912345', '2024-12-20', 0, 'aprobado', '2024-09-20 11:45:00', '2025-06-14 21:15:51', NULL, 20),
(53, 'Asistente de Datos', '<p>Nos encontramos en la búsqueda de un/a <b>Asistente de carga y análisis de datos</b> para colaborar con el área de BI.</p>\r\n    <p><u>Se requiere manejo intermedio de Excel</u> y valoramos conocimientos de SQL.</p>', 'Sistemas Cuyo', 'Mendoza', 'presencial', 'parcial', 'Lunes a viernes de 9 a 13 hs', 0, '', 'reclutamiento@sistemascuyo.com', '2619876543', '2025-01-05', 0, 'aprobado', '2024-10-01 13:00:00', '2025-06-14 21:15:51', NULL, 2),
(54, 'Desarrollador Full Stack', '<p>Empresa tecnológica en expansión busca <b>Desarrollador Full Stack</b> con experiencia en:</p>\r\n    <ul>\r\n        <li>Laravel + Vue.js</li>\r\n        <li>Manejo de base de datos MySQL</li>\r\n        <li>APIs y autenticación OAuth</li>\r\n    </ul>\r\n    <p>Ofrecemos buen clima laboral y posibilidad de crecimiento.</p>', 'Databyte', 'CABA', 'híbrido', 'completa', '9 a 18 hs', 3, '', 'talento@databyte.com', '1133344455', '2025-02-15', 0, 'aprobado', '2024-11-10 15:00:00', '2025-06-14 21:15:51', NULL, 12),
(55, 'Documentación Técnica', '<p><b>Convocatoria </b>para estudiantes de carreras técnicas. Tareas:</p>\r\n    <ul>\r\n        <li>Redacción de manuales y guías de usuario</li>\r\n        <li>Actualización de documentación interna</li>\r\n    </ul>\r\n    <p>No se requiere experiencia previa.</p>', 'DocuSoft', 'La Plata', 'remoto', 'parcial', 'Horario a convenir', 0, '', 'pasantias@docusoft.com', '2214567890', '2025-03-10', 0, 'aprobado', '2024-10-20 16:30:00', '2025-06-14 21:29:09', NULL, 28),
(56, 'Desarrollador Backend Node.js', '<p><b>¡Sumate a nuestro equipo!</b></p><p>Buscamos un perfil con experiencia en <i>Node.js</i> y <u>arquitectura de microservicios</u>.</p><ul><li>Modalidad híbrida</li><li>Jornada completa</li><li>Zona: CABA</li></ul>', 'Innovar Tech', 'Buenos Aires, Argentina', 'híbrido', 'completa', '9 a 17hs', 3, NULL, 'rrhh@innovartech.com', NULL, NULL, 1, 'aprobado', '2025-06-13 13:00:00', '2025-06-14 23:59:06', NULL, 14),
(57, 'Desarrollador Backend Node.js', '<b>Buscamos Desarrollador Backend</b> con conocimientos en <i>Node.js</i>, <u>Express</u> y bases de datos <b>MongoDB</b>.<br><br>\r\n  <ul>\r\n    <li>Trabajo en equipo ágil</li>\r\n    <li>Buenas prácticas de desarrollo</li>\r\n    <li>Conexión con servicios REST y GraphQL</li>\r\n  </ul>', 'TechNova', 'CABA, Argentina', 'híbrido', 'completa', '9 a 17hs', 2, NULL, 'rrhh@technova.com', NULL, NULL, 1, 'aprobado', '2025-06-09 19:10:00', '2025-06-14 23:59:06', NULL, 10),
(58, 'QA Tester Jr.', '<b>¡Únete a nuestro equipo de QA!</b><br><br>\r\n  <ul>\r\n    <li>Diseño y ejecución de casos de prueba</li>\r\n    <li><i>Automatización</i> con Selenium</li>\r\n    <li>Seguimiento de errores</li>\r\n  </ul>\r\n  <p><u>No se requiere experiencia previa</u>, solo compromiso y ganas de aprender.</p>', 'ControlSoft', 'La Plata', 'presencial', 'parcial', '8 a 13hs', 0, NULL, 'qa@controlsoft.com.ar', NULL, NULL, 1, 'aprobado', '2025-06-11 14:20:00', '2025-06-14 23:59:06', NULL, 8),
(59, 'Data Analyst Jr.', '<p><b>Responsabilidades:</b></p>\r\n  <ul>\r\n    <li>Recolección de datos</li>\r\n    <li><i>Limpieza y análisis</i> usando Python y Pandas</li>\r\n    <li>Visualización con Power BI</li>\r\n  </ul>\r\n  <p>Modalidad <b>remota</b>. Jornada <u>completa</u>. Excelente clima laboral.</p>', 'Datamind', 'Remoto', 'remoto', 'completa', 'flexible', 1, NULL, 'talento@datamind.org', NULL, NULL, 1, 'aprobado', '2025-06-10 17:45:00', '2025-06-14 23:59:06', NULL, 14),
(60, 'Soporte Técnico Nivel 1', '<b>Requisitos:</b>\r\n  <ul>\r\n    <li>Conocimientos básicos de hardware y redes</li>\r\n    <li>Atención al cliente</li>\r\n    <li><i>Instalación de software</i></li>\r\n  </ul>\r\n  <p>Se ofrece capacitación y posibilidades de crecimiento.</p>', 'InnovaTech', 'Morón, Buenos Aires', 'presencial', 'completa', '8 a 16hs', 0, NULL, 'soporte@innovatech.com', NULL, NULL, 1, 'aprobado', '2025-06-12 12:30:00', '2025-06-14 23:59:06', NULL, 2),
(61, 'Asistente de Gestión Académica', '<b>Funciones principales:</b><br>\r\n  <ul>\r\n    <li>Gestión de legajos estudiantiles</li>\r\n    <li>Seguimiento de inscripciones y regularidades</li>\r\n    <li><i>Atención a docentes y alumnos</i></li>\r\n  </ul>\r\n  <p><u>Manejo de SIU Guaraní</u> es un plus.</p>', 'Instituto Técnico Superior N°4', 'José C. Paz', 'presencial', 'parcial', '13 a 18hs', 1, NULL, 'gestion@its4.edu.ar', NULL, NULL, 1, 'aprobado', '2025-06-05 20:25:00', '2025-06-14 23:59:06', NULL, 21),
(62, 'Frontend Developer React', '<p><b>¿Te apasiona el desarrollo web?</b></p>\r\n  <p>Buscamos desarrollador con experiencia en:</p>\r\n  <ul>\r\n    <li><b>React</b> (hooks, context)</li>\r\n    <li><i>Styled Components</i></li>\r\n    <li>Integración con APIs REST</li>\r\n  </ul>\r\n  <p><u>Remoto 100%</u>. Jornada flexible. Se valora experiencia con Git y metodologías ágiles.</p>', 'WebCraft', 'Remoto', 'remoto', 'completa', 'flexible', 2, NULL, 'jobs@webcraft.dev', NULL, NULL, 1, 'aprobado', '2025-06-06 16:40:00', '2025-06-14 23:59:06', NULL, 20),
(63, 'Docente para cursos de Python', '<b>Se busca docente</b> para dictado de cursos de <u>Python nivel inicial y medio</u>.<br><br>\r\n  <i>Requisitos:</i>\r\n  <ul>\r\n    <li>Experiencia comprobable en enseñanza</li>\r\n    <li>Dominio de Python, Jupyter, y fundamentos de programación</li>\r\n    <li>Capacidad de comunicación y manejo grupal</li>\r\n  </ul>', 'Centro de Capacitación IT', 'San Miguel', 'híbrido', 'parcial', 'Lunes, miércoles y viernes de 18 a 21hs', 1, NULL, 'cursos@capacita-it.com.ar', NULL, NULL, 1, 'aprobado', '2025-06-07 18:30:00', '2025-06-14 23:59:06', NULL, 12),
(64, 'Soporte de sistemas', '<p><b>Oportunidad para estudiantes:</b></p>\r\n  <ul>\r\n    <li>Instalación y configuración de software</li>\r\n    <li>Soporte remoto a usuarios</li>\r\n    <li>Documentación de procesos</li>\r\n  </ul>\r\n  <p><i>Ideal para estudiantes de 2° o 3° año de Análisis de Sistemas.</i></p>', 'IT Solutions', 'San Martín', 'híbrido', 'parcial', 'Turno mañana o tarde', 0, 'https://ejemplo.com/postulacion/frontend', 'talento@itsolutions.com', '+541140289217', '2025-06-30', 1, 'pendiente', '2025-06-08 11:00:00', '2025-06-15 12:19:13', NULL, 9),
(65, 'Data Analyst Junior', '<b>Empresa fintech busca Data Analyst Jr.</b><br>\r\n  <ul>\r\n    <li>Conocimientos de SQL y Power BI</li>\r\n    <li><i>Deseable:</i> Python, R</li>\r\n    <li>Trabajo en equipo y buena comunicación</li>\r\n  </ul>\r\n  <p>Se ofrece jornada híbrida, posibilidad de crecimiento y capacitaciones internas.</p>', 'FinTechNow', 'Palermo, CABA', 'híbrido', 'completa', '9 a 17 hs', 0, NULL, 'rrhh@fintechnow.com', NULL, '2025-09-30', 1, 'pendiente', '2025-05-31 21:00:00', '2025-06-14 23:59:06', NULL, 28),
(66, 'Testing QA Manual', '<p><b>Rol:</b> Tester QA Manual para proyectos web.</p>\r\n  <ul>\r\n    <li>Creación de casos de prueba</li>\r\n    <li><u>Reportes de bugs</u> en Jira</li>\r\n    <li>Pruebas funcionales y exploratorias</li>\r\n  </ul>\r\n  <p><i>Se valora ISTQB (no excluyente)</i></p>', 'QALatam', 'Morón', 'remoto', 'parcial', 'turno tarde', 1, NULL, 'qa@qalatam.tech', NULL, '2025-08-15', 0, 'aprobado', '2025-06-01 12:50:00', '2025-06-14 23:59:06', NULL, 10),
(67, 'Asistente técnico en redes', '<p><b>Tareas:</b></p>\r\n  <ul>\r\n    <li>Configuración de routers y switches</li>\r\n    <li><i>Soporte a usuarios</i> y documentación</li>\r\n    <li>Manejo básico de Linux</li>\r\n  </ul>', 'NetServicios SRL', 'Tigre', 'presencial', 'completa', '9 a 17hs', 2, NULL, 'soporte@netservicios.com.ar', NULL, '2024-12-31', 0, 'aprobado', '2025-06-02 19:45:00', '2025-06-14 23:59:06', NULL, 14),
(68, 'Diseñador UX/UI Trainee', '<b>Buscamos Trainee en diseño UX/UI</b><br>\r\n  <p><u>Responsabilidades:</u></p>\r\n  <ul>\r\n    <li>Wireframes y prototipos en Figma</li>\r\n    <li>Participación en tests de usabilidad</li>\r\n    <li><i>Trabajo en equipo con desarrolladores y PMs</i></li>\r\n  </ul>', 'UX Studio', 'Online', 'remoto', 'parcial', 'turno tarde', 0, NULL, 'ux@uxstudio.org', NULL, NULL, 1, 'rechazado', '2025-06-03 13:00:00', '2025-06-14 23:59:06', NULL, 15),
(69, 'DevOps - AWS Junior', '<p><b>Requisitos:</b></p>\r\n  <ul>\r\n    <li>Experiencia mínima en servicios AWS</li>\r\n    <li>Conocimientos básicos en Docker</li>\r\n    <li>Manejo de GitLab CI/CD</li>\r\n  </ul>\r\n  <p><i>Se valora certificación Cloud Practitioner</i></p>', 'CloudTeam', 'San Isidro', 'híbrido', 'completa', '10 a 18 hs', 1, NULL, 'talento@cloudteam.io', NULL, '2025-10-31', 0, 'pendiente', '2025-06-04 15:15:00', '2025-06-14 23:59:06', NULL, 1),
(70, 'Soporte técnico Helpdesk', '<p><b>Responsabilidades:</b></p>\r\n  <ul>\r\n    <li>Atención a usuarios en primer nivel</li>\r\n    <li><i>Configuración de equipos</i> y periféricos</li>\r\n    <li>Manejo de herramientas de ticketing</li>\r\n  </ul>\r\n  <p>Requiere buena comunicación y orientación al cliente.</p>', 'TecniRed', 'Avellaneda', 'presencial', 'completa', '08 a 16hs', 1, 'https://www.tecnired.com.ar/postulacion', 'rrhh@tecnired.com.ar', '1123456789', '2025-09-01', 1, 'aprobado', '2025-06-14 21:25:15', NULL, NULL, 21),
(71, 'Diseñador Web Frontend', '<p><b>Requisitos:</b> HTML5, CSS3, JavaScript, Bootstrap.</p>\r\n  <ul>\r\n    <li>Maquetado responsivo</li>\r\n    <li>Uso de Figma para interfaces</li>\r\n    <li><u>Buenas prácticas de accesibilidad</u></li>\r\n  </ul>', 'Pixel Creativo', 'Lanús', 'híbrido', 'parcial', '10 a 14hs', 2, 'https://pixelcreativo.com/trabaja-con-nosotros', 'jobs@pixelcreativo.com', '1134589977', '2025-08-10', 1, 'aprobado', '2025-05-29 14:10:00', '2025-06-14 23:59:06', NULL, 20),
(72, 'Backend Developer con Django', '<p><b>Buscamos:</b> estudiante avanzado con conocimientos en Django y PostgreSQL.</p>\r\n  <p><i>Trabajo colaborativo con frontend React.</i></p>\r\n  <ul>\r\n    <li>APIs RESTful</li>\r\n    <li>ORM y modelos relacionales</li>\r\n    <li>Autenticación y permisos</li>\r\n  </ul>', 'SoftLink', 'CABA - Belgrano', 'remoto', 'completa', 'Flex', 0, 'https://softlink.dev/apply', 'desarrollo@softlink.com', '1167892345', '2025-10-20', 1, 'pendiente', '2025-06-14 21:25:15', NULL, NULL, 19),
(73, 'Asistente funcional', '<b>Empresa de software administrativo busca Asistente funcional trainee</b>\r\n  <ul>\r\n    <li>Capacitación interna</li>\r\n    <li>Atención a usuarios finales</li>\r\n    <li>Relevamiento de requerimientos</li>\r\n  </ul>\r\n  <p><i>Requiere buena redacción y habilidades blandas</i></p>', 'GESA S.A.', 'Quilmes', 'presencial', 'parcial', 'Turno mañana', 0, 'https://gesa.com.ar/oportunidades', 'busquedas@gesa.com.ar', '1145332288', '2025-07-25', 0, 'rechazado', '2025-06-14 21:25:15', NULL, NULL, 9),
(74, 'Administrador de bases de datos Jr.', '<p><b>Stack:</b> PostgreSQL, MySQL, backup & recovery, modelado ER</p>\r\n  <ul>\r\n    <li>Mantenimiento y monitoreo de bases de datos</li>\r\n    <li>Documentación técnica</li>\r\n    <li>Reportes e informes a equipos funcionales</li>\r\n  </ul>', 'DataMaster', 'San Fernando', 'híbrido', 'completa', '9 a 17hs', 2, 'https://datamaster.org/postulate', 'reclutamiento@datamaster.org', '1167321122', '2025-11-05', 1, 'pendiente', '2025-05-30 17:30:00', '2025-06-14 23:59:06', NULL, 18),
(75, 'Ingeniero/a DevOps Senior', '<p><b>Responsabilidades:</b></p>\r\n  <ul>\r\n    <li>Diseñar y mantener pipelines CI/CD (GitLab CI, Jenkins)</li>\r\n    <li>Automatización con Ansible y Terraform</li>\r\n    <li>Monitorización (Prometheus, Grafana)</li>\r\n  </ul>\r\n  <p><i>Experiencia deseada: AWS, Kubernetes, seguridad en despliegue.</i></p>', 'CloudMasters', 'CABA - Microcentro', 'remoto', 'completa', 'Flexible', 5, 'https://cloudmasters.io/devops', 'infra@cloudmasters.io', NULL, '2025-11-30', 1, 'aprobado', '2025-06-04 13:25:00', '2025-06-14 23:49:45', NULL, 12),
(76, 'Especialista en Seguridad Informática', '<p><b>Perfil:</b> Conocimientos en pentesting, OWASP, normas ISO/IEC 27001</p>\r\n  <ul>\r\n    <li>Implementación de medidas de ciberseguridad</li>\r\n    <li>Análisis de vulnerabilidades</li>\r\n    <li>Respuestas ante incidentes</li>\r\n  </ul>', 'SecureIT', 'Mar del Plata', 'híbrido', 'completa', '9 a 17hs', 4, '', 'seguridad@secureit.com', '1122334455', '2025-10-01', 1, 'pendiente', '2025-05-01 18:40:00', '2025-06-14 23:49:57', NULL, 14),
(77, 'Data Scientist Jr. con Python', '<p><b>Se requiere:</b> conocimientos en ML, Pandas, Scikit-learn</p>\r\n  <p><i>Preferentemente con experiencia en visualización (Seaborn, Plotly)</i></p>\r\n  <ul>\r\n    <li>Procesamiento de datasets</li>\r\n    <li>Entrenamiento y ajuste de modelos</li>\r\n    <li>Reporte de resultados</li>\r\n  </ul>', 'AI Lab Latam', 'CABA', 'remoto', 'parcial', '14 a 18hs', 2, 'https://ailablatam.org/jobs/python-ds', 'ai@ailablatam.org', NULL, '2025-08-30', 1, 'aprobado', '2025-06-02 11:50:00', NULL, NULL, 28),
(78, 'Machine Learning Engineer', '<p><b>Perfil buscado:</b></p>\r\n  <ul>\r\n    <li>Modelado con TensorFlow / PyTorch</li>\r\n    <li>Flujos de trabajo con MLFlow</li>\r\n    <li>Infraestructura: GPUs, optimización, cloud (GCP/Azure)</li>\r\n  </ul>\r\n  <p><i>Contribución en papers y proyectos open source es valorada.</i></p>', 'DeepSolve', 'Rosario', 'remoto', 'completa', 'Flexible', 4, 'https://deepsolve.io/ml-job', 'recruit@deepsolve.io', NULL, '2025-12-10', 1, 'aprobado', '2025-05-25 16:10:00', NULL, NULL, 10),
(79, 'Asistente QA (Testing Manual)', '<p><b>Tareas:</b></p>\r\n  <ul>\r\n    <li>Diseño y ejecución de casos de prueba</li>\r\n    <li>Reporte de bugs en Jira</li>\r\n    <li>Pruebas de regresión funcional</li>\r\n  </ul>', 'QATools', 'La Plata', 'presencial', 'parcial', 'Turno tarde', 1, 'https://qatools.net/postulacion', 'qa@qatools.net', NULL, '2025-08-15', 1, 'rechazado', '2025-06-05 20:25:00', NULL, NULL, 2),
(80, 'Testing Automatizado', '<p><b>Descripción:</b> Buscamos estudiante avanzado para tareas de QA automatizado.</p>\r\n   <ul>\r\n     <li>Escritura de scripts con Selenium</li>\r\n     <li>Revisión de logs y reporte de errores</li>\r\n     <li><i>Posibilidad de continuidad laboral</i></li>\r\n   </ul>', 'TechnoLabs', 'Morón', 'presencial', 'parcial', 'Lunes a viernes de 9 a 13hs', 0, 'https://technolabs.com.ar/oportunidades', 'qa@technolabs.com.ar', '', '2024-11-15', 0, 'aprobado', '2024-09-20 12:40:00', '2025-06-14 21:29:30', NULL, 20),
(81, 'Soporte Técnico Nivel 1', '<p><b>Responsabilidades:</b></p>\r\n   <ul>\r\n     <li>Atención a usuarios vía chat y teléfono</li>\r\n     <li>Derivación de tickets</li>\r\n     <li>Manejo de herramientas de mesa de ayuda</li>\r\n   </ul>', 'HelpDeskPro', 'Lomas de Zamora', 'presencial', 'completa', 'Turno mañana', 1, NULL, NULL, '1144559988', '2025-01-10', 0, 'aprobado', '2024-10-01 17:05:00', NULL, NULL, 1),
(82, 'Junior Frontend Developer', '<p><b>Requisitos:</b> conocimientos en HTML, CSS, JavaScript (ES6).</p>\r\n   <ul>\r\n     <li>Experiencia básica con React o Vue (no excluyente)</li>\r\n     <li>Colaboración con equipo de diseño UI</li>\r\n   </ul>', 'BitFrame', 'Lanús', 'híbrido', 'completa', '10 a 18hs', 1, NULL, 'rrhh@bitframe.dev', NULL, '2025-03-30', 0, 'rechazado', '2024-11-15 15:20:00', NULL, NULL, 14),
(83, 'Backend Developer - Django', '<p><b>Requisitos:</b></p>\r\n   <ul>\r\n     <li>Experiencia comprobable con Django 3+</li>\r\n     <li>APIs RESTful</li>\r\n     <li>ORM y PostgreSQL</li>\r\n     <li><i>Deseable: conocimientos de Docker</i></li>\r\n   </ul>', 'Ingenia', 'San Justo', 'remoto', 'completa', 'Horario flexible', 2, 'https://ingenia.io/trabaja-con-nosotros', 'dev@ingenia.io', NULL, '2025-02-20', 0, 'aprobado', '2024-12-01 14:10:00', NULL, NULL, 28),
(84, 'Soporte a Aplicaciones Web', '<p><b>Tareas:</b> monitoreo de sistemas, carga de datos, asistencia funcional.</p>\r\n   <p>Ideal para estudiantes de 2° o 3° año de carreras en informática o sistemas.</p>', 'Grupo NetSys', 'CABA', 'presencial', 'parcial', '16 a 20hs', 1, NULL, 'aplicaciones@netsys.com.ar', '1166789900', '2025-04-10', 0, 'aprobado', '2024-12-20 21:00:00', NULL, NULL, 9),
(85, 'Asistente de documentación técnica (Remoto)', '<p><b>Objetivo:</b> Colaborar con la redacción y revisión de manuales técnicos.</p>\r\n   <ul>\r\n     <li>Requiere inglés técnico básico (lectura)</li>\r\n     <li>Trabajo remoto con entregas semanales</li>\r\n     <li>Se valorará precisión y redacción clara</li>\r\n   </ul>', 'SoftDocs', 'Remoto', 'remoto', 'parcial', 'Flexible', 0, NULL, 'reclutamiento@softdocs.io', NULL, '2025-08-01', 1, 'aprobado', '2025-06-05 13:00:00', NULL, NULL, 22),
(86, 'Práctica profesional en soporte de sistemas', '<p><b>Descripción:</b> El puesto es ideal para estudiantes con cursada activa.</p>\r\n   <ul>\r\n     <li>Tareas de soporte básico y seguimiento de tickets</li>\r\n     <li>Documentación interna</li>\r\n     <li>No se requiere experiencia previa</li>\r\n   </ul>', 'Círculo Informático', 'San Martín', 'presencial', 'parcial', 'Lunes a viernes, 14 a 18hs', 0, NULL, 'practicas@circuloinformatico.com', NULL, '2025-07-20', 1, 'aprobado', '2025-06-02 18:00:00', NULL, NULL, 8),
(87, 'Pasante QA Manual - Inglés básico', '<p><b>Funciones:</b> ejecutar pruebas manuales y reportar bugs.</p>\r\n   <p><u>Inglés técnico requerido</u>: lectura de casos de prueba y documentación de errores.</p>', 'Globant Labs', 'Remoto', 'remoto', 'parcial', '9 a 13hs', 0, NULL, 'qa-intern@globantlabs.com', NULL, '2025-08-15', 1, 'aprobado', '2025-06-03 15:30:00', NULL, NULL, 19),
(88, 'Asistente de soporte técnico - semi presenciallll', '<p><b>Modalidad:</b> 3 días remoto / 2 presencial.</p>\r\n   <ul>\r\n     <li>Soporte técnico inicial</li>\r\n     <li>Seguimiento de incidencias en GLPI</li>\r\n     <li>Formación interna provista</li>\r\n   </ul>', 'ITFlow', 'Tres de Febrero', 'híbrido', 'parcial', 'Lunes a viernes, 10 a 14hs', 0, '', 'soporte@itflow.net', '', '2025-07-31', 1, 'pendiente', '2025-06-04 12:15:00', '2025-06-15 11:15:00', NULL, 9),
(89, 'Pasantía Frontend - HTML/CSS/JS', '<p><b>Requisitos:</b></p>\r\n   <ul>\r\n     <li>Conocimientos en diseño responsivo</li>\r\n     <li>Manejo de Git y GitHub</li>\r\n     <li>Disponibilidad de 20hs semanales</li>\r\n   </ul>\r\n   <p><i>Se brindará capacitación en Figma y frameworks.</i></p>', 'WebStart', 'Moreno', 'híbrido', 'parcial', 'A convenir', 0, 'https://webstart.com/pasantias', 'frontend@webstart.com', NULL, '2025-08-10', 1, 'aprobado', '2025-06-06 11:45:00', NULL, NULL, 28),
(90, 'Desarrollador Backend Java (Spring Boot)', '<p>Buscamos un <b>backend developer</b> con sólida experiencia en Java y Spring Boot.</p>\r\n   <ul>\r\n     <li>Manejo de bases de datos relacionales (PostgreSQL)</li>\r\n     <li><i>Prácticas de TDD y Clean Code</i></li>\r\n     <li>API REST, Swagger, Postman</li>\r\n     <li>Deseable: Docker y CI/CD</li>\r\n   </ul>\r\n   <p><u>Requiere 3+ años de experiencia.</u></p>', 'Nexux Labs', 'Villa Ballester', 'presencial', 'completa', '9 a 18hs', 3, NULL, 'rrhh@nexuxlabs.tech', NULL, '2025-07-30', 1, 'aprobado', '2025-06-01 14:00:00', NULL, NULL, 20),
(91, 'Desarrollador Backend Java (Spring Boot)', '<p>Buscamos un <b>backend developer</b> con sólida experiencia en Java y Spring Boot.</p>\r\n   <ul>\r\n     <li>Manejo de bases de datos relacionales (PostgreSQL)</li>\r\n     <li><i>Prácticas de TDD y Clean Code</i></li>\r\n     <li>API REST, Swagger, Postman</li>\r\n     <li>Deseable: Docker y CI/CD</li>\r\n   </ul>\r\n   <p><u>Requiere 3+ años de experiencia.</u></p>', 'Nexux Labs', 'Villa Ballester', 'presencial', 'completa', '9 a 18hs', 3, NULL, 'rrhh@nexuxlabs.tech', NULL, '2025-07-30', 1, 'aprobado', '2025-06-01 14:00:00', NULL, NULL, 20),
(92, 'DevOps Engineer - AWS', '<p>Se busca DevOps para liderar la infraestructura en AWS.</p>\r\n   <ul>\r\n     <li><b>Requisitos clave:</b></li>\r\n     <li>Terraform, CloudFormation</li>\r\n     <li>Experiencia en CI/CD (GitHub Actions, GitLab CI)</li>\r\n     <li>Docker, EKS, Lambda</li>\r\n   </ul>\r\n   <p><i>5 años de experiencia mínimo en entornos Cloud.</i></p>', 'CloudNet', 'Remoto', 'remoto', 'completa', 'Flexible', 5, NULL, 'cloudops@cloudnet.io', NULL, '2025-08-10', 1, 'aprobado', '2025-05-24 12:00:00', NULL, NULL, 14),
(93, 'QA Automation - Selenium y Cypress', '<p>Responsabilidades:</p>\r\n   <ul>\r\n     <li>Diseño y ejecución de pruebas automatizadas</li>\r\n     <li>Frameworks: <b>Selenium, Cypress</b>, Playwright</li>\r\n     <li>Integración con pipelines CI</li>\r\n     <li><u>Documentación y reporte de errores</u></li>\r\n   </ul>\r\n   <p><i>Requiere al menos 4 años de experiencia en testing automatizado.</i></p>', 'TestCraft', 'San Miguel', 'híbrido', 'completa', '9 a 17hs', 4, NULL, 'qa@testcraft.ar', NULL, '2025-07-25', 1, 'aprobado', '2025-05-28 17:30:00', NULL, NULL, 17),
(94, 'Fullstack Developer - React + Node.js', '<p><b>Stack principal:</b> React, Node.js, Express, MongoDB</p>\r\n   <ul>\r\n     <li>Experiencia en diseño de APIs</li>\r\n     <li>Consumo y documentación con Swagger</li>\r\n     <li>Test unitarios y de integración</li>\r\n     <li>Requiere conocimiento en <i>hooks y context API</i></li>\r\n   </ul>', 'CodeRise', 'Remoto', 'remoto', 'completa', '10 a 18hs', 4, 'https://coderise.dev/apply', 'busquedas@coderise.dev', NULL, '2025-08-12', 1, 'aprobado', '2025-06-01 20:00:00', NULL, NULL, 12),
(95, 'Desarrollador Python con Flask', '<p><b>Proyecto en ONG tecnológica</b></p>\r\n   <ul>\r\n     <li>Backend en Python + Flask</li>\r\n     <li>Conexión con PostgreSQL</li>\r\n     <li>Despliegue en Heroku</li>\r\n     <li><i>3 años de experiencia</i></li>\r\n   </ul>', 'Tech4Good', 'Capital Federal', 'presencial', 'parcial', '10 a 16hs', 3, NULL, 'flaskjobs@tech4good.org', NULL, '2024-12-10', 0, 'aprobado', '2024-10-15 16:45:00', NULL, NULL, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('estudiante','profesor','administrativo') NOT NULL DEFAULT 'estudiante',
  `activo` tinyint(1) DEFAULT 1,
  `estado_aprobacion` enum('pendiente','aprobado','rechazado') DEFAULT 'aprobado',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expira` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `password`, `rol`, `activo`, `estado_aprobacion`, `reset_token`, `reset_expira`) VALUES
(1, 'Ana', 'Gómez', 'ana.admin@example.com', '$2y$12$iIMqF2MhNaL6uQ1RgfmoL.Gwj4JxnWpDBsWOsPwT7Vfep1tBzPO9O', 'administrativo', 1, 'aprobado', NULL, NULL),
(2, 'Juan', 'Pérez', 'juan.estudiante@example.com', '$2y$12$iIMqF2MhNaL6uQ1RgfmoL.Gwj4JxnWpDBsWOsPwT7Vfep1tBzPO9O', 'estudiante', 1, 'aprobado', NULL, NULL),
(3, 'Lucía', 'Fernández', 'lucia.estudiante@example.com', '$2y$12$iIMqF2MhNaL6uQ1RgfmoL.Gwj4JxnWpDBsWOsPwT7Vfep1tBzPO9O', 'profesor', 0, 'rechazado', NULL, NULL),
(4, 'Carlos', 'López', 'carlos.profesor@example.com', '$2y$12$iIMqF2MhNaL6uQ1RgfmoL.Gwj4JxnWpDBsWOsPwT7Vfep1tBzPO9O', 'profesor', 1, 'rechazado', NULL, NULL),
(8, 'Ludmila', 'Vergini', 'ludmila@example.com', '$2y$12$7kpjeTg2Xpv/QOZ3wlJUIe7oeWzXvDiLyLbLHcNXksXLU4LyyNYw6', 'estudiante', 1, 'aprobado', NULL, NULL),
(9, 'Ezequiel', 'Maranda', 'ezequiel@ejemplo.com', '$2y$10$n.OrMaeyJfcW5.BSof0mguteew31Uk5AQon72qFaCcbnEGO5UDFzO', 'estudiante', 1, 'aprobado', NULL, NULL),
(10, 'Mara', 'Amitraon', 'mara@ejemplo.com', '$2y$12$OjzinsjC8N7YwZjtaT3j/uyeUNKEUvXqtsvxkvNkYJiS4g49/k4B2', 'estudiante', 1, 'rechazado', NULL, NULL),
(11, 'Julieta', 'Rodríguez', 'julieta@ejemplo.com', '$2y$12$gTqLlq5fXdWViWLDdpAzZuQ8e2MIVOl9tKvb22ysrA2u3WFGZBm2y', 'estudiante', 1, 'rechazado', NULL, NULL),
(12, 'Lucía', 'Fernández', 'lucia@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 1, 'aprobado', NULL, NULL),
(13, 'Martín', 'Gómez', 'martin@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 0, 'aprobado', NULL, NULL),
(14, 'Ana', 'Pérez', 'ana@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 1, 'aprobado', NULL, NULL),
(15, 'Carlos', 'López', 'carlos@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'estudiante', 0, 'aprobado', NULL, NULL),
(16, 'Sofía', 'Martínez', 'sofia@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 1, 'rechazado', NULL, NULL),
(17, 'Diego', 'Ramírez', 'diego@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 0, 'aprobado', NULL, NULL),
(18, 'Carla', 'Sosa', 'carla@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'administrativo', 1, 'pendiente', NULL, NULL),
(19, 'Juan', 'Rojas', 'juan@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'estudiante', 1, 'pendiente', NULL, NULL),
(20, 'Micaela', 'Domínguez', 'mica@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'profesor', 1, 'aprobado', NULL, NULL),
(21, 'Tomás', 'Herrera', 'tomas@example.com', '$2y$10$HcI8/RPjU8j7C6iAX1.Bc.M6xYyJmDn3V4cQ6I4bn/1Z7ep3M9FOS', 'administrativo', 1, 'aprobado', NULL, NULL),
(22, 'prueba', 'registro', 'registro@ejemplo.com', '$2y$12$ZsDXolYHf2EdeVM2A76H3.HNrcg/WUXrpOpIs7IgsE1SAjMS4rME6', 'estudiante', 1, 'aprobado', NULL, NULL),
(23, 'prueba2', 'pru', 'registro2@ejemplo.com', '$2y$12$UzP1cuCtdzhyjXXzRqnEvunUEsTipT8h99lTUTUZZpswL8G92ke0W', 'estudiante', 1, 'rechazado', NULL, NULL),
(24, 'prueba3', 'ld', 'registro3@ejemplo.com', '$2y$12$MK9f/zUpPLJYKp59ysz/ruTwZz.ii/sDDSL2G2wrpefnjQYP/Z2O6', 'profesor', 1, 'aprobado', NULL, NULL),
(25, 'prueba4', 'ddd', 'registro4@ejemplo.com', '$2y$12$fYYzuCgX0MsmkwKjHLGcC.qKGsEx3FMEv5iy11rnoZfKt9ggAJivW', 'estudiante', 1, 'aprobado', NULL, NULL),
(26, 'registro5', 'dd', 'registro5@ejemplo.com', '$2y$12$3L3J3XXqOC16FGfYeSyYhejcxZnbDwcnu0cWL/rl9Md.EIhDncFpa', 'profesor', 0, 'aprobado', NULL, NULL),
(27, 'prueba10', 'd', 'registro10@ejemplo.com', '$2y$12$K55zqyidV1MCTiA/qoRHxeoZ5nBRLYmaxe9XnbkBQ/s7.vf4DSILi', 'profesor', 1, 'aprobado', NULL, NULL),
(28, 'ludmila', 'vergini', 'lvergini@gmail.com', '$2y$10$VzAtiryfewFrqTvj/psoQeOIlQYyfTzshSIutGTIOvwA6VuRvmzSm', 'estudiante', 1, 'aprobado', NULL, NULL),
(29, 'Ludmila', 'Verigni', 'ludmila.vergini@bue.edu.ar', '$2y$12$TyliE6Zz.A.Te7U3QtcUJ.paTDQ1Dz94YHsS/s46FdepFLHdXMIhq', 'estudiante', 1, 'aprobado', NULL, NULL),
(30, 'prueba aprobacion', 'aprobacion', 'prueba@aprobacion.com', '$2y$12$o/EUH70MjRj6qCW5UfKikOyNxY3oI.yEEiFd2.Ymdd8N2OecoSnLa', 'estudiante', 1, 'rechazado', NULL, NULL),
(31, 'julieta', 'Vergini', 'ludmila@ejemplo.com', '$2y$12$UoH8j8QdaoNbzhorFI1yz.8z063RV0wkf/c9M06nz1.zq8d6vtJb2', 'estudiante', 1, 'pendiente', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clave` (`clave`);

--
-- Indices de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD PRIMARY KEY (`id_oferta`),
  ADD KEY `fk_publicada_por` (`publicada_por`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `configuracion_sistema`
--
ALTER TABLE `configuracion_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ofertas`
--
ALTER TABLE `ofertas`
  MODIFY `id_oferta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `ofertas`
--
ALTER TABLE `ofertas`
  ADD CONSTRAINT `fk_publicada_por` FOREIGN KEY (`publicada_por`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
