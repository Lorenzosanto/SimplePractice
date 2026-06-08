<?php

require __DIR__ . "/../../api/database.php";

$commitments = [];
$databaseError = "";

try {
    $pdo = getDatabaseConnection();
    $statement = $pdo->query(
        "SELECT id, tarefa, data_criacao FROM compromissos ORDER BY data_criacao DESC"
    );
    $commitments = $statement->fetchAll();
} catch (Throwable $error) {
    $databaseError = "Não foi possível carregar os compromissos. Verifique se o banco foi criado.";
}

function formatDateTime(string $value): string
{
    $date = new DateTime($value);

    return $date->format("d/m/Y H:i");
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compromissos Registrados</title>
    <link rel="stylesheet" href="../assets/CSS/style.css">
</head>
<body data-page="compromissos" data-root-prefix="../../" data-config="../assets/data/site.json">
    <button class="menu-toggle" type="button" id="menuToggle" aria-expanded="false" aria-controls="pageDrawer">
        <span class="menu-toggle__icon" aria-hidden="true">
            <span></span>
            <span></span>
            <span></span>
        </span>
        <span class="menu-toggle__text">Páginas</span>
    </button>

    <div class="menu-overlay" id="menuOverlay" hidden></div>

    <aside class="page-drawer" id="pageDrawer" aria-label="Navegação entre páginas">
        <div class="page-drawer__brand">
            <p class="page-drawer__eyebrow">SimplePractice</p>
            <h2 id="brandTitle">Procrastinação</h2>
            <p class="page-drawer__description">Projeto educativo feito em grupo</p>
        </div>

        <nav class="page-drawer__nav" aria-label="Páginas do site">
            <ul class="drawer-links" id="drawerLinks"></ul>
        </nav>

        <div class="page-drawer__footer" id="brandFooter">2026</div>
    </aside>

    <div class="page-layout">
        <aside class="page-summary" aria-label="Resumo da página">
            <div class="summary-card">
                <p class="summary-kicker">Banco de dados</p>
                <h2>PHP + MySQL</h2>
                <ul class="summary-links">
                    <li><a class="active" href="#registros">Registros salvos</a></li>
                </ul>
                <div class="progress-indicator">
                    <div class="progress-bar" id="progressBar"></div>
                </div>
            </div>
        </aside>

        <main class="main-content">
            <header class="hero">
                <p class="hero__eyebrow">Área PHP</p>
                <h1>Compromissos Registrados</h1>
                <p class="subtitle">Esta página consulta o banco de dados e mostra as tarefas enviadas pelo formulário.</p>
            </header>

            <section id="registros" class="content-section commitment-section">
                <h2>Registros do banco</h2>

                <?php if ($databaseError !== ""): ?>
                    <div class="info-box">
                        <h4>Banco não encontrado</h4>
                        <p><?= htmlspecialchars($databaseError, ENT_QUOTES, "UTF-8") ?></p>
                    </div>
                <?php elseif (count($commitments) === 0): ?>
                    <div class="info-box">
                        <h4>Nenhum compromisso ainda</h4>
                        <p>Use o formulário da página Ação para gravar a primeira tarefa no banco.</p>
                    </div>
                <?php else: ?>
                    <div class="commitments-table-wrap">
                        <table class="commitments-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tarefa</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($commitments as $commitment): ?>
                                    <tr>
                                        <td><?= (int) $commitment["id"] ?></td>
                                        <td><?= htmlspecialchars($commitment["tarefa"], ENT_QUOTES, "UTF-8") ?></td>
                                        <td><?= htmlspecialchars(formatDateTime($commitment["data_criacao"]), ENT_QUOTES, "UTF-8") ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <div class="cta-buttons">
                    <a href="tela3.html" class="btn btn-primary">Registrar compromisso</a>
                    <a href="../../index.html" class="btn btn-secondary">Voltar ao início</a>
                </div>
            </section>
        </main>
    </div>

    <div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="true"></div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
