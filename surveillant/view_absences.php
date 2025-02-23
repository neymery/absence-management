<?php
session_start();
require '../includes/db.php';
require '../includes/functions.php';
include '../includes/header.php';
redirectIfNotLoggedIn();

// Fetch stagiaires with optional filters
$filiere = isset($_POST['filiere']) ? $_POST['filiere'] : '';
$group_number = isset($_POST['group_number']) ? $_POST['group_number'] : '';
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';

$query = "
    SELECT stagiaires.id, stagiaires.cin, stagiaires.name, stagiaires.email
    FROM stagiaires
    WHERE 1=1";

if ($filiere) {
    $query .= " AND stagiaires.filiere = :filiere";
}
if ($group_number) {
    $query .= " AND stagiaires.groupe = :group_number";
}
if ($academic_year) {
    $query .= " AND stagiaires.academic_year = :academic_year";
}

$stmt = $conn->prepare($query);

if ($filiere) {
    $stmt->bindParam(':filiere', $filiere);
}
if ($group_number) {
    $stmt->bindParam(':group_number', $group_number);
}
if ($academic_year) {
    $stmt->bindParam(':academic_year', $academic_year);
}

$stmt->execute();
$stagiaires = $stmt->fetchAll();

$absences = [];
foreach ($stagiaires as $stagiaire) {
    $stagiaire_id = $stagiaire['id'];
    
    $absence_query = "
        SELECT SUM(CASE WHEN absences.status = 'absence injustifiée' THEN absences.hours ELSE 0 END) as total_unjustified_hours
        FROM absences
        WHERE absences.stagiaire_id = :stagiaire_id
    ";
    
    $absence_stmt = $conn->prepare($absence_query);
    $absence_stmt->bindParam(':stagiaire_id', $stagiaire_id);
    $absence_stmt->execute();
    $absence_data = $absence_stmt->fetch();
    
    $total_unjustified_hours = $absence_data['total_unjustified_hours'] ?? 0;
    
    $absences[] = [
        'cin' => $stagiaire['cin'],
        'name' => $stagiaire['name'],
        'email' => $stagiaire['email'],
        'total_unjustified_hours' => $total_unjustified_hours
    ];
}

function determineSanction($hours) {
    if ($hours >= 50) return 'Exclusion définitive';
    if ($hours >= 35) return 'Exclusion temporaire ou définitive';
    if ($hours >= 30) return 'Exclusion de 2 jours';
    if ($hours >= 25) return 'Blâme';
    if ($hours >= 20) return '2ème avertissement';
    if ($hours >= 15) return '1er avertissement';
    if ($hours >= 10) return '2ème Mise en garde';
    if ($hours >= 5)  return '1ère Mise en garde';
    return 'Aucune sanction';
}
?>


    <div class="container">
        <h2 class="text-center my-4">Suivi des Absences</h2>
        <form method="POST" class="form-inline justify-content-center mb-4">
            <label for="filiere" class="mr-2">Filière :</label>
            <select class="form-control mr-4" id="filiere" name="filiere">
                <option value="">Tous</option>
                <option value="developpement" <?= $filiere == 'developpement' ? 'selected' : '' ?>>Développement</option>
                <option value="sécurité" <?= $filiere == 'sécurité' ? 'selected' : '' ?>>Sécurité</option>
                <option value="TM" <?= $filiere == 'TM' ? 'selected' : '' ?>>TM</option>
            </select>

            <label for="group_number" class="mr-2">Groupe :</label>
            <select class="form-control mr-4" id="group_number" name="group_number">
                <option value="">Tous</option>
                <option value="201" <?= $group_number == '201' ? 'selected' : '' ?>>201</option>
                <option value="103" <?= $group_number == '103' ? 'selected' : '' ?>>103</option>
                <option value="300" <?= $group_number == '300' ? 'selected' : '' ?>>300</option>
            </select>

            <label for="academic_year" class="mr-2">Année scolaire :</label>
            <select class="form-control" id="academic_year" name="academic_year">
                <option value="">Tous</option>
                <option value="2024/2025" <?= $academic_year == '2024/2025' ? 'selected' : '' ?>>2024/2025</option>
                <option value="2023/2024" <?= $academic_year == '2023/2024' ? 'selected' : '' ?>>2023/2024</option>
                <option value="2022/2023" <?= $academic_year == '2022/2023' ? 'selected' : '' ?>>2022/2023</option>
            </select>

            <button type="submit" class="btn btn-primary ml-4">Afficher</button>
        </form>

        <input type="text" id="searchName" class="form-control mb-4" placeholder="Rechercher un nom">

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>CIN</th>
                    <th>Nom/Prénom</th>
                    <th>Email</th>
                    <th>Total absence injustifiée</th>
                    <th>Type sanction</th>
                    <th>Contacter stagiaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($absences as $absence): ?>
                    <tr>
                        <td><?= htmlspecialchars($absence['cin']) ?></td>
                        <td><?= htmlspecialchars($absence['name']) ?></td>
                        <td><?= htmlspecialchars($absence['email']) ?></td>
                        <td><?= htmlspecialchars($absence['total_unjustified_hours']) ?></td>
                        <td><?= determineSanction($absence['total_unjustified_hours']) ?></td>
                        <td>
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#emailModal" data-name="<?= htmlspecialchars($absence['name']) ?>" data-email="<?= htmlspecialchars($absence['email']) ?>">Email</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="emailModal" tabindex="-1" role="dialog" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" action="./send_email.php">
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailModalLabel">Envoyer un email</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="stagiaire_name">Nom de stagiaire</label>
                            <input type="text" class="form-control" id="stagiaire_name" name="stagiaire_name" readonly>
                        </div>
                        <div class="form-group">
                            <label for="email_subject">Sujet</label>
                            <input type="text" class="form-control" id="email_subject" name="subject" required>
                        </div>
                        <div class="form-group">
                            <label for="email_content">Contenu</label>
                            <textarea class="form-control" id="email_content" name="content" rows="4" required></textarea>
                        </div>
                        <input type="hidden" id="stagiaire_email" name="email">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Envoyer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('searchName').addEventListener('keyup', function() {
        var searchValue = this.value.toLowerCase();
        var rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var name = row.querySelectorAll('td')[1].textContent.toLowerCase();
            if (name.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    $('#emailModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var email = button.data('email');
        var modal = $(this);
        modal.find('.modal-body #stagiaire_name').val(name);
        modal.find('.modal-body #stagiaire_email').val(email);
    });
    </script>

    <?php include '../includes/footer.php'; ?>

