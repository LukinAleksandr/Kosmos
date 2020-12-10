import Validation from '../../Validation'
import Page from '../Page'
import Localstorage from '../../localstorage/Localstorage'
import {
  disabledForm,
  enabledForm,
} from '../../enabled-disabled/enabled-disabled'

export default class Authorization extends Page {
  constructor() {
    super()
    this.setup()
  }
  setup = () => {
    document
      .querySelector('#authorization-form')
      .addEventListener('submit', this.userAuthorization)
    document.addEventListener('DOMContentLoaded', this.getSubcategoryList)
  }

  getSubcategoryList = () => {
    fetch('/index/load', {})
      .then((respo) => respo.json())
      .then((data) => {
        if (data.status) {
          let subcategories = []
          data.response.subcategories.map((item) => {
            subcategories.push({
              name: item['sub_category'],
              id: item['sub_category_id'],
            })
          })
          Localstorage.clearLocalStorage()
          Localstorage.setLocalStorage('subcategories', subcategories)
        } else {
          console.log(data.response.message)
        }
      })
      .finally(() => {
        setTimeout(() => {
          document.querySelector('.loader').style.display = 'none'
        }, 1000)
      })
  }

  userAuthorization = (ev) => {
    ev.preventDefault()
    let formValidation = new Validation(ev.target)
    if (!formValidation.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidation.status.message
    } else {
      disabledForm(ev.target)
      fetch('/user/auth', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          email: ev.target.querySelector('#authorization-login').value,
          password: ev.target.querySelector('#authorization-password').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          if (data.status) {
            window.location.href = 'https://kosmos.filearchive.website/'
          } else {
            ev.target.querySelector('.error-message').innerHTML =
              data.response.message
          }
        })
        .catch((error) => {
          ev.target.querySelector('.error-message').innerHTML = 'Помилка!'
        })
        .finally(() => {
          enabledForm(ev.target)
        })
    }
  }
}
