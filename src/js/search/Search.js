import Validation from '../Validation'
import Alert from '../Alert/Alert'
import Page from '../pages/Page'
import confirm from '../modal/confirm'
import EventGroup from '../eventGroup/EventGroup'
import { disabledForm, enabledForm } from '../enabled-disabled/enabled-disabled'

export default class Search extends Page {
  constructor() {
    super()
    this.searchForm = document.querySelector('#search-form')
    this.page = ''
    this.compBlock = this.searchForm.querySelector('.company-block') || null
    this.catBlock = this.searchForm.querySelector('.category-block') || null
    this.subcatBlock = this.searchForm.querySelector('.subcategory-block')
    this.setup()
  }

  setup = () => {
    this.searchForm.addEventListener('submit', this.actionSearch)

    if (this.compBlock) {
      this.compBlock
        .querySelector('#search-company')
        .addEventListener('click', (ev) =>
          super.buildSelectList(ev, 'company', 'companies')
        )
      this.compBlock
        .querySelector('#search-company')
        .addEventListener('input', (ev) =>
          super.buildSelectList(ev, 'company', 'companies')
        )
    }
    if (this.catBlock) {
      this.catBlock
        .querySelector('#search-category')
        .addEventListener('click', (ev) =>
          super.buildSelectList(ev, 'category', 'categories')
        )
      this.catBlock
        .querySelector('#search-category')
        .addEventListener('input', (ev) =>
          super.buildSelectList(ev, 'category', 'categories')
        )
    }
    this.subcatBlock
      .querySelector('#search-subcategory')
      .addEventListener('click', (ev) =>
        super.buildSelectList(ev, 'subcategory', 'subcategories')
      )
    this.subcatBlock
      .querySelector('#search-subcategory')
      .addEventListener('input', (ev) =>
        super.buildSelectList(ev, 'subcategory', 'subcategories')
      )
  }

  sendMoreInfo = (ev) => {
    let id = ev.target.dataset.id
    confirm({
      title: 'Детальніше',
      message:
        'На вашу электрону адресу прийде лист з детальною інформацією про запис',
    })
      .then(() => {
        fetch(`/detailed/description?id=${id}`, {
          method: 'GET',
          headers: new Headers(),
          cache: 'default',
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
          })
      })
      .catch((data) => console.log('Отмена'))
  }

  //метод выполнения поиска по базе
  actionSearch = (ev) => {
    //остановка выполнения дефолтного поведения формы
    ev.preventDefault()
    let list = '',
      uri = '',
      option = {}

    //Отправка заполненой формы поиска на валидацию
    let formValidationSearch = new Validation(ev.target)
    if (!formValidationSearch.status.valid) {
      //если форма не валидна, вывод ошибки
      ev.target.querySelector('.error-message').innerHTML =
        formValidationSearch.status.message
    } else {
      //Блокировка кнопки поиска, очистка сообщений об ошибках, запуск лоадера
      disabledForm(ev.target)
      document.querySelector(
        '.result-list'
      ).innerHTML = `<div class="lds-hourglass"></div>`
      if (this.compBlock !== null && this.catBlock !== null) {
        //Поиск для пользователя ПК
        this.page = 'user'
        uri = '/user/search'
        option = {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            keyword: ev.target.querySelector('#search-keyword').value,
            company: ev.target.querySelector('#search-company').value,
            category: ev.target.querySelector('#search-category').value,
            subcategory: ev.target.querySelector('#search-subcategory').value,
          }),
        }
      } else {
        //Поиск для пользователя ПК
        this.page = 'index'
        uri = '/index/search'
        option = {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            keyword: ev.target.querySelector('#search-keyword').value,
            subcategory: ev.target.querySelector('#search-subcategory').value,
          }),
        }
      }
      //отправка запроса на сервер, и построение списка на основании ответа
      fetch(uri, option)
        .then((respo) => respo.json())
        .then((data) => {
          setTimeout(() => {
            enabledForm(ev.target)
            if (data.status) {
              if (data.response.length < 1) {
                document.querySelector(
                  '.result-list'
                ).innerHTML = `<li class='result-item center'>Збігів, не знайдено!</li>`
                return false
              }
              list = this.buildSearchList(data.response)
              document.querySelector('.result-list').innerHTML = list
              EventGroup.addEventGroup(
                document.querySelectorAll('.more'),
                'click',
                this.sendMoreInfo
              )
            } else {
              ev.target.querySelector('.error-message').innerHTML =
                data.response
              document.querySelector('.result-list').innerHTML = data.response
            }
          }, 1000)
        })
        .catch((err) => {
          document.querySelector(
            '.result-list'
          ).innerHTML = `<li class='result-item center'>Збігів, не знайдено!</li>`
        })
    }
  }

  buildSearchList = (data) => {
    let list = ''
    data.map((item) => {
      list += `
                <li class="result-item">
                    <div class="result-row">
                        <span class="cell cell-header bold">Підприємство:</span>
                        <span class="cell cell-content regular">${
                          item.company_name
                        }</span>
                    </div>
                    <div class="result-row">
                        <span class="cell cell-header bold">Категорія:</span>
                        <span class="cell cell-content regular">${
                          item.category
                        }</span>
                    </div>
                    <div class="result-row">
                        <span class="cell cell-header bold">Підкатегорія:</span>
                        <span class="cell cell-content regular">${
                          item.sub_category
                        }</span>
                    </div>
                    <div class="result-row">
                        <span class="cell cell-header bold">Опис:</span>
                        <span class="cell cell-content regular">${
                          item.note
                        }</span>
                    </div>
                    ${
                      this.page === 'user'
                        ? ` <div class="result-row"><i title="Детальніше" class="fas fa-mail-bulk more" data-id='${item.post_id}'></i></div>`
                        : ``
                    }
                </li>
            `
    })
    return list
  }
}
