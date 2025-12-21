function getToken() {
	return localStorage.getItem('authToken') || null
}

function saveToken(token) {
	localStorage.setItem('authToken', token)
}

function clearAuth() {
	localStorage.removeItem('authToken')
}

async function authedFetch(endpoint, options = {}) {
	const token = getToken()
	if (!token) throw new Error('Не авторизован')

	const resp = await fetch(`/api${endpoint}`, {
		...options,
		headers: {
			Authorization: `Bearer ${token}`,
			'Content-Type': 'application/json',
			...options.headers,
		},
	})

	if (!resp.ok) {
		if (resp.status === 401) {
			clearAuth()
			window.location.href = '/login.html'
		}
		throw new Error(`HTTP ${resp.status}`)
	}

	return resp
}
