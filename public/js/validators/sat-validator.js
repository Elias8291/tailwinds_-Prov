class SATValidator {
    static validate(qrUrl) {
        return qrUrl.startsWith('https://siat.sat.gob.mx/app/qr/faces/pages/mobile/validadorqr.jsf');
    }

    static getErrorMessage() {
        return 'El código QR no corresponde a una constancia del SAT';
    }
}

export default SATValidator; 