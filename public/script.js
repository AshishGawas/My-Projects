const form = document.getElementById('contact-form');
const idInput = document.getElementById('contact-id');
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const addressInput = document.getElementById('address');
const resetBtn = document.getElementById('reset');
const tableBody = document.querySelector('#contacts-table tbody');

async function fetchContacts() {
  const res = await fetch('/api/contacts');
  const contacts = await res.json();
  renderContacts(contacts);
}

function renderContacts(contacts) {
  tableBody.innerHTML = '';
  for (const c of contacts) {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${escapeHtml(c.name)}</td>
      <td>${escapeHtml(c.email)}</td>
      <td>${escapeHtml(c.phone)}</td>
      <td>${escapeHtml(c.address || '')}</td>
      <td class="actions">
        <button data-edit="${c._id}">Edit</button>
        <button class="secondary" data-delete="${c._id}">Delete</button>
      </td>
    `;
    tableBody.appendChild(tr);
  }
}

function escapeHtml(text) {
  return String(text)
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

form.addEventListener('submit', async (e) => {
  e.preventDefault();
  const payload = {
    name: nameInput.value.trim(),
    email: emailInput.value.trim(),
    phone: phoneInput.value.trim(),
    address: addressInput.value.trim(),
  };
  const id = idInput.value;
  const method = id ? 'PUT' : 'POST';
  const url = id ? `/api/contacts/${id}` : '/api/contacts';

  const res = await fetch(url, {
    method,
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });

  if (!res.ok) {
    const data = await res.json().catch(() => ({}));
    alert(data.error || 'Request failed');
    return;
  }

  await fetchContacts();
  form.reset();
  idInput.value = '';
});

resetBtn.addEventListener('click', () => {
  form.reset();
  idInput.value = '';
});

tableBody.addEventListener('click', async (e) => {
  const editId = e.target.getAttribute('data-edit');
  const deleteId = e.target.getAttribute('data-delete');
  if (editId) {
    const res = await fetch(`/api/contacts/${editId}`);
    const c = await res.json();
    idInput.value = c._id;
    nameInput.value = c.name;
    emailInput.value = c.email;
    phoneInput.value = c.phone;
    addressInput.value = c.address || '';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  } else if (deleteId) {
    if (!confirm('Delete this contact?')) return;
    const res = await fetch(`/api/contacts/${deleteId}`, { method: 'DELETE' });
    if (!res.ok) {
      const data = await res.json().catch(() => ({}));
      alert(data.error || 'Delete failed');
      return;
    }
    await fetchContacts();
  }
});

fetchContacts();
