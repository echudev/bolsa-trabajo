
// Script para guardar y restaurar scroll 

// Clave única para esta vista
const SCROLL_KEY = "scrollData_" + location.pathname;
const SCROLL_TIMEOUT = 60000; // 60 segundos

// Guardar posición y hora antes de abandonar
window.addEventListener("beforeunload", function () {
    const scrollData = {
        y: window.scrollY,
        time: Date.now()
    };
    sessionStorage.setItem(SCROLL_KEY, JSON.stringify(scrollData));
});

// Restaurar si no pasó demasiado tiempo
window.addEventListener("load", function () {
    const stored = sessionStorage.getItem(SCROLL_KEY);
    if (stored) {
        const { y, time } = JSON.parse(stored);
        const now = Date.now();
        if (now - time <= SCROLL_TIMEOUT) {
            window.scrollTo(0, y);
        }
        sessionStorage.removeItem(SCROLL_KEY);
    }
});
