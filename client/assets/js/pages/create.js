class ApplicationForm {
	constructor() {
		this.form = document.getElementById('applicationForm')
		this.submitBtn = document.getElementById('submitBtn')
		this.loadingSpinner = document.getElementById('loadingSpinner')
		this.successMessage = document.getElementById('successMessage')
		this.fileInput = document.getElementById('photos')
		this.filePreview = document.getElementById('filePreview')

		// Каскадные селекты
		this.floorSelect = document.getElementById('floorSelect')
		this.roomSelect = document.getElementById('roomSelect')
		this.sectionSelect = document.getElementById('sectionSelect')
		this.placeId = document.getElementById('placeId')
		this.defectTypeSelect = document.getElementById('defectType')
		this.prioritySelect = document.getElementById('priority')
		this.description = document.getElementById('description')

		// Проверяем существование элементов
		if (!this.form || !this.submitBtn) {
			console.error('Критические элементы формы не найдены')
			return
		}

		this.files = []
		this.places = []
		this.defectTypes = []
		this.priorities = []
		this.selectedPlace = null

		this.init()
	}

	async init() {
		try {
			await this.loadReferenceData()
			this.setupEventListeners()
		} catch (error) {
			console.error('Ошибка инициализации:', error)
			this.showError('Не удалось загрузить данные. Проверьте авторизацию.')
		}
	}

	async loadReferenceData() {
		try {
			console.log('Загрузка справочников...')

			this.places = await this.safeApiCall('places')
			this.defectTypes = await this.safeApiCall('defectTypes')
			this.priorities = await this.safeApiCall('priorities')

			console.log('Справочники загружены:', {
				places: this.places?.length || 0,
				defectTypes: this.defectTypes?.length || 0,
				priorities: this.priorities?.length || 0,
			})

			this.populateFloorSelect()
			this.populateSelects()
			this.enableForm()
		} catch (error) {
			console.error('Ошибка загрузки справочников:', error)
			this.showError('Не удалось загрузить справочники. Проверьте авторизацию.')
		}
	}

	async safeApiCall(endpointKey) {
		try {
			const data = await API.getReferenceData(endpointKey)

			if (!Array.isArray(data)) {
				console.warn(`${endpointKey} вернул не массив:`, data)
				return []
			}

			return data
		} catch (error) {
			console.error(`Ошибка ${endpointKey}:`, error)

			if (error.message.includes('401')) {
				throw new Error('Необходима авторизация')
			}

			return []
		}
	}

	populateFloorSelect() {
		const floors = [...new Set(this.places.map(p => p.floor))].sort(
			(a, b) => a - b
		)

		this.floorSelect.innerHTML = '<option value="">Выберите этаж</option>'
		floors.forEach(floor => {
			const option = document.createElement('option')
			option.value = floor
			option.textContent = `Этаж ${floor}`
			this.floorSelect.appendChild(option)
		})
	}

	populateRoomsForFloor(floor) {
		const rooms = [
			...new Set(this.places.filter(p => p.floor == floor).map(p => p.room)),
		].sort()

		this.roomSelect.innerHTML = '<option value="">Выберите комнату</option>'
		rooms.forEach(room => {
			const option = document.createElement('option')
			option.value = room
			option.textContent = room
			this.roomSelect.appendChild(option)
		})

		// Сбрасываем секцию
		this.sectionSelect.innerHTML =
			'<option value="">Сначала выберите комнату</option>'
		this.sectionSelect.disabled = true
		this.selectedPlace = null
		this.placeId.value = ''
	}

	populateSectionsForRoom(floor, room) {
		const sections = [
			...new Set(
				this.places
					.filter(p => p.floor == floor && p.room === room)
					.map(p => p.section)
					.filter(s => s)
			),
		].sort()

		this.sectionSelect.innerHTML = '<option value="">Без секции</option>'
		sections.forEach(section => {
			const option = document.createElement('option')
			option.value = section
			option.textContent = section
			this.sectionSelect.appendChild(option)
		})
	}

	populateSelects() {
		this.populateDefectTypes()
		this.populatePriorities()
	}

	populateDefectTypes() {
		if (!Array.isArray(this.defectTypes)) return

		this.defectTypeSelect.innerHTML =
			'<option value="">Выберите тип дефекта</option>'
		this.defectTypes.forEach(type => {
			const option = document.createElement('option')
			option.value = type.id
			option.textContent = type.name || 'Неизвестный тип'
			this.defectTypeSelect.appendChild(option)
		})
	}

	populatePriorities() {
		if (!Array.isArray(this.priorities)) return

		this.prioritySelect.innerHTML =
			'<option value="">Выберите приоритет</option>'
		this.priorities.forEach(priority => {
			const option = document.createElement('option')
			option.value = priority.id
			option.textContent = priority.name || 'Неизвестный приоритет'
			this.prioritySelect.appendChild(option)
		})
	}

	enableForm() {
		this.floorSelect.disabled = false
		if (this.defectTypeSelect) this.defectTypeSelect.disabled = false
		if (this.prioritySelect) this.prioritySelect.disabled = false
		this.submitBtn.disabled = false
	}

	setupEventListeners() {
		if (this.form) {
			this.form.addEventListener('submit', e => this.handleSubmit(e))
		}

		if (this.fileInput) {
			this.fileInput.addEventListener('change', e => this.handleFilePreview(e))
		}

		// Каскадные селекты
		this.floorSelect.addEventListener('change', e => {
			const floor = e.target.value
			this.roomSelect.disabled = !floor
			this.roomSelect.value = ''

			if (floor) {
				this.populateRoomsForFloor(floor)
			} else {
				this.resetPlaceSelection()
			}
			this.validateAll()
		})

		this.roomSelect.addEventListener('change', e => {
			const floor = this.floorSelect.value
			const room = e.target.value
			this.sectionSelect.disabled = !room
			this.sectionSelect.value = ''

			if (floor && room) {
				this.populateSectionsForRoom(floor, room)
			}
			this.updatePlaceId()
			this.validateAll()
		})

		this.sectionSelect.addEventListener('change', () => {
			this.updatePlaceId()
			this.validateAll()
		})

		// Валидация
		;[
			'floorSelect',
			'roomSelect',
			'defectType',
			'priority',
			'description',
		].forEach(id => {
			const field = this[id]
			if (field) {
				field.addEventListener('blur', () => this.validateField(field))
				field.addEventListener('input', () => this.clearFieldError(field))
			}
		})
	}

	updatePlaceId() {
		const floor = this.floorSelect.value
		const room = this.roomSelect.value
		const section = this.sectionSelect.value

		if (!floor || !room) {
			this.selectedPlace = null
			this.placeId.value = ''
			return
		}

		this.selectedPlace = this.places.find(
			p =>
				p.floor == floor &&
				p.room === room &&
				(!section || p.section === section)
		)

		if (this.selectedPlace) {
			this.placeId.value = this.selectedPlace.id
		}
	}

	resetPlaceSelection() {
		this.roomSelect.innerHTML =
			'<option value="">Сначала выберите этаж</option>'
		this.roomSelect.disabled = true
		this.sectionSelect.innerHTML =
			'<option value="">Сначала выберите комнату</option>'
		this.sectionSelect.disabled = true
		this.placeId.value = ''
		this.selectedPlace = null
	}

	validateField(field) {
		const value = field.value.trim()
		const errorEl = field.parentElement?.querySelector('.errorMessageInline')
		field.classList.remove('error', 'valid')

		if (!value && field.hasAttribute('required')) {
			this.showFieldError(field, 'Обязательное поле', errorEl)
			return false
		}

		if (field === this.description) {
			if (value.length < 5) {
				this.showFieldError(field, 'Минимум 5 символов', errorEl)
				return false
			}
			if (value.length > 1000) {
				this.showFieldError(field, 'Максимум 1000 символов', errorEl)
				return false
			}
		}

		field.classList.add('valid')
		if (errorEl) errorEl.textContent = ''
		return true
	}

	validateAll() {
		let isValid = true
		;[
			'floorSelect',
			'roomSelect',
			'defectType',
			'priority',
			'description',
		].forEach(id => {
			const field = this[id]
			if (field && !this.validateField(field)) isValid = false
		})
		return isValid && !!this.selectedPlace && !!this.placeId.value
	}

	showFieldError(field, message, errorEl) {
		field.classList.add('error')
		if (errorEl) errorEl.textContent = message
	}

	clearFieldError(field) {
		field.classList.remove('error')
		const errorEl = field.parentElement?.querySelector('.errorMessageInline')
		if (errorEl) errorEl.textContent = ''
	}

	handleFilePreview(e) {
		this.filePreview.innerHTML = ''
		this.files = Array.from(e.target.files)

		this.files.forEach((file, index) => {
			const reader = new FileReader()
			reader.onload = e => {
				const previewItem = document.createElement('div')
				previewItem.className = 'file-preview-item'
				previewItem.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button type="button" class="file-remove-btn" data-index="${index}">×</button>
                `

				previewItem
					.querySelector('.file-remove-btn')
					.addEventListener('click', () => {
						this.files.splice(index, 1)
						this.fileInput.files = this.createFileList(this.files)
						this.handleFilePreview({ target: { files: this.fileInput.files } })
					})

				this.filePreview.appendChild(previewItem)
			}
			reader.readAsDataURL(file)
		})
	}

	createFileList(files) {
		const dataTransfer = new DataTransfer()
		files.forEach(file => dataTransfer.items.add(file))
		return dataTransfer.files
	}

	async handleSubmit(e) {
		e.preventDefault()

		if (!this.validateAll()) return

		this.setLoading(true)

		const formData = new FormData()
		formData.append('place_id', this.placeId.value)
		formData.append('description', this.description.value.trim())
		formData.append('defect_type_id', this.defectTypeSelect.value)
		formData.append('priority_id', this.prioritySelect.value)

		this.files.forEach(file => {
			formData.append('photos[]', file)
		})

		try {
			const result = await API.createApplication(formData)
			console.log('Заявка создана:', result)
			this.showSuccess()
		} catch (error) {
			console.error('Error:', error)
			alert('Ошибка при создании заявки: ' + error.message)
		} finally {
			this.setLoading(false)
		}
	}

	setLoading(loading) {
		this.submitBtn.disabled = loading
		this.loadingSpinner.style.display = loading ? 'flex' : 'none'
		this.successMessage.style.display = 'none'
	}

	showSuccess() {
		this.setLoading(false)
		this.successMessage.style.display = 'flex'
		this.form.reset()
		this.filePreview.innerHTML = ''
		this.files = []
		this.resetPlaceSelection()
	}

	showError(message) {
		alert(message)
	}
}

document.addEventListener('DOMContentLoaded', () => {
	new ApplicationForm()
})
