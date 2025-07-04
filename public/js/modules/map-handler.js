export class MapHandler {
    constructor() {
        this.mapaModal = document.getElementById('mapa-modal');
        this.mapaContainer = document.getElementById('mapa-modal-container');
        this.direccionCompleta = document.getElementById('direccion-modal-completa');
        this.map = null;
        this.marker = null;
    }

    async initializeMap(address) {
        if (!this.mapaContainer) return;

        try {
            const coordinates = await this.geocodeAddress(address);
            
            this.map = new google.maps.Map(this.mapaContainer, {
                center: coordinates,
                zoom: 16,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                mapTypeControl: false,
                fullscreenControl: true,
                streetViewControl: true,
                zoomControl: true
            });

            this.marker = new google.maps.Marker({
                position: coordinates,
                map: this.map,
                title: address,
                animation: google.maps.Animation.DROP
            });

            // Agregar círculo de radio
            new google.maps.Circle({
                strokeColor: '#1a73e8',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                fillColor: '#1a73e8',
                fillOpacity: 0.1,
                map: this.map,
                center: coordinates,
                radius: 100 // Radio en metros
            });
        } catch (error) {
            console.error('Error al inicializar el mapa:', error);
            alert('Error al cargar el mapa. Por favor, intente nuevamente.');
        }
    }

    async geocodeAddress(address) {
        return new Promise((resolve, reject) => {
            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({ address }, (results, status) => {
                if (status === 'OK') {
                    resolve(results[0].geometry.location);
                } else {
                    reject(new Error('Error al geocodificar la dirección'));
                }
            });
        });
    }

    abrirModalMapa(address) {
        if (!this.mapaModal || !address) return;

        this.mapaModal.classList.remove('hidden');
        this.direccionCompleta.textContent = address;
        this.initializeMap(address);
    }

    cerrarModalMapa() {
        if (!this.mapaModal) return;
        this.mapaModal.classList.add('hidden');
        if (this.map) {
            this.map = null;
            this.marker = null;
        }
    }

    cambiarVistaMapaModal(tipo) {
        if (!this.map) return;
        this.map.setMapTypeId(google.maps.MapTypeId[tipo.toUpperCase()]);
    }

    copiarDireccion() {
        if (!this.direccionCompleta) return;
        
        const direccion = this.direccionCompleta.textContent;
        navigator.clipboard.writeText(direccion)
            .then(() => alert('Dirección copiada al portapapeles'))
            .catch(err => {
                console.error('Error al copiar:', err);
                alert('Error al copiar la dirección');
            });
    }
} 