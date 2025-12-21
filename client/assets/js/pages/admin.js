class AdminDashboard {
	constructor() {
		this.applications = []
		this.stats = []
		this.statuses = []
		this.search = ''
		this.statusId = ''
		this.pagination = { page: 1, pages: 1, total: 0, limit: 12 }
		this.init()
	}

	async init() {
		try {
			await this.loadUserProfile()
			await Promise.all([this.loadStats(), this.loadApplications()])
			this.setupEventListeners()
		} catch (e) {
			console.error('Init error:', e)
			showToast('Ошибка инициализации', 'error')
		}
	}

	async loadUserProfile() {
		try {
			const resp = await authedFetch(API.endpoints.userMe)
			const user = await resp.json()
			const userNameEl = document.getElementById('userName')
			const userRoleEl = document.getElementById('userRole')

			if (userNameEl) userNameEl.textContent = user.login || 'Админ'
			if (userRoleEl) userRoleEl.textContent = 'Комендант'
		} catch (error) {
			showToast('Ошибка загрузки профиля', 'error')
		}
	}

	async loadStats() {
		try {
			const resp = await authedFetch('/application/admin/stats')
			const data = await resp.json()
			this.stats = data.stats || []
			this.statuses = this.stats
			this.renderStats()
		} catch (error) {
			console.error('Stats error:', error)
		}
	}

	renderStats() {
		const container = document.getElementById('statsRow')
		if (!container) return

		container.innerHTML = this.stats
			.map(
				stat => `
        <div class="statBadge status-${stat.id_status}" data-status-id="${stat.id_status}">
            <div class="statLabel">${stat.name}</div>
            <div class="statCount">${stat.count}</div>
        </div>
    `
			)
			.join('')
	}

	setupEventListeners() {
		// Модалка
		document
			.getElementById('modalCloseBtn')
			.addEventListener('click', () => this.closeModal())
		document.getElementById('applicationModal').addEventListener('click', e => {
			if (e.target.id === 'applicationModal') this.closeModal()
		})
		document.addEventListener('keydown', e => {
			if (e.key === 'Escape') this.closeModal()
		})

		// Поиск
		const searchInput = document.getElementById('searchInput')
		searchInput.addEventListener(
			'input',
			debounce(e => {
				this.search = e.target.value.trim()
				this.pagination.page = 1
				this.loadApplications()
			}, 300)
		)

		// Фильтр статусов
		const statusFilter = document.getElementById('statusFilter')
		statusFilter.addEventListener('change', e => {
			this.statusId = e.target.value
			this.pagination.page = 1
			this.loadApplications()
		})

		// Выход
		document.getElementById('logoutBtn').addEventListener('click', () => {
			clearAuth()
			window.location.href = '/login.html'
		})

		// Пагинация
		document.getElementById('prevPageBtn').addEventListener('click', () => {
			if (this.pagination.page > 1) {
				this.pagination.page--
				this.loadApplications()
			}
		})
		document.getElementById('nextPageBtn').addEventListener('click', () => {
			if (this.pagination.page < this.pagination.pages) {
				this.pagination.page++
				this.loadApplications()
			}
		})

		// Смена статуса в модалке
		document.getElementById('statusSelect').addEventListener('change', e => {
			this.updateApplicationStatus(e.target.value)
		})
	}

	async loadApplications() {
		const container = document.getElementById('applicationsContainer')
		container.innerHTML =
			'<div class="loading"><div class="spinner"></div>Загрузка...</div>'

		try {
			const params = new URLSearchParams({
				page: this.pagination.page,
				limit: this.pagination.limit,
			})
			if (this.search) params.append('search', this.search)
			if (this.statusId) params.append('status_id', this.statusId)

			const resp = await authedFetch(`/application/list?${params.toString()}`)
			const data = await resp.json()

			this.applications = data.applications || []
			this.pagination = data.pagination || this.pagination

			this.populateStatusFilter()
			this.renderApplications()
			this.updatePaginationUI()

			document.getElementById('emptyState').style.display = this.applications
				.length
				? 'none'
				: 'block'
		} catch (error) {
			showToast('Ошибка загрузки заявок', 'error')
			container.innerHTML =
				'<div style="padding:2rem;color:#999;text-align:center">Ошибка загрузки заявок</div>'
		}
	}

	populateStatusFilter() {
		const filter = document.getElementById('statusFilter')
		const current = this.statusId
		filter.innerHTML = '<option value="">Все статусы</option>'

		this.statuses.forEach(status => {
			const opt = document.createElement('option')
			opt.value = status.id_status
			opt.textContent = `${status.name} (${status.count})`
			filter.appendChild(opt)
		})
		filter.value = current
	}

	renderApplications() {
		const container = document.getElementById('applicationsContainer')
		if (!this.applications.length) {
			container.innerHTML = ''
			document.getElementById('emptyState').style.display = 'block'
			return
		}

		document.getElementById('emptyState').style.display = 'none'

		container.innerHTML = this.applications
			.map(a => this.createApplicationCard(a))
			.join('')

		container.querySelectorAll('.applicationCard').forEach(card => {
			card.addEventListener('click', () => {
				const id = Number(card.dataset.appId)
				const app = this.applications.find(x => Number(x.id_application) === id)
				if (app) this.openApplicationModal(app)
			})
		})
	}

	createApplicationCard(app) {
		const created = new Date(app.created_at).toLocaleDateString('ru-RU')
		const place = `${app.floor ?? ''} этаж, ${app.room ?? ''} комната`.trim()
		const user = app.user_fio || app.user_login || '—'
		return `
            <div class="applicationCard" data-app-id="${app.id_application}">
                <div class="cardHeader">
                    <span class="cardNumber">#${app.id_application}</span>
                    <span style="font-size:0.78rem;color:#9ca3af">${created}</span>
                </div>
                <div class="cardPlace">${place}</div>
                <div class="cardMeta">
                    <span class="statusBadge status-${app.status_id || 1}">${
			app.status || ''
		}</span>
                    <span class="badge">${app.priority}</span>
                    <small style="color:#9ca3af">${user}</small>
                </div>
            </div>
        `
	}

	updatePaginationUI() {
		const info = document.getElementById('paginationInfo')
		const prev = document.getElementById('prevPageBtn')
		const next = document.getElementById('nextPageBtn')
		const { page, pages, total } = this.pagination

		info.textContent = `Страница ${page} из ${pages || 1} · ${total} заявок`
		prev.disabled = page <= 1
		next.disabled = page >= (pages || 1)
	}

	openApplicationModal(app) {
		document.getElementById(
			'modalAppNumber'
		).textContent = `#${app.id_application}`
		document.getElementById('modalPlace').textContent = `${
			app.floor ?? ''
		} этаж, ${app.section ?? ''}, ${app.room ?? ''}`.trim()
		document.getElementById('modalCreatedAt').textContent = new Date(
			app.created_at
		).toLocaleString('ru-RU')
		document.getElementById('modalUser').textContent =
			app.user_fio || app.user_login || '—'
		document.getElementById('modalDefectType').textContent =
			app.defect_type || '—'
		document.getElementById('modalPriority').textContent = app.priority || '—'
		document.getElementById('modalDescription').textContent =
			app.description || 'Описание отсутствует'

		// Заполняем select статусов
		const statusSelect = document.getElementById('statusSelect')
		statusSelect.innerHTML = this.statuses
			.map(
				s =>
					`<option value="${s.id_status}" ${
						s.id_status == app.status_id ? 'selected' : ''
					}>${s.name}</option>`
			)
			.join('')

		this.renderPhotosSlider(app)
		document.getElementById('applicationModal').style.display = 'flex'
	}

	async updateApplicationStatus(newStatusId) {
		const appNumberEl = document.getElementById('modalAppNumber')
		const appNumber = appNumberEl.textContent.match(/#(\d+)/)?.[1]
		if (!appNumber) return

		try {
			const resp = await fetch('api/application/admin/status', {
				method: 'PUT',
				headers: {
					Authorization: `Bearer ${getToken()}`,
					'Content-Type': 'application/json',
				},
				body: JSON.stringify({
					application_id: parseInt(appNumber),
					status_id: parseInt(newStatusId),
				}),
			})

			if (!resp.ok) throw new Error('Ошибка обновления')

			showToast('Статус обновлён', 'success')
			this.closeModal()
			this.loadApplications()
			this.loadStats()
		} catch (error) {
			showToast('Ошибка смены статуса', 'error')
			console.error('Status update error:', error)
		}
	}

	renderPhotosSlider(app) {
		const paths = Array.isArray(app.photos) ? app.photos : []
		const modalPhotos = document.getElementById('modalPhotos')

		if (!paths.length) {
			modalPhotos.innerHTML = '<div class="noPhotos">Фото не добавлено</div>'
			return
		}

		const slidesHtml = paths
			.map(
				(src, index) => `
            <div class="photoSlide ${
							index === 0 ? 'active' : ''
						}" data-index="${index}">
                <img src="/api/${src}" alt="Фото ${index + 1}" loading="lazy">
            </div>
        `
			)
			.join('')

		modalPhotos.innerHTML = `
            <div class="photoSlider">
                ${slidesHtml}
                <div class="sliderControls">
                    <button class="sliderBtn" type="button" data-dir="prev">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button class="sliderBtn" type="button" data-dir="next">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
            <div class="sliderDots" id="photoDots"></div>
        `

		this.initSliderControls(modalPhotos)
	}

	initSliderControls(root) {
		const slides = root.querySelectorAll('.photoSlide')
		const prevBtn = root.querySelector('.sliderBtn[data-dir="prev"]')
		const nextBtn = root.querySelector('.sliderBtn[data-dir="next"]')
		const dotsContainer = root.querySelector('.sliderDots')

		let current = 0

		function showSlide(index) {
			slides.forEach((slide, i) =>
				slide.classList.toggle('active', i === index)
			)
			root
				.querySelectorAll('.sliderDot')
				.forEach((dot, i) => dot.classList.toggle('active', i === index))
		}

		if (prevBtn)
			prevBtn.addEventListener('click', () => {
				current = (current - 1 + slides.length) % slides.length
				showSlide(current)
			})

		if (nextBtn)
			nextBtn.addEventListener('click', () => {
				current = (current + 1) % slides.length
				showSlide(current)
			})

		Array.from(slides).forEach((_, index) => {
			const dot = document.createElement('div')
			dot.className = `sliderDot ${index === 0 ? 'active' : ''}`
			dot.addEventListener('click', () => showSlide(index))
			dotsContainer.appendChild(dot)
		})
	}

	closeModal() {
		document.getElementById('applicationModal').style.display = 'none'
	}
}

function debounce(fn, wait) {
	let t
	return function (...args) {
		clearTimeout(t)
		t = setTimeout(() => fn.apply(this, args), wait)
	}
}

function showToast(message, type = 'error') {
	const container = document.getElementById('toastContainer')
	if (!container) return
	const el = document.createElement('div')
	el.className = `toast ${type}`
	el.textContent = message
	container.appendChild(el)
	requestAnimationFrame(() => el.classList.add('show'))
	setTimeout(() => {
		el.classList.remove('show')
		setTimeout(() => el.remove(), 300)
	}, 4000)
}

document.addEventListener('DOMContentLoaded', () => new AdminDashboard())
