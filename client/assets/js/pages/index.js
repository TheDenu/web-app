;(async function initIndex() {
	try {
		const token = getToken()

		if (!token) {
			window.location.href = '/login.html'
			return
		}

		const resp = await authedFetch(API.endpoints.userMe)
		const user = await resp.json()

		if (user.role === 'admin') {
			window.location.href = '/admin.html'
			return
		} else {
			window.location.href = '/dashboard.html'
			return
		}
	} catch (error) {
		clearAuth()
		window.location.href = '/login.html'
	}
})()
