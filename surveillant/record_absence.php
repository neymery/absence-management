<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include '../includes/db.php';
include '../includes/header.php';
$filiere;
$group_number;
$stagiaires = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter'])) {
        $filiere = $_POST['filiere'];
        $group_number = $_POST['group_number'];
        $absence_date = $_POST['absence_date'];

        try {
            $stmt = $conn->prepare("SELECT * FROM stagiaires WHERE filiere = :filiere AND groupe = :group_number");
            $stmt->bindParam(':filiere', $filiere);
            $stmt->bindParam(':group_number', $group_number);
            $stmt->execute();
            $stagiaires = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['create'])) {
        $stagiaire_id = $_POST['stagiaire_id'];
        $absence_date = $_POST['absence_date'];
        $status = $_POST['status'];
        $hours = $_POST['hours'];
        $filiere = $_POST['filiere'];
        $group_number = $_POST['group_number'];

        try {
            $stmt = $conn->prepare("INSERT INTO absences (stagiaire_id, absence_date, status, hours, filiere, group_number) VALUES (:stagiaire_id, :absence_date, :status, :hours, :filiere, :group_number)");
            for ($i = 0; $i < count($stagiaire_id); $i++) {
                $stmt->bindParam(':stagiaire_id', $stagiaire_id[$i]);
                $stmt->bindParam(':absence_date', $absence_date);
                $stmt->bindParam(':status', $status[$i]);
                $stmt->bindParam(':hours', $hours[$i]);
                $stmt->bindParam(':filiere', $filiere);
                $stmt->bindParam(':group_number', $group_number);
                $stmt->execute();
            }

            $message = "Absences ajoutées avec succès!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
?>

<div class="container">
    <?php if (isset($message)) {
        echo "<p class='text-success'>$message</p>";
    } ?>

    <form method="POST" action="" class="w-100 bg-white">
        <div class="mx-4 my-5 bg-light w-80 text-center border border-left border-dark p-3">
            <div class="d-flex justify-content-around">
                <div class="d-flex justify-content-around align-items-center font-weight-bold">
                    <label for="filiere">Filiere: </label>
                    <select class="form-control" id="filiere" name="filiere" required>
                        <option value="developpement">developpement</option>
                        <option value="security">security</option>
                        <option value="tm">TM</option>
                    </select>
                </div>
                <div class="d-flex align-items-center font-weight-bold">
                    <label for="group_number">Group:</label>
                    <select class="form-control" id="group_number" name="group_number" required>
                        <option value="201">201</option>
                        <option value="103">103</option>
                        <option value="300">300</option>
                    </select>
                </div>
                <div class="d-flex align-items-center font-weight-bold">
                    <label for="absence_date">Date:</label>
                    <input type="date" class="form-control" id="absence_date" name="absence_date" required>
                </div>
            </div>
            <button type="submit" name="filter" class="btn btn-primary mx-auto mt-5">Afficher</button>
        </div>
    </form>

    <?php if (!empty($stagiaires)) { ?>
    <form method="POST" action="" class="w-100 bg-white">
        <input type="hidden" name="absence_date" value="<?php echo htmlspecialchars($_POST['absence_date']); ?>">
        <input type="hidden" name="filiere" value="<?php echo htmlspecialchars($_POST['filiere']); ?>">
        <input type="hidden" name="group_number" value="<?php echo htmlspecialchars($_POST['group_number']); ?>">
        <table class="w-100 table-bordered p-3 my-2">
            <thead>
                <th>Nom/Prénom</th>
                <th>CIN</th>
                <th>Status</th>
                <th>Nbr /h absence</th>
            </thead>
            <tbody>
                <?php foreach ($stagiaires as $stagiaire) { ?>
                <tr class="order">
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="hidden" name="stagiaire_id[]" value="<?php echo $stagiaire['id']; ?>">
                            <input type="text" class="form-control" name="stagiaire_name[]" value="<?php echo htmlspecialchars($stagiaire['name']); ?>" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="text" class="form-control" name="cin[]" value="<?php echo htmlspecialchars($stagiaire['cin']); ?>" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <select class="form-control" name="status[]" required>
                                <option value="absence justifiée">justifié</option>
                                <option value="absence injustifiée">unjustifié</option>
                                <option value="présence">présence</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="number" class="form-control" name="hours[]" required>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" name="create" class="btn btn-primary">Valider</button>
    </form>
    <?php } ?>
</div>

<?php include '../includes/footer.php'; ?>
