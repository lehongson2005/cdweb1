<?php

require_once 'BaseModel.php';

class UserModel extends BaseModel {

    /**
     * Find user by ID
     */
    public function findUserById($id) {
        $stmt = self::$_connection->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Find user by keyword (username or email)
     */
    public function findUser($keyword) {
        $stmt = self::$_connection->prepare(
            "SELECT * FROM users WHERE user_name LIKE ? OR user_email LIKE ?"
        );
        $likeKeyword = "%$keyword%";
        $stmt->bind_param("ss", $likeKeyword, $likeKeyword);
        $stmt->execute();
        $res = $stmt->get_result();
        $users = $res->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $users;
    }

    /**
     * Authenticate user
     */
    public function auth($userName, $password) {
        $md5Password = md5($password); // Bạn có thể đổi sang password_hash() cho bảo mật tốt hơn
        $stmt = self::$_connection->prepare(
            "SELECT * FROM users WHERE name = ? AND password = ?"
        );
        $stmt->bind_param("ss", $userName, $md5Password);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Delete user by ID
     * Chỉ gọi khi request là POST + CSRF token hợp lệ
     */
    public function deleteUserById($id) {
        $stmt = self::$_connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Update user
     */
    public function updateUser($input) {
        $stmt = self::$_connection->prepare(
            "UPDATE users SET name = ?, password = ? WHERE id = ?"
        );
        $hashedPassword = md5($input['password']); // Hoặc password_hash()
        $stmt->bind_param("ssi", $input['name'], $hashedPassword, $input['id']);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Insert user
     */
    public function insertUser($input) {
        $stmt = self::$_connection->prepare(
            "INSERT INTO users (name, password) VALUES (?, ?)"
        );
        $hashedPassword = md5($input['password']); // Hoặc password_hash()
        $stmt->bind_param("ss", $input['name'], $hashedPassword);
        $stmt->execute();
        $insertId = $stmt->insert_id;
        $stmt->close();

        return $insertId;
    }

    /**
     * Get all users or search by keyword
     */
    public function getUsers($params = []) {
        if (!empty($params['keyword'])) {
            $stmt = self::$_connection->prepare("SELECT * FROM users WHERE name LIKE ?");
            $keyword = "%".$params['keyword']."%";
            $stmt->bind_param("s", $keyword);
            $stmt->execute();
            $res = $stmt->get_result();
            $users = $res->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        } else {
            $res = self::$_connection->query("SELECT * FROM users");
            $users = $res->fetch_all(MYSQLI_ASSOC);
        }

        return $users;
    }
}
