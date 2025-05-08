function menu(){
 
    document.querySelector('.dropdown-button').addEventListener('click', () => {
        const menu = document.querySelector('.dropdown-menu');
        menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
    });

    // Chiudi il menu se si clicca fuori
    window.addEventListener('click', (e) => {
        if (!e.target.matches('.dropdown-button') && !e.target.matches('.menu-icon')) {
            document.querySelector('.dropdown-menu').style.display = 'none';
        }
    });
}

function aggiornaOrario() {
    const orario = new Date().toLocaleTimeString();
    document.getElementById('orarioLocale').textContent = `Orario locale: ${orario}`;
}
setInterval(aggiornaOrario, 1000); // Aggiorna ogni secondo
aggiornaOrario(); // Inizializza subito