;(function () {
	const form = document.getElementById('loginForm')
	const usernameInput = document.getElementById('username')
	const passwordInput = document.getElementById('password')
	const togglePasswordBtn = document.getElementById('togglePassword')
	const passwordIcon = document.getElementById('passwordIcon')
	const loginBtn = document.getElementById('loginBtn')
	const spinner = document.getElementById('spinner')
	const loginIcon = document.getElementById('loginIcon')
	const errorMessage = document.getElementById('errorMessage')
	const usernameError = document.getElementById('usernameError')
	const passwordError = document.getElementById('passwordError')
	const showRegisterBtn = document.getElementById('showRegisterBtn')

	// Toggle пароля
	togglePasswordBtn.addEventListener('click', () => {
		const isPassword = passwordInput.type === 'password'
		passwordInput.type = isPassword ? 'text' : 'password'
		passwordIcon.className = isPassword ? 'bi bi-eye-slash' : 'bi bi-eye'
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

		setTimeout(() => {
			toast.classList.remove('show')
			setTimeout(() => toast.remove(), 300)
		}, 10000)
	}

	function validateForm() {
		const errors = []

		// Логин
		const login = usernameInput.value.trim()
		if (login.length < 3) {
			errors.push('Логин должен содержать минимум 3 символа')
		} else {
			clearFieldError(usernameInput)
		}

		// Пароль
		const password = passwordInput.value
		if (password.length < 8) {
			errors.push('Пароль должен содержать минимум 8 символов')
			isValid = false
		} else {
			clearFieldError(passwordInput)
		}

		return true
	}

	// API запрос
	async function login(credentials) {
		setLoading(true)

		try {
			const resp = await fetch(API.BASE_URL + API.endpoints.login, {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify(credentials),
			})

			if (!resp.ok) {
				const errorData = await resp.json()
				throw new Error(errorData.error || 'Ошибка сервера')
			}

			const data = await resp.json()
			console.log('login /api/login data =', data)

			// Сохраняем токен
			saveToken(data.token)

			showToast('Авторизация успешна! Переходим в личный кабинет...', 'success')
			setTimeout(() => {
				window.location.href = '/dashboard.html'
			}, 1500)
		} catch (error) {
			showToast(error.message, 'error')
		} finally {
			setLoading(false)
		}
	}

	// UI утилиты
	function setLoading(loading) {
		loginBtn.disabled = loading
		spinner.style.display = loading ? 'inline-block' : 'none'
		loginIcon.style.display = loading ? 'none' : 'inline-block'
	}

	function showError(message) {
		errorMessage.textContent = message
		errorMessage.style.display = 'block'
		setTimeout(() => {
			errorMessage.style.display = 'none'
		}, 5000)
	}

	// События
	form.addEventListener('submit', async e => {
		e.preventDefault()

		if (!validateForm()) return

		const credentials = {
			login: usernameInput.value.trim(),
			password: passwordInput.value,
		}

		await login(credentials)
	})

	// Переход к регистрации
	showRegisterBtn.addEventListener('click', () => {
		window.location.href = '/registration.html'
	})

	// Enter в любом поле → submit
	;[usernameInput, passwordInput].forEach(input => {
		input.addEventListener('keypress', e => {
			if (e.key === 'Enter') {
				form.dispatchEvent(new Event('submit'))
			}
		})
	})
})()
