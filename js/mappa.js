var map = L.map('map').setView([37.5921295, 14.9742222], 17);

// Aggiunta del tile layer
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Aggiunta di un layer per le segnalazioni
var segnalazioniLayer = L.layerGroup().addTo(map);

// Recupera le segnalazioni da localStorage
let segnalazioni = JSON.parse(localStorage.getItem('segnalazioni')) || [];

// Log per verificare i dati caricati
console.log('Segnalazioni caricate:', segnalazioni);

// Aggiungi i marker delle segnalazioni alla mappa
segnalazioni.forEach(segnalazione => {
    if (segnalazione.latitudine && segnalazione.longitudine) {
        L.marker([segnalazione.latitudine, segnalazione.longitudine]).addTo(segnalazioniLayer)
            .bindPopup(`
                <strong>Motivo:</strong> ${segnalazione.motivo}<br>
                <strong>Data:</strong> ${segnalazione.data}<br>
                <strong>Ora:</strong> ${segnalazione.ora}<br>
                <strong>Note:</strong> ${segnalazione.note || 'Nessuna nota'}
            `);
    }
});

// Aggiungi evento click sulla mappa
map.on('click', function (e) {
    const { lat, lng } = e.latlng;

    const popupContent = `
        <div>
            <h3>Segnala un problema</h3>
            <button onclick="reportIssue('Rifiuti abbandonati', ${lat}, ${lng})">Rifiuti abbandonati</button>
            <button onclick="reportIssue('Inquinamento acustico', ${lat}, ${lng})">Inquinamento acustico</button>
            <button onclick="reportIssue('Alberi da piantare', ${lat}, ${lng})">Alberi da piantare</button>
        </div>
    `;

    L.popup()
        .setLatLng([lat, lng])
        .setContent(popupContent)
        .openOn(map);
});

function reportIssue(motivo, lat, lng) {
    const data = new Date().toLocaleDateString();
    const ora = new Date().toLocaleTimeString();

    const nuovaSegnalazione = {
        data,
        ora,
        motivo,
        latitudine: lat,
        longitudine: lng,
        note: 'Segnalazione aggiunta dalla mappa'
    };

    // Salva la segnalazione in localStorage
    let segnalazioni = JSON.parse(localStorage.getItem('segnalazioni')) || [];
    segnalazioni.push(nuovaSegnalazione);
    localStorage.setItem('segnalazioni', JSON.stringify(segnalazioni));

    // Aggiungi il marker alla mappa
    L.marker([lat, lng]).addTo(segnalazioniLayer)
        .bindPopup(`
            <strong>Motivo:</strong> ${motivo}<br>
            <strong>Data:</strong> ${data}<br>
            <strong>Ora:</strong> ${ora}
        `);

    alert('Segnalazione aggiunta con successo!');
}

function cercaIndirizzo() {
    const indirizzo = document.getElementById('searchInput').value;

    if (!indirizzo) {
        alert('Inserisci un indirizzo valido.');
        return;
    }

    // Chiamata al servizio Nominatim per la geocodifica
    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(indirizzo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                alert('Indirizzo non trovato.');
                return;
            }

            // Ottieni le coordinate del primo risultato
            const { lat, lon } = data[0];

            // Sposta la mappa al punto trovato
            map.setView([lat, lon], 17);

            alert(`Indirizzo trovato: ${indirizzo}`);
        })
        .catch(error => {
            console.error('Errore durante la ricerca:', error);
            alert('Si è verificato un errore durante la ricerca.');
        });
}

function cercaCoordinate() {
    const lat = document.getElementById('latInput').value;
    const lng = document.getElementById('lngInput').value;

    if (!lat || !lng || isNaN(lat) || isNaN(lng)) {
        alert('Inserisci coordinate valide.');
        return;
    }

    // Chiamata al servizio Nominatim per la geocodifica inversa
    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.display_name) {
                alert('Indirizzo non trovato per queste coordinate.');
                return;
            }

            // Sposta la mappa al punto trovato
            map.setView([lat, lng], 17);

            alert(`Indirizzo trovato: ${data.display_name}`);
        })
        .catch(error => {
            console.error('Errore durante la ricerca:', error);
            alert('Si è verificato un errore durante la ricerca.');
        });
}