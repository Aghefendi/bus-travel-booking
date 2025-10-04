<html>
<?php $title = "Search"; ?>
<?php include 'includes/head.inc.php'; ?>
<?php include 'includes/secureSession.inc.php'; ?>
<?php include 'includes/db.inc.php'; ?>

<body>



    <div class="container mt-5">


        <h2>Search Results</h2>
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Arrival</th>
                    <th>Departure</th>
                    <th>Capacity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $results->fetchArray(SQLITE3_ASSOC)) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['departure_city']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['destination_city']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['arrival_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['departure_time']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>

        <?php

        ?>
    </div>
</body>

</html>