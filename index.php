<?php
declare(strict_types=1);
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = (string) ($_SESSION['username'] ?? 'Utilisateur');
$isAdmin = (bool) ($_SESSION['is_admin'] ?? false);
$initial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="CraftpickChat, une application de discussion moderne inspirée de Slack.">
  <title>CraftpickChat</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="app">
    <aside class="workspaces"><button class="workspace main">C</button><button class="workspace active">CP</button><button class="workspace">+</button></aside>
    <aside class="sidebar">
      <div class="sidebar-top"><div><small>Espace de travail</small><h1>CraftpickChat</h1></div><button class="round" id="newMessage">✎</button></div>
      <div class="user-card"><span class="avatar me"><?= htmlspecialchars($initial, ENT_QUOTES, 'UTF-8') ?></span><div><strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong><small><i class="online"></i> En ligne · <a href="logout.php" style="color:inherit">Déconnexion</a></small></div></div>
      <nav><button class="nav active">💬 Discussions</button><button class="nav">@ Mentions</button><button class="nav">🔖 Enregistrés</button><?php if ($isAdmin): ?><a class="nav" href="admin.php" style="display:block;text-decoration:none">⚙ Administration</a><?php endif; ?></nav>
      <div class="section-title"><span>Canaux</span><button id="addChannel">+</button></div>
      <div id="channels"><button class="channel active" data-name="général" data-description="Échanges généraux de l'équipe Craftpick."># général</button><button class="channel" data-name="annonces" data-description="Toutes les annonces importantes."># annonces</button><button class="channel" data-name="développement" data-description="Discussions autour du développement."># développement</button><button class="channel" data-name="support" data-description="Aide et support de l'équipe."># support</button></div>
      <div class="section-title direct-title"><span>Messages directs</span></div>
      <button class="dm"><span class="avatar green">A</span> Arthur <i class="online"></i></button><button class="dm"><span class="avatar purple">M</span> Marie <i class="online away"></i></button><button class="dm"><span class="avatar blue">T</span> Thomas</button>
    </aside>
    <main class="chat">
      <header class="chat-header"><div><h2># <span id="channelName">général</span></h2><p id="channelDescription">Échanges généraux de l'équipe Craftpick.</p></div><div class="header-actions"><button>👥 12</button><button id="search">⌕</button><button>ⓘ</button></div></header>
      <section class="messages" id="messages"><div class="date"><span>Aujourd'hui</span></div><article class="message"><span class="avatar green">A</span><div><div class="meta"><strong>Arthur</strong><time>14:03</time></div><p>Bienvenue sur CraftpickChat 👋</p></div></article></section>
      <footer class="composer-wrap"><form class="composer" id="messageForm"><div class="format"><button type="button"><b>B</b></button><button type="button"><i>I</i></button><button type="button">🔗</button><button type="button">☷</button></div><textarea id="messageInput" rows="2" placeholder="Envoyer un message dans #général"></textarea><div class="composer-actions"><div><button type="button">＋</button><button type="button">☺</button><button type="button">@</button></div><button class="send" type="submit">➤</button></div></form></footer>
    </main>
  </div>
  <div class="modal-bg hidden" id="modal"><div class="modal"><div class="modal-head"><h3>Créer un canal</h3><button id="closeModal">×</button></div><form id="channelForm"><label for="channelInput">Nom du canal</label><input id="channelInput" maxlength="24" placeholder="exemple : projets" required><button class="primary" type="submit">Créer</button></form></div></div>
  <script src="script.js"></script>
</body>
</html>
