document.addEventListener('DOMContentLoaded', function () {
    if (document.getElementById('mapa')) {
        
        const geoapifyApiKey = '73914997462340adb40353110672d1e2';

        const latitudInicial = -34.6037;
        const longitudInicial = -58.3816;
        const zoomInicial = 10;

        const map = L.map('mapa').setView([latitudInicial, longitudInicial], zoomInicial);

        L.tileLayer('https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey={apiKey}', {
            attribution: 'Powered by <a href="https://www.geoapify.com/" target="_blank">Geoapify</a> | © <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors',
            maxZoom: 20,
            apiKey: geoapifyApiKey
        }).addTo(map);

        let marker = L.marker([latitudInicial, longitudInicial], { draggable: true }).addTo(map)
            .bindPopup('Arrastrame o haz click en el mapa')
            .openPopup();

        function actualizarUbicacion(lat, lon) {
            fetch(`https://api.geoapify.com/v1/geocode/reverse?lat=${lat}&lon=${lon}&apiKey=${geoapifyApiKey}`)
                .then(response => response.json())
                .then(result => {
                    let pais = '';
                    let ciudad = '';
                    
                    if (result.features && result.features.length) {
                        const properties = result.features[0].properties;
                        pais = properties.country || '';
                        ciudad = properties.city || '';
                    }

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