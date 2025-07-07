    <script>
        // Inicializar el handler cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            initEstadoTramiteHandler({{ $tramite->id }});
        });

        async function reagendarCita(tramiteId) {
            try {
                // Obtener el siguiente día hábil disponible
                const response = await fetch(`/citas/siguiente-dia-disponible/${tramiteId}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success && data.fecha_disponible) {
                    // Mostrar modal de confirmación con la fecha disponible
                    if (confirm(`¿Desea reagendar su cita para el día ${data.fecha_disponible}?`)) {
                        // Realizar la reagendación
                        const reagendarResponse = await fetch(`/citas/reagendar/${tramiteId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                fecha_hora: data.fecha_disponible
                            })
                        });

                        const reagendarData = await reagendarResponse.json();

                        if (reagendarData.success) {
                            alert('Cita reagendada exitosamente');
                            window.location.reload();
                        } else {
                            alert(reagendarData.message || 'Error al reagendar la cita');
                        }
                    }
                } else {
                    alert(data.message || 'No hay fechas disponibles para reagendar');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error al procesar la solicitud');
            }
        }

        function updateCitaCountdown() {
            const fechaCita = new Date('{{ $tramite->cita->fecha_hora }}');
            const now = new Date();
            const difference = fechaCita - now;

            if (difference <= 0) {
                document.getElementById('countdown-days').textContent = '0';
                document.getElementById('countdown-hours').textContent = '00';
                document.getElementById('countdown-minutes').textContent = '00';
                return;
            }

            const days = Math.floor(difference / (1000 * 60 * 60 * 24));
            const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));

            document.getElementById('countdown-days').textContent = days;
            document.getElementById('countdown-hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('countdown-minutes').textContent = minutes.toString().padStart(2, '0');
        }

        // Actualizar cada minuto
        setInterval(updateCitaCountdown, 60000);
        updateCitaCountdown();

        function toggleDocumentosModal() {
            const modal = document.getElementById('modal-documentos');
            modal.classList.toggle('hidden');
        }
    </script> 