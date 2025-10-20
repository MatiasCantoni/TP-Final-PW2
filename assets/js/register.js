document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('mapa')) {
        
        const latitudInicial = -34.6037;
        const longitudInicial = -58.3816;
        const zoomInicial = 10;

        const map = L.map('mapa').setView([latitudInicial, longitudInicial], zoomInicial);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        let marker = L.marker([latitudInicial, longitudInicial], { draggable: true }).addTo(map)
            .bindPopup('Arrastrame o haz click en el mapa')
            .openPopup();

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
