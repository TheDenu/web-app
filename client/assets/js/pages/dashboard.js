class StudentDashboard {
	constructor() {
		this.applications = []
		this.search = ''
		this.statusId = ''
		this.pagination = { page: 1, pages: 1, total: 0, limit: 12 }
		this.sort = 'date_desc'
		this.init()
	}

	async init() {
		try {
			await this.loadUserProfile()
			this.setupEventListeners()
			await this.loadApplications()
		} catch (e) {
			showToast('Ошибка инициализации', 'error')
		}
	}
	async loadUserProfile() {
		try {
			const resp = await authedFetch(API.endpoints.userMe)
			const user = await resp.json()
			const userNameEl = document.getElementById('userName')
			const userRoleEl = document.getElementById('userRole')

			if (userNameEl) {
				userNameEl.textContent = user.login || 'Пользователь'
			}

			if (userRoleEl) {
				if (user.role === 'admin') {
					userRoleEl.textContent = 'Комендант'
					const adminLink = document.querySelector('.adminLink')
					if (adminLink) {
						adminLink.style.display = 'flex'
					}
				} else {
					userRoleEl.textContent = 'Пользователь'
				}
			}
		} catch (error) {
			showToast('Ошибка загрузки профиля', 'error')
		}
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

		// Поиск (debounce)
		const searchInput = document.getElementById('searchInput')
		searchInput.addEventListener(
			'input',
			debounce(e => {
				this.search = e.target.value.trim()
				this.pagination.page = 1
				this.loadApplications()
			}, 300)
		)

		const statusFilter = document.getElementById('statusFilter')
		statusFilter.addEventListener('change', e => {
			this.statusId = e.target.value
			this.pagination.page = 1
			this.loadApplications()
		})

		const sortSelect = document.getElementById('sortSelect')
		sortSelect.addEventListener('change', e => {
			this.sort = e.target.value
			this.renderApplications()
		})

		// Выход
		document.getElementById('logoutBtn').addEventListener('click', () => {
			clearAuth()
			window.location.href = '/login.html'
		})

		// Навигация
		document.querySelectorAll('.navItem').forEach(item => {
			item.addEventListener('click', e => {
				e.preventDefault()
				this.switchView(item.dataset.view)
			})
		})

		//Пагинация
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

		const adminLink = document.querySelector('.adminLink')
		if (adminLink) {
			adminLink.addEventListener('click', e => {
				e.preventDefault()
				window.location.href = '/admin.html'
			})
		}
		const createLink = document.querySelector('.createLink')
		if (createLink) {
			createLink.addEventListener('click', e => {
				e.preventDefault()
				window.location.href = '/create.html'
			})
		}
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

			const resp = await authedFetch(
				`${API.endpoints.myApplications}?${params.toString()}`
			)
			const data = await resp.json()

			this.applications = data.applications || []
			this.pagination = data.pagination || this.pagination

			const countEl = document.getElementById('applicationsCount')
			if (countEl) {
				const total = this.pagination.total || this.applications.length
				countEl.textContent = `${total} заявок`
			}

			this.populateStatusFilter()
			this.renderApplications()
			this.updatePaginationUI()
		} catch (error) {
			showToast('Ошибка загрузки заявок', 'error')
			document.getElementById('applicationsContainer').innerHTML =
				'<div style="padding:2rem;color:#999;text-align:center">Ошибка загрузки заявок</div>'
		}
	}

	populateStatusFilter() {
		const filter = document.getElementById('statusFilter')
		const current = this.statusId

		if (this.statusOptions) {
			filter.innerHTML = this.statusOptions
			filter.value = current
			return
		}

		filter.innerHTML = '<option value="">Все статусы</option>'

		const usedIds = new Set()

		this.applications.forEach(app => {
			if (!app.status_id || !app.status) return
			if (usedIds.has(app.status_id)) return
			usedIds.add(app.status_id)

			const opt = document.createElement('option')
			opt.value = app.status_id
			opt.textContent = app.status
			filter.appendChild(opt)
		})

		// закэшировать разметку
		this.statusOptions = filter.innerHTML
		filter.value = current
	}

	renderApplications() {
		const container = document.getElementById('applicationsContainer')
		if (!this.applications.length) {
			container.innerHTML =
				'<div style="grid-column:1/-1;text-align:center;padding:4rem;color:#6b7280"><i class="bi bi-inbox" style="font-size:3rem;opacity:0.5"></i><h3>Нет заявок</h3><p>Создайте первую заявку</p></div>'
			return
		}

		let apps = [...this.applications]

		if (this.sort === 'date_asc') {
			apps.sort((a, b) => new Date(a.created_at) - new Date(b.created_at))
		} else {
			apps.sort((a, b) => new Date(b.created_at) - new Date(a.created_at))
		}

		container.innerHTML = apps.map(a => this.createApplicationCard(a)).join('')

		container.querySelectorAll('.applicationCard').forEach(card => {
			card.addEventListener('click', () => {
				const id = Number(card.dataset.appId)
				const app = apps.find(x => Number(x.id_application) === id)
				if (app) this.openApplicationModal(app)
			})
		})
	}

	createApplicationCard(app) {
		const created = new Date(app.created_at).toLocaleDateString('ru-RU')
		const place = `${app.floor ?? ''} этаж, ${app.room ?? ''} комната`.trim()
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
            </div>
        </div>
    `
	}

	updatePaginationUI() {
		const info = document.getElementById('paginationInfo')
		const prev = document.getElementById('prevPageBtn')
		const next = document.getElementById('nextPageBtn')
		const { page, pages, total } = this.pagination
		info.textContent = `Страница ${page} из ${
			pages || 1
		} · всего ${total} заявок`
		prev.disabled = page <= 1
		next.disabled = page >= (pages || 1)
	}

	openApplicationModal(app) {
		document.getElementById(
			'modalAppNumber'
		).textContent = `#${app.id_application}`
		document.getElementById('modalStatus').textContent = app.status
		document.getElementById('modalStatus').className = `statusBadge status-${
			app.status_id || 1
		}`
		document.getElementById(
			'modalPlace'
		).textContent = `${app.floor} этаж, ${app.section}, ${app.room}`
		document.getElementById('modalCreatedAt').textContent = new Date(
			app.created_at
		).toLocaleString('ru-RU')
		document.getElementById('modalDefectType').textContent = app.defect_type
		document.getElementById('modalPriority').textContent = app.priority
		document.getElementById('modalDescription').textContent =
			app.description || 'Описание отсутствует'
		this.renderPhotosSlider(app)
		document.getElementById('applicationModal').style.display = 'flex'
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
			document
				.querySelectorAll('.sliderDot')
				.forEach((dot, i) => dot.classList.toggle('active', i === index))
		}

		prevBtn.addEventListener('click', () => {
			current = (current - 1 + slides.length) % slides.length
			showSlide(current)
		})

		nextBtn.addEventListener('click', () => {
			current = (current + 1) % slides.length
			showSlide(current)
		})

		// Доты
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

	updatePagination(totalPages) {
		// Реализация пагинации (добавить в HTML)
		this.currentPage = Math.min(this.currentPage, totalPages || 1)
	}

	switchView(view) {
		document
			.querySelectorAll('.navItem')
			.forEach(item => item.classList.remove('active'))
		document.querySelector(`[data-view="${view}"]`).classList.add('active')
		// Логика переключения views
	}
}

function debounce(fn, wait) {
	let t
	return function (...args) {
		clearTimeout(t)
		t = setTimeout(() => fn.apply(this, args), wait)
	}
}

function logout() {
	clearAuth()
	window.location.href = '/login.html'
}

function showToast(message, type = 'error') {
	const container = document.getElementById('toastContainer')
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

document.addEventListener('DOMContentLoaded', () => new StudentDashboard())
