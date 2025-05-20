<?php
session_start();
// Caminhos para conexao.php e db_functions.php são um nível acima (../)
require __DIR__ . '/../conexao.php'; 
require __DIR__ . '/../src/db_functions.php';

if (!isset($_SESSION['admin_logado']) || $_SESSION['admin_logado'] !== true) {
    $_SESSION['mensagem_admin_usuarios'] = 'Acesso não autorizado.';
    $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    header('Location: login_admin.php'); // Redireciona para login dentro da pasta admin
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? null;

if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($usuario) || empty($nome_completo) || empty($senha) || empty($confirmar_senha)) {
        $_SESSION['mensagem_admin_usuarios'] = 'Todos os campos são obrigatórios para criar um novo administrador.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } elseif ($senha !== $confirmar_senha) {
        $_SESSION['mensagem_admin_usuarios'] = 'As senhas não coincidem.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } elseif (strlen($senha) < 6) {
        $_SESSION['mensagem_admin_usuarios'] = 'A senha deve ter pelo menos 6 caracteres.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } elseif (buscarAdminPorUsuario($pdo, $usuario)) {
        $_SESSION['mensagem_admin_usuarios'] = 'Este nome de usuário já existe.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } else {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        if (criarAdmin($pdo, $usuario, $senhaHash, $nome_completo)) {
            $_SESSION['mensagem_admin_usuarios'] = 'Administrador criado com sucesso!';
            $_SESSION['tipo_mensagem_admin_usuarios'] = 'success';
        } else {
            $_SESSION['mensagem_admin_usuarios'] = 'Erro ao criar administrador.';
            $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
        }
    }
    header('Location: gerenciar_usuarios_admin.php'); // Redireciona para a mesma pasta
    exit;

} elseif ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $admin_id = (int)($_POST['admin_id'] ?? 0);
    $usuario = trim($_POST['usuario'] ?? '');
    $nome_completo = trim($_POST['nome_completo'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar_senha = $_POST['confirmar_senha'] ?? '';

    if (empty($admin_id) || empty($usuario) || empty($nome_completo)) {
        $_SESSION['mensagem_admin_usuarios'] = 'Usuário e Nome Completo são obrigatórios para atualizar.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } else {
        $adminExistente = buscarAdminPorUsuario($pdo, $usuario);
        if ($adminExistente && $adminExistente['id'] !== $admin_id) {
            $_SESSION['mensagem_admin_usuarios'] = 'Este nome de usuário já está em uso por outro administrador.';
            $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
        } else {
            $novaSenhaHash = null;
            if (!empty($senha)) {
                if ($senha !== $confirmar_senha) {
                    $_SESSION['mensagem_admin_usuarios'] = 'As novas senhas não coincidem.';
                    $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
                    header('Location: gerenciar_usuarios_admin.php?action=edit&id=' . $admin_id);
                    exit;
                }
                if (strlen($senha) < 6) {
                    $_SESSION['mensagem_admin_usuarios'] = 'A nova senha deve ter pelo menos 6 caracteres.';
                    $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
                    header('Location: gerenciar_usuarios_admin.php?action=edit&id=' . $admin_id);
                    exit;
                }
                $novaSenhaHash = password_hash($senha, PASSWORD_DEFAULT);
            }

            if (!isset($_SESSION['mensagem_admin_usuarios'])) { 
                if (atualizarAdmin($pdo, $admin_id, $usuario, $nome_completo, $novaSenhaHash)) {
                    $_SESSION['mensagem_admin_usuarios'] = 'Administrador atualizado com sucesso!';
                    $_SESSION['tipo_mensagem_admin_usuarios'] = 'success';
                } else {
                    $_SESSION['mensagem_admin_usuarios'] = 'Erro ao atualizar administrador.';
                    $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
                }
            }
        }
    }
    if (isset($_SESSION['tipo_mensagem_admin_usuarios']) && $_SESSION['tipo_mensagem_admin_usuarios'] === 'error') {
        header('Location: gerenciar_usuarios_admin.php?action=edit&id=' . $admin_id);
    } else {
        header('Location: gerenciar_usuarios_admin.php');
    }
    exit;

} elseif ($action === 'delete' && isset($_GET['id'])) {
    $admin_id = (int)$_GET['id'];
    $admin_logado_id = $_SESSION['admin_id'] ?? 0;
    $admin_a_excluir = buscarAdminPorId($pdo, $admin_id);

    if ($admin_id === $admin_logado_id) {
        $_SESSION['mensagem_admin_usuarios'] = 'Você não pode excluir sua própria conta.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } elseif ($admin_a_excluir && strtolower($admin_a_excluir['usuario']) === 'admin') {
         $_SESSION['mensagem_admin_usuarios'] = 'O usuário "admin" principal não pode ser excluído.';
         $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    } elseif (excluirAdmin($pdo, $admin_id)) {
        $_SESSION['mensagem_admin_usuarios'] = 'Administrador excluído com sucesso!';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'success';
    } else {
        $_SESSION['mensagem_admin_usuarios'] = 'Erro ao excluir administrador ou administrador não encontrado.';
        $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    }
    header('Location: gerenciar_usuarios_admin.php'); // Redireciona para a mesma pasta
    exit;

} else {
    $_SESSION['mensagem_admin_usuarios'] = 'Ação inválida.';
    $_SESSION['tipo_mensagem_admin_usuarios'] = 'error';
    header('Location: gerenciar_usuarios_admin.php'); // Redireciona para a mesma pasta
    exit;
}
?>
