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
$absence_date;
$absences = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['filter'])) {
        $filiere = $_POST['filiere'];
        $group_number = $_POST['group_number'];
        $absence_date = $_POST['absence_date'];

        try {
            $stmt = $conn->prepare("SELECT a.*, s.name, s.cin FROM absences a JOIN stagiaires s ON a.stagiaire_id = s.id WHERE a.filiere = :filiere AND a.group_number = :group_number AND a.absence_date = :absence_date");
            $stmt->bindParam(':filiere', $filiere);
            $stmt->bindParam(':group_number', $group_number);
            $stmt->bindParam(':absence_date', $absence_date);
            $stmt->execute();
            $absences = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    if (isset($_POST['update'])) {
        $absence_ids = $_POST['absence_id'];
        $status = $_POST['status'];
        $hours = $_POST['hours'];

        try {
            $stmt = $conn->prepare("UPDATE absences SET status = :status, hours = :hours WHERE id = :absence_id");
            for ($i = 0; $i < count($absence_ids); $i++) {
                $stmt->bindParam(':status', $status[$i]);
                $stmt->bindParam(':hours', $hours[$i]);
                $stmt->bindParam(':absence_id', $absence_ids[$i]);
                $stmt->execute();
            }

            $message = "Absences mises à jour avec succès!";
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
                <div class="d-flex justify-content-around align-items-center font-weight-bold ">
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

    <?php if (!empty($absences)) { ?>
    <form method="POST" action="" class="w-100 bg-white">
        <table class="w-100 table-bordered p-3 my-2">
            <thead>
                <th>Nom/Prénom</th>
                <th>CIN</th>
                <th>Status</th>
                <th>Nbr /h absence</th>
            </thead>
            <tbody>
                <?php foreach ($absences as $absence) { ?>
                <tr class="order">
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="hidden" name="absence_id[]" value="<?php echo $absence['id']; ?>">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($absence['name']); ?>" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="text" class="form-control" value="<?php echo htmlspecialchars($absence['cin']); ?>" readonly>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <select class="form-control" name="status[]" required>
                                <option value="absence justifiée" <?php echo $absence['status'] == 'absence justifiée' ? 'selected' : ''; ?>>justifié</option>
                                <option value="absence injustifiée" <?php echo $absence['status'] == 'absence injustifiée' ? 'selected' : ''; ?>>unjustifié</option>
                                <option value="présence" <?php echo $absence['status'] == 'présence' ? 'selected' : ''; ?>>présence</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="form-group my-5 mx-3">
                            <input type="number" class="form-control" name="hours[]" value="<?php echo $absence['hours']; ?>" required>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <button type="submit" name="update" class="btn btn-primary">Modifier</button>
    </form>
    <?php } ?>
</div>

<?php include '../includes/footer.php'; ?>
