function initializeHighlight() {
    if (typeof hljs !== 'undefined') {
        hljs.highlightAll();
    }
}

// Écouteur pour le chargement initial
document.addEventListener('DOMContentLoaded', initializeHighlight);

// Écouteur pour les navigations Turbo
document.addEventListener('turbo:load', initializeHighlight);