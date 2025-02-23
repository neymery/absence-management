<?php
session_start();
require '../includes/db.php';
require '../includes/functions.php';
include '../includes/header.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

redirectIfNotLoggedIn();

$filiere = isset($_POST['filiere']) ? $_POST['filiere'] : '';
$group_number = isset($_POST['group_number']) ? $_POST['group_number'] : '';
$academic_year = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';

$query = "
    SELECT stagiaires.cin, stagiaires.name, stagiaires.filiere, stagiaires.groupe,
           IFNULL(SUM(CASE WHEN absences.status = 'absence injustifiée' THEN absences.hours ELSE 0 END), 0) as total_unjustified_hours,
           IFNULL(SUM(CASE WHEN absences.status = 'absence justifiée' THEN absences.hours ELSE 0 END), 0) as total_justified_hours
    FROM stagiaires
    LEFT JOIN absences ON absences.stagiaire_id = stagiaires.id
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

$query .= " GROUP BY stagiaires.cin, stagiaires.name, stagiaires.filiere, stagiaires.groupe";

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
$absences = $stmt->fetchAll();

function determineSanction($hours)
{
    if ($hours >= 50)
        return 'Exclusion définitive';
    if ($hours >= 35)
        return 'Exclusion temporaire ou définitive';
    if ($hours >= 30)
        return 'Exclusion de 2 jours';
    if ($hours >= 25)
        return 'Blâme';
    if ($hours >= 20)
        return '2ème avertissement';
    if ($hours >= 15)
        return '1er avertissement';
    if ($hours >= 10)
        return '2ème Mise en garde';
    if ($hours >= 5)
        return '1ère Mise en garde';
    return 'Aucune sanction';
}
?>

<div class="container">
    <h2 class="text-center my-4">Tableau de bord de l'administrateur</h2>
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
                <th>Nom/Prénom</th>
                <th>CIN</th>
                <th>Filière</th>
                <th>Groupe</th>
                <th>Somme absence justifiée</th>
                <th>Somme absence injustifiée</th>
                <th>Type de sanction</th>
                <th>Rapport</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($absences as $absence): ?>
                <tr>
                    <td><?= htmlspecialchars($absence['name']) ?></td>
                    <td><?= htmlspecialchars($absence['cin']) ?></td>
                    <td><?= htmlspecialchars($absence['filiere']) ?></td>
                    <td><?= htmlspecialchars($absence['groupe']) ?></td>
                    <td><?= htmlspecialchars($absence['total_justified_hours']) ?></td>
                    <td><?= htmlspecialchars($absence['total_unjustified_hours']) ?></td>
                    <td class="<?= determineSanction($absence['total_unjustified_hours'])=='Exclusion définitive'? 'text-danger':''  ?>" ><?= determineSanction($absence['total_unjustified_hours']) ?></td>
                    <td>
                        <button type="button" class="btn btn-success generate-pdf" data-toggle="modal"
                            data-target="#reportModal" data-name="<?= htmlspecialchars($absence['name']) ?>"
                            data-id="<?= htmlspecialchars($absence['cin']) ?>">Rapport</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" aria-labelledby="reportModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Generate Report</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="reportForm" action="./create_pdf.php" method="GET">
                    <div class="form-group">
                        <label for="fromDate">From date</label>
                        <input type="date" class="form-control" id="fromDate" name="fromDate" required>
                    </div>
                    <div class="form-group">
                        <label for="toDate">To date</label>
                        <input type="date" class="form-control" id="toDate" name="toDate" required>
                    </div>
                    <input type="hidden" id="stagiaireId" name="stagiaireId">
                    <button type="submit" class="btn btn-success">Create</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchName').addEventListener('keyup', function() {
        var searchValue = this.value.toLowerCase();
        var rows = document.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            var name = row.querySelectorAll('td')[0].textContent.toLowerCase();
            if (name.includes(searchValue)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    $('#reportModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var name = button.data('name');
        var id = button.data('id');
        var modal = $(this);
        modal.find('#reportModalLabel').text('Generate Report for ' + name);
        modal.find('#stagiaireId').val(id);
    });
</script>

<?= include '../includes/footer.php' ?>;
