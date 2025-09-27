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
     * Find user by username for authentication (returns password)
     */
    public function findByUsernameForAuth(string $username) {
        $stmt = self::$_connection->prepare(
            "SELECT id, password, name FROM users WHERE name = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();

        return $user;
    }

    /**
     * Find user by keyword (username or email)
     */
    public function findUser($keyword) {
        $stmt = self::$_connection->prepare(
            "SELECT * FROM users WHERE name LIKE ? OR user_email LIKE ?"
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
        // Cập nhật để sử dụng password_verify()
        $user = $this->findByUsernameForAuth($userName);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return null;
    }

    /**
     * Delete user by ID
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
    public function updateUser($data) {
        $sql = "UPDATE users SET name = ?";
        $params = [$data['name']];
        
        if (!empty($data['password'])) {
            $sql .= ", password = ?";
            $params[] = password($data['password'], PASSWORD_DEFAULT);
        }

        $sql .= " WHERE id = ?";
        $params[] = $data['id'];

        $stmt = self::$_connection->prepare($sql);
        $types = str_repeat('s', count($params) - 1) . 'i';
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $affectedRows = $stmt->affected_rows;
        $stmt->close();

        return $affectedRows;
    }

    /**
     * Insert user
     */
    public function insertUser($data) {
        // Đã thêm trường `fullname`, `email`, `type`, và `version` với giá trị mặc định để tránh lỗi
        $stmt = self::$_connection->prepare(
            "INSERT INTO users (name, fullname, email, password, type, version) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $hashedPassword = password($data['password'], PASSWORD_DEFAULT);
        $fullname = '';
        $email = '';
        $type = 'user';
        $version = '1.0'; // Gán giá trị mặc định cho `version`
        $stmt->bind_param("ssssss", $data['name'], $fullname, $email, $hashedPassword, $type, $version);
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
