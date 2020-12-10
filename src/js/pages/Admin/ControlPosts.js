import Alert from '../../Alert/Alert'
import Page from '../Page'
import Localstorage from '../../localstorage/Localstorage'
import EventGroup from '../../eventGroup/EventGroup'
import confirm from '../../modal/confirm'

export default class ControlPosts extends Page {
  constructor() {
    super()
  }
  buildPostsList() {
    let $list = ``,
      posts = Localstorage.getLocalStorage('checking_post')
    Page.clearList(document.querySelectorAll('.checking-post-list li'))

    for (let post of posts) {
      $list += `<li class="cheking-post-item" key="${post.post_id}">
                        <div class="card-row post-company-block">
                            <span class="cell cell-header bold">Підприємство:</span>
                            <span class="cell cell-content regular">${post.company_name}</span>
                        </div>
                        <div class="card-row post-name-block">
                            <span class="cell cell-header bold">П.І.П:</span>
                            <span class="cell cell-content regular">${post.user_name}</span>
                        </div>
                        <div class="card-row post-position-block">
                            <span class="cell cell-header bold">Посада:</span>
                            <span class="cell cell-content regular">${post.user_position}</span>
                        </div>
                        <div class="card-row post-email-block">
                            <span class="cell cell-header bold">E-mail:</span>
                            <span class="cell cell-content regular">${post.user_email}</span>
                        </div>
                        <div class="card-row post-phone-block">
                            <span class="cell cell-header bold">Телефон:</span>
                            <span class="cell cell-content regular">${post.user_phone}</span>
                        </div>
                        <div class="card-row post-category-block">
                            <span class="cell cell-header bold">Категорія:</span>
                            <span class="cell cell-content regular">${post.category}</span>
                        </div>
                        <div class="card-row post-subcategory-block">
                            <span class="cell cell-header bold">Підтегорія:</span>
                            <span class="cell cell-content regular">${post.sub_category}</span>
                        </div>
                        <div class="card-row post-note-block">
                            <span class="cell cell-header bold">Опис:</span>
                            <span class="cell cell-content regular">${post.note}</span>
                        </div>
                        
                        <div class="card-row buttons-row">
                            <i class="fas fa-trash-alt"></i>
                            <i class="fas fa-thumbs-up"></i>
                        </div>  
                    </li>`
    }
    document.querySelector('.checking-post-list').innerHTML = $list
    EventGroup.addEventGroup(
      document.querySelectorAll('.checking-post-list .fa-trash-alt'),
      'click',
      this.deleteСheckedPost.bind(this)
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('.checking-post-list .fa-thumbs-up'),
      'click',
      this.confirmCheckedPost.bind(this)
    )
  }
  deleteСheckedPost = (ev) => {
    confirm({
      title: 'Видалення запису.',
      message:
        'Цей запис не відповидає вимогам, та буде видалений остаточно з бази.',
    })
      .then(() => {
        fetch('/admin/delet', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            action: 'post',
            id: ev.target.parentNode.parentNode.attributes.key.value,
          }),
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              Localstorage.setLocalStorage(
                'checking_post',
                data.response.checking_posts
              )
              Page.clearList(
                document.querySelectorAll('.checking-post-list li')
              )
              this.buildPostsList()
            }
          })
      })
      .catch((data) => {})
  }
  confirmCheckedPost = (ev) => {
    confirm({
      title: 'Підтвердити запис.',
      message:
        "Після підтвердження запис з'явиться в пошуку іншим користувачам.",
    })
      .then(() => {
        fetch('/admin/chenge', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            action: 'post',
            id: ev.target.parentNode.parentNode.attributes.key.value,
          }),
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              Localstorage.setLocalStorage(
                'checking_post',
                data.response.checking_posts
              )
              Page.clearList(
                document.querySelectorAll('.checking-post-list li')
              )
              this.buildPostsList()
            }
          })
      })
      .catch((data) => {})
  }
}
