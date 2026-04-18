function setMapImage(mapFile) {
    document.getElementById('mapBg').src = `/assets/images/maps/${mapFile}`;
}

function renderPins(clans) {
    const container = document.getElementById('mapPins');
    const infoBox = document.getElementById('mapInfo');

    container.innerHTML = '';

    clans.forEach(clan => {
        const pin = document.createElement('div');
        pin.className = 'pin';

        pin.style.left = clan.x + '%';
        pin.style.top = clan.y + '%';

        pin.innerHTML = `<img src="/assets/images/pin.png">`;

        pin.onclick = () => {
            infoBox.innerHTML = `
                <h3>${clan.clan_name}</h3>
                <p>${clan.leader || ''}</p>
            `;
        };

        container.appendChild(pin);
    });
}