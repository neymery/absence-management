<?php
session_start();
require '../includes/db.php';
require '../includes/functions.php';
include '../includes/header.php';
redirectIfNotLoggedIn();
redirectIfNotAdmin();

$year = isset($_GET['year']) ? intval($_GET['year']) : date('Y');

$statistics = $conn->query("
    SELECT 
        (SELECT COUNT(*) FROM Stagiaires WHERE academic_year LIKE '%$year%') AS total_Stagiaires,
        (SELECT COUNT(DISTINCT filiere) FROM Stagiaires WHERE academic_year LIKE '%$year%') AS total_filieres,
        (SELECT COUNT(*) FROM Stagiaires WHERE id IN (SELECT stagiaire_id FROM absences WHERE hours > 25) AND academic_year LIKE '%$year%') AS excluded_Stagiaires
")->fetch();
?>

    <div class="container">
        <h2 class="text-center my-4">Statistiques</h2>
        <form method="GET" action="" class="form-inline justify-content-center mb-4">
            <div class="form-group">
                <label for="year" class="mr-2">Année:</label>
                <input type="number" id="year" name="year" class="form-control" value="<?= htmlspecialchars($year) ?>"
                    required>
                <button type="submit" class="btn btn-primary ml-2">Rechercher</button>
            </div>
        </form>
        <div class="statistics-box">
            <div class="statistic">
                <div class="d-flex justify-content-between">
                    <h3><?= $statistics['total_Stagiaires'] ?>
                    </h3>
                    <img src="../assets/man-student-svgrepo-com.svg" alt="stagiaire icon" height="100" width="100">
                </div>
                <p>Somme des stagiaires</p>
            </div>
            <div class="statistic">
                <div class="d-flex justify-content-between">

                    <h3><?= $statistics['total_filieres'] ?></h3>
                    <img src="../assets/organization-svgrepo-com.svg" alt="stagiaire icon" height="100" width="100">
                </div>
                <p>Somme des filières</p>
            </div>
            <div class="statistic">
                <div class="d-flex justify-content-between">
                    <h3><?= $statistics['excluded_Stagiaires'] ?></h3>
                    <img src="../assets/user-banned-svgrepo-com.svg" alt="stagiaire icon" height="100" width="100">

                </div>
                <p>Stagiaires exclus</p>
            </div>
        </div>
    </div>
<?= include '../includes/footer.php' ?>;