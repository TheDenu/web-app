const API = {
	BASE_URL: '/api',
	endpoints: {
		login: '/login',
		registration: '/registration',
		userMe: '/user/me',

		myApplications: '/application/my-list',
		createApplication: '/application/create',
		applications: '/application/list',

		statuses: '/reference/statuses',
		places: '/reference/places',
		defectTypes: '/reference/defect-types',
		priorities: '/reference/priorities',
	},

	cache: {
		places: null,
		defectTypes: null,
		priorities: null,
		statuses: null,
		lastUpdated: null,
	},

	async getReferenceData(endpointKey) {
		const now = Date.now()
		const CACHE_DURATION = 5 * 60 * 1000

		if (
			this.cache[endpointKey] &&
			this.cache.lastUpdated &&
			now - this.cache.lastUpdated < CACHE_DURATION
		) {
			return this.cache[endpointKey]
		}

		try {
			const response = await authedFetch(this.endpoints[endpointKey])
			const data = await response.json()

			this.cache[endpointKey] = data
			this.cache.lastUpdated = now

			return data
		} catch (error) {
			console.error(`Ошибка загрузки ${endpointKey}:`, error)
			return this.cache[endpointKey] || []
		}
	},

	async createApplication(formData) {
		const token = getToken()
		if (!token) {
			throw new Error('Не авторизован')
		}

		const response = await fetch(`/api${API.endpoints.createApplication}`, {
			method: 'POST',
			headers: {
				Authorization: `Bearer ${token}`,
			},
			body: formData,
		})

		if (!response.ok) {
			if (response.status === 401) {
				clearAuth()
				window.location.href = '/login.html'
				throw new Error('Необходима авторизация')
			}
			const errorData = await response.json().catch(() => ({}))
			throw new Error(errorData.message || `HTTP ${response.status}`)
		}

		return response.json()
	},

	clearCache() {
		this.cache = {
			places: null,
			defectTypes: null,
			priorities: null,
			statuses: null,
			lastUpdated: null,
		}
	},
}
