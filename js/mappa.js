var map = L.map('map').setView([37.5921295, 14.9742222], 17);

// Aggiunta del tile layer
L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

// Aggiunta di un layer per le segnalazioni
var segnalazioniLayer = L.layerGroup().addTo(map);

// Carica le segnalazioni dal database tramite AJAX
fetch('filtra_segnalazioni.php')
    .then(response => response.json())
    .then(segnalazioni => {
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
    });

// Aggiungi evento click sulla mappa
map.on('click', function (e) {
    const { lat, lng } = e.latlng;

    const popupContent = `
        <div>
            <h3>Segnala un problema</h3>
            <button onclick="reportIssue('Rifiuti', ${lat}, ${lng})">Rifiuti</button>
            <button onclick="reportIssue('Abbandono', ${lat}, ${lng})">Abbandono</button>
            <button onclick="reportIssue('Pericolo', ${lat}, ${lng})">Pericolo</button>
        </div>
    `;

    L.popup()
        .setLatLng([lat, lng])
        .setContent(popupContent)
        .openOn(map);
});

window.reportIssue = function(motivo, lat, lng) {
    const data = new Date().toISOString().slice(0, 10);
    const ora = new Date().toTimeString().slice(0, 8);

    const nuovaSegnalazione = {
        data,
        ora,
        motivo,
        latitudine: lat,
        longitudine: lng,
        note: 'Segnalazione aggiunta dalla mappa'
    };

    // Invia la segnalazione al server per salvarla nel database
    fetch('salva_segnalazione_mappa.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(nuovaSegnalazione)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            // Aggiungi il marker alla mappa solo se il salvataggio è andato a buon fine
            L.marker([lat, lng]).addTo(segnalazioniLayer)
                .bindPopup(`
                    <strong>Motivo:</strong> ${motivo}<br>
                    <strong>Data:</strong> ${data}<br>
                    <strong>Ora:</strong> ${ora}
                `);
            alert('Segnalazione aggiunta con successo!');
        } else {
            alert('Errore nel salvataggio della segnalazione.');
        }
    })
    .catch(() => {
        alert('Errore di rete durante il salvataggio della segnalazione.');
    });
};

function cercaIndirizzo() {
    const indirizzo = document.getElementById('searchInput').value;

    if (!indirizzo) {
        alert('Inserisci un indirizzo valido.');
        return;
    }

    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(indirizzo)}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                alert('Indirizzo non trovato.');
                return;
            }
            const { lat, lon } = data[0];
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

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
        .then(response => response.json())
        .then(data => {
            if (!data || !data.display_name) {
                alert('Indirizzo non trovato per queste coordinate.');
                return;
            }
            map.setView([lat, lng], 17);
            alert(`Indirizzo trovato: ${data.display_name}`);
        })
        .catch(error => {
            console.error('Errore durante la ricerca:', error);
            alert('Si è verificato un errore durante la ricerca.');
        });
}