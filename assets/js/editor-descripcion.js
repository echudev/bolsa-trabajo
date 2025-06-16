/**
 * Editor de texto enriquecido para el campo "Descripción" de la oferta.
 * Este script permite aplicar formatos básicos al texto del editor visual
 * (negrita, cursiva, subrayado, lista).
 * 
 * Al enviar el formulario, el contenido HTML generado en el editor visual
 * se copia al campo <textarea hidden> que será enviado al servidor.
 */

/**
 * Aplica un comando de formato al texto seleccionado en el editor.
 * 
 * @param {string} comando - Nombre del comando a aplicar (bold, italic, underline, insertUnorderedList).
 */
function formato(comando) {
    document.execCommand(comando, false, null);
    actualizarBotones();
}
document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('editor');

    /**
     * Aplica un comando de formato al contenido del editor.
     * 
     * @param {string} comando - Comando de formato a ejecutar (ej: 'bold', 'italic', etc.)
     */
    function formato(comando) {
        document.execCommand(comando, false, null);
        actualizarBotones();
    }

    /**
     * Actualiza la clase CSS 'activo' de los botones de la barra de herramientas
     * según el formato aplicado a la selección actual del editor.
     */
    function actualizarBotones() {
        const botones = document.querySelectorAll('.editor-toolbar button[data-comando]');
        botones.forEach(btn => {
            const comando = btn.getAttribute('data-comando');
            if (document.queryCommandState(comando)) {
                btn.classList.add('activo');
            } else {
                btn.classList.remove('activo');
            }
        });
    }

    /**
     * Inicializa los botones de la barra de herramientas.
     * Agrega un listener a cada botón para aplicar el comando correspondiente
     * y restaurar el foco en el editor tras el clic.
     */
    const botonesToolbar = document.querySelectorAll('.editor-toolbar button[data-comando]');
    botonesToolbar.forEach(btn => {
        btn.addEventListener('click', function(event) {
            event.preventDefault();  // Evita que el botón robe el foco
            const comando = btn.getAttribute('data-comando');
            formato(comando);

            // Restaurar el foco en el editor
            if (editor) {
                editor.focus();
            }
        });
    });

    /**
     * Listener global para actualizar los botones cuando cambia la selección de texto.
     */
    document.addEventListener('selectionchange', () => {
        const selection = window.getSelection();
        if (editor.contains(selection.anchorNode)) {
            actualizarBotones();
        }
    });

    /**
     * Sincroniza el contenido HTML del editor con el campo <textarea> oculto
     * al enviar el formulario.
     */
    document.querySelector('form').addEventListener('submit', function() {
        const editorHTML = editor.innerHTML.trim();
        document.getElementById('descripcion').value = editorHTML;
    });
});