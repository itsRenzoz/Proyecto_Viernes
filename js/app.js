// Utilidades de fetch JSON
async function apiFetch(url, options = {}) {
	const res = await fetch(url, {
		credentials: 'include',
		headers: { 'Content-Type': 'application/json' },
		...options,
	});
	const data = await res.json().catch(() => ({}));
	if (!res.ok || data.ok === false) throw new Error(data.error || 'Error de servidor');
	return data;
}

function qs(sel) { return document.querySelector(sel); }
function qsa(sel) { return document.querySelectorAll(sel); }

function setMessage(el, msg, type = 'ok') {
	if (!el) return;
	el.textContent = msg;
	el.style.color = type === 'ok' ? '#2e7d32' : '#c62828';
}

// Formateo de moneda CRC
const currencyCRC = new Intl.NumberFormat('es-CR', { style: 'currency', currency: 'CRC', maximumFractionDigits: 0 });
function formatCRC(value) { return currencyCRC.format(Number(value) || 0); }

function attachPasswordToggles() {
	qsa('.toggle-password').forEach(btn => {
		btn.addEventListener('click', () => {
			const targetId = btn.getAttribute('data-target');
			const input = qs('#' + targetId);
			if (!input) return;
			if (input.type === 'password') {
				input.type = 'text';
				btn.textContent = '🙈';
			} else {
				input.type = 'password';
				btn.textContent = '👁';
			}
		});
	});
}

function attachAuthSwitch() {
	const showReg = qs('#link-show-register');
	const showLogin = qs('#link-show-login');
	const registerPanel = qs('#register-panel');
	const loginForm = qs('#login-form');
	if (showReg && registerPanel && loginForm) {
		showReg.addEventListener('click', (e) => {
			e.preventDefault();
			registerPanel.style.display = '';
			loginForm.closest('.auth-card').style.display = 'none';
		});
	}
	if (showLogin && registerPanel && loginForm) {
		showLogin.addEventListener('click', (e) => {
			e.preventDefault();
			registerPanel.style.display = 'none';
			loginForm.closest('.auth-card').style.display = '';
		});
	}
}

function validateEmailInput(input) {
	const value = input.value.trim();
	const ok = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
	input.classList.toggle('is-valid', ok && value !== '');
	input.classList.toggle('is-invalid', !ok && value !== '');
	return ok;
}

function validatePasswordInput(input, min = 6) {
	const ok = (input.value || '').length >= min;
	input.classList.toggle('is-valid', ok);
	input.classList.toggle('is-invalid', !ok && input.value !== '');
	return ok;
}

function formatTime12h(hhmm) {
	const [hStr, mStr] = hhmm.split(':');
	let h = parseInt(hStr, 10);
	const ampm = h >= 12 ? 'PM' : 'AM';
	h = h % 12; if (h === 0) h = 12;
	return `${String(h)}:${mStr} ${ampm}`;
}

async function renderUserStatus() {
	const container = qs('#user-status');
	const navAuth = qs('#nav-auth');
	const navAdmin = qs('#nav-admin');
	if (!container) return null;
	try {
		const { user } = await apiFetch('api/auth/me.php');
		container.innerHTML = '';
		if (user) {
			if (navAuth) navAuth.style.display = 'none';
			if (navAdmin) navAdmin.style.display = (user.role === 'admin') ? '' : 'none';
			const span = document.createElement('span');
			span.textContent = `Conectado: ${user.name || user.email} (${user.role})`;
			container.appendChild(span);
			if (user.role === 'admin') {
				const goAdmin = document.createElement('a');
				goAdmin.href = 'admin.html';
				goAdmin.className = 'btn';
				goAdmin.textContent = 'Panel Admin';
				container.appendChild(goAdmin);
			}
			const btn = document.createElement('button');
			btn.textContent = 'Cerrar sesión';
			btn.addEventListener('click', async () => {
				try {
					await apiFetch('api/auth/logout.php', { method: 'POST' });
					window.location.href = 'index.html';
				} catch (e) {}
			});
			container.appendChild(btn);
		} else {
			if (navAuth) navAuth.style.display = '';
			if (navAdmin) navAdmin.style.display = 'none';
			const a = document.createElement('a');
			a.href = 'login.html';
			a.className = 'btn';
			a.textContent = 'Iniciar Sesión';
			container.appendChild(a);
		}
		return user || null;
	} catch (e) {
		return null;
	}
}

