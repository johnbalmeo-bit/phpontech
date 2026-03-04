// Main JS for Linamnam
function showToast(message, type = 'info') {
  let toast = document.querySelector('.toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.className = 'toast';
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.style.background = type === 'error' ? '#e53935' : '#323232';
  toast.style.display = 'block';
  setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

function ajaxAction(data, callback) {
  fetch('ajax_actions.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: new URLSearchParams(data)
  })
  .then(res => res.json())
  .then(json => {
    if (json.status === 'success') {
      showToast(json.action + ' successful!', 'info');
    } else {
      showToast(json.message, 'error');
    }
    if (callback) callback(json);
  })
  .catch(() => showToast('Network error', 'error'));
}
