const channels = document.getElementById('channels');
const channelName = document.getElementById('channelName');
const channelDescription = document.getElementById('channelDescription');
const messageInput = document.getElementById('messageInput');
const messageForm = document.getElementById('messageForm');
const messages = document.getElementById('messages');
const modal = document.getElementById('modal');
const addChannel = document.getElementById('addChannel');
const closeModal = document.getElementById('closeModal');
const channelForm = document.getElementById('channelForm');
const channelInput = document.getElementById('channelInput');

function selectChannel(button) {
  document.querySelectorAll('.channel').forEach(item => item.classList.remove('active'));
  button.classList.add('active');
  channelName.textContent = button.dataset.name;
  channelDescription.textContent = button.dataset.description || `Salon #${button.dataset.name}`;
  messageInput.placeholder = `Envoyer un message dans #${button.dataset.name}`;
}

channels.addEventListener('click', event => {
  const button = event.target.closest('.channel');
  if (button) selectChannel(button);
});

messageForm.addEventListener('submit', event => {
  event.preventDefault();
  const text = messageInput.value.trim();
  if (!text) return;

  const time = new Intl.DateTimeFormat('fr-FR', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date());

  const article = document.createElement('article');
  article.className = 'message';
  article.innerHTML = `
    <span class="avatar me">L</span>
    <div>
      <div class="meta"><strong>Louis</strong><time>${time}</time></div>
      <p></p>
    </div>
  `;
  article.querySelector('p').textContent = text;
  messages.appendChild(article);
  messageInput.value = '';
  messages.scrollTop = messages.scrollHeight;
});

messageInput.addEventListener('keydown', event => {
  if (event.key === 'Enter' && !event.shiftKey) {
    event.preventDefault();
    messageForm.requestSubmit();
  }
});

addChannel.addEventListener('click', () => {
  modal.classList.remove('hidden');
  channelInput.focus();
});

closeModal.addEventListener('click', () => modal.classList.add('hidden'));
modal.addEventListener('click', event => {
  if (event.target === modal) modal.classList.add('hidden');
});

channelForm.addEventListener('submit', event => {
  event.preventDefault();
  const value = channelInput.value.trim().toLowerCase().replace(/\s+/g, '-');
  if (!value) return;

  const button = document.createElement('button');
  button.className = 'channel';
  button.dataset.name = value;
  button.dataset.description = `Salon #${value}`;
  button.textContent = `# ${value}`;
  channels.appendChild(button);

  selectChannel(button);
  channelInput.value = '';
  modal.classList.add('hidden');
});

document.getElementById('search').addEventListener('click', () => {
  const query = prompt('Rechercher un message :');
  if (!query) return;
  const match = [...messages.querySelectorAll('.message p')]
    .find(paragraph => paragraph.textContent.toLowerCase().includes(query.toLowerCase()));
  if (match) {
    match.scrollIntoView({ behavior: 'smooth', block: 'center' });
    match.closest('.message').animate(
      [{ background: 'rgba(56,189,248,.25)' }, { background: 'transparent' }],
      { duration: 1200 }
    );
  } else {
    alert('Aucun message trouvé.');
  }
});

document.querySelectorAll('.reactions button').forEach(button => {
  button.addEventListener('click', () => {
    const parts = button.textContent.trim().split(' ');
    const count = Number(parts.pop()) || 0;
    button.textContent = `${parts.join(' ')} ${count + 1}`;
  });
});
