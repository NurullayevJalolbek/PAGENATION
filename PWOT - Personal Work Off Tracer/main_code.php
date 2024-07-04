<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ish vaqti kiritish</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        p.qizil-tex {
            color: red;
        }
    </style>
</head>

<body>
    <?php $umumiyMINUT = NULL; ?>
    <div class="container">
        <label>
            Ish vaqti 08:00 dan<br>
            Tugashi 17:00 gacha shu oraliqda hisoblanadi
        </label><br>
        <div class="container">
            <h1> PWOT - Personal Off Tracer</h1>
        </div>

        <form action="main_code.php" method="POST">
            <div class="row g-3">
                <div class="col">
                    <input type="datetime-local" class="form-control" name="arrival_time">
                </div>
                <div class="col">
                    <input type="datetime-local" class="form-control" name="end_time">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">SUBMIT</button>
            <br>
        </form>
        <?php
        require "Read_information.php";

        if (isset($_POST['arrival_time']) && isset($_POST['end_time'])) {
            if ($_POST['arrival_time'] != ""  &&  $_POST['end_time'] != "") {
                $ARRIVAL_TIME = $_POST['arrival_time'];
                $END_TIME = $_POST['end_time'];
                $num = new Read_information($ARRIVAL_TIME, $END_TIME);
                $ish_soati =  $num->Working_time();
                $qarz_soati =  $num->Debt();

                $pdo = new PDO(
                    $dsn = 'mysql:host=localhost;dbname=pwot',
                    $username = 'root',
                    $password = '@jalol2004'
                );

                $query = "INSERT INTO  pwot_table (arrival_time, time_to_leave, worked, debt)
                                        VALUES (:arrival_time, :time_to_leave, :worked, :debt)";
                $stmt = $pdo->prepare($query);
                $stmt->bindParam(':arrival_time', $ARRIVAL_TIME);
                $stmt->bindParam(':time_to_leave', $END_TIME);
                $stmt->bindParam(':worked', $ish_soati);
                $stmt->bindParam(':debt', $qarz_soati);

                $stmt->execute();
            }
        }

        $pdo = new PDO(
            $dsn = 'mysql:host=localhost;dbname=pwot',
            $username = 'root',
            $password = '@jalol2004'
        );

        $limit = 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $number = $pdo->query("SELECT COUNT(*) FROM pwot_table")->fetchColumn();
        $num2 = ceil($number / $limit);

        $query = $pdo->prepare("SELECT * FROM pwot_table LIMIT :limit OFFSET :offset");
        $query->bindParam(':limit', $limit, PDO::PARAM_INT);
        $query->bindParam(':offset', $offset, PDO::PARAM_INT);
        $query->execute();
        $rows = $query->fetchAll();

        echo "<h3> Malumotlar Jadval ko'rinishida </h3>";
        echo '<table class="table table-striped-columns">';
        echo '<thead class="thead-dark">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>arrival_time</th>';
        echo '<th>time_to_leave</th>';
        echo '<th>debt</th>';
        echo '<th>one_and_zero</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ($rows as $row) {
            $a1 = $row['debt'];
            $a1 = explode(":", $a1);
            $b1 = $row['worked'];
            $b1 = explode(":", $b1);

            echo '<tr>';
            echo '<td>' . $row['id'] . '</td>';
            echo '<td>' . $row['arrival_time'] . '</td>';
            echo '<td>' . $row['time_to_leave'] . '</td>';
            echo '<td>' . $row['debt'] . '</td>';
            echo '<td>';
            if ($a1[1] == '00' && $a1[0] == '00') {
                $row['debt'] = '00'; ?>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" checked disabled>
                    <label class="form-check-label">
                        Done
                    </label>
                </div>
            <?php } else { ?>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal<?php echo $row['id']; ?>">
                    Done
                </button>

                <div class="modal fade" id="exampleModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                Saqlidigan bo'lsangiz qaytarib bo'lmaydi.......!
                            </div>
                            <div class="modal-footer">
                                <form action="./main_code.php" method="POST">
                                    <input type="hidden" name="done" value="<?php echo $row['id']; ?>">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Saqlash</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        <?php  }
        }
        echo '</tbody>';
        echo '</table>';

        foreach ($rows as $row) {
            $time_parts = explode(":", $row['debt']);
            $umumiyMINUT += ($time_parts[0] * 60) + $time_parts[1];
        }
        $soat1 = floor($umumiyMINUT / 60);
        $daqiqa1 = $umumiyMINUT % 60;

        echo '<p class="qizil-tex">Umumiy qarzingiz: ' . "$soat1 soat $daqiqa1 daqiqa" . '</p>';

        echo '<nav aria-label="Page navigation">';
        echo '<ul class="pagination">';
        if ($page > 1) {
            echo '<li class="page-item"><a class="page-link" href="?page=' . $page - 1 . '">Orqaga</a></li>';
        }
        for ($i = 1; $i <= $num2; $i++) {
            $active = $i == $page ? 'active' : '';
            echo '<li class="page-item' . $active . '"><a class="page-link" href= ?page='.$i.'">' . $i . '</a></li>';
        }
        if ($page < $num2) {
            echo '<li class="page-item"><a class="page-link" href="?page=' .$page + 1 . '">Oldinga</a></li>';
        }
        echo '</ul>';
        echo '</nav>';
        ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>