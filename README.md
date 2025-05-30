# WP WhatsApp Connect

Un plugin sencillo de WordPress que añade un botón flotante de WhatsApp con seguimiento de clics y soporte para múltiples agentes.

## Descripción

**WP WhatsApp Connect** permite a los usuarios de tu sitio web contactarte a través de WhatsApp con un botón flotante personalizable. El plugin incluye las siguientes características:

- **Botón flotante de WhatsApp**: Muestra un botón en tu sitio que abre un chat de WhatsApp con un mensaje predeterminado.
- **Soporte para múltiples agentes**: Configura diferentes números de WhatsApp para distintos departamentos (por ejemplo, Ventas, Soporte, etc.).
- **Seguimiento de clics**: Registra los clics en el botón y genera estadísticas detalladas, incluyendo clics por fecha, IP y departamento.
- **Personalización del diseño**: Ajusta el color, posición y tamaño del botón desde los ajustes.
- **Control de visibilidad**: Decide dónde mostrar el botón (en todo el sitio, excluyendo ciertas páginas, o solo en páginas específicas).
- **Efecto de pulso**: Añade un efecto visual de pulso al botón para hacerlo más atractivo.

Este plugin es ideal para pequeñas y medianas empresas que desean ofrecer una forma rápida y directa de contacto a sus clientes a través de WhatsApp.

## Instalación

1. **Descarga el plugin**:
   - Descarga el archivo ZIP del plugin desde el repositorio o tu fuente preferida.

2. **Sube el plugin a WordPress**:
   - Ve a **Plugins > Añadir nuevo** en tu panel de administración de WordPress.
   - Haz clic en **Subir plugin** y selecciona el archivo ZIP descargado.
   - Haz clic en **Instalar ahora**.

3. **Activa el plugin**:
   - Una vez instalado, haz clic en **Activar plugin**.

4. **Configura el plugin**:
   - Ve a **WhatsApp Connect > Ajustes** en el menú de administración de WordPress para configurar el plugin (más detalles en la sección de Uso).

## Requisitos

- **WordPress**: Versión 5.0 o superior.
- **PHP**: Versión 7.4 o superior.
- **MySQL**: Compatible con WordPress (versión 5.6 o superior recomendada).
- **Acceso a internet**: Para que el botón enlace a WhatsApp y las estadísticas funcionen correctamente.

## Uso

### Configuración Inicial
1. Ve a **WhatsApp Connect > Ajustes**.
2. Configura las opciones básicas:
   - **Activar Plugin**: Habilita o deshabilita el botón.
   - **Mostrar en Móviles**: Decide si el botón se muestra en dispositivos móviles.
   - **Mensaje Predeterminado**: Define el mensaje que se enviará por WhatsApp (por ejemplo, "¡Hola, necesito ayuda!").
   - **Título y Subtítulo del Selector**: Personaliza el texto que aparece en el selector de departamentos (si tienes múltiples agentes).

### Añadir Agentes
1. En la sección **Agentes**, añade uno o más agentes:
   - **Departamento**: Nombre del departamento (ejemplo: "Ventas").
   - **Número**: Número de WhatsApp (ejemplo: "+1234567890").
2. Usa los botones **Añadir Agente** y **Eliminar** para gestionar los agentes.

### Personalización del Diseño
1. Ajusta el diseño del botón en los ajustes:
   - **Color del Botón**: Selecciona el color de fondo del botón.
   - **Color de Fondo del Selector**: Cambia el fondo del selector de departamentos.
   - **Posición del Botón**: Elige entre "Abajo-Derecha" o "Abajo-Izquierda".
   - **Tamaño del Ícono**: Selecciona entre "Pequeño (40px)", "Mediano (60px)" o "Grande (80px)".

### Control de Visibilidad
1. Configura dónde se muestra el botón:
   - **Visibilidad del Botón**: Elige entre:
     - "Mostrar en todo el sitio".
     - "Excluir en ciertas páginas".
     - "Mostrar solo en ciertas páginas".
   - **Páginas Afectadas**: Selecciona las páginas donde se aplicará la regla de visibilidad (mantén presionado Ctrl/Cmd para seleccionar múltiples páginas).

### Ver Estadísticas
1. Ve a **WhatsApp Connect > Reportes** para ver las estadísticas de clics.
2. Selecciona entre reportes **Diarios** o **Mensuales**.
3. Descarga los reportes en formato CSV haciendo clic en **Exportar a CSV**.

## Preguntas Frecuentes

### ¿El plugin funciona en dispositivos móviles?
Sí, puedes habilitar o deshabilitar la visibilidad en dispositivos móviles desde los ajustes.

### ¿Puedo usar el plugin con varios departamentos?
Sí, el plugin soporta múltiples agentes. Puedes añadir diferentes números de WhatsApp para distintos departamentos.

### ¿Qué pasa si no configuro ningún agente?
Si no configuras agentes, se mostrará un mensaje de error en el frontend pidiéndote que añadas al menos un número de WhatsApp.

### ¿El plugin es compatible con plugins de caché?
El plugin debería funcionar bien con la mayoría de los plugins de caché, pero asegúrate de excluir la ruta `/wp-admin/admin-ajax.php` para evitar problemas con el registro de clics.

## Solución de Problemas

- **El botón no aparece**:
  - Verifica que el plugin esté activado en los ajustes.
  - Revisa las opciones de visibilidad para asegurarte de que el botón no esté excluido en la página actual.
  - Confirma que hay al menos un agente configurado.
- **Los clics no se registran**:
  - Asegúrate de que no haya plugins de caché interfiriendo con las solicitudes AJAX.
  - Revisa el archivo `wp-content/debug.log` para errores relacionados con la base de datos.
- **Las estadísticas muestran fechas incorrectas**:
  - Asegúrate de que tu servidor tenga la zona horaria configurada correctamente en `wp-config.php` o los ajustes de WordPress.

## Estructura del Plugin

- `wp-whatsapp-connect.php`: Archivo principal del plugin.
- `includes/frontend/class-wpwc-button.php`: Maneja la lógica del botón flotante en el frontend.
- `includes/admin/class-wpwc-admin.php`: Gestiona la interfaz administrativa y los ajustes.
- `includes/utils/class-wpwc-tracking.php`: Controla el seguimiento de clics y las estadísticas.
- `assets/`: Contiene los archivos CSS, JS e imágenes (como `whatsapp-icon.png`).

## Licencia

Este plugin está licenciado bajo la [GPLv2 o posterior](https://www.gnu.org/licenses/gpl-2.0.html), compatible con WordPress.

## Créditos

Desarrollado por [santy77_ec] para [Web Systems].