function enforcePageAccess(user) {
	const path = (location.pathname || '').toLowerCase();
	const isReservas = path.endsWith('/reservas.html') || path.endsWith('reservas.html');
	const isAdmin = path.endsWith('/admin.html') || path.endsWith('admin.html');
	if (isReservas && !user) {
		window.location.href = 'login.html';
		return;
	}
	if (isAdmin) {
		if (!user) {
			window.location.href = 'login.html';
			return;
		}
		if (user.role !== 'admin') {
			window.location.href = 'index.html';
			return;
		}
	}
}

// Login / Registro
function initAuth() {
	const loginForm = qs('#login-form');
	const regForm = qs('#register-form');
	const loginMsg = qs('#login-message');
	const regMsg = qs('#register-message');

	if (loginForm) {
		const emailInput = qs('#email');
		const passInput = qs('#password');
		emailInput.addEventListener('input', () => validateEmailInput(emailInput));
		passInput.addEventListener('input', () => validatePasswordInput(passInput, 6));
		loginForm.addEventListener('submit', async (e) => {
			e.preventDefault();
			const okEmail = validateEmailInput(emailInput);
			const okPass = validatePasswordInput(passInput, 6);
			if (!okEmail || !okPass) return setMessage(loginMsg, 'Revisa los campos en rojo', 'err');
			try {
				await apiFetch('api/auth/login.php', { method: 'POST', body: JSON.stringify({ email: emailInput.value.trim(), password: passInput.value }) });
				setMessage(loginMsg, 'Inicio de sesión exitoso');
				setTimeout(() => window.location.href = 'reservas.html', 600);
			} catch (err) { setMessage(loginMsg, err.message, 'err'); }
		});
	}
	if (regForm) {
		const nameInput = qs('#nombre');
		const emailRegInput = qs('#email-registro');
		const passRegInput = qs('#password-registro');
		emailRegInput.addEventListener('input', () => validateEmailInput(emailRegInput));
		passRegInput.addEventListener('input', () => validatePasswordInput(passRegInput, 6));
		regForm.addEventListener('submit', async (e) => {
			e.preventDefault();
			const okName = (nameInput.value.trim() !== '');
			const okEmail = validateEmailInput(emailRegInput);
			const okPass = validatePasswordInput(passRegInput, 6);
			nameInput.classList.toggle('is-invalid', !okName);
			nameInput.classList.toggle('is-valid', okName);
			if (!okName || !okEmail || !okPass) return setMessage(regMsg, 'Revisa los campos en rojo', 'err');
			try {
				await apiFetch('api/auth/register.php', { method: 'POST', body: JSON.stringify({ nombre: nameInput.value.trim(), email: emailRegInput.value.trim(), password: passRegInput.value }) });
				setMessage(regMsg, 'Registro exitoso. Redirigiendo...');
				setTimeout(() => window.location.href = 'reservas.html', 600);
			} catch (err) { setMessage(regMsg, err.message, 'err'); }
		});
	}

	attachPasswordToggles();
	attachAuthSwitch();
}

