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
                    nombreCompleto: '',
                    // Datos adicionales del SAT
                    estado: '',
                    municipio: '',
                    localidad: '',
                    tipoVialidad: '',
                    entreCalles: '',
                    telefono: '',
                    fax: '',
                    correoElectronico: '',
                    sitioWeb: '',
                    actividadEconomica: '',
                    fechaInicioOperaciones: '',
                    estatusContribuyente: '',
                    fechaUltimoCambioSituacion: '',
                    entidadFederativa: '',
                    regimenCapital: '',
                    numeroInteriorCompleto: '',
                    numeroExteriorCompleto: '',
                    referencia: '',
                    tipoAsentamiento: '',
                    claveMunicipio: '',
                    claveEntidad: '',
                    paisResidencia: ''
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
        
        // Datos básicos
        if (/correo|email/i.test(label)) details.email = value;
        if (/denominación|razón social/i.test(label)) details.razonSocial = value;
        if (labelLower === 'nombre') details.nombre = value;
        if (/apellido paterno/i.test(label)) details.apellidoPaterno = value;
        if (/apellido materno/i.test(label)) details.apellidoMaterno = value;
        if (/rfc/i.test(label)) details.rfc = value;
        if (/código postal|cp/i.test(label)) details.cp = value;
        if (/colonia|asentamiento/i.test(label) && !details.colonia) details.colonia = value;
        if (/nombre de la vialidad|calle|vialidad/i.test(label)) details.nombreVialidad = value;
        if (/número exterior|numero exterior|no exterior/i.test(label)) details.numeroExterior = value;
        if (/número interior|numero interior|no interior/i.test(label)) details.numeroInterior = value;
        if (/curp/i.test(label)) details.curp = value;

        // Datos adicionales de dirección
        if (/estado|entidad federativa/i.test(label)) details.estado = value;
        if (/municipio/i.test(label) && !details.municipio) details.municipio = value;
        if (/localidad/i.test(label)) details.localidad = value;
        if (/tipo de vialidad/i.test(label)) details.tipoVialidad = value;
        if (/entre calles/i.test(label)) details.entreCalles = value;
        if (/tipo de asentamiento/i.test(label)) details.tipoAsentamiento = value;
        if (/referencia/i.test(label)) details.referencia = value;

        // Datos de contacto
        if (/teléfono|telefono/i.test(label)) details.telefono = value;
        if (/fax/i.test(label)) details.fax = value;
        if (/correo electrónico/i.test(label)) details.correoElectronico = value;
        if (/sitio web|página web/i.test(label)) details.sitioWeb = value;

        // Datos fiscales y de actividad
        if (/actividad económica|actividad economica|giro/i.test(label)) details.actividadEconomica = value;
        if (/fecha de inicio de operaciones|inicio de operaciones/i.test(label)) details.fechaInicioOperaciones = value;
        if (/estatus|situación del contribuyente/i.test(label)) details.estatusContribuyente = value;
        if (/fecha del último cambio|ultimo cambio/i.test(label)) details.fechaUltimoCambioSituacion = value;
        if (/régimen de capital/i.test(label)) details.regimenCapital = value;
        if (/país de residencia/i.test(label)) details.paisResidencia = value;

        // Códigos y claves
        if (/clave del municipio/i.test(label)) details.claveMunicipio = value;
        if (/clave de la entidad/i.test(label)) details.claveEntidad = value;

        // Log para debugging
        console.log(`Campo asignado: ${label} -> ${value}`);
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
                <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
                    <h4 class="text-base font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Información Principal
                    </h4>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    ${data.details.rfc ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase">RFC</span>
                            <p class="text-sm font-bold text-gray-900 font-mono">${data.details.rfc}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.tipoPersona ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase">Tipo de Persona</span>
                            <p class="text-sm font-bold text-gray-900">${data.details.tipoPersona}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.nombreCompleto || data.details.razonSocial ? `
                        <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                            <span class="text-xs font-medium text-gray-500 uppercase">${data.details.tipoPersona === 'Física' ? 'Nombre Completo' : 'Razón Social'}</span>
                            <p class="text-sm font-bold text-gray-900">${data.details.nombreCompleto || data.details.razonSocial}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.curp && data.details.tipoPersona === 'Física' ? `
                        <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                            <span class="text-xs font-medium text-gray-500 uppercase">CURP</span>
                            <p class="text-sm font-bold text-gray-900 font-mono">${data.details.curp}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.estatusContribuyente ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase">Estatus</span>
                            <p class="text-sm font-bold text-gray-900">${data.details.estatusContribuyente}</p>
                        </div>
                    ` : ''}
                    
                    ${data.details.fechaInicioOperaciones ? `
                        <div class="bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs font-medium text-gray-500 uppercase">Inicio de Operaciones</span>
                            <p class="text-sm font-bold text-gray-900">${data.details.fechaInicioOperaciones}</p>
                        </div>
                    ` : ''}
                </div>
            </div>`;

        // Información de dirección
        const hasAddressData = data.details.nombreVialidad || data.details.colonia || data.details.cp || 
                              data.details.estado || data.details.municipio || data.details.localidad;
        
        if (hasAddressData) {
            content += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
                        <h4 class="text-base font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Domicilio Fiscal
                        </h4>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${data.details.tipoVialidad || data.details.nombreVialidad ? `
                            <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                <span class="text-xs font-medium text-gray-500 uppercase">Dirección</span>
                                <p class="text-sm font-bold text-gray-900">
                                    ${data.details.tipoVialidad ? data.details.tipoVialidad + ' ' : ''}${data.details.nombreVialidad || ''}
                                    ${data.details.numeroExterior ? ' #' + data.details.numeroExterior : ''}
                                    ${data.details.numeroInterior ? ' Int. ' + data.details.numeroInterior : ''}
                                </p>
                            </div>
                        ` : ''}
                        
                        ${data.details.colonia ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Colonia</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.colonia}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.cp ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Código Postal</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.cp}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.localidad ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Localidad</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.localidad}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.municipio ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Municipio</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.municipio}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.estado ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Estado</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.estado}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.paisResidencia ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">País</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.paisResidencia}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.entreCalles ? `
                            <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                <span class="text-xs font-medium text-gray-500 uppercase">Entre Calles</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.entreCalles}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.referencia ? `
                            <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                <span class="text-xs font-medium text-gray-500 uppercase">Referencia</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.referencia}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }

        // Información de contacto
        const hasContactData = data.details.email || data.details.correoElectronico || 
                              data.details.telefono || data.details.fax || data.details.sitioWeb;
        
        if (hasContactData) {
            content += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
                        <h4 class="text-base font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Información de Contacto
                        </h4>
                    </div>
                    <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        ${data.details.email || data.details.correoElectronico ? `
                            <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                <span class="text-xs font-medium text-gray-500 uppercase">Correo Electrónico</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.email || data.details.correoElectronico}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.telefono ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Teléfono</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.telefono}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.fax ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Fax</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.fax}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.sitioWeb ? `
                            <div class="bg-gray-50 p-3 rounded-lg md:col-span-2">
                                <span class="text-xs font-medium text-gray-500 uppercase">Sitio Web</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.sitioWeb}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }

        // Información fiscal y actividad económica
        const hasFiscalData = data.details.actividadEconomica || data.details.regimenCapital || 
                             data.details.fechaUltimoCambioSituacion;
        
        if (hasFiscalData) {
            content += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
                        <h4 class="text-base font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Información Fiscal
                        </h4>
                    </div>
                    <div class="p-4 grid grid-cols-1 gap-4">
                        ${data.details.actividadEconomica ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Actividad Económica</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.actividadEconomica}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.regimenCapital ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Régimen de Capital</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.regimenCapital}</p>
                            </div>
                        ` : ''}
                        
                        ${data.details.fechaUltimoCambioSituacion ? `
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <span class="text-xs font-medium text-gray-500 uppercase">Fecha Último Cambio</span>
                                <p class="text-sm font-bold text-gray-900">${data.details.fechaUltimoCambioSituacion}</p>
                            </div>
                        ` : ''}
                    </div>
                </div>`;
        }

        // Mostrar todas las secciones disponibles si existen
        if (data.sections && data.sections.length > 0) {
            content += `
                <div class="bg-white rounded-lg shadow-sm border border-gray-100">
                    <div class="px-4 py-3 border-b border-gray-100 bg-gradient-to-r from-[#9d2449] to-[#7a1d37]">
                        <h4 class="text-base font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            Información Adicional del SAT
                        </h4>
                    </div>
                    <div class="p-4 space-y-4">`;
            
            data.sections.forEach(section => {
                if (section.fields && section.fields.length > 0) {
                    content += `
                        <div class="border border-gray-200 rounded-lg p-3">
                            <h5 class="text-sm font-semibold text-gray-800 mb-3 border-b border-gray-200 pb-2">${section.title}</h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">`;
                    
                    section.fields.forEach(field => {
                        if (field.value && field.value.trim()) {
                            content += `
                                <div class="bg-gray-50 p-2 rounded">
                                    <span class="text-xs font-medium text-gray-500 uppercase block">${field.label}</span>
                                    <p class="text-sm text-gray-900 mt-1">${field.value}</p>
                                </div>`;
                        }
                    });
                    
                    content += `
                            </div>
                        </div>`;
                }
            });
            
            content += `
                    </div>
                </div>`;
        }

        content += '</div>';
        console.log('Contenido completo generado correctamente');

        const verBtn = document.getElementById('verSatModalBtn');
        if (verBtn) verBtn.classList.add('hidden');

        return content;
    }
}

export default SATScraper; 