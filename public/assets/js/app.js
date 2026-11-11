document.addEventListener('DOMContentLoaded', () => {
    // Modale de details : remplie a partir des attributs data-* du bouton cliqué.
    const modal = document.getElementById('trajetModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) {
                return;
            }
            for (const field of ['auteur', 'telephone', 'email', 'places']) {
                const target = modal.querySelector(`[data-field="${field}"]`);
                if (target) {
                    target.textContent = button.dataset[field] ?? '';
                }
            }
        });
    }

    // Demande de confirmation avant l'envoi d'un formulaire portant data-confirm.
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
});