// Calendario y reservas
function buildCalendar(container, currentDate) {
	container.innerHTML = '';
	const header = document.createElement('div');
	header.className = 'cal-header';
	const prev = document.createElement('button'); prev.textContent = '◀'; prev.className = 'cal-nav-btn';
	const next = document.createElement('button'); next.textContent = '▶'; next.className = 'cal-nav-btn';
	const title = document.createElement('span'); title.className = 'cal-title'; title.textContent = currentDate.toLocaleString('es-ES', { month: 'long', year: 'numeric' });
	header.appendChild(prev); header.appendChild(title); header.appendChild(next);
	container.appendChild(header);

	const wd = document.createElement('div'); wd.className = 'cal-weekdays';
	['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'].forEach(d => { const el = document.createElement('div'); el.textContent = d; wd.appendChild(el); });
	container.appendChild(wd);

	const gridWrap = document.createElement('div'); gridWrap.className = 'cal-grid';
	container.appendChild(gridWrap);

	const firstDay = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
	const lastDay = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 0);
	const start = firstDay.getDay(); // domingo=0
	let day = 1 - start;

	const today = new Date();
	const todayStr = today.toISOString().slice(0,10);

	for (let r = 0; r < 6; r++) {
		const row = document.createElement('div'); row.className = 'cal-week';
		for (let c = 0; c < 7; c++) {
			const cell = document.createElement('button'); cell.className = 'cal-cell';
			const date = new Date(currentDate.getFullYear(), currentDate.getMonth(), day);
			const inMonth = day >= 1 && day <= lastDay.getDate();
			if (!inMonth) { cell.classList.add('is-out'); cell.disabled = true; cell.textContent = ''; }
			else {
				cell.textContent = String(day);
				const iso = date.toISOString().slice(0, 10);
				cell.dataset.date = iso;
				if (iso === todayStr) cell.classList.add('is-today');
			}
			row.appendChild(cell);
			day++;
		}
		gridWrap.appendChild(row);
	}
	return { prev, next };
}

function initReservas() {
	const calContainer = qs('#calendar');
	const slotsContainer = qs('#time-slots');
	const form = qs('#reservar-form');
	const msg = qs('#reservar-message');
	if (!calContainer || !form) return;
	let current = new Date();
	let selectedDate = null;

	const nav = buildCalendar(calContainer, current);
	nav.prev.addEventListener('click', () => { current.setMonth(current.getMonth() - 1); buildCalendar(calContainer, current); attachCellHandlers(); });
	nav.next.addEventListener('click', () => { current.setMonth(current.getMonth() + 1); buildCalendar(calContainer, current); attachCellHandlers(); });
	attachCellHandlers();

	function attachCellHandlers() {
		qsa('.cal-cell').forEach(btn => {
			btn.addEventListener('click', async () => {
				if (!btn.dataset.date) return;
				selectedDate = btn.dataset.date;
				qsa('.cal-cell.is-selected').forEach(el => el.classList.remove('is-selected'));
				btn.classList.add('is-selected');
				setMessage(msg, 'Cargando horarios...');
				try {
					const { slots } = await apiFetch(`api/citas/check_availability.php?date=${selectedDate}`);
					slotsContainer.innerHTML = '';
					if (!slots.length) { slotsContainer.textContent = 'Sin horarios disponibles.'; setMessage(msg, ''); return; }
					slots.forEach(h => {
						const b = document.createElement('button'); b.textContent = formatTime12h(h); b.type = 'button'; b.className = 'slot-btn';
						b.addEventListener('click', () => { qs('#hora').value = h; setMessage(msg, `Horario seleccionado: ${formatTime12h(h)}`); });
						slotsContainer.appendChild(b);
					});
					qs('#fecha').value = selectedDate;
					setMessage(msg, 'Selecciona una hora.');
				} catch (err) { setMessage(msg, err.message, 'err'); }
			});
		});
	}

	form.addEventListener('submit', async (e) => {
		e.preventDefault();
		const date = qs('#fecha').value;
		const time = qs('#hora').value;
		const mascota = qs('#mascota').value.trim();
		const motivo = qs('#motivo') ? qs('#motivo').value : 'consultas';
		if (!date || !time || !mascota) return setMessage(msg, 'Completa todos los campos', 'err');
		try {
			await apiFetch('api/citas/create.php', { method: 'POST', body: JSON.stringify({ date, time, mascota, motivo }) });
			setMessage(msg, 'Cita reservada correctamente');
			form.reset(); slotsContainer.innerHTML = '';
		} catch (err) { setMessage(msg, err.message, 'err'); }
	});
}

