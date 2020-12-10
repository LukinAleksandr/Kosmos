import EventGroup from '../../eventGroup/EventGroup'
import Alert from '../../Alert/Alert'
import User from '../../User/User'
import Validation from '../../Validation'
import Post from '../../posts/Post'
import Search from '../../search/Search'
import Page from '../Page'
import {
  disabledForm,
  enabledForm,
} from '../../enabled-disabled/enabled-disabled'

export default class UserPage extends Page {
  constructor() {
    super()
    this.$menuList = document.querySelectorAll('.nav_radio')
    this.$searchForm = document.querySelector('#search-form')
    this.user = new User()
    this.search = new Search()
    this.$newPassForm = document.querySelector('#newpass-form')
    this.$creatCardForm = document.querySelector('.creat-card-form')
    this.$catBlock = this.$creatCardForm.querySelector('.category-block')
    this.$subcatBlock = this.$creatCardForm.querySelector('.subcategory-block')

    this.setup()
  }

  setup = () => {
    EventGroup.addEventGroup(
      this.$menuList,
      'click',
      this.switchMenu.bind(this)
    )
    this.$creatCardForm.addEventListener('submit', this.creatCard.bind(this))
    this.$creatCardForm
      .querySelector('#agreement')
      .addEventListener('change', this.agreementToggle)
    disabledForm(this.$creatCardForm)

    this.$catBlock
      .querySelector('#card-category')
      .addEventListener('click', (ev) =>
        super.buildSelectList.apply(this, [ev, 'category', 'categories'])
      )

    this.$subcatBlock
      .querySelector('#card-subcategory')
      .addEventListener('click', (ev) =>
        super.buildSelectList.apply(this, [ev, 'subcategory', 'subcategories'])
      )

    document
      .querySelector('.destroy')
      .addEventListener('click', this.sessionDestroy)
  }
  //метод переключения меню
  switchMenu(ev) {
    const $articleList = [...document.querySelectorAll('.article')]
    $articleList.map((item) => {
      item.classList.contains(ev.target.value)
        ? item.classList.remove('hide')
        : item.classList.add('hide')
    })
    if (ev.target.value === 'search') {
      this.$searchForm.classList.remove('hide')
      this.$newPassForm.classList.add('hide')
    } else {
      this.$searchForm.classList.add('hide')
      this.$newPassForm.classList.remove('hide')
    }
  }
  //метод создания записи пользователя
  creatCard = (ev) => {
    ev.preventDefault()
    let formValidationSearch = new Validation(ev.target)
    if (!formValidationSearch.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidationSearch.status.message
    } else {
      let uri = '/user/create?action=card'
      disabledForm(ev.target)
      fetch(uri, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          category: ev.target.querySelector('#card-category').value,
          subcategory: ev.target.querySelector('#card-subcategory').value,
          note: ev.target.querySelector('#card-note').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          new Alert(data)
          if (data.status) {
            this.user.posts = data.response.posts.map((item) => new Post(item))
            Page.clearList(document.querySelectorAll('.cards-list .cards-item'))
            this.user.printerPost.toUserProfile(
              document.querySelector('.cards-list'),
              this.user.posts,
              this.user.removePost
            )
            ev.target.querySelector('textarea').value = ''
            ev.target.querySelector('.error-message').innerHTML = ''
          } else {
            ev.target.querySelector('.error-message').innerHTML =
              data.response.message
          }
        })
        .finally(() => {
          setTimeout(() => {
            enabledForm(ev.target)
          }, 1000)
        })
    }
  }
  agreementToggle = (ev) => {
    ev.target.checked
      ? enabledForm(this.$creatCardForm)
      : disabledForm(this.$creatCardForm)
  }

  sessionDestroy = (ev) => {
    fetch(
      '/user/exit',
      (this.fetchInit = {
        method: 'GET',
        headers: new Headers(),
        cache: 'default',
      })
    ).then((data) => {
      window.location.href = 'https://kosmos.filearchive.website/'
    })
  }
}
