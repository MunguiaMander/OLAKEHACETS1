function showAttendeesModal(eventId) {
    const attendeesList = document.getElementById('attendeesList');
    attendeesList.innerHTML = '';
    fetch(`/publisher/event/${eventId}/attendees`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            if (data.length === 0) {
                const listItem = document.createElement('li');
                listItem.classList.add('list-group-item', 'text-center');
                listItem.textContent = 'No hay asistentes registrados para este evento.';
                attendeesList.appendChild(listItem);
            } else {
                data.forEach(attendee => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item');
                    listItem.innerHTML = `<strong>${attendee.name}</strong> - ${attendee.email}`;
                    attendeesList.appendChild(listItem);
                });
            }
            $('#attendeesModal').modal('show');
        })
        .catch(error => console.error('Error al cargar asistentes:', error));
}

document.addEventListener('DOMContentLoaded', function () {
    const viewButtons = document.querySelectorAll('button[onclick^="showAttendeesModal"]');
    viewButtons.forEach(button => {
        button.addEventListener('click', function () {
            const eventId = this.getAttribute('onclick').match(/\d+/)[0];
            showAttendeesModal(eventId);
        });
    });
});
