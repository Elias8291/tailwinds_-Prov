// Configuración de Google Maps
const GOOGLE_MAPS_CONFIG = {
    apiKey: 'AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs',
    version: 'weekly',
    libraries: ['places'],
    language: 'es',
    region: 'MX'
};

class GoogleMapsLoader {
    static instance = null;
    static loadPromise = null;

    static getInstance() {
        if (!GoogleMapsLoader.instance) {
            GoogleMapsLoader.instance = new GoogleMapsLoader();
        }
        return GoogleMapsLoader.instance;
    }

    async load() {
        if (GoogleMapsLoader.loadPromise) {
            return GoogleMapsLoader.loadPromise;
        }

        GoogleMapsLoader.loadPromise = new Promise((resolve, reject) => {
            // Si Google Maps ya está cargado, resolver inmediatamente
            if (window.google && window.google.maps) {
                resolve(window.google.maps);
                return;
            }

            // Cargar directamente desde Google CDN
            const script = document.createElement('script');
            script.type = 'text/javascript';
            const libraries = GOOGLE_MAPS_CONFIG.libraries.join(',');
            script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_CONFIG.apiKey}&libraries=${libraries}&v=${GOOGLE_MAPS_CONFIG.version}&language=${GOOGLE_MAPS_CONFIG.language}&region=${GOOGLE_MAPS_CONFIG.region}&callback=initGoogleMaps`;
            script.async = true;
            script.defer = true;

            // Configurar callback global
            window.initGoogleMaps = () => {
                if (window.google && window.google.maps) {
                    resolve(window.google.maps);
                } else {
                    reject(new Error('Google Maps no se cargó correctamente'));
                }
                // Limpiar el callback global después de usarlo
                delete window.initGoogleMaps;
            };

            // Manejar errores
            script.onerror = (error) => {
                GoogleMapsLoader.loadPromise = null;
                console.error('Error cargando Google Maps:', error);
                
                // Verificar si el error es por bloqueador
                if (error.target.src.includes('maps.googleapis.com')) {
                    const viewer = new GoogleMapsViewer();
                    viewer.showBlockedMessage();
                }
                
                reject(new Error('No se pudo cargar Google Maps'));
            };

            document.head.appendChild(script);
        });

        return GoogleMapsLoader.loadPromise;
    }
}

class AddressFieldsFinder {
    constructor(formElement) {
        this.formElement = formElement;
    }

    getFieldText(labelText) {
        const fieldGroup = Array.from(this.formElement.querySelectorAll('.form-group')).find(group => {
            const label = group.querySelector('label');
            return label && label.textContent.trim() === labelText;
        });
        
        if (!fieldGroup) return null;
        
        const valueDiv = fieldGroup.querySelector('.bg-gray-50');
        return valueDiv ? valueDiv.textContent.trim() : null;
    }

    findFields() {
        if (!this.formElement) {
            throw new Error('El formulario no está disponible');
        }

        const fields = {
            estado: this.getFieldText('Estado'),
            municipio: this.getFieldText('Municipio'),
            asentamiento: this.getFieldText('Asentamiento'),
            calle: this.getFieldText('Calle'),
            numeroExterior: this.getFieldText('Número Exterior'),
            numeroInterior: this.getFieldText('Número Interior'),
            codigoPostal: this.getFieldText('Código Postal')
        };

        const requiredFields = ['estado', 'municipio', 'asentamiento', 'calle', 'numeroExterior'];
        const missingFields = requiredFields.filter(field => !fields[field]);

        if (missingFields.length > 0) {
            throw new Error(`Campos requeridos no encontrados: ${missingFields.join(', ')}`);
        }

        return fields;
    }

    formatAddress(fields) {
        const numeroCompleto = fields.numeroInterior 
            ? `${fields.numeroExterior} Int. ${fields.numeroInterior}`
            : fields.numeroExterior;

        return `${fields.calle} ${numeroCompleto}, ${fields.asentamiento}, ${fields.municipio}, ${fields.estado}, ${fields.codigoPostal || ''}, México`;
    }
}

class GoogleMapsViewer {
    constructor(mapRef, streetViewRef, mapLoaderRef, locationInfoRef, locationDetailsRef) {
        this.mapRef = mapRef;
        this.streetViewRef = streetViewRef;
        this.mapLoaderRef = mapLoaderRef;
        this.locationInfoRef = locationInfoRef;
        this.locationDetailsRef = locationDetailsRef;
        this.map = null;
        this.streetView = null;
        this.marker = null;
        this.isStreetViewVisible = false;
        this.mapType = 'roadmap';
        this.geocoder = null;
        this.placesService = null;
        this.nearbyStreets = [];
        this.circle = null;
        this.addressValidation = {
            isValid: false,
            messages: []
        };
        this.addressFinder = null;
    }

    showBlockedMessage() {
        if (this.mapRef) {
            const message = document.createElement('div');
            message.className = 'p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700';
            message.innerHTML = `
                <h3 class="font-bold mb-2">Google Maps está bloqueado</h3>
                <p class="mb-2">Parece que un bloqueador de anuncios está impidiendo cargar Google Maps.</p>
                <p class="mb-2">Para ver el mapa, por favor:</p>
                <ol class="list-decimal list-inside">
                    <li class="mb-1">Desactive temporalmente su bloqueador de anuncios para este sitio</li>
                    <li class="mb-1">Recargue la página</li>
                </ol>
            `;
            this.mapRef.appendChild(message);
        }
    }

    async initialize() {
        if (!this.mapRef) {
            console.error('Referencias del mapa no disponibles');
            return;
        }

        try {
            this.showLoading(true);

            // Inicializar el buscador de direcciones
            const formulario = this.mapRef.closest('.grid').querySelector('[x-ref="formulario"]');
            this.addressFinder = new AddressFieldsFinder(formulario);

            // Cargar Google Maps
            await GoogleMapsLoader.getInstance().load();

            // Inicializar componentes del mapa
            await this.initializeMapComponents();

            // Cargar la dirección del formulario
            await this.loadAddressFromForm();

            this.showLoading(false);
        } catch (error) {
            console.error('Error al inicializar el mapa:', error);
            this.showError('No se pudo cargar el mapa. Por favor, inténtelo de nuevo.');
            this.showLoading(false);
        }
    }

    async initializeMapComponents() {
        // Inicializar Street View
        this.streetView = new google.maps.StreetViewPanorama(this.streetViewRef, {
            position: { lat: 19.4326, lng: -99.1332 },
            pov: { heading: 0, pitch: 0 },
            visible: false,
            addressControl: true,
            linksControl: true,
            panControl: true,
            enableCloseButton: true,
            fullscreenControl: true,
            motionTracking: true,
            motionTrackingControl: true,
            showRoadLabels: true
        });

        // Inicializar el mapa
        this.map = new google.maps.Map(this.mapRef, {
            zoom: 17,
            center: { lat: 19.4326, lng: -99.1332 },
            mapTypeControl: false,
            streetViewControl: true,
            fullscreenControl: true,
            zoomControl: true,
            streetView: this.streetView
        });

        this.geocoder = new google.maps.Geocoder();
        this.placesService = new google.maps.places.PlacesService(this.map);

        // Configurar eventos
        google.maps.event.addListener(this.streetView, 'visible_changed', () => {
            const isVisible = this.streetView.getVisible();
            this.streetViewRef.style.display = isVisible ? 'block' : 'none';
            this.mapRef.style.display = isVisible ? 'none' : 'block';
            this.isStreetViewVisible = isVisible;
        });
    }

    async loadAddressFromForm() {
        try {
            const fields = this.addressFinder.findFields();
            const address = this.addressFinder.formatAddress(fields);
            await this.searchAddress(address, fields);
        } catch (error) {
            console.error('Error al cargar la dirección:', error);
            this.showError('No se pudo cargar la dirección del formulario.');
        }
    }

    toggleStreetView() {
        if (!this.streetView) return;
        this.streetView.setVisible(!this.streetView.getVisible());
    }

    async findNearbyStreets(location) {
        return new Promise((resolve) => {
            const request = {
                location: location,
                radius: 100,
                types: ['route'],
                fields: ['name', 'geometry', 'formatted_address', 'place_id']
            };

            this.placesService.findPlaceFromQuery(request, (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    this.nearbyStreets = results;
                    resolve(results);
                } else {
                    console.warn('No se encontraron calles cercanas:', status);
                    resolve([]);
                }
            });
        });
    }

    async validateAddress(result, location, inputFields) {
        const validation = {
            isValid: true,
            messages: [],
            details: {}
        };

        try {
            // 1. Validar código postal
            const postalCodeComponent = result.address_components.find(
                component => component.types.includes('postal_code')
            );
            
            if (postalCodeComponent) {
                const matchesInput = postalCodeComponent.long_name === inputFields.codigoPostal;
                validation.details.postalCode = {
                    found: true,
                    matches: matchesInput,
                    expected: inputFields.codigoPostal,
                    actual: postalCodeComponent.long_name
                };
                
                if (!matchesInput) {
                    validation.isValid = false;
                    validation.messages.push(`El código postal no coincide (Esperado: ${inputFields.codigoPostal}, Encontrado: ${postalCodeComponent.long_name})`);
                }
            } else {
                validation.details.postalCode = { found: false };
                validation.messages.push('No se encontró el código postal en la dirección');
            }

            // 2. Validar estado y municipio
            const adminComponents = {
                estado: result.address_components.find(c => c.types.includes('administrative_area_level_1')),
                municipio: result.address_components.find(c => c.types.includes('administrative_area_level_2'))
            };

            for (const [type, component] of Object.entries(adminComponents)) {
                if (component) {
                    const inputValue = inputFields[type].toLowerCase();
                    const foundValue = component.long_name.toLowerCase();
                    const matches = foundValue.includes(inputValue) || inputValue.includes(foundValue);
                    
                    validation.details[type] = {
                        found: true,
                        matches: matches,
                        expected: inputFields[type],
                        actual: component.long_name
                    };

                    if (!matches) {
                        validation.isValid = false;
                        validation.messages.push(`El ${type} no coincide (Esperado: ${inputFields[type]}, Encontrado: ${component.long_name})`);
                    }
                } else {
                    validation.details[type] = { found: false };
                    validation.messages.push(`No se encontró el ${type} en la dirección`);
                }
            }

            // 3. Buscar lugares cercanos importantes
            const nearbyPlaces = await this.findNearbyPlaces(location);
            validation.details.nearbyPlaces = nearbyPlaces;

            // 4. Verificar si la dirección es residencial
            const isResidential = result.types.some(type => 
                ['premise', 'street_address', 'subpremise', 'residential'].includes(type)
            );
            validation.details.isResidential = isResidential;
            if (!isResidential) {
                validation.messages.push('La dirección no parece ser residencial');
            }

            // 5. Verificar precisión de la geolocalización
            const locationTypes = result.geometry.location_type;
            validation.details.precision = {
                type: locationTypes,
                isHighPrecision: locationTypes === 'ROOFTOP'
            };
            
            if (locationTypes !== 'ROOFTOP') {
                validation.messages.push('La ubicación no es precisa a nivel de dirección exacta');
            }

            return validation;
        } catch (error) {
            console.error('Error en la validación:', error);
            return {
                isValid: false,
                messages: ['Error al validar la dirección'],
                details: {}
            };
        }
    }

    async findNearbyPlaces(location) {
        const places = [];
        const searchTypes = [
            { type: ['police'], label: 'Estación de policía' },
            { type: ['hospital'], label: 'Hospital' },
            { type: ['school'], label: 'Escuela' },
            { type: ['fire_station'], label: 'Estación de bomberos' },
            { type: ['pharmacy'], label: 'Farmacia' }
        ];

        for (const { type, label } of searchTypes) {
            try {
                const request = {
                    location: location,
                    radius: 1000,
                    types: type,
                    fields: ['name', 'geometry', 'formatted_address', 'place_id']
                };

                const results = await new Promise((resolve, reject) => {
                    this.placesService.findPlaceFromQuery(request, (results, status) => {
                        if (status === google.maps.places.PlacesServiceStatus.OK) {
                            resolve(results);
                        } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                            resolve([]);
                        } else {
                            reject(new Error(`Error al buscar lugares cercanos: ${status}`));
                        }
                    });
                });

                if (results.length > 0) {
                    places.push({
                        type: label,
                        count: results.length,
                        nearest: results[0]
                    });
                }
            } catch (error) {
                console.warn(`Error al buscar ${label}:`, error);
            }
        }

        return places;
    }

    showValidationStatus(validation) {
        const statusClasses = validation.isValid 
            ? 'bg-green-100 text-green-800 border-green-200'
            : 'bg-yellow-100 text-yellow-800 border-yellow-200';

        let html = `
            <div class="mb-4 p-4 rounded-lg border ${statusClasses}">
                <h3 class="font-semibold mb-2">
                    ${validation.isValid ? '✓ Dirección Válida' : '⚠ Atención Requerida'}
                </h3>
        `;

        if (validation.messages.length > 0) {
            html += '<ul class="list-disc list-inside space-y-1">';
            validation.messages.forEach(message => {
                html += `<li>${message}</li>`;
            });
            html += '</ul>';
        }

        // Detalles adicionales
        if (validation.details) {
            html += '<div class="mt-3 pt-3 border-t border-gray-200">';
            
            // Código Postal
            if (validation.details.postalCode) {
                const pc = validation.details.postalCode;
                if (pc.found) {
                    html += `
                        <div class="flex items-center gap-2 mb-2">
                            <span class="${pc.matches ? 'text-green-600' : 'text-yellow-600'}">
                                ${pc.matches ? '✓' : '⚠'} Código Postal
                            </span>
                        </div>
                    `;
                }
            }

            // Precisión de la ubicación
            if (validation.details.precision) {
                const prec = validation.details.precision;
                html += `
                    <div class="flex items-center gap-2 mb-2">
                        <span class="${prec.isHighPrecision ? 'text-green-600' : 'text-yellow-600'}">
                            ${prec.isHighPrecision ? '✓' : '⚠'} Precisión: ${prec.isHighPrecision ? 'Alta' : 'Media'}
                        </span>
                    </div>
                `;
            }

            // Tipo de dirección
            if (validation.details.isResidential !== undefined) {
                html += `
                    <div class="flex items-center gap-2 mb-2">
                        <span class="${validation.details.isResidential ? 'text-green-600' : 'text-yellow-600'}">
                            ${validation.details.isResidential ? '✓' : '⚠'} 
                            ${validation.details.isResidential ? 'Dirección Residencial' : 'Dirección No Residencial'}
                        </span>
                    </div>
                `;
            }

            html += '</div>';
        }

        html += '</div>';
        return html;
    }

    updateLocationInfo(result, location, inputFields) {
        if (!this.locationInfoRef || !this.locationDetailsRef) return;

        const formattedAddress = result.formatted_address;
        const placeType = result.types[0].replace(/_/g, ' ');
        const coordinates = `${location.lat().toFixed(6)}, ${location.lng().toFixed(6)}`;

        // Dibujar círculo de 100m alrededor del punto
        if (this.circle) {
            this.circle.setMap(null);
        }
        this.circle = new google.maps.Circle({
            map: this.map,
            center: location,
            radius: 100,
            fillColor: '#9d244933',
            fillOpacity: 0.2,
            strokeColor: '#9d2449',
            strokeWeight: 1
        });

        this.validateAddress(result, location, inputFields).then(validation => {
            this.addressValidation = validation;

            this.locationInfoRef.style.display = 'block';
            this.locationInfoRef.className = 'absolute bottom-4 right-4 bg-white/95 backdrop-blur-sm rounded-lg shadow-lg w-[350px] transition-all duration-300 ease-in-out';
            
            const validationStatusHtml = this.showValidationStatus(validation);
            
            // Separar la dirección en componentes
            const addressParts = result.address_components || [];
            const streetNumber = addressParts.find(part => part.types.includes('street_number'))?.long_name || '';
            const route = addressParts.find(part => part.types.includes('route'))?.long_name || '';
            const sublocality = addressParts.find(part => part.types.includes('sublocality'))?.long_name || '';
            const locality = addressParts.find(part => part.types.includes('locality'))?.long_name || '';
            const area1 = addressParts.find(part => part.types.includes('administrative_area_level_1'))?.long_name || '';
            const area2 = addressParts.find(part => part.types.includes('administrative_area_level_2'))?.long_name || '';
            const postalCode = addressParts.find(part => part.types.includes('postal_code'))?.long_name || '';

            this.locationDetailsRef.innerHTML = `
                <div x-data="{ isExpanded: true }" class="text-sm">
                    <!-- Encabezado con botón para minimizar -->
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-t-lg border-b cursor-pointer"
                         @click="isExpanded = !isExpanded">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-map-marker-alt text-[#9d2449]"></i>
                            <h3 class="font-semibold text-gray-700">Detalles de Ubicación</h3>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600 focus:outline-none transform transition-transform duration-200"
                                :class="{ 'rotate-180': !isExpanded }">
                            <i class="fas fa-chevron-up"></i>
                        </button>
                    </div>

                    <!-- Contenido colapsable -->
                    <div x-show="isExpanded"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform -translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 transform translate-y-0"
                         x-transition:leave-end="opacity-0 transform -translate-y-2"
                         class="p-3 space-y-4 max-h-[calc(100vh-16rem)] overflow-y-auto">
                        
                        <!-- Dirección -->
                        <div class="space-y-1">
                            <div class="font-medium text-[#9d2449] text-xs uppercase tracking-wide">Dirección</div>
                            ${route ? `
                                <div class="font-medium">${route} ${streetNumber}</div>
                            ` : ''}
                            ${sublocality ? `
                                <div class="text-gray-600 text-sm">Col. ${sublocality}</div>
                            ` : ''}
                            <div class="text-gray-600 text-sm">
                                ${locality}${area2 ? `, ${area2}` : ''}
                            </div>
                            <div class="text-gray-600 text-sm">
                                ${area1}${postalCode ? `, CP ${postalCode}` : ''}
                            </div>
                        </div>

                        <!-- Coordenadas -->
                        <div class="space-y-1 border-t pt-2">
                            <div class="font-medium text-[#9d2449] text-xs uppercase tracking-wide">Ubicación</div>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div class="text-gray-600">
                                    <span class="font-medium">Tipo:</span><br/>
                                    <span class="capitalize">${placeType}</span>
                                </div>
                                <div class="text-gray-600">
                                    <span class="font-medium">Coordenadas:</span><br/>
                                    <span class="font-mono text-xs">${coordinates}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Estado de validación -->
                        <div class="border-t pt-2">
                            ${validationStatusHtml}
                        </div>
                    </div>
                </div>
            `;

            // Mostrar mensajes de validación si hay errores
            if (!validation.isValid) {
                const errorContainer = document.createElement('div');
                errorContainer.className = 'p-3 bg-red-50 rounded-b-lg border-t border-red-200';
                errorContainer.innerHTML = `
                    <div class="flex items-center space-x-2 text-red-600 mb-2">
                        <i class="fas fa-exclamation-triangle text-sm"></i>
                        <h3 class="font-medium text-sm">Advertencias</h3>
                    </div>
                    <ul class="space-y-1 pl-6">
                        ${validation.messages.map(msg => `
                            <li class="text-xs text-red-600 flex items-start space-x-2">
                                <span class="mt-1">•</span>
                                <span>${msg}</span>
                            </li>
                        `).join('')}
                    </ul>
                `;
                this.locationDetailsRef.appendChild(errorContainer);
            }
        });
    }

    toggleMapType() {
        if (!this.map) return;
        
        const types = ['roadmap', 'satellite', 'hybrid'];
        const currentIndex = types.indexOf(this.mapType);
        this.mapType = types[(currentIndex + 1) % types.length];
        this.map.setMapTypeId(this.mapType);
    }

    showError(message) {
        if (this.mapRef) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'p-4 bg-red-100 border-l-4 border-red-500 text-red-700';
            errorDiv.innerHTML = `
                <h3 class="font-bold mb-2">Error</h3>
                <p>${message}</p>
            `;
            
            // Limpiar errores anteriores
            const previousErrors = this.mapRef.querySelectorAll('.bg-red-100');
            previousErrors.forEach(error => error.remove());
            
            this.mapRef.appendChild(errorDiv);
            
            // Ocultar el loader si está visible
            this.showLoading(false);
        }
    }

    showLoading(show = true) {
        if (this.mapLoaderRef) {
            this.mapLoaderRef.style.display = show ? 'flex' : 'none';
        }
    }

    async searchAddress(address, inputFields) {
        if (!this.geocoder || !this.map) {
            await this.initialize();
        }

        try {
            const response = await this.geocoder.geocode({ address });
            
            if (response.results.length > 0) {
                const location = response.results[0].geometry.location;
                const result = response.results[0];
                
                this.map.setCenter(location);
                
                if (this.marker) {
                    this.marker.setMap(null);
                }
                
                this.marker = new google.maps.Marker({
                    map: this.map,
                    position: location,
                    animation: google.maps.Animation.DROP
                });

                this.map.setZoom(17);
                this.streetView.setPosition(location);
                this.updateLocationInfo(result, location, inputFields);

                // Verificar disponibilidad de Street View
                const streetViewService = new google.maps.StreetViewService();
                streetViewService.getPanorama({ location, radius: 50 }, (data, status) => {
                    if (status === 'OK') {
                        this.streetView.setPosition(location);
                        this.streetView.setPov({
                            heading: google.maps.geometry.spherical.computeHeading(
                                data.location.latLng,
                                location
                            ),
                            pitch: 0
                        });
                    }
                });

                return true;
            }
            
            throw new Error('No se encontró la dirección');
        } catch (error) {
            console.error('Error al buscar la dirección:', error);
            this.showError(error.message);
            return false;
        } finally {
            this.showLoading(false);
        }
    }
}

// Exportar la clase para usarla en otros archivos
window.GoogleMapsViewer = GoogleMapsViewer;

// Alpine.js component definition
document.addEventListener('alpine:init', () => {
    Alpine.data('documentViewer', (titulo, documentId) => ({
        syncScroll: false,
        currentZoom: 100,
        sideBySide: true,
        isDomicilio: titulo.toLowerCase() === 'domicilio y comprobante',
        mapsViewer: null,

        init() {
            if (this.isDomicilio) {
                setTimeout(() => this.loadGoogleMaps(), 1000);
                
                const observer = new MutationObserver((mutations) => {
                    for (const mutation of mutations) {
                        if ((mutation.type === 'childList' || mutation.type === 'subtree') && !this.mapsViewer?.map) {
                            this.initMap();
                            break;
                        }
                    }
                });
                
                observer.observe(this.$refs.formulario, {
                    childList: true,
                    subtree: true,
                    attributes: true
                });
            }

            this.initScrollSync();
        },

        initScrollSync() {
            this.$watch('syncScroll', value => {
                const containers = document.querySelectorAll('.overflow-y-auto');
                containers.forEach(container => {
                    container.onscroll = value ? function() {
                        const scrollPercentage = this.scrollTop / (this.scrollHeight - this.clientHeight);
                        containers.forEach(otherContainer => {
                            if (otherContainer !== this) {
                                otherContainer.scrollTop = scrollPercentage * (otherContainer.scrollHeight - otherContainer.clientHeight);
                            }
                        });
                    } : null;
                });
            });
        },

        toggleSyncScroll() {
            this.syncScroll = !this.syncScroll;
        },

        toggleSideBySide() {
            this.sideBySide = !this.sideBySide;
            this.$refs.container.classList.toggle('lg:grid-cols-2');
            this.$refs.container.classList.toggle('lg:grid-cols-1');
        },

        toggleStreetView() {
            this.mapsViewer?.toggleStreetView();
        },

        toggleMapType() {
            this.mapsViewer?.toggleMapType();
        },

        zoomIn() {
            this.currentZoom += 10;
            this.updateZoom();
        },

        zoomOut() {
            this.currentZoom = Math.max(50, this.currentZoom - 10);
            this.updateZoom();
        },

        updateZoom() {
            document.querySelectorAll('iframe').forEach(iframe => {
                iframe.style.transform = `scale(${this.currentZoom / 100})`;
                iframe.style.transformOrigin = 'top left';
            });
        },

        async loadGoogleMaps() {
            if (!window.googleMapsLoaded && !document.querySelector('script[src*="maps.googleapis.com"]')) {
                const script = document.createElement('script');
                script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyCUqfgNQ2Q4AVy8OTNMfogJceDbA0FHZKs&libraries=places&callback=initGoogleMaps`;
                script.async = true;
                script.defer = true;
                document.head.appendChild(script);
            }

            if (!window.googleMapsLoaded) {
                await new Promise(resolve => {
                    document.addEventListener('google-maps-loaded', resolve, { once: true });
                });
            }

            await this.initMap();
        },

        async initMap() {
            if (!this.$refs.map || !this.$refs.formulario) {
                console.log('Referencias del DOM no disponibles todavía');
                return;
            }

            try {
                const addressFinder = new AddressFieldsFinder(this.$refs.formulario);
                const fields = addressFinder.findFields();
                
                if (!fields.calle || !fields.numeroExterior) {
                    this.$refs.map.innerHTML = `
                        <div class="flex flex-col items-center justify-center h-full bg-gray-50 space-y-4">
                            <i class="fas fa-map-marker-alt text-gray-400 text-4xl"></i>
                            <p class="text-gray-500">Complete los campos de dirección para ver el mapa</p>
                        </div>
                    `;
                    this.$refs.mapLoader.style.display = 'none';
                    return;
                }

                const address = addressFinder.formatAddress(fields);

                if (!this.mapsViewer) {
                    this.mapsViewer = new GoogleMapsViewer(
                        this.$refs.map,
                        this.$refs.streetView,
                        this.$refs.mapLoader,
                        this.$refs.locationInfo,
                        this.$refs.locationDetails
                    );
                }

                await this.mapsViewer.searchAddress(address, fields);
            } catch (error) {
                console.error('Error al inicializar el mapa:', error);
                if (this.mapsViewer) {
                    this.mapsViewer.showError(error.message);
                }
                this.$refs.mapLoader.style.display = 'none';
            }
        }
    }));
}); 