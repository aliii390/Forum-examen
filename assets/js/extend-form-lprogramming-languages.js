// code pour aggrandir le formulaire de question quand on choisit "Programmation"
function extendFormProgrammingLanguage() {
    const categorySelect = document.getElementById('publication_category'); 
    const langageField = document.getElementById('langage-field');

    // Debug pour voir si les éléments sont trouvés
    //console.log('Category Select:', categorySelect);
    //console.log('Langage Field:', langageField);

    if (!categorySelect || !langageField) {
        console.error('Éléments non trouvés:', {
            categorySelect: !!categorySelect,
            langageField: !!langageField
        });
        return;
    }

    function handleCategoryChange() {
        const selectedValue = categorySelect.value;
        console.log('Catégorie sélectionnée:', selectedValue);

        // Vérifiez si la valeur correspond à la catégorie "Programmation"
        if (selectedValue === '3') { 
            langageField.style.display = 'block';
        } else {
            langageField.style.display = 'none';
        }
    }

    categorySelect.addEventListener('change', handleCategoryChange);
    handleCategoryChange(); 
}


// Écouteur pour le chargement initial
document.addEventListener('DOMContentLoaded', extendFormProgrammingLanguage);

// Écouteur pour les navigations Turbo
document.addEventListener('turbo:load', extendFormProgrammingLanguage);