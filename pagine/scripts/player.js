// Elementi del DOM
const playerBar    = document.querySelector('.player-bar');
const audio        = document.getElementById('main-audio');
const playBtn      = document.getElementById('play-pause');
const progressBar  = document.getElementById('progress-bar');
const progressArea = document.querySelector('.progress-container');
const titleDisplay = document.getElementById('player-title');
const timeDisplay  = document.getElementById('current-time');

// Funzione per avviare un brano (chiama questa dai tuoi brani)
function playSong(path, title) {
    playerBar.style.display = 'block'; // Mostra il player
    
    audio.src = path;
    titleDisplay.textContent = title;
    
    audio.play();
    playBtn.textContent = '⏸';
}

// Play/Pause toggle
playBtn.addEventListener('click', () => {
    if (audio.paused) {
        audio.play();
        playBtn.textContent = '⏸';
    } else {
        audio.pause();
        playBtn.textContent = '▶';
    }
});

// Aggiornamento della barra bianca mentre la musica va
audio.addEventListener('timeupdate', () => {
    if (audio.duration) {
        const percent = (audio.currentTime / audio.duration) * 100;
        progressBar.style.width = percent + '%';
        
        // Calcola minuti e secondi
        let m = Math.floor(audio.currentTime / 60);
        let s = Math.floor(audio.currentTime % 60);
        timeDisplay.textContent = m.toString().padStart(2, '0') + ':' + s.toString().padStart(2, '0');
    }
});

// Click sulla barra per saltare in un punto
progressArea.addEventListener('click', (e) => {
    const rect = progressArea.getBoundingClientRect();
    const x = e.clientX - rect.left; // Posizione del click
    const width = rect.width;
    const percentage = x / width;
    
    // Cambia il tempo dell'audio
    if (audio.duration) {
        audio.currentTime = percentage * audio.duration;
    }
});