class SATScraper {
    static async scrapeData(url) {
        console.log('Iniciando scraping de datos del SAT:', url);
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'text/html',
                    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/91.0.4472.124 Safari/537.36'
                },
                timeout: 15000
            });

            console.log('Respuesta del servidor SAT:', {
                status: response.status,
                ok: response.ok,
                headers: Object.fromEntries(response.headers)
            });

            if (!response.ok) {
                const text = await response.text();
                console.error('Error en respuesta del SAT:', text);
                if (response.status >= 500 || text.includes('Weblogic Bridge Message') || text.includes('No backend server available')) {
                    throw new Error('La página del SAT no está disponible temporalmente');
                }
                throw new Error(`Error al obtener datos: ${response.status}`);
            }

            const html = await response.text();
            console.log('HTML recibido del SAT:', html.substring(0, 200) + '...');

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
                console.error('No se encontró el RFC en el HTML');
                throw new Error('No se pudo encontrar el RFC en el documento');
            }

            data.details.rfc = rfcMatch[1];
            data.details.tipoPersona = data.details.rfc.length === 12 ? 'Moral' : 'Física';
            console.log('RFC extraído:', data.details.rfc, 'Tipo de persona:', data.details.tipoPersona);

            // Procesar secciones de datos
            const sections = doc.querySelectorAll('[data-role="listview"]');
            console.log('Secciones encontradas:', sections.length);

            sections.forEach((section, index) => {
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
                    console.log(`Campo encontrado en ${title}:`, { label, value });

                    fields.push({ label, value });
                });

                if (fields.length) {
                    data.sections.push({
                        title,
                        fields
                    });
                    console.log(`Sección procesada: ${title} con ${fields.length} campos`);
                }
            });

            // Procesar nombre completo
            this.processFullName(data.details);
            console.log('Datos procesados:', data);

            return { success: true, data: data };

        } catch (error) {
            console.error('Error detallado al obtener datos del SAT:', error);
            return { success: false, error: error.message };
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
        console.log('Generando contenido con datos:', data);
        
        if (!data || typeof data !== 'object') {
            console.error('Datos inválidos proporcionados a generateModalContent:', data);
            return '<p class="text-red-600">No se pudieron obtener los datos del SAT</p>';
        }

        try {
            // Asegurarse de que los objetos necesarios existan
            data.details = data.details || {};
            data.sections = data.sections || [];

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
                    <div class="bg-white rounded-xl p-6 border border-[#B4325E]/10 shadow-sm">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-14 h-14 bg-gradient-to-br from-[#B4325E] to-[#93264B] rounded-xl flex items-center justify-center shadow-lg">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    ${data.details.tipoPersona === 'Moral' ? 
                                        (data.details.razonSocial || 'Razón Social No Disponible') : 
                                        (data.details.nombreCompleto || 'Nombre No Disponible')}
                                </h3>
                                <div class="flex flex-wrap gap-2">
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                        RFC: ${data.details.rfc || 'No disponible'}
                                    </span>
                                    ${data.details.curp ? `
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                            CURP: ${data.details.curp}
                                        </span>
                                    ` : ''}
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-[#B4325E]/10 text-[#B4325E]">
                                        Persona ${data.details.tipoPersona || 'No especificada'}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>`;

            // Agregar secciones si existen
            if (data.sections && data.sections.length > 0) {
                data.sections.forEach(section => {
                    if (section.fields && section.fields.length > 0) {
                        content += `
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                                <div class="px-6 py-4 border-b border-gray-100">
                                    <h4 class="text-lg font-semibold text-gray-800">
                                        ${section.title || 'Información Adicional'}
                                    </h4>
                                </div>
                                <div class="divide-y divide-gray-100">
                                    ${section.fields.map(field => `
                                        <div class="px-6 py-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center">
                                                <div class="sm:w-1/3">
                                                    <span class="text-sm font-medium text-gray-500">${field.label || ''}</span>
                                                </div>
                                                <div class="sm:w-2/3 mt-1 sm:mt-0">
                                                    <span class="text-sm text-gray-900">${field.value || ''}</span>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>`;
                    }
                });
            }

            // Agregar información de dirección si existe
            if (data.details.nombreVialidad || data.details.colonia || data.details.cp) {
                content += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-100">
                            <h4 class="text-lg font-semibold text-gray-800">
                                Dirección
                            </h4>
                        </div>
                        <div class="p-6">
                            <div class="space-y-2">
                                ${data.details.nombreVialidad ? `
                                    <p class="text-sm text-gray-900">
                                        ${data.details.nombreVialidad}
                                        ${data.details.numeroExterior ? ` #${data.details.numeroExterior}` : ''}
                                        ${data.details.numeroInterior ? ` Int. ${data.details.numeroInterior}` : ''}
                                    </p>
                                ` : ''}
                                ${data.details.colonia ? `<p class="text-sm text-gray-600">Col. ${data.details.colonia}</p>` : ''}
                                ${data.details.cp ? `<p class="text-sm text-gray-600">CP ${data.details.cp}</p>` : ''}
                            </div>
                        </div>
                    </div>`;
            }

            content += '</div>';
            console.log('Contenido generado correctamente');

            // Después de mostrar el modal y llenar los datos:
            const verBtn = document.getElementById('verSatModalBtn');
            if (verBtn) verBtn.classList.add('hidden');

            return content;

        } catch (error) {
            console.error('Error al generar el contenido:', error);
            return '<p class="text-red-600">Error al procesar los datos del SAT</p>';
        }
    }
}

export default SATScraper; 