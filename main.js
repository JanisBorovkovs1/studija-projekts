function updateNotifications() {
    // Pieprasām datus no next.php
    fetch('?get_count=1')
    .then(response => response.text())
    .then(countText => {
        // 1. Notīrām atstarpes un pārvēršam tekstu par skaitli
        let count = parseInt(countText.trim());

        // Atrodam elementus
        let badge = document.getElementById('notifBadge');
        let countDisplay = document.getElementById('notifCount');

        // 2. Ja izdevās iegūt skaitli un tas ir lielāks par 0
        if (!isNaN(count) && count > 0) {
            badge.style.display = 'inline-block'; // Parādām badge
            if (countDisplay) countDisplay.innerText = count;
            badge.innerText = count;
        } else {
            // Ja paziņojumu nav (0) vai radās kļūda, paslēpjam
            badge.style.display = 'none';
        }
    })
    .catch(error => console.error('Kļūda ielādējot paziņojumus:', error));
}

// Pārbaudām ik pēc 5 sekundēm
setInterval(updateNotifications, 5000);

// Izsaucam arī uzreiz ielādējot lapu
updateNotifications();