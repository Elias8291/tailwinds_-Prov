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
        if (/colonia|asentamiento/i.test(label)) details.colonia = value;
        if (/nombre de la vialidad|calle|vialidad/i.test(label)) details.nombreVialidad = value;
        if (/número exterior|numero exterior|no exterior/i.test(label)) details.numeroExterior = value;
        if (/número interior|numero interior|no interior/i.test(label)) details.numeroInterior = value;
        if (/curp/i.test(label)) details.curp = value;

        // Asegurarse de que los valores estén presentes
        if (details.razonSocial) {
            console.log('Razón Social encontrada:', details.razonSocial);
        }
        if (details.cp) {
            console.log('Código Postal encontrado:', details.cp);
        }
    }

    static processFullName(details) {
        const nameParts = [details.nombre, details.apellidoPaterno, details.apellidoMaterno].filter(Boolean);
        details.nombreCompleto = nameParts.join(' ');
        
        // Asegurarse de que la razón social esté establecida correctamente
        if (details.tipoPersona === 'Física') {
            details.razonSocial = details.nombreCompleto;
            console.log('Nombre completo asignado a razón social:', details.razonSocial);
        } else if (!details.razonSocial) {
            // Si es persona moral y no se encontró la razón social, buscar en las secciones
            console.warn('No se encontró razón social para persona moral');
        }
    }

    static generateModalContent(data) {
        console.log('Generando contenido con datos:', data);
        
        if (!data || !data.details) {
            return '<div class="text-center text-gray-500">No hay datos disponibles</div>';
        }

        let content = '<div class="space-y-4">';

        // Información principal
        content += `
            <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                <div class="px-4 py-3 border-b border-gray-100">
                    <h4 class="text-base font-semibold text-gray-800">
                        Información Principal
                    </h4>
                </div>
                <div class="p-4 space-y-3">
                    ${data.details.rfc ? `
                        <div>
                            <span class="text-xs font-medium text-gray-500">RFC</span>
                            <p class="text-sm font-medium text-gray-900">${data.details.rfc}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.tipoPersona ? `
                        <div>
                            <span class="text-xs font-medium text-gray-500">Tipo de Persona</span>
                            <p class="text-sm font-medium text-gray-900">${data.details.tipoPersona}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.nombreCompleto || data.details.razonSocial ? `
                        <div>
                            <span class="text-xs font-medium text-gray-500">${data.details.tipoPersona === 'Física' ? 'Nombre Completo' : 'Razón Social'}</span>
                            <p class="text-sm font-medium text-gray-900">${data.details.nombreCompleto || data.details.razonSocial}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.curp && data.details.tipoPersona === 'Física' ? `
                        <div>
                            <span class="text-xs font-medium text-gray-500">CURP</span>
                            <p class="text-sm font-medium text-gray-900">${data.details.curp}</p>
                        </div>
                    ` : ''}
                </div>
            </div>`;

        // Información de dirección
        if (data.details.nombreVialidad || data.details.colonia || data.details.cp) {
            content += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <h4 class="text-base font-semibold text-gray-800">
                            Dirección
                        </h4>
                    </div>
                    <div class="p-4 space-y-3">
                        ${data.details.nombreVialidad ? `
                            <div>
                                <span class="text-xs font-medium text-gray-500">Calle</span>
                                <p class="text-sm font-medium text-gray-900">
                                    ${data.details.nombreVialidad}
                                    ${data.details.numeroExterior ? `#${data.details.numeroExterior}` : ''}
                                    ${data.details.numeroInterior ? `Int. ${data.details.numeroInterior}` : ''}
                                </p>
                            </div>
                        ` : ''}
                        
                        ${data.details.colonia ? `
                            <div>
                                <span class="text-xs font-medium text-gray-500">Colonia</span>
                                <p class="text-sm font-medium text-gray-900">${data.details.colonia}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.cp ? `
                            <div>
                                <span class="text-xs font-medium text-gray-500">Código Postal</span>
                                <p class="text-sm font-medium text-gray-900">${data.details.cp}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }

        content += '</div>';
        console.log('Contenido generado correctamente');

        const verBtn = document.getElementById('verSatModalBtn');
        if (verBtn) verBtn.classList.add('hidden');

        return content;
    }
}

export default SATScraper; 