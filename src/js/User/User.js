import Validation from '../Validation'
import Alert from '../Alert/Alert'
import confirm from '../modal/confirm'
import Loading from '../loading/Loading'
import EventGroup from '../eventGroup/EventGroup'
import Page from '../pages/Page'
import Post from '../posts/Post'
import PrinterPosts from '../posts/PrinterPosts'
import { disabledForm, enabledForm } from '../enabled-disabled/enabled-disabled'

export default class User {
  constructor() {
    this.user_name = ''
    this.user_phone = ''
    this.user_position = ''
    this.company_name = ''
    this.company_address = ''
    this.user_email = ''
    this.loading = new Loading(this)
    this.posts = []
    this.printerPost = new PrinterPosts()
    this.$newPassForm = document.querySelector('#newpass-form')

    this.$userInfoButtons = document.querySelectorAll('.section_user-info i')
    this.setup()
  }

  setup() {
    this.loading.loadUserPage()
    EventGroup.addEventGroup(
      [...this.$userInfoButtons],
      'click',
      this.editUserInfo.bind(this)
    )
    this.$newPassForm.addEventListener('submit', this.changePass.bind(this))
  }

  renderInfo() {
    document.querySelector('#header_user-name').innerHTML = this.user_name
    document.querySelector(
      '.info-company .info-cell-content'
    ).innerHTML = this.company_name
    document.querySelector(
      '.info-position .info-cell-content'
    ).innerHTML = this.user_position
    document.querySelector(
      '.info-name .info-cell-content'
    ).innerHTML = this.user_name
    document.querySelector(
      '.info-phone .info-cell-content'
    ).innerHTML = this.user_phone
    document.querySelector(
      '.info-adress .info-cell-content'
    ).innerHTML = this.company_address
    document.querySelector(
      '.info-email .info-cell-content'
    ).innerHTML = this.user_email
  }

  removePost = (ev) => {
    let uri = `/user/delet?action=card&key=${ev.target.parentNode.parentNode.attributes.key.value}`
    confirm({
      title: 'Видалення запису',
      message: 'Ви впевнені що бажаете видалити запис?',
    })
      .then(() => {
        fetch(uri, this.fetchInit)
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              this.posts = data.response.post_list.map((item) => new Post(item))
              Page.clearList(
                document.querySelectorAll('.cards-list .cards-item')
              )
              this.printerPost.toUserProfile(
                document.querySelector('.cards-list'),
                this.posts,
                this.removePost
              )
            }
          })
      })
      .catch((data) => console.log('Отмена'))
  }

  //метод изменения личных данных пользователя
  editUserInfo(ev) {
    let $rowUserInfo = ev.target.parentNode.parentNode,
      $userInfoContentCell = $rowUserInfo.querySelector('.info-cell-content'),
      $userInfoInputCell = $rowUserInfo.querySelector('.info-cell-input'),
      $userInfoInput = $userInfoInputCell.querySelector('input')

    const actionEditProfile = (className, target) => {
      if (target.classList.contains('fa-edit')) {
        $userInfoInput.value = $userInfoContentCell.innerHTML
      } else {
        $userInfoContentCell.innerHTML = $userInfoInput.value
        if (
          $userInfoInput.value != this[className] &&
          $userInfoInput.value != ''
        ) {
          fetch(
            `/user/chenge?action=profile&changer=${className}&value=${$userInfoInput.value}`,
            this.fetchInit
          )
            .then((respo) => respo.json())
            .then((data) => {
              this[className] = $userInfoInput.value
              this.renderInfo()
              new Alert(data)
            })
        }
      }
      target.classList.toggle('fa-edit')
      target.classList.toggle('fa-save')
      $userInfoContentCell.classList.toggle('hide')
      $userInfoInputCell.classList.toggle('hide')
    }

    switch (true) {
      case $rowUserInfo.classList.contains('info-position'):
        actionEditProfile('user_position', ev.target)
        break
      case $rowUserInfo.classList.contains('info-name'):
        actionEditProfile('user_name', ev.target)
        break
      case $rowUserInfo.classList.contains('info-phone'):
        actionEditProfile('user_phone', ev.target)
        break
    }
  }
  //метод изменения пароля пользователя
  changePass(ev) {
    ev.preventDefault()
    let formValidationPass = new Validation(ev.target)
    if (!formValidationPass.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidationPass.status.message
      return false
    } else {
      disabledForm(ev.target)
      fetch('/password/change', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          old: ev.target.querySelector('#oldpass').value,
          new: ev.target.querySelector('#newpass').value,
          newRep: ev.target.querySelector('#newpass-repeat').value,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          new Alert(data)
          if (data.status) {
            ev.target.querySelector('#oldpass').value = ''
            ev.target.querySelector('#newpass').value = ''
            ev.target.querySelector('#newpass-repeat').value = ''
          } else {
            ev.target.querySelector('.error-message').innerHTML =
              data.response.message
          }
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
