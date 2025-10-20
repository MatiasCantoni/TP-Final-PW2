document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('mapa')) {
        
        // 1. Coordenadas de Buenos Aires y un nivel de zoom más cercano
        const latitudInicial = -34.6037;
        const longitudInicial = -58.3816;
        const zoomInicial = 10;

        // INICIALIZA EL MAPA CON LA VISTA EN BUENOS AIRES
        const map = L.map('mapa').setView([latitudInicial, longitudInicial], zoomInicial);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // COLOCA EL MARCADOR INICIAL EN BUENOS AIRES
        let marker = L.marker([latitudInicial, longitudInicial], { draggable: true }).addTo(map)
            .bindPopup('Arrastrame o haz click en el mapa')
            .openPopup();
        
        // El resto del código para actualizar la ubicación sigue igual
        function actualizarUbicacion(lat, lon) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
                .then(response => response.json())
                .then(data => {
                    const address = data.address;
                    const pais = address.country || '';
                    const ciudad = address.city || address.town || address.village || '';
                    document.getElementById('pais').value = pais;
                    document.getElementById('ciudad').value = ciudad;

                    const textoUbicacion = document.getElementById('ubicacion-seleccionada');
                    if (textoUbicacion) {
                        if (pais) {
                            textoUbicacion.innerText = `Ubicación: ${ciudad}, ${pais}`;
                        } else {
                            textoUbicacion.innerText = 'Ubicación no encontrada. Intenta de nuevo.';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error al obtener la ubicación:', error);
                    const textoUbicacion = document.getElementById('ubicacion-seleccionada');
                    if (textoUbicacion) {
                        textoUbicacion.innerText = 'No se pudo obtener la ubicación.';
                    }
                });
        }

        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            actualizarUbicacion(e.latlng.lat, e.latlng.lng);
        });

        marker.on('dragend', function(e) {
            const latlng = e.target.getLatLng();
            actualizarUbicacion(latlng.lat, latlng.lng);
        });
    }
});