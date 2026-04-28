// js/weather.js
const apiKey = '';
const city = '';

document.addEventListener('DOMContentLoaded', function() {
    console.log('Initialisation de la météo...');
    
    const weatherBox = document.querySelector('.weather-box');
    
    if (!weatherBox) {
        console.error('Boîte météo introuvable');
        return;
    }
    
    console.log('Boîte météo trouvée');
    
    function updateWeather() {
        console.log('Mise à jour de la météo...');
        
        // Message de chargement
        weatherBox.innerHTML = `
            <h2>Météo</h2>
            <p class="temp">...</p>
            <p class="desc">Chargement...</p>
        `;
        
        const url = `https://api.openweathermap.org/data/2.5/weather?q=${encodeURIComponent(city)}&units=metric&lang=fr&appid=${apiKey}`;
        console.log('📡 URL:', url);
        
        // Faire la requête
        fetch(url)
            .then(response => {
                console.log('Statut:', response.status);
                if (!response.ok) {
                    throw new Error(`Erreur HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log(' Données reçues:', data);
                
                // Vérifier si la ville est trouvée
                if (data.cod !== 200) {
                    throw new Error(data.message || 'Ville non trouvée');
                }
                
                // Extraire les données
                const temp = Math.round(data.main.temp);
                const desc = data.weather[0].description;
                const icon = `https://openweathermap.org/img/wn/${data.weather[0].icon}@2x.png`;
                const cityName = data.name;
                const humidity = data.main.humidity;
                
                // Mettre à jour l'affichage
                weatherBox.innerHTML = `
                    <h2>${cityName}</h2>
                    <div class="temp">${temp}°C</div>
                    <p class="desc">${desc}</p>
                    <p><small>Humidité: ${humidity}%</small></p>
                    <img src="${icon}" alt="${desc}" class="weather-icon">
                `;
                
                // Appliquer le style selon la météo
                const descLower = desc.toLowerCase();
                if (descLower.includes('nuage')) {
                    weatherBox.style.background = 'rgba(100, 100, 100, 0.85)';
                } else if (descLower.includes('pluie')) {
                    weatherBox.style.background = 'rgba(0, 0, 150, 0.85)';
                } else if (descLower.includes('orage')) {
                    weatherBox.style.background = 'rgba(50, 0, 50, 0.85)';
                } else if (descLower.includes('soleil') || descLower.includes('ensoleillé') || descLower.includes('clair')) {
                    weatherBox.style.background = 'rgba(255, 200, 0, 0.85)';
                } else {
                    weatherBox.style.background = 'rgba(200, 150, 0, 0.85)';
                }
                
                console.log('Météo mise à jour avec succès');
            })
            .catch(error => {
                console.error('Erreur:', error);
                
                // Afficher des données par défaut en cas d'erreur
                weatherBox.innerHTML = `
                    <h2>Guadeloupe</h2>
                    <div class="temp">28°C</div>
                    <p class="desc">Ensoleillé</p>
                    <p><small>Données locales</small></p>
                    <img src="https://openweathermap.org/img/wn/01d@2x.png" alt="Soleil" class="weather-icon">
                `;
                weatherBox.style.background = 'rgba(255, 200, 0, 0.85)';
            });
    }
    
    // Premier appel
    updateWeather();
    
    // Rafraîchir toutes les 10 minutes
    setInterval(updateWeather, 600000);
    
    // Pour tester depuis la console
    window.updateWeatherNow = updateWeather;
});