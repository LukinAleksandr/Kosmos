import Validation from '../../Validation'
import {
  disabledForm,
  enabledForm,
} from '../../enabled-disabled/enabled-disabled'
import Page from '../Page'

export default class Password extends Page {
  constructor() {
    super()
    this.form = document.querySelector('#password-form')
    document.addEventListener('DOMContentLoaded', () => {
      setTimeout(() => {
        document.querySelector('.loader').style.display = 'none'
      }, 1000)
    })
    this.form.addEventListener('submit', this.actionPassword)
  }

  actionPassword(ev) {
    ev.preventDefault()
    let formValidationPass = new Validation(ev.target)
    if (!formValidationPass.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidationPass.status.message
      return false
    } else {
      disabledForm(ev.target)
      fetch('/password/request', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          email: ev.target.querySelector('#password-login').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          ev.target.querySelector('#password-login').value = ''
          ev.target.querySelector('.error-message').innerHTML =
            data.response.message
        })
        .catch((error) => {
          ev.target.querySelector('.error-message').innerHTML = 'Помилка!'
        })
        .finally(() => {
          setTimeout(() => {
            enabledForm(ev.target)
          }, 1000)
        })
    }
  }
}
