document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.ranking-table tbody tr');
    
    rows.forEach(row => {
        const verPerfilBtn = row.querySelector('a[href*="perfil/show?id="]'); 
        
        if (verPerfilBtn) {
            const url = new URL(verPerfilBtn.href);
            const idUsuario = parseInt(url.searchParams.get('id'));
            if (typeof idUsuarioActual !== 'undefined' && idUsuario === idUsuarioActual) { 
                row.classList.add('current-user');
            }
        }
    });
});