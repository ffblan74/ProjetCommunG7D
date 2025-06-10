console.log('Test de chargement du JS');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM chargé - Test');
    
    // Test simple des clics
    const links = document.querySelectorAll('.nav-item');
    console.log('Liens trouvés:', links.length);
    
    links.forEach(link => {
        console.log('Ajout listener sur:', link.dataset.section);
        link.onclick = function(e) {
            e.preventDefault();
            console.log('CLICK sur:', this.dataset.section);
            alert('Click sur ' + this.dataset.section);
        };
    });
});

// Fonction pour charger les événements
function chargerEvenements() {
    fetch('index.php?page=admin&action=getEvents')
        .then(response => response.json())
        .then(events => {
            const tbody = document.getElementById('events-table-body');
            tbody.innerHTML = '';
            
            if (events.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7">Aucun événement trouvé</td></tr>';
                return;
            }
            
            events.forEach(event => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${event.id_event}</td>
                    <td>${event.nom_event}</td>
                    <td>${new Date(event.date_planifiee).toLocaleDateString()}</td>
                    <td>${event.adresse_event}</td>
                    <td>${event.nom_organisateur}</td>
                    <td>${event.etat}</td>
                    <td>
                        <button onclick="supprimerEvenement(${event.id_event})">Supprimer</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('events-table-body').innerHTML = 
                '<tr><td colspan="7">Erreur lors du chargement des événements</td></tr>';
        });
}

// Fonction pour charger les utilisateurs
function chargerUtilisateurs() {
    fetch('index.php?page=admin&action=getUsers')
        .then(response => response.json())
        .then(users => {
            const tbody = document.getElementById('users-table-body');
            tbody.innerHTML = '';
            
            if (users.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5">Aucun utilisateur trouvé</td></tr>';
                return;
            }
            
            users.forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${user.id_participant}</td>
                    <td>${user.nom_participant}</td>
                    <td>${user.prenom}</td>
                    <td>${user.email}</td>
                    <td>
                        <button onclick="supprimerUtilisateur(${user.id_participant})">Supprimer</button>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('users-table-body').innerHTML = 
                '<tr><td colspan="5">Erreur lors du chargement des utilisateurs</td></tr>';
        });
}

// Fonction pour supprimer un événement
function supprimerEvenement(id) {
    if (confirm('Voulez-vous vraiment supprimer cet événement ?')) {
        fetch('index.php?page=admin&action=deleteEvent', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'eventId=' + id
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                chargerEvenements();
            } else {
                alert('Erreur lors de la suppression');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
        });
    }
}

// Fonction pour supprimer un utilisateur
function supprimerUtilisateur(id) {
    if (confirm('Voulez-vous vraiment supprimer cet utilisateur ?')) {
        fetch('index.php?page=admin&action=deleteUser', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
            body: 'userId=' + id
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
                chargerUtilisateurs();
        } else {
                alert('Erreur lors de la suppression');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
            alert('Erreur lors de la suppression');
    });
    }
} 