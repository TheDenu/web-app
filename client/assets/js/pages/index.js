;(async function initIndex() {
	try {
		const token = getToken()

		if (!token) {
			window.location.href = '/login.html'
			return
		}

		const resp = await authedFetch(API.BASE_URL + API.endpoints.userMe)
		if (resp.ok) {
			window.location.href = '/dashboard.html'
		} else {
			clearAuth()
			window.location.href = '/login.html'
		}
	} catch (error) {
		console.error('Auth check failed:', error)
		window.location.href = '/login.html'
	}
})()
