;(function () {
	// DOM элементы
	const form = document.getElementById('registerForm')
	const usernameInput = document.getElementById('regUsername')
	const fioInput = document.getElementById('regFio')
	const passwordInput = document.getElementById('regPassword')
	const confirmPasswordInput = document.getElementById('regConfirmPassword')
	const togglePasswordBtn = document.getElementById('toggleRegPassword')
	const toggleConfirmBtn = document.getElementById('toggleConfirmPassword')
	const passwordIcon = document.getElementById('regPasswordIcon')
	const confirmIcon = document.getElementById('confirmPasswordIcon')
	const registerBtn = document.getElementById('registerBtn')
	const regSpinner = document.getElementById('regSpinner')
	const registerIcon = document.getElementById('registerIcon')
	const errorMessage = document.querySelector('.alert')
	const showLoginBtn = document.getElementById('showLoginBtn')

	// Toggle паролей
	togglePasswordBtn.addEventListener('click', () => {
		const isPassword = passwordInput.type === 'password'
		passwordInput.type = isPassword ? 'text' : 'password'
		passwordIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye'
	})

	toggleConfirmBtn.addEventListener('click', () => {
		const isPassword = confirmPasswordInput.type === 'password'
		confirmPasswordInput.type = isPassword ? 'text' : 'password'
		confirmIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye'
	})

	// Валидация
	function clearFieldError(field) {
		field.classList.remove('error')
		field.parentElement.nextElementSibling.textContent = ''
	}

	function showToast(message, type = 'error') {
		const container = document.getElementById('toastContainer')
		const toast = document.createElement('div')
		toast.className = `toast ${type}`
		toast.textContent = message

		container.appendChild(toast)

		// Анимация появления
		requestAnimationFrame(() => toast.classList.add('show'))

		// Автоудаление через 10 сек
		setTimeout(() => {
			toast.classList.remove('show')
			setTimeout(() => toast.remove(), 300)
		}, 10000)
	}

	function validateForm() {
		const errors = []

		// Логин (3-32 символа, буквы+цифры)
		const login = usernameInput.value.trim()
		if (login.length < 3) errors.push('Логин минимум 3 символа')
		else if (login.length > 32) errors.push('Логин максимум 32 символа')
		else clearFieldError(usernameInput)

		// ФИО (5-100 символов)
		const fio = fioInput.value.trim()
		if (fio.length < 5) errors.push('ФИО минимум 5 символов')
		else if (fio.length > 100) errors.push('ФИО максимум 100 символов')
		else clearFieldError(fioInput)

		// Пароль (минимум 8)
		const password = passwordInput.value
		if (password.length < 8) errors.push('Пароль минимум 8 символов')
		else clearFieldError(passwordInput)

		// Подтверждение пароля
		if (password !== confirmPasswordInput.value)
			errors.push('Пароли не совпадают')
		else clearFieldError(confirmPasswordInput)

		if (errors.length > 0) {
			showToast(errors.join(' | '), 'error')
			return false
		}

		return true
	}

	// API запрос
	async function register(userData) {
		setLoading(true)

		try {
			const resp = await fetch(API.BASE_URL + API.endpoints.registration, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(userData),
			})

			if (!resp.ok) {
				const errorData = await resp.json()
				throw new Error(errorData.error || 'Ошибка сервера')
			}

			const data = await resp.json()
			console.log('register /api/registration data =', data)

			showToast('Регистрация успешна! Переходим к входу...', 'success')
			setTimeout(() => {
				window.location.href = '/login.html'
			}, 1500)
		} catch (error) {
			showToast(error.message, 'error')
		} finally {
			setLoading(false)
		}
	}

	// UI утилиты
	function setLoading(loading) {
		registerBtn.disabled = loading
		regSpinner.style.display = loading ? 'inline-block' : 'none'
		registerIcon.style.display = loading ? 'none' : 'inline-block'
	}

	function showError(message) {
		if (errorMessage) {
			errorMessage.textContent = message
			errorMessage.style.display = 'block'
			setTimeout(() => (errorMessage.style.display = 'none'), 5000)
		}
	}

	form.addEventListener('submit', async e => {
		e.preventDefault()

		if (!validateForm()) return

		const userData = {
			login: usernameInput.value.trim(),
			fio: fioInput.value.trim(),
			password: passwordInput.value,
		}

		await register(userData)
	})

	showLoginBtn.addEventListener('click', () => {
		window.location.href = '/login.html'
	})

	// Enter → submit
	;[usernameInput, fioInput, passwordInput, confirmPasswordInput].forEach(
		input => {
			input.addEventListener('keypress', e => {
				if (e.key === 'Enter') form.dispatchEvent(new Event('submit'))
			})
		}
	)
})()
