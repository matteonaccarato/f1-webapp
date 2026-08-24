<?php

declare(strict_types=1);

require_once dirname(__DIR__) . "/DB/DB.php";

use PHPUnit\Framework\TestCase;

final class DatabaseSchemaTest extends TestCase
{
    private mysqli $connection;

    protected function setUp(): void
    {
        $this->connection = DB::connect(__FILE__, "/");
    }

    protected function tearDown(): void
    {
        if (isset($this->connection) && $this->connection instanceof mysqli) {
            @$this->connection->close();
        }
    }

    public function testExpectedTablesExist(): void
    {
        $expectedTables = [
            "cookies",
            "teams",
            "users",
            "products",
            "orders",
            "orders_products",
        ];

        $actualTables = [];
        $result = $this->connection->query("SHOW TABLES");

        $this->assertNotFalse($result);

        while ($row = $result->fetch_row()) {
            $actualTables[] = $row[0];
        }

        foreach ($expectedTables as $table) {
            $this->assertContains($table, $actualTables, sprintf("Expected table '%s' to exist.", $table));
        }
    }

    public function testUsersTableContainsExpectedColumns(): void
    {
        $result = $this->connection->query("SHOW COLUMNS FROM users");
        $this->assertNotFalse($result);

        $columns = [];
        while ($row = $result->fetch_assoc()) {
            $columns[] = $row["Field"];
        }

        $this->assertContains("email", $columns);
        $this->assertContains("password", $columns);
        $this->assertContains("cookie_id", $columns);
    }

    public function testOrdersHaveAForeignKeyToUsers(): void
    {
        $result = $this->connection->query(
            "SELECT COUNT(*) AS total
             FROM information_schema.REFERENTIAL_CONSTRAINTS
             WHERE CONSTRAINT_SCHEMA = DATABASE()
               AND TABLE_NAME = 'orders'
               AND REFERENCED_TABLE_NAME = 'users'"
        );

        $this->assertNotFalse($result);
        $count = (int) $result->fetch_assoc()["total"];

        $this->assertGreaterThan(0, $count);
    }
}
