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
    const pass1 = document.getElementById('contrasena');
    const pass2 = document.getElementById('contrasena_repetida');
    const mensaje = document.getElementById('mensaje-password');
    const btnSubmit = document.querySelector('button[type="submit"]');

    function validarPasswords() {
        const valor1 = pass1.value;
        const valor2 = pass2.value;
        if (valor2 === '') {
            mensaje.style.display = 'none';
            btnSubmit.disabled = false;
            return;
        }

        mensaje.style.display = 'block';
        if (valor1 === valor2) {
            mensaje.innerText = 'Las contraseñas coinciden';
            mensaje.style.color = 'green';
            pass2.style.borderColor = 'green';
            btnSubmit.disabled = false;
        } else {
            mensaje.innerText = 'Las contraseñas no coinciden';
            mensaje.style.color = 'red';
            pass2.style.borderColor = 'red';
            btnSubmit.disabled = true; 
        }
    }
    if (pass1 && pass2) {
        pass1.addEventListener('input', validarPasswords);
        pass2.addEventListener('input', validarPasswords);
    }
});