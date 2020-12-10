import Validation from '../../Validation'
import Alert from '../../Alert/Alert'
import Localstorage from '../../localstorage/Localstorage'
import EventGroup from '../../eventGroup/EventGroup'
import {
  disabledForm,
  enabledForm,
} from '../../enabled-disabled/enabled-disabled'
import { cleaningForms } from '../../cleaningForms/cleaningForms'
import Page from '../Page'

export default class ControlUsers extends Page {
  constructor() {
    super()
    this.$userCreatForm = document.querySelector('.creat-users-form')
    this.setup()
    this.temp = {
      userId: null,
    }
  }

  setup = () => {
    this.$userCreatForm
      .querySelector('#creat-user-company')
      .addEventListener('click', (ev) =>
        super.buildSelectList(ev, 'company', 'companies')
      )
    this.$userCreatForm
      .querySelector('#creat-user-company')
      .addEventListener('input', (ev) =>
        super.buildSelectList(ev, 'company', 'companies')
      )

    this.$userCreatForm
      .querySelector('#creat-user-role')
      .addEventListener('click', (ev) => super.buildRoleList(ev))

    this.$userCreatForm.addEventListener('submit', this.userAction)
  }

  userAction = (ev) => {
    ev.preventDefault()
    let formValidation = new Validation(ev.target)
    if (!formValidation.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidation.status.message
    } else {
      disabledForm(ev.target)
      let uri = ''
      ev.target.querySelector('.primory-btn').classList.contains('edit-btn')
        ? (uri = '/admin/chenge')
        : (uri = '/admin/creat')

      //отправка запроса на сервер, и построение списка на основании ответа
      fetch(uri, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          action: 'user',
          id: this.temp.userId,
          name: ev.target.querySelector('#creat-user-name').value,
          company: ev.target.querySelector('#creat-user-company').value,
          position: ev.target.querySelector('#creat-user-position').value,
          phone: ev.target.querySelector('#creat-user-phone').value,
          email: ev.target.querySelector('#creat-user-email').value,
          role: ev.target.querySelector('#creat-user-role').value,
          status: ev.target.querySelector('#creat-user-status').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          new Alert(data)
          if (data.status) {
            Localstorage.setLocalStorage('users', data.response.users)
            this.buildUsersList()
            cleaningForms(ev.target)
            ev.target.querySelector('#creat-user-email').disabled = false
            ev.target.querySelector('.primory-btn').value = 'Створити'
            this.$userCreatForm
              .querySelector('.primory-btn')
              .classList.toggle('edit-btn')
          }
        })
        .catch(
          (error) =>
            (ev.target.querySelector('.error-message').innerHTML = 'Помилка!')
        )
        .finally(() => {
          setTimeout(() => {
            enabledForm(ev.target)
          }, 1000)
        })
    }
  }

  buildUsersList() {
    let users = Localstorage.getLocalStorage('users')
    Page.clearList(document.querySelectorAll('#users-table tr'))
    let $list = `<tr class="table-row-header">
                    <th class="table-header">id</th>
                    <th class="table-header">Пошта</th>
                    <th class="table-header">П.І.П</th>
                    <th class="table-header">Підприємство</th>
                    <th class="table-header">Телефон</th>
                    <th class="table-header">Посада</th>
                    <th class="table-header">Роль</th>
                    <th class="table-header">Статус</th>
                    <th class="table-header"></th>
                </tr>`
    for (let user of users) {
      $list += `<tr class="table-row">
                  <td class="table-cell table-cell_id">${user.user_id}</td>
                  <td class="table-cell table-cell_email">${
                    user.user_email
                  }</td>
                  <td class="table-cell table-cell_name">${user.user_name}</td>
                  <td class="table-cell table-cell_company">${
                    user.company_name
                  }</td>
                  <td class="table-cell table-cell_phone">${
                    user.user_phone
                  }</td>
                  <td class="table-cell table-cell_position">${
                    user.user_position
                  }</td>
                  <td class="table-cell table-cell_role">${user.user_role}</td>
                  <td class="table-cell table-cell_status">${
                    user.user_status
                  }</td>
                  <td class="table-cell">${
                    user.user_role === 'Адміністратор'
                      ? ''
                      : '<i class="fas fa-edit"></i>'
                  }</td>
              </tr>`
    }
    document.querySelector('#users-table').innerHTML = $list
    EventGroup.addEventGroup(
      document.querySelectorAll('#users-table .fa-edit'),
      'click',
      this.userEditing.bind(this)
    )
  }

  userEditing = (ev) => {
    let $row = ev.target.parentNode.parentNode
    this.$userCreatForm.querySelector(
      '.user-name-input'
    ).value = $row.querySelector('.table-cell_name').innerHTML
    this.$userCreatForm.querySelector(
      '.user-company-input'
    ).value = $row.querySelector('.table-cell_company').innerHTML
    this.$userCreatForm.querySelector(
      '.user-phone-input'
    ).value = $row.querySelector('.table-cell_phone').innerHTML
    this.$userCreatForm.querySelector(
      '.user-position-input'
    ).value = $row.querySelector('.table-cell_position').innerHTML
    this.$userCreatForm.querySelector(
      '.user-email-input'
    ).value = $row.querySelector('.table-cell_email').innerHTML
    this.$userCreatForm.querySelector('.user-email-input').disabled = true
    this.$userCreatForm.querySelector(
      '.user-role-input'
    ).value = $row.querySelector('.table-cell_role').innerHTML
    this.$userCreatForm.querySelector(
      '.user-status-input'
    ).value = $row.querySelector('.table-cell_status').innerHTML
    this.$userCreatForm.querySelector('.primory-btn').value = 'Змінити'
    this.$userCreatForm
      .querySelector('.primory-btn')
      .classList.toggle('edit-btn')
    this.temp = {
      userId: $row.querySelector('.table-cell_id').innerHTML,
    }
    this.$userCreatForm.scrollIntoView({ block: 'center', behavior: 'smooth' })
  }
}
