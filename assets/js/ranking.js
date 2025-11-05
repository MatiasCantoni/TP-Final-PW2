// Agregar posición y destacar top 3
    document.addEventListener('DOMContentLoaded', function() {
        const rows = document.querySelectorAll('.ranking-table tbody tr');
        rows.forEach((row, index) => {
            const position = index + 1;
            
            // Agregar atributo de posición para CSS
            row.setAttribute('data-position', position);
            
            // Marcar top 3
            if (position === 1) {
                row.querySelector('.position-cell span').classList.add('esPrimero');
            } else if (position === 2) {
                row.querySelector('.position-cell span').classList.add('esSegundo');
            } else if (position === 3) {
                row.querySelector('.position-cell span').classList.add('esTercero');
            }
        });

        
        rows.forEach(row => {
            const verPerfilBtn = row.querySelector('a[href*="perfil?id="]');
            if (verPerfilBtn) {
                const url = new URL(verPerfilBtn.href);
                const idUsuario = parseInt(url.searchParams.get('id'));
                if (idUsuario === idUsuarioActual) {
                    row.classList.add('current-user');
                }
            }
        });
    });