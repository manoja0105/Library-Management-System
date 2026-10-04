
<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";
require_once "../../includes/csrf.php";


$holds = $conn->query(
    "SELECT h.*,
            s.name AS student_name,
            s.index_number,
            b.book_id AS display_book_id,
            b.title
     FROM book_holds h
     JOIN students s ON h.student_id = s.id
     JOIN books b ON h.book_id = b.id
     ORDER BY h.id DESC"
);

if (!$holds) {
    die("Database Error: " . htmlspecialchars($conn->error));
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hold Requests</title>

    <link rel="stylesheet" href="../../assets/css/style.css">

    <style>
        
        .actions {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        .btn.small {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            color: #ffffff;
            font-size: 13px;
            cursor: pointer;
            font-weight: 500;
        }

        .btn.small.success {
             background: #1769aa;
    color: #ffffff;
    border-color: #1769aa;
     min-height: 32px;
    padding: 6px 11px;
    font-size: 12px;
    border-radius: 5px;
        }

        .btn.small.success:hover {
            background: #216783;
            border-color: #216783;
        }

        .btn.small.danger {
            background: #b83f4b;
            color: #ffffff;
            border-color: #b83f4b;
        }

        .btn.small.danger:hover {
            background: #bb2d3b;
        }

        .inline-form {
            display: inline;
            margin: 0;
        }

        
        .status-expired {
            color: #dc3545;
            font-weight: bold;
        }

        .status-cancelled {
            color: #6c757d;
            font-weight: bold;
        }

        .status-borrowed {
            color: #198754;
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
            font-weight: 500;
        }

        
        .badge {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-active {
            background: #d1e7dd;
            color: #0f5132;
             min-height: 32px;
    padding: 6px 11px;
    font-size: 12px;
    border-radius: 5px;
        }

        .status-expired {
            background: #f8d7da;
            color: #842029;
            padding: 5px 9px;
           
             min-height: 32px;
    padding: 6px 11px;
    font-size: 12px;
    border-radius: 5px;
        }

        .status-cancelled {
            background: #e2e3e5;
            color: #41464b;
            padding: 5px 9px;
            
             min-height: 32px;
    padding: 6px 11px;
    font-size: 12px;
    border-radius: 5px;
        }

        .status-borrowed {
            background: #d1e7dd;
            color: #0f5132;
            padding: 5px 9px;
           
             min-height: 32px;
    padding: 6px 11px;
    font-size: 12px;
    border-radius: 5px;
        }
    </style>
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="page-head">

        <h1>Hold Requests</h1>

        <a class="btn" href="../dashboard.php">
            ← Dashboard
        </a>

    </div>


    
    <?php if (!empty($_SESSION["hold_admin_message"])): ?>

        <div class="alert success">

            <?= htmlspecialchars($_SESSION["hold_admin_message"]) ?>

        </div>

        <?php unset($_SESSION["hold_admin_message"]); ?>

    <?php endif; ?>


    
    <?php if (!empty($_SESSION["hold_admin_error"])): ?>

        <div class="alert error">

            <?= htmlspecialchars($_SESSION["hold_admin_error"]) ?>

        </div>

        <?php unset($_SESSION["hold_admin_error"]); ?>

    <?php endif; ?>


    <div class="table-wrap">

        <table>

            <thead>

            <tr>

                <th>Student</th>

                <th>Book</th>

                <th>Email</th>

                <th>Phone</th>

                <th>Hold Time</th>

                <th>Expiry</th>

                <th>Status</th>

                <th>Notification</th>

                <th>Action</th>

            </tr>

            </thead>


            <tbody>

            <?php if ($holds->num_rows > 0): ?>

                <?php while ($h = $holds->fetch_assoc()): ?>

                    <tr>

                       
                        <td>

                            <?= htmlspecialchars($h["index_number"]) ?>

                            <br>

                            <?= htmlspecialchars($h["student_name"]) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars($h["display_book_id"]) ?>

                            <br>

                            <?= htmlspecialchars($h["title"]) ?>

                        </td>


                        
                        <td>

                            <?= htmlspecialchars($h["email"]) ?>

                        </td>


                        
                        <td>

                            <?= htmlspecialchars($h["phone_number"]) ?>

                        </td>


                        
                        <td>

                            <?= htmlspecialchars(
                                date(
                                    "d M Y, h:i A",
                                    strtotime($h["hold_time"])
                                )
                            ) ?>

                        </td>


                        
                        <td>

                            <?= htmlspecialchars(
                                date(
                                    "d M Y, h:i A",
                                    strtotime($h["expiry_time"])
                                )
                            ) ?>

                        </td>


                        
                        <td>

                            <?php if ($h["status"] === "ACTIVE"): ?>

                                <span class="badge status-active">
                                    ACTIVE
                                </span>

                            <?php elseif ($h["status"] === "EXPIRED"): ?>

                                <span class="badge status-expired">
                                    EXPIRED
                                </span>

                            <?php elseif ($h["status"] === "CANCELLED"): ?>

                                <span class="badge status-cancelled">
                                    CANCELLED
                                </span>

                            <?php elseif ($h["status"] === "BORROWED"): ?>

                                <span class="badge status-borrowed">
                                    BORROWED
                                </span>

                            <?php else: ?>

                                <span class="badge">
                                    <?= htmlspecialchars($h["status"]) ?>
                                </span>

                            <?php endif; ?>

                        </td>


                        
                        <td>

                            <?php if (
                                (int)$h["notification_sent"] === 1
                            ): ?>

                                <span class="badge">
                                    Sent
                                </span>

                            <?php elseif (
                                $h["status"] === "ACTIVE"
                            ): ?>

                                <span class="text-muted">
                                    Pending
                                </span>

                            <?php else: ?>

                                <span class="text-muted">
                                    Not sent
                                </span>

                            <?php endif; ?>

                        </td>


            

<td>

    <?php
   
    $isExpiredByTime =
        strtotime($h["expiry_time"]) <= time();

  
    if (
        $h["status"] === "ACTIVE" &&
        !$isExpiredByTime
    ):
    ?>

        <div class="actions">

            
            <a class="btn small success"
                href="borrow.php?id=<?= (int)$h["id"] ?>" >
                Borrow
            </a>

            
            <form method="POST"
                action="cancel.php"
                class="inline-form" >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= htmlspecialchars(csrf_token()) ?>" >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$h["id"] ?>" >

                <button
                    type="submit"
                    class="btn small danger"
                    onclick="return confirm('Cancel this active hold?');">
                    Cancel
                </button>

            </form>

        </div>

    <?php elseif (
        $h["status"] === "ACTIVE" &&
        $isExpiredByTime
    ): ?>

    
        <span class="status-expired">
            Expired
        </span>

    <?php elseif ($h["status"] === "EXPIRED"): ?>

        <span class="status-expired">
            Expired
        </span>

    <?php elseif ($h["status"] === "CANCELLED"): ?>

        <span class="status-cancelled">
            Cancelled
        </span>

    <?php elseif ($h["status"] === "BORROWED"): ?>

        <span class="status-borrowed">
            Borrowed
        </span>

    <?php endif; ?>

</td>


                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="9"
                        class="empty-state"
                    >
                        No hold requests found.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>

</html>

