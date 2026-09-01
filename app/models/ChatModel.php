<?php
class ChatModel {
    /**
     * Send a new message
     */
    public static function sendMessage($pdo, $sender_id, $receiver_id, $message, $attach_path = null, $attach_type = 'text', $is_group = 0) {
        $sql = "INSERT INTO internal_chat_messages (sender_id, receiver_id, message, attachment_path, attachment_type, is_group) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$sender_id, $receiver_id, $message, $attach_path, $attach_type, $is_group]);
    }

    /**
     * Get chat history between two users
     */
    public static function getMessages($pdo, $user1, $other_id, $is_group = 0, $limit = 100) {
        if ($is_group) {
            $sql = "SELECT m.*, p.nama_pengguna as sender_name 
                    FROM internal_chat_messages m
                    LEFT JOIN pengguna p ON m.sender_id = p.id_pengguna
                    LEFT JOIN internal_chat_deleted_messages dm ON m.id = dm.message_id AND dm.user_id = :me
                    WHERE m.receiver_id = :other AND m.is_group = 1 
                      AND m.is_deleted = 0 AND dm.id IS NULL
                    ORDER BY m.created_at ASC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':other', $other_id, PDO::PARAM_INT);
            $stmt->bindValue(':me', $user1, PDO::PARAM_INT);
        } else {
            $sql = "SELECT m.*, p.nama_pengguna as sender_name 
                    FROM internal_chat_messages m
                    LEFT JOIN pengguna p ON m.sender_id = p.id_pengguna
                    LEFT JOIN internal_chat_deleted_messages dm ON m.id = dm.message_id AND dm.user_id = :me
                    WHERE ((m.sender_id = :u1 AND m.receiver_id = :u2) 
                       OR (m.sender_id = :u3 AND m.receiver_id = :u4)) 
                       AND m.is_group = 0 
                       AND dm.id IS NULL
                    ORDER BY m.created_at ASC LIMIT :limit";
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':u1', $user1, PDO::PARAM_INT);
            $stmt->bindValue(':u2', $other_id, PDO::PARAM_INT);
            $stmt->bindValue(':u3', $other_id, PDO::PARAM_INT);
            $stmt->bindValue(':u4', $user1, PDO::PARAM_INT);
            $stmt->bindValue(':me', $user1, PDO::PARAM_INT);
        }
        
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Mark as read (only for individual chats)
        if (!$is_group) {
            $sql_read = "UPDATE internal_chat_messages SET is_read = 1 
                         WHERE sender_id = ? AND receiver_id = ? AND is_read = 0 AND is_group = 0";
            $stmt_read = $pdo->prepare($sql_read);
            $stmt_read->execute([$other_id, $user1]);
        }
        
        return $messages;
    }

    /**
     * Get unread message count for a user
     */
    public static function getUnreadCount($pdo, $userId) {
        $sql = "SELECT COUNT(*) FROM internal_chat_messages WHERE receiver_id = ? AND is_read = 0 AND is_group = 0";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }

    /**
     * Check if a student user belongs to a specific class
     */
    public static function isStudentInClass($pdo, $userId, $classId) {
        $stmt = $pdo->prepare("SELECT 1 FROM penempatan_siswa ps 
                               JOIN siswa s ON ps.id_siswa = s.id_siswa 
                               WHERE s.id_pengguna = ? AND ps.id_kelas = ? LIMIT 1");
        $stmt->execute([$userId, $classId]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Get recent chats for a user (Inbox list) with Presence
     */
    public static function getRecentChats($pdo, $userId, $isStudent = false) {
        // Individual chats
        $sql_indiv = "SELECT 
                        p.id_pengguna as id, 
                        p.nama_pengguna as name, 
                        p.foto,
                        p.last_activity,
                        m.message as last_message,
                        m.created_at as last_time,
                        m.attachment_type,
                        0 as is_group,
                        (SELECT COUNT(*) FROM internal_chat_messages 
                         WHERE sender_id = p.id_pengguna AND receiver_id = :me1 AND is_read = 0 AND is_group = 0 AND is_deleted = 0) as unread_count
                    FROM pengguna p
                    JOIN (
                        SELECT 
                            CASE WHEN sender_id = :me2 THEN receiver_id ELSE sender_id END as other_user_id,
                            MAX(id) as max_id
                        FROM internal_chat_messages
                        WHERE (sender_id = :me3 OR receiver_id = :me4) AND is_group = 0
                        GROUP BY other_user_id
                    ) last_msg ON p.id_pengguna = last_msg.other_user_id
                    JOIN internal_chat_messages m ON m.id = last_msg.max_id
                    LEFT JOIN internal_chat_deleted_messages dm ON m.id = dm.message_id AND dm.user_id = :me5
                    WHERE m.is_deleted = 0 AND dm.id IS NULL";

        // Group chats (Classes) - Jika siswa, HANYA tampilkan grup kelasnya sendiri
        $sql_student_filter = "";
        if ($isStudent) {
            $sql_student_filter = " AND k.id_kelas IN (SELECT ps.id_kelas FROM penempatan_siswa ps JOIN siswa s ON ps.id_siswa = s.id_siswa WHERE s.id_pengguna = :me_grp) ";
        }

        $sql_group = "SELECT 
                        k.id_kelas as id,
                        CONCAT('Grup ', k.nama_kelas) as name,
                        NULL as foto,
                        NULL as last_activity,
                        m.message as last_message,
                        m.created_at as last_time,
                        m.attachment_type,
                        1 as is_group,
                        0 as unread_count
                    FROM kelas k
                    JOIN (
                        SELECT receiver_id as group_id, MAX(id) as max_id
                        FROM internal_chat_messages
                        WHERE is_group = 1
                        GROUP BY receiver_id
                    ) last_grp ON k.id_kelas = last_grp.group_id
                    JOIN internal_chat_messages m ON m.id = last_grp.max_id
                    LEFT JOIN internal_chat_deleted_messages dm ON m.id = dm.message_id AND dm.user_id = :me6
                    WHERE m.is_deleted = 0 AND dm.id IS NULL $sql_student_filter";
        
        // Combine and order
        $sql = "($sql_indiv) UNION ($sql_group) ORDER BY last_time DESC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':me1', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':me2', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':me3', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':me4', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':me5', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':me6', $userId, PDO::PARAM_INT);
        if ($isStudent) {
            $stmt->bindValue(':me_grp', $userId, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Search for users with advanced filters (Role/Class)
     */
    public static function searchUsers($pdo, $query, $currentUserId, $isStudent = false, $roleId = null, $classId = null, $taId = null) {
        $params = ['me' => $currentUserId];
        $results = [];

        // 1. Search Classes (Groups) - Siswa hanya dapat melihat grup kelas tempat dia terdaftar
        if (!$roleId || $roleId == 'group') {
            $sql_k = "SELECT id_kelas as id, CONCAT('Grup ', nama_kelas) as name, NULL as foto, NULL as last_activity, 'Grup Kelas' as role_name, nama_kelas as class_name, 1 as is_group
                      FROM kelas WHERE 1=1";
            $p_k = [];
            if ($isStudent) {
                $sql_k .= " AND id_kelas IN (SELECT ps.id_kelas FROM penempatan_siswa ps JOIN siswa s ON ps.id_siswa = s.id_siswa WHERE s.id_pengguna = :me_student)";
                $p_k['me_student'] = $currentUserId;
            }
            if ($query) {
                $sql_k .= " AND nama_kelas LIKE :q";
                $p_k['q'] = "%$query%";
            }
            if ($classId) {
                $sql_k .= " AND id_kelas = :cid";
                $p_k['cid'] = $classId;
            }
            if ($taId) {
                $sql_k .= " AND id_ta = :taId";
                $p_k['taId'] = $taId;
            }
            $stmt_k = $pdo->prepare($sql_k);
            $stmt_k->execute($p_k);
            $results = array_merge($results, $stmt_k->fetchAll(PDO::FETCH_ASSOC));
        }

        // 2. Search Users
        $sql_u = "SELECT p.id_pengguna as id, p.nama_pengguna as name, p.foto, p.last_activity, rn.nama_peran as role_name, 
                       (SELECT k.nama_kelas FROM penempatan_siswa ps JOIN kelas k ON ps.id_kelas = k.id_kelas WHERE ps.id_siswa = s.id_siswa ORDER BY ps.id_penempatan DESC LIMIT 1) as class_name, 0 as is_group
                FROM pengguna p
                JOIN pengguna_peran pp ON p.id_pengguna = pp.id_pengguna
                JOIN peran rn ON pp.id_peran = rn.id_peran
                LEFT JOIN siswa s ON p.id_pengguna = s.id_pengguna
                WHERE p.id_pengguna != :me";
        
        if ($query) {
            $sql_u .= " AND p.nama_pengguna LIKE :query";
            $params['query'] = "%$query%";
        }

        // Batasan untuk siswa telah dicabut agar siswa bisa mencari semua kontak

        if ($roleId && $roleId != 'group') {
            $sql_u .= " AND rn.id_peran = :roleId";
            $params['roleId'] = $roleId;
        }

        if ($classId && $classId !== '') {
            $sql_u .= " AND EXISTS (SELECT 1 FROM penempatan_siswa ps2 WHERE ps2.id_siswa = s.id_siswa AND ps2.id_kelas = :classId)";
            $params['classId'] = $classId;
        }

        if ($taId) {
            // Only search for students who are active in this TA, or non-students
            $sql_u .= " AND (rn.nama_peran != 'Siswa' OR EXISTS (SELECT 1 FROM penempatan_siswa ps3 WHERE ps3.id_siswa = s.id_siswa AND ps3.id_ta = :taId2))";
            $params['taId2'] = $taId;
        }
        
        $sql_u .= " GROUP BY p.id_pengguna ORDER BY p.nama_pengguna ASC LIMIT 20";
        
        $stmt_u = $pdo->prepare($sql_u);
        $stmt_u->execute($params);
        $results = array_merge($results, $stmt_u->fetchAll(PDO::FETCH_ASSOC));
        
        // 3. Search Students directly (even if they don't have id_pengguna yet)
        if ($roleId == 6 || (!$roleId && !$query)) { 
            $sql_s = "SELECT 0 as id, s.nama as name, NULL as foto, NULL as last_activity, 'Siswa (Belum ada akun)' as role_name, k.nama_kelas as class_name, 0 as is_group, s.id_siswa
                      FROM siswa s
                      JOIN penempatan_siswa ps ON s.id_siswa = ps.id_siswa
                      JOIN kelas k ON ps.id_kelas = k.id_kelas
                      WHERE 1=1";
            $p_s = [];
            if ($query) {
                $sql_s .= " AND s.nama LIKE :q";
                $p_s['q'] = "%$query%";
            }
            if ($classId && $classId !== '') {
                $sql_s .= " AND ps.id_kelas = :cid";
                $p_s['cid'] = $classId;
            }
            if ($taId) {
                $sql_s .= " AND ps.id_ta = :taId";
                $p_s['taId'] = $taId;
            }
            $sql_s .= " AND s.id_pengguna IS NULL";
            $sql_s .= " LIMIT 20";
            $stmt_s = $pdo->prepare($sql_s);
            $stmt_s->execute($p_s);
            $results = array_merge($results, $stmt_s->fetchAll(PDO::FETCH_ASSOC));
        }

        return $results;
    }

    /**
     * Soft-delete a message (only by the sender)
     */
    public static function deleteMessage($pdo, $message_id, $user_id) {
        $sql = "UPDATE internal_chat_messages SET is_deleted = 1 WHERE id = ? AND sender_id = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$message_id, $user_id]);
    }

    /**
     * Clear all messages in a conversation
     */
    public static function clearChat($pdo, $me, $other, $is_group, $for_everyone) {
        if ($for_everyone) {
            if ($is_group) {
                $sql = "UPDATE internal_chat_messages SET is_deleted = 1 
                        WHERE receiver_id = ? AND is_group = 1";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([$other]);
            } else {
                $sql = "UPDATE internal_chat_messages SET is_deleted = 1 
                        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
                          AND is_group = 0";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([$me, $other, $other, $me]);
            }
        } else {
            if ($is_group) {
                $sql = "INSERT IGNORE INTO internal_chat_deleted_messages (message_id, user_id)
                        SELECT id, ? FROM internal_chat_messages 
                        WHERE receiver_id = ? AND is_group = 1";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([$me, $other]);
            } else {
                $sql = "INSERT IGNORE INTO internal_chat_deleted_messages (message_id, user_id)
                        SELECT id, ? FROM internal_chat_messages 
                        WHERE ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) 
                          AND is_group = 0";
                $stmt = $pdo->prepare($sql);
                return $stmt->execute([$me, $me, $other, $other, $me]);
            }
        }
    }
}
