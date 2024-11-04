document.addEventListener("DOMContentLoaded", function () {
    const createEventBtn = document.getElementById("createEventBtn");
    const eventModal = document.getElementById("eventModal");
    if (createEventBtn && eventModal) {
        const bootstrapModal = new bootstrap.Modal(eventModal);

        createEventBtn.addEventListener("click", () => {
            bootstrapModal.show();
        });
    }
    console.log("publisher.js cargado correctamente: funcionalidad de visualización activada");
});
