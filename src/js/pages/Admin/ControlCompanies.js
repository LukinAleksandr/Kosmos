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

export default class ControlCompanies extends Page {
  constructor() {
    super()
    this.$companyCreatForm = document.querySelector('.creat-company-form')
    this.setup()
    this.temp = {
      companyId: null,
    }
  }

  setup = () => {
    this.$companyCreatForm.addEventListener('submit', this.companyAction)
  }

  companyAction = (ev) => {
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
          action: 'company',
          id: this.temp.companyId,
          name: ev.target.querySelector('#creat-company-name').value,
          address: ev.target.querySelector('#creat-company-address').value,
          site: ev.target.querySelector('#creat-company-site').value,
          phone: ev.target.querySelector('#creat-company-phone').value,
          email: ev.target.querySelector('#creat-company-email').value,
          pay: ev.target.querySelector('#creat-company-pay').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          new Alert(data)
          if (data.status) {
            Localstorage.setLocalStorage('companies', data.response.companies)
            this.buildCompaniesList(data.response.companies)
            cleaningForms(ev.target)
            ev.target.querySelector('#creat-company-email').disabled = false
            ev.target.querySelector('.primory-btn').value = 'Створити'
            this.$companyCreatForm
              .querySelector('.primory-btn')
              .classList.remove('edit-btn')
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

  buildCompaniesList(companies) {
    let ls = []
    companies.map((item) => {
      ls.push({
        name: item['company_name'],
        id: item['company_id'],
      })
    })
    Localstorage.setLocalStorage('companies', ls)
    Page.clearList(document.querySelectorAll('#companies-table tr'))
    let $list = `<tr class="table-row-header">
                        <th class="table-header">id</th>
                        <th class="table-header">Назва</th>
                        <th class="table-header">Пошта</th>
                        <th class="table-header">Сайт</th>
                        <th class="table-header">Адреса</th>
                        <th class="table-header">Телефон</th>
                        <th class="table-header">Оплата</th>
                        <th class="table-header"></th>
                    </tr>`
    for (let company of companies) {
      $list += `<tr class="table-row">
                        <td class="table-cell_id">${company.company_id}</td>
                        <td class="table-cell_name">${company.company_name}</td>
                        <td class="table-cell_email">${company.company_email}</td>
                        <td class="table-cell_site"><a target="_blank" href="${company.company_site}">${company.company_site}</a></td>
                        <td class="table-cell_address">${company.company_address}</td>
                        <td class="table-cell_phone">${company.company_phone}</td>
                        <td class="table-cell_pay">${company.company_payment}</td>
                        <td class="table-cell"><i class="fas fa-edit"></i></td>
                    </tr>`
    }
    document.querySelector('#companies-table').innerHTML = $list
    EventGroup.addEventGroup(
      document.querySelectorAll('#companies-table .fa-edit'),
      'click',
      this.companyEditing.bind(this)
    )
  }

  companyEditing = (ev) => {
    let $row = ev.target.parentNode.parentNode
    this.$companyCreatForm.querySelector(
      '.company-name-input'
    ).value = $row.querySelector('.table-cell_name').innerHTML
    this.$companyCreatForm.querySelector(
      '.company-address-input'
    ).value = $row.querySelector('.table-cell_address').innerHTML
    this.$companyCreatForm.querySelector(
      '.company-site-input'
    ).value = $row.querySelector('.table-cell_site a').innerHTML
    this.$companyCreatForm.querySelector(
      '.company-email-input'
    ).value = $row.querySelector('.table-cell_email').innerHTML
    this.$companyCreatForm.querySelector('.company-email-input').disabled = true
    this.$companyCreatForm.querySelector(
      '.company-phone-input'
    ).value = $row.querySelector('.table-cell_phone').innerHTML
    this.$companyCreatForm.querySelector(
      '.company-pay-input'
    ).value = $row.querySelector('.table-cell_pay').innerHTML
    this.$companyCreatForm.querySelector('.primory-btn').value = 'Змінити'
    this.$companyCreatForm
      .querySelector('.primory-btn')
      .classList.add('edit-btn')
    this.temp.companyId = $row.querySelector('.table-cell_id').innerHTML
    this.$companyCreatForm.scrollIntoView({
      block: 'center',
      behavior: 'smooth',
    })
  }
}
