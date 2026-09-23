<?php
require_once "../../config/db.php";
require_once "../../includes/auth.php";

$q = trim($_GET["q"] ?? "");

if ($q !== "") {

    $like = "%" . $q . "%";

    $s = $conn->prepare(
        "SELECT * FROM students 
         WHERE index_number LIKE ? OR name LIKE ? 
         ORDER BY id DESC"
    );

    $s->bind_param("ss", $like, $like);
    $s->execute();

    $students = $s->get_result();

} else {

    $students = $conn->query(
        "SELECT * FROM students ORDER BY id DESC"
    );
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Students</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<?php include "../../includes/navbar.php"; ?>

<div class="container">

    <div class="page-head">

        <h1>Students</h1>

        <a class="btn primary" href="add.php">
            + Add Student
        </a>

    </div>

    <form class="search-form">

        <input 
            name="q" 
            value="<?= htmlspecialchars($q) ?>" 
            placeholder="Search index number or name"
        >

        <button class="btn">
            Search
        </button>

    </form>

    <div class="table-wrap">

        <table>

            <tr>
                <th>Index Number</th>
                <th>Name</th>
                <th>Grade</th>
                <th>Contact</th>
                <th>Registration</th>
                <th>Actions</th>
            </tr>

            <?php while ($s = $students->fetch_assoc()): ?>

                <tr>

                    <td>
                        <?= htmlspecialchars($s["index_number"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($s["name"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($s["grade"]) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($s["contact_number"]) ?>
                    </td>

                    <td>
                        <?= $s["registration_date"] ?>
                    </td>

                    <td>

                        <a 
                            class="btn small" 
                            href="edit.php?id=<?= $s["id"] ?>"
                        >
                            Edit
                        </a>

                        <a 
                            class="btn small danger" 
                            onclick="return confirm('Delete this student?')" 
                            href="delete.php?id=<?= $s["id"] ?>"
                        >
                            Delete
                        </a>

                    </td>

                </tr>

            <?php endwhile; ?>

        </table>

    </div>

</div>

</body>
</html>