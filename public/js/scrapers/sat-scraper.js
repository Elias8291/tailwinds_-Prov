class SATScraper {
    static async scrapeData(url) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'text/html',
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124 Safari/537.36'
                },
                timeout: 15000
            });

            if (!response.ok) {
                const text = await response.text();
                if (response.status >= 500 || text.includes('Weblogic Bridge Message') || text.includes('No backend server available')) {
                    throw new Error('La página del SAT no está disponible temporalmente');
                }
                throw new Error(`Error al obtener datos: ${response.status}`);
            }

            const html = await response.text();
            if (html.includes('Weblogic Bridge Message') || html.includes('No backend server available')) {
                throw new Error('La página del SAT no está disponible temporalmente');
            }

            if (!html.includes('RFC:') && !html.includes('Denominación/Razón Social:')) {
                throw new Error('El documento no parece ser una Constancia de Situación Fiscal válida');
            }

            const doc = new DOMParser().parseFromString(html, 'text/html');
            
            // Datos base
            const data = {
                sections: [], // Para las secciones con tablas
                details: {   // Para datos específicos
                    email: '',
                    razonSocial: '',
                    nombre: '',
                    apellidoPaterno: '',
                    apellidoMaterno: '',
                    rfc: '',
                    cp: '',
                    colonia: '',
                    nombreVialidad: '',
                    numeroExterior: '',
                    numeroInterior: '',
                    tipoPersona: '',
                    curp: '',
                    nombreCompleto: ''
                }
            };

            // Extraer RFC y determinar tipo de persona
            const rfcMatch = html.match(/RFC:\s*([A-Z0-9]+)/i);
            if (!rfcMatch) {
                throw new Error('No se pudo encontrar el RFC en el documento');
            }

            data.details.rfc = rfcMatch[1];
            data.details.tipoPersona = data.details.rfc.length === 12 ? 'Moral' : 'Física';

            // Procesar secciones de datos
            doc.querySelectorAll('[data-role="listview"]').forEach((section, index) => {
                const title = section.querySelector('[data-role="list-divider"]')?.textContent.trim() || `Sección ${index + 1}`;
                if (!section.querySelector('table')) return;

                const fields = [];
                section.querySelectorAll('tr[data-ri]').forEach(row => {
                    const [labelCell, valueCell] = row.querySelectorAll('td[role="gridcell"]');
                    if (!labelCell || !valueCell) return;

                    const label = labelCell.textContent.replace(/:/g, '').trim();
                    const value = valueCell.textContent.trim();
                    
                    if (!label || !value || value.includes('$(function')) return;
                    if (fields.some(f => f.label === label)) return;

                    // Asignar valores específicos
                    this.assignSpecificValue(data.details, label, value);

                    fields.push({ label, value });
                });

                if (fields.length) {
                    data.sections.push({
                        title,
                        fields
                    });
                }
            });

            // Procesar nombre completo
            this.processFullName(data.details);

            return data;
        } catch (error) {
            console.warn('Error al obtener datos del SAT:', error);
            throw error; // Re-throw the error to be handled by the caller
        }
    }

    static assignSpecificValue(details, label, value) {
        const labelLower = label.toLowerCase();
        
        if (/correo|email/i.test(label)) details.email = value;
        if (/denominación|razón social/i.test(label)) details.razonSocial = value;
        if (labelLower === 'nombre') details.nombre = value;
        if (/apellido paterno/i.test(label)) details.apellidoPaterno = value;
        if (/apellido materno/i.test(label)) details.apellidoMaterno = value;
        if (/rfc/i.test(label)) details.rfc = value;
        if (/código postal|cp/i.test(label)) details.cp = value;
        if (/colonia/i.test(label)) details.colonia = value;
        if (/nombre de la vialidad|calle|vialidad/i.test(label)) details.nombreVialidad = value;
        if (/número exterior|numero exterior|no exterior/i.test(label)) details.numeroExterior = value;
        if (/número interior|numero interior|no interior/i.test(label)) details.numeroInterior = value;
        if (/curp/i.test(label)) details.curp = value;
    }

    static processFullName(details) {
        const nameParts = [details.nombre, details.apellidoPaterno, details.apellidoMaterno].filter(Boolean);
        details.nombreCompleto = nameParts.join(' ');
        
        if (details.tipoPersona === 'Física') {
            details.razonSocial = details.nombreCompleto;
        }
    }

    static generateModalContent(data) {
        if (!data) return '<p class="text-red-600">No se pudieron obtener los datos del SAT</p>';

        // Autocompletar el correo solo si existe y no está vacío
        if (data.details.email && data.details.email.trim() !== '') {
            const emailInput = document.getElementById('email');
            if (emailInput) {
                emailInput.value = data.details.email;
            }
        }

        let content = `
            <div class="space-y-6">
                <!-- Sección de información principal -->
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 rounded-xl p-6 border border-primary/10 shadow-sm">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-14 h-14 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center shadow-lg transform rotate-3">
                                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 mb-2 leading-tight">
                                ${data.details.tipoPersona === 'Moral' ? data.details.razonSocial : data.details.nombreCompleto}
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-primary/10 text-primary border border-primary/20">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                                    </svg>
                                    RFC: ${data.details.rfc}
                                </span>
                                ${data.details.curp ? `
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-primary/10 text-primary border border-primary/20">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                        </svg>
                                        CURP: ${data.details.curp}
                                    </span>
                                ` : ''}
                                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-primary/10 text-primary border border-primary/20">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    Persona ${data.details.tipoPersona}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>`;

        // Agregar secciones con nuevo diseño
        data.sections.forEach(section => {
            content += `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            ${section.title}
                        </h4>
                    </div>
                    <div class="divide-y divide-gray-100">
                        ${section.fields.map(field => `
                            <div class="px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                                <div class="flex flex-col sm:flex-row sm:items-center">
                                    <div class="sm:w-1/3">
                                        <span class="text-sm font-medium text-gray-500">${field.label}</span>
                                    </div>
                                    <div class="sm:w-2/3 mt-1 sm:mt-0">
                                        <span class="text-sm text-gray-900">${field.value}</span>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>`;
        });

        // Agregar información de dirección con nuevo diseño
        if (data.details.nombreVialidad || data.details.colonia || data.details.cp) {
            content += `
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
                        <h4 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Dirección
                        </h4>
                    </div>
                    <div class="p-6">
                        ${data.details.nombreVialidad ? `
                            <div class="flex items-start space-x-3">
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm text-gray-900 font-medium">
                                        ${data.details.nombreVialidad}
                                        ${data.details.numeroExterior ? ` #${data.details.numeroExterior}` : ''}
                                        ${data.details.numeroInterior ? ` Int. ${data.details.numeroInterior}` : ''}
                                    </p>
                                    ${data.details.colonia ? `<p class="text-sm text-gray-600">Col. ${data.details.colonia}</p>` : ''}
                                    ${data.details.cp ? `<p class="text-sm text-gray-600">CP ${data.details.cp}</p>` : ''}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }

        content += '</div>';

        // Solo preparar el contenido del modal, NO mostrarlo
        const modalContent = document.getElementById('satDataContent');
        if (modalContent) {
            modalContent.innerHTML = content;
        }

        return content;
    }
}

export default SATScraper; 