<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Absence Management</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .statistics-box {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .statistic {
            text-align: left;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            width: 300px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .statistic h3 {
            margin: 0;
            font-size: 36px;
        }

        .statistic p {
            margin: 5px 0 0;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light px-5">
        <a class="navbar-brand" href="#"><img src="../assets/ofppt-logo.png" alt="logo" height="80" width="80" class="my-2"></a>
        <div class="collapse navbar-collapse">
            <?php if($_SESSION['role']=="surveillant"){ ?>
            <ul class="navbar-nav mr-auto">
                <li class="nav-item  rounded">
                    <a class="nav-link " href="../surveillant/record_absence.php">Saisir absence</a>
                </li>
                <li class="nav-item rounded">
                    <a class="nav-link " href="../surveillant/edit_absence.php">Modifier absence</a>
                </li>
                <li class="nav-item rounded">
                    <a class="nav-link " href="../surveillant/view_absences.php">Suivi Absence</a>
                </li>
            </ul>
            <?php }else{ ?>
                <ul class="navbar-nav mr-auto">
                <li class="nav-item  rounded">
                    <a class="nav-link " href="../directeur/dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item rounded">
                    <a class="nav-link " href="../directeur/view_statistics.php"> Voir les statistiques</a>
                </li>
            </ul>
            <?php } ?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Log out</a>
                </li>
            </ul>
        </div>
    </nav>
