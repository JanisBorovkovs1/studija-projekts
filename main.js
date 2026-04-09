function updateNotifications() {
    fetch('?get_count=1')
    .then(response => response.text())
    .then(countText => {
        let count = parseInt(countText.trim());
        let badge = document.getElementById('notifBadge');
        let countDisplay = document.getElementById('notifCount');

        if (!isNaN(count) && count > 0) {
            // Parādām badge un ierakstām skaitli
            badge.style.setProperty('display', 'inline-block', 'important');
            badge.style.setProperty('visibility', 'visible', 'important');
            badge.style.setProperty('opacity', '1', 'important');
            
            if (countDisplay) countDisplay.innerText = count;
        } else {
            // Paslēpjam, ja paziņojumu tiešām nav
            badge.style.setProperty('display', 'none', 'important');
        }
    })
    .catch(err => console.error('Kļūda:', err));
}

// Pārbaude ik pēc 5 sekundēm
setInterval(updateNotifications, 5000);
updateNotifications();