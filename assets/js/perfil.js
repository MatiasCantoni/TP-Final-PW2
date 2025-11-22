document.addEventListener('DOMContentLoaded', function() {
    
    if (typeof urlPerfil !== 'undefined' && document.getElementById("qrcode")) {
        new QRCode(document.getElementById("qrcode"), {
            text: urlPerfil,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    }

    document.querySelectorAll('.partida-fecha').forEach(function(elem) {
        const fechaTexto = elem.textContent.trim();
        if (fechaTexto && !fechaTexto.includes('/')) {
            const fecha = new Date(fechaTexto);
            if (!isNaN(fecha)) {
                elem.textContent = fecha.toLocaleDateString('es-AR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }
        }
    });

    const btnEditar = document.getElementById('btn-editar');
    const btnCancelar = document.getElementById('btn-cancelar');
    const vistaDatos = document.getElementById('vista-datos');
    const formEditar = document.getElementById('form-editar');
    const geoapifyApiKey = '73914997462340adb40353110672d1e2';

    let mapVisual, mapEdit, markerEdit;

    function inicializarMapas() {
        if (typeof usuarioPais === 'undefined' || typeof usuarioCiudad === 'undefined') return;

        fetch(`https://api.geoapify.com/v1/geocode/search?text=${encodeURIComponent(usuarioCiudad + ', ' + usuarioPais)}&apiKey=${geoapifyApiKey}`)
            .then(response => response.json())
            .then(result => {
                if (result.features && result.features.length > 0) {
                    const coords = result.features[0].geometry.coordinates; // [lon, lat]
                    const lat = coords[1];
                    const lon = coords[0];

                    if(document.getElementById('mapa-perfil')) {
                        mapVisual = L.map('mapa-perfil', { zoomControl: false, dragging: false, scrollWheelZoom: false, doubleClickZoom: false }).setView([lat, lon], 10);
                        L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${geoapifyApiKey}`, { maxZoom: 20 }).addTo(mapVisual);
                        L.marker([lat, lon]).addTo(mapVisual).bindPopup(`${usuarioCiudad}, ${usuarioPais}`).openPopup();
                    }

                    if(document.getElementById('mapa-edicion')) {
                        mapEdit = L.map('mapa-edicion').setView([lat, lon], 10);
                        L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${geoapifyApiKey}`, { maxZoom: 20 }).addTo(mapEdit);
                        markerEdit = L.marker([lat, lon], { draggable: true }).addTo(mapEdit);

                        mapEdit.on('click', (e) => actualizarUbicacionEdit(e.latlng));
                        markerEdit.on('dragend', (e) => actualizarUbicacionEdit(e.target.getLatLng()));
                    }
                }
            });
    }

    function actualizarUbicacionEdit(latlng) {
        markerEdit.setLatLng(latlng);
        fetch(`https://api.geoapify.com/v1/geocode/reverse?lat=${latlng.lat}&lon=${latlng.lng}&apiKey=${geoapifyApiKey}`)
            .then(res => res.json())
            .then(data => {
                if (data.features && data.features.length) {
                    const props = data.features[0].properties;
                    const nuevoPais = props.country || '';
                    const nuevaCiudad = props.city || props.town || props.village || '';
                    
                    document.getElementById('input-pais').value = nuevoPais;
                    document.getElementById('input-ciudad').value = nuevaCiudad;
                    document.getElementById('ubicacion-seleccionada').innerText = `Seleccionado: ${nuevaCiudad}, ${nuevoPais}`;
                }
            });
    }

    if(btnEditar) {
        btnEditar.addEventListener('click', function() {
            vistaDatos.style.display = 'none';
            formEditar.style.display = 'block';
            btnEditar.style.display = 'none';
            
            setTimeout(() => { 
                if(mapEdit) mapEdit.invalidateSize(); 
            }, 100);
        });
    }

    if(btnCancelar) {
        btnCancelar.addEventListener('click', function() {
            vistaDatos.style.display = 'block';
            formEditar.style.display = 'none';
            btnEditar.style.display = 'inline-block';
        });
    }
    inicializarMapas();
});