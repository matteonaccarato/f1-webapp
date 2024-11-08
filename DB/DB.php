<?php
if (!set_include_path("{$_SERVER['DOCUMENT_ROOT']}"))
    error("500", "set_include_path()");

require_once("utility/error_handling.php");

$ini = parse_ini_file("config/keys.ini");

const USERS_ARRAY = ["id", "first_name", "last_name", "email", "password", "role", "date_of_birth", "cookie_id", "img_url", "newsletter"];
const USERS_MAX_LENGTHS = [-1, 255, 255, 255, 255, -1, 10, 255, 255, -1];
const PRODUCTS_DEFAULT_SIZE = "one";
const PRODUCTS_ARRAY = ["id", "title", "description", "price", "img_url", "team_id", "color", "size", "alt"];
const PRODUCTS_MAX_LENGTHS = [-1, 150, 500, -1, 700, -1, 20, 20, 400];
const COOKIE_LENGTH = 255;
const COOKIE_EXP_DATE = 3600*24*30; // 30 days

/**
 * DB Class
 * It aims to interact with the chosen DataBase
 */
class DB {

    /**
     * Connect to DB (credentials are already inside the function)
     * @param string $source
     * @param string $redirect_error
     * @return mysqli
     */
    public static function connect(string $source = "", string $redirect_error = ""): mysqli {
        global $ini;

        error_reporting(0);
        mysqli_report(MYSQLI_REPORT_OFF);

        $conn = new mysqli($ini["hostname"], $ini["username"], $ini["password"], $ini["database"], $ini["port"]);
        if ($conn->connect_errno) {
            error("500", "mysqli error: $conn->error", $source, $redirect_error);
            exit;
        }
        $conn->set_charset("utf8mb4");
        if ($conn->errno) {
            error("500", "mysqli error: $conn->error", $source, $redirect_error);
            exit;
        }

        return $conn;
    }

    /**
     * Get record from DB through Prepared Statement
     * @param mysqli $conn
     * @param string $query
     * @param array $type_params
     * @param array $params
     * @param string $source
     * @param string $redirect_error
     * @return array => result of $res->fetch_all(MYSQLI_ASSOC)
     */
    public static function get_record_by_field(mysqli $conn, string $query, array $type_params, array $params, string $source = "", string $redirect_error = "") : array {

        $stmt = self::p_stmt_bind_execute($conn, $query, $type_params, $params, $source, $redirect_error);

        if (!$res = $stmt->get_result()) {
            error("500", "get_result(): $stmt->error", $source, $redirect_error);
            exit;
        }
        if (!$stmt->close()) {
            error("500", "stmt_close error: $stmt->error", $source, $redirect_error);
            exit;
        }
        $element = $res->fetch_all(MYSQLI_ASSOC);
        $res->free_result();

        return $element;
    }

    /**
     * Simple Query execution.
     * Not designed for "SELECT" (it does not return anything)
     * @param mysqli $conn
     * @param string $query
     * @param string $source
     * @param string $redirect_error
     * @return void
     */
    public static function stmt_no_select(mysqli $conn, string $query, string $source = "", string $redirect_error = ""): void {
        if (!$conn->query($query)) {
            error("500", "mysqli: $conn->error", $source, $redirect_error);
            exit;
        }
    }

    /**
     * Execute query (SELECT).
     * It returns $res->query($query)
     * @param mysqli $conn
     * @param string $query
     * @param string $source
     * @param string $redirect_error
     * @return array
     */
    public static function stmt_get_record_by_field(mysqli $conn, string $query, string $source = "NA", string $redirect_error = ""): array {
        if (!($res = $conn->query($query))) {
            error("500", "mysqli: $conn->error", $source, $redirect_error);
            exit;
        }
        return [$res->num_rows, $res->fetch_all(MYSQLI_ASSOC)];
    }

    /**
     * Prepared statement (NOT designed for SELECT)
     * @param mysqli $conn
     * @param string $query
     * @param array $type_params => array (e.g. ["s", "i", ...])
     * @param array $params => array (e.g. [$id, $name, ...])
     * @param string $source
     * @param string $redirect_error
     * @param string|null $order_delete_id => required if an order should also be deleted (e.g. You try to buy a product which has been recently deleted)
     * @return void
     */
    public static function p_stmt_no_select(mysqli $conn, string $query, array $type_params, array $params, string $source = "", string $redirect_error = "", string $order_delete_id = null): void {

        $stmt = self::p_stmt_bind_execute($conn, $query, $type_params, $params, $source, $redirect_error, $order_delete_id);

        if (!$stmt->close()) {
            error("500", "stmt_close error: $stmt->error", $source, $redirect_error);
            exit;
        }
    }

    /**
     * Just bind and execute Prepared Statement
     * @param mysqli $conn
     * @param string $query
     * @param array $type_params => array (e.g. ["s", "i", ...])
     * @param array $params => array (e.g. [$id, $name, ...])
     * @param string $source
     * @param string $redirect_error
     * @param string|null $order_delete_id
     * @return mysqli_stmt
     */
    public static function p_stmt_bind_execute(mysqli $conn, string $query, array $type_params, array $params, string $source = "", string $redirect_error  = "", string $order_delete_id = null): mysqli_stmt {
        $s_type_params = implode("", $type_params);

        if (!$stmt = $conn->prepare($query)) {
            error("500", "mysqli prepare: $conn->error", $source, $redirect_error);
            exit;
        }
        if (!$stmt->bind_param($s_type_params, ...$params)) {
            error("500", "mysqli bind_param: $conn->error", $source, $redirect_error);
            exit;
        }
        if (!$stmt->execute()) {
            if ($order_delete_id)
                DB::p_stmt_no_select($conn, "DELETE FROM orders WHERE id = ?;", ["i"], [$order_delete_id], "DB.php", "/f1-webapp/views/public/store/cart.php");
            error("500", "stmt_execute error: $stmt->error", $source, $redirect_error, $order_delete_id?"Something went wrong! Maybe one of your products is not available anymore.":"");
            exit;
        }
        return $stmt;
    }
}