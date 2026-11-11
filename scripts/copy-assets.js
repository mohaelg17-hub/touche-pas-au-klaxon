// Copie les polices d'icones et le JavaScript de Bootstrap vers le dossier public.
const fs = require('fs');

fs.cpSync('node_modules/bootstrap-icons/font/fonts', 'public/assets/fonts', { recursive: true });
fs.mkdirSync('public/assets/js', { recursive: true });
fs.copyFileSync(
    'node_modules/bootstrap/dist/js/bootstrap.bundle.min.js',
    'public/assets/js/bootstrap.bundle.min.js'
);