// Tienda
async function initTienda() {
	const cont = qs('#products');
	if (!cont) return;
	try {
		const { productos } = await apiFetch('api/productos/list.php');
		cont.innerHTML = '';
		productos.forEach(p => {
			const card = document.createElement('div'); card.className = 'producto';
			card.innerHTML = `<h3>${p.nombre}</h3><p>${p.descripcion || ''}</p><p><strong>${formatCRC(p.precio)}</strong></p><p>Stock: ${p.stock}</p><button data-id="${p.id}">Reservar</button>`;
			card.querySelector('button').addEventListener('click', () => {
				alert('Producto se retira y paga en tienda');
				// Aquí podrías agregar lógica para una reserva real en el futuro
			});
			cont.appendChild(card);
		});
	} catch (err) {
		cont.textContent = 'No se pudieron cargar los productos.';
	}
}

async function checkout(items) {
	try {
		const res = await apiFetch('api/tienda/checkout.php', { method: 'POST', body: JSON.stringify({ items }) });
		alert('Compra realizada. Pedido #' + res.pedido_id + ' Total: ' + formatCRC(res.total));
	} catch (err) {
		alert('Error: ' + err.message);
	}
}

// Info clínica
async function initInfoClinica() {
	const el = qs('#info-contenido');
	if (!el) return;
	try {
		const { info } = await apiFetch('api/info/get.php');
		el.innerHTML = '';
		const ul = document.createElement('ul');
		ul.innerHTML = `
			<li>Horario: ${info?.horarios || ''}</li>
			<li>Teléfono: ${info?.telefono || ''}</li>
			<li>Dirección: ${info?.direccion || ''}</li>
		`;
		if (Array.isArray(info?.servicios_lista) && info.servicios_lista.length) {
			const h = document.createElement('h3'); h.textContent = 'Servicios';
			const svc = document.createElement('ul');
			info.servicios_lista.forEach(s => { const li = document.createElement('li'); li.textContent = s; svc.appendChild(li); });
			el.appendChild(h);
			el.appendChild(svc);
		}
		el.appendChild(ul);
	} catch (err) {
		// Silenciar y dejar contenido estático si falla
	}
}


function attachPasswordToggles() {
  document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", () => {
      const targetId = button.getAttribute("data-target");
      const input = document.getElementById(targetId);
      const icon = button.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    });
  });
}


document.addEventListener("DOMContentLoaded", attachPasswordToggles);



// Inicialización
window.addEventListener('DOMContentLoaded', async () => {
	const user = await renderUserStatus();
	enforcePageAccess(user);
	initAuth();
	initReservas();
	initTienda();
	initInfoClinica();
	attachPasswordToggles();
	attachAuthSwitch();

	fetch("partials/header.html")
		.then(res => res.text())
		.then(data => {
			document.getElementById("header").innerHTML = data;

		
			const path = window.location.pathname.toLowerCase();

			const esPublica = 
				path.endsWith("/") || 
				path.endsWith("index.html") || 
				path.includes("tienda.html") || 
				path.includes("info-clinica.html") || 
				path.includes("login.html");

			if (esPublica) {
				requestAnimationFrame(() => {
			
					const reservarBtn = document.querySelector("nav ul li a[href='reservas.html']"); 
					if (reservarBtn) reservarBtn.parentElement.style.display = "none";

					if (path.includes("login.html")) {
						const nav = document.querySelector("nav ul"); 
						if (nav) nav.style.justifyContent = "center";

						const loginBtn = document.querySelector(".login-btn");
						if (loginBtn) loginBtn.style.display = "none";
					}
				});
			}
		});

});
