function updateNotifications() {
    fetch('?get_count=1')
    .then(response => response.text())
    .then(count => {
        document.getElementById('notifCount').innerText = count;

        let badge = document.getElementById('notifBadge');

        if (count > 0) {
            badge.style.display = 'inline-block';
            badge.innerText = count;
        } else {
            badge.style.display = 'none';
        }
    });
}

setInterval(updateNotifications, 5000);
updateNotifications();