<?php
require_once __DIR__ . '/../models/ChatModel.php';

function chat_index($pdo) {
    $userId = $_SESSION['user_id'] ?? 0;
    if (!$userId) {
        header('Location: index.php?mod=login');
        exit;
    }
    
    $userRoles = $_SESSION['roles'] ?? [];
    $isStudent = in_array('Siswa', $userRoles) && !in_array('Admin', $userRoles) && !in_array('Guru', $userRoles);

    // Get roles for filter
    $stmt_roles = $pdo->query("SELECT id_peran, nama_peran FROM peran ORDER BY nama_peran ASC");
    $peranList = $stmt_roles->fetchAll(PDO::FETCH_ASSOC);

    // Get classes for filter (Active TA only, atau hanya kelas siswa jika user adalah siswa)
    $taId = $_SESSION['id_ta_aktif'] ?? 0;
    if ($isStudent) {
        $stmt_kelas = $pdo->prepare("SELECT k.id_kelas, k.nama_kelas 
                                     FROM kelas k 
                                     JOIN penempatan_siswa ps ON k.id_kelas = ps.id_kelas 
                                     JOIN siswa s ON ps.id_siswa = s.id_siswa 
                                     WHERE s.id_pengguna = ? AND k.id_ta = ? 
                                     ORDER BY k.nama_kelas ASC");
        $stmt_kelas->execute([$userId, $taId]);
    } else {
        $stmt_kelas = $pdo->prepare("SELECT id_kelas, nama_kelas FROM kelas WHERE id_ta = ? ORDER BY nama_kelas ASC");
        $stmt_kelas->execute([$taId]);
    }
    $kelasList = $stmt_kelas->fetchAll(PDO::FETCH_ASSOC);

    $recentChats = ChatModel::getRecentChats($pdo, $userId, $isStudent);
    
    include __DIR__ . '/../views/chat_index.php';
}

function api_chat_get_history($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    $other = $_GET['id_other'] ?? 0;
    $isGroup = $_GET['is_group'] ?? 0;
    
    if (!$me || !$other) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid parameters']);
        exit;
    }

    // Proteksi grup kelas untuk siswa
    $userRoles = $_SESSION['roles'] ?? [];
    $isStudent = in_array('Siswa', $userRoles) && !in_array('Admin', $userRoles) && !in_array('Guru', $userRoles);
    if ($isGroup && $isStudent) {
        if (!ChatModel::isStudentInClass($pdo, $me, $other)) {
            echo json_encode(['status' => 'error', 'msg' => 'Anda tidak memiliki akses ke grup kelas ini']);
            exit;
        }
    }
    
    $messages = ChatModel::getMessages($pdo, $me, $other, $isGroup);
    echo json_encode(['status' => 'ok', 'data' => $messages]);
    exit;
}

function api_chat_send($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    $receiver = $_POST['receiver_id'] ?? 0;
    $message = $_POST['message'] ?? '';
    $isGroup = $_POST['is_group'] ?? 0;
    
    if (!$me || !$receiver) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid sender/receiver']);
        exit;
    }

    // Proteksi pengiriman pesan grup kelas untuk siswa
    $userRoles = $_SESSION['roles'] ?? [];
    $isStudent = in_array('Siswa', $userRoles) && !in_array('Admin', $userRoles) && !in_array('Guru', $userRoles);
    if ($isGroup && $isStudent) {
        if (!ChatModel::isStudentInClass($pdo, $me, $receiver)) {
            echo json_encode(['status' => 'error', 'msg' => 'Anda tidak terdaftar di grup kelas ini']);
            exit;
        }
    }

    $attach_path = null;
    $attach_type = 'text';

    if (!empty($_FILES['attachment']['name'])) {
        $upload_dir = 'uploads/chat/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $file_name = time() . '_' . basename($_FILES['attachment']['name']);
        $target_file = $upload_dir . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed_images = ['jpg', 'jpeg', 'png', 'gif'];
        $allowed_docs = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
        $allowed_audio = ['mp3', 'wav', 'ogg', 'webm', 'm4a'];
        $allowed_video = ['mp4', 'webm', 'ogg', 'mov', 'avi'];

        if (in_array($file_type, $allowed_images)) {
            $attach_type = 'image';
        } elseif (in_array($file_type, $allowed_docs)) {
            $attach_type = 'document';
        } elseif (in_array($file_type, $allowed_audio)) {
            $attach_type = 'audio';
        } elseif (in_array($file_type, $allowed_video)) {
            $attach_type = 'video';
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Tipe file tidak diizinkan (' . $file_type . ')']);
            exit;
        }

        if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target_file)) {
            $attach_path = $file_name;
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Gagal mengupload file']);
            exit;
        }
    }

    if (empty($message) && !$attach_path) {
        echo json_encode(['status' => 'error', 'msg' => 'Pesan tidak boleh kosong']);
        exit;
    }

    $success = ChatModel::sendMessage($pdo, $me, $receiver, $message, $attach_path, $attach_type, $isGroup);
    
    if ($success) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal mengirim pesan']);
    }
    exit;
}

function api_chat_clear($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    $other = $_POST['other_id'] ?? 0;
    $isGroup = $_POST['is_group'] ?? 0;
    $forEveryone = ($_POST['for_everyone'] ?? 0) == 1;

    if (!$me || !$other) {
        echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak valid']);
        exit;
    }

    $success = ChatModel::clearChat($pdo, $me, $other, $isGroup, $forEveryone);
    if ($success) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal membersihkan percakapan']);
    }
    exit;
}

function api_chat_search_users($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    $query = $_GET['q'] ?? '';
    $roleId = $_GET['role_id'] ?? null;
    $classId = $_GET['class_id'] ?? null;

    $userRoles = $_SESSION['roles'] ?? [];
    $isStudent = in_array('Siswa', $userRoles) && !in_array('Admin', $userRoles) && !in_array('Guru', $userRoles);
    $taId = $_SESSION['id_ta_aktif'] ?? 0;
    
    $users = ChatModel::searchUsers($pdo, $query, $me, $isStudent, $roleId, $classId, $taId);
    echo json_encode(['status' => 'ok', 'data' => $users]);
    exit;
}

function api_chat_recent($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    if (!$me) {
        echo json_encode(['status' => 'error', 'msg' => 'Not logged in']);
        exit;
    }

    $userRoles = $_SESSION['roles'] ?? [];
    $isStudent = in_array('Siswa', $userRoles) && !in_array('Admin', $userRoles) && !in_array('Guru', $userRoles);
    
    $chats = ChatModel::getRecentChats($pdo, $me, $isStudent);
    echo json_encode(['status' => 'ok', 'data' => $chats]);
    exit;
}

function api_chat_unread_count($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    if (!$me) {
        echo json_encode(['status' => 'ok', 'count' => 0]);
        exit;
    }
    $count = ChatModel::getUnreadCount($pdo, $me);
    echo json_encode(['status' => 'ok', 'count' => $count]);
    exit;
}

function api_chat_delete($pdo) {
    if (!headers_sent()) header('Content-Type: application/json');
    if (ob_get_length()) ob_clean();
    $me = $_SESSION['user_id'] ?? 0;
    $messageId = $_POST['message_id'] ?? 0;
    if (!$me || !$messageId) {
        echo json_encode(['status' => 'error', 'msg' => 'Parameter tidak valid']);
        exit;
    }
    $success = ChatModel::deleteMessage($pdo, $messageId, $me);
    if ($success) {
        echo json_encode(['status' => 'ok']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gagal menghapus pesan']);
    }
    exit;
}
