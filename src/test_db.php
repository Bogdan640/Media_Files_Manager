<?php
// test_mysql_connection.php
require_once 'config.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>MySQL Connection Test</h1>";

try {
    // Get connection from the function
    $conn = getDbConnection();

    // Test basic connectivity
    $stmt = $conn->query('SELECT 1 as connection_test');
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<p style='color:green;font-weight:bold;'>✓ Database connection successful!</p>";

    // Show database info
    echo "<h2>Connection Details:</h2>";
    echo "<ul>";
    echo "<li>Database type: MySQL</li>";
    echo "<li>Host: localhost</li>";
    echo "<li>Database name: multimedia_collection</li>";
    echo "<li>PHP version: " . phpversion() . "</li>";
    echo "<li>PDO drivers available: " . implode(", ", PDO::getAvailableDrivers()) . "</li>";
    echo "</ul>";

    // Test if tables exist
    try {
        $stmt = $conn->query('SHOW TABLES');
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "<h2>Database Tables:</h2>";
        if (count($tables) > 0) {
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";

            // Now display the contents of each table
            echo "<h2>Table Contents:</h2>";

            foreach ($tables as $table) {
                echo "<h3>Table: $table</h3>";

                try {
                    $query = $conn->query("SELECT * FROM `$table` LIMIT 100");
                    $rows = $query->fetchAll(PDO::FETCH_ASSOC);

                    if (count($rows) > 0) {
                        echo "<table border='1' cellpadding='5' cellspacing='0'>";

                        // Table headers
                        echo "<tr style='background-color:#f2f2f2;'>";
                        foreach (array_keys($rows[0]) as $column) {
                            echo "<th>$column</th>";
                        }
                        echo "</tr>";

                        // Table data
                        foreach ($rows as $row) {
                            echo "<tr>";
                            foreach ($row as $value) {
                                echo "<td>" . (is_null($value) ? "NULL" : htmlspecialchars($value)) . "</td>";
                            }
                            echo "</tr>";
                        }

                        echo "</table>";
                        echo "<p>Total rows: " . count($rows) . "</p>";
                    } else {
                        echo "<p style='color:orange;'>Table is empty</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p style='color:orange;'>Error reading from table $table: " . $e->getMessage() . "</p>";
                }

                echo "<hr>";
            }
        } else {
            echo "<p>No tables found. Database structure may need to be created.</p>";
        }
    } catch (PDOException $e) {
        echo "<p style='color:orange;'>Could not fetch tables: " . $e->getMessage() . "</p>";
    }

} catch (PDOException $e) {
    echo "<p style='color:red;font-weight:bold;'>✗ Database connection failed: " . $e->getMessage() . "</p>";

    echo "<h2>Troubleshooting Tips:</h2>";
    echo "<ol>";
    echo "<li>Make sure XAMPP's MySQL service is running</li>";
    echo "<li>Check that the database 'multimedia_collection' exists</li>";
    echo "<li>Verify your username and password in config.php</li>";
    echo "<li>Confirm that the MySQL port (usually 3306) is not blocked</li>";
    echo "</ol>";
}
?>