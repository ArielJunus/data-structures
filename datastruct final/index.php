<?php
require_once("dbfetch.php");
$query = "select * from loan_table";
$result = mysqli_query($conn, $query);

if (isset($_GET['id'])) {
    $id=$_GET['id'];
    $delete=mysqli_query($conn, "DELETE FROM `loan_table` WHERE `id` = '$id'");
    header("location:index.php");
    exit();
}

$today = date('Y-m-d');
$warning_date = date('Y-m-d', strtotime('+3 days'));

$date_sql = "SELECT * FROM loan_table WHERE duedate BETWEEN '$today' AND '$warning_date'";
$date_result = $conn->query($date_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
    <link rel="stylesheet" href="bootstrap.min.css" />
    <title>Loan System Admin Webpage</title>
</head>

<body class="bg-dark">
    <div class="container">
        <div class="container text-center">
            <div class="panel-heading text-center text-white">
                <h1>Loan System Admin Page</h1>
                <div class="sub-container">
                    <form action="connect.php" method="post">
                        <?php
                        session_start();
                        if (isset($_SESSION['errors'])) {
                            foreach ($_SESSION['errors'] as $error) {
                                echo "<div class='alert alert-danger' style='color:red;'>$error</div>";
                            }
                            unset($_SESSION['errors']);
                        }
                        if (isset($_SESSION['success'])) {
                            echo "<div class='alert alert-success' style='color:green;'>{$_SESSION['success']}</div>";
                            unset($_SESSION['success']);
                        }
                        ?>

                        <div class="user-amount-container text-black">
                            <h3 style="color:black;">Name</h3>
                            <input type="text" class="form-control" id="name" placeholder="Enter Name" name='name'>
                            <h3 style="color:black;">Loan Amount</h3>
                            <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter Total Amount">
                            <h3 style="color:black;">Due Date</h3>
                            <input type="date" id="duedate" name="duedate" placeholder="Enter Date">
                            <button class="submit" id="total-amount-button">
                                Input Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row mt-5">
                <div class="col">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="display-6 text-center">Loan Database</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered text-center">
                                <thead class="text-white bg-dark">
                                    <tr>
                                        <th>user id</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                        <th>Delete</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                        <tr>
                                            <td><?php echo $row['id']; ?></td>
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['amount']; ?></td>
                                            <td><?php echo $row['duedate']; ?></td>
                                            <td><a href="index.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger">Delete</a></td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card mt-5">
                        <div class="card-header">
                            <h3 class="display-6 text-center text-danger"> !!! Due soon !!!</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered text-center">
                                <thead class="text-white bg-danger">
                                    <tr>
                                        <th>user id</th>
                                        <th>Name</th>
                                        <th>Amount</th>
                                        <th>Due Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($date_result->num_rows > 0) { ?>
                                        <?php while ($row = $date_result->fetch_assoc()) { ?>
                                            <tr>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['amount']; ?></td>
                                                <td><?php echo $row['duedate']; ?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="4">No loans due in the next 3 days.</td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-5">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h3 class="display-6 text-center">Summarized Loan Applications</h3>
                    </div>
                    <div class="card-body text-center">
                        <!-- Button to redirect to the summarization PHP page -->
                        <form action="summarize.php" method="post">
                            <button type="submit" class="btn btn-primary">Generate Summary</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>