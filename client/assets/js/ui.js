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
