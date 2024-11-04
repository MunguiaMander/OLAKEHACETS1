document.addEventListener('DOMContentLoaded', function () {
    const toastMessage = document.body.getAttribute('data-toast-message');
    const toastType = document.body.getAttribute('data-toast-type');

    if (toastMessage) {
        const toastContainer = document.querySelector('.toast-container');
        const toastElement = document.getElementById('toastMessage');

        // Configura el tipo y mensaje del toast
        toastElement.classList.remove('bg-info', 'bg-success', 'bg-danger');
        toastElement.classList.add(toastType || 'bg-info');
        toastElement.querySelector('.toast-body').textContent = toastMessage;

        // Inicializa el toast con la duración de 3 segundos (3000 ms)
        const toast = new bootstrap.Toast(toastElement, { delay: 3000 });
        
        // Muestra el toast
        toast.show();
    }
});
