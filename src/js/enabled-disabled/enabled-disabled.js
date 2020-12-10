export function disabledForm(form) {
  form.querySelector('.primory-btn').classList.add('disabled')
  form.querySelector('.primory-btn').disabled = true
  form.querySelector('.error-message').innerHTML = ''
}

export function enabledForm(form) {
  form.querySelector('.primory-btn').classList.remove('disabled')
  form.querySelector('.primory-btn').disabled = false
}
