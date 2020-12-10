import EventGroup from '../eventGroup/EventGroup'

export default class PrinterPosts {
  toUserProfile = (target, list, fn) => {
    let str = ''
    for (let post of list) {
      str += `<li class="cards-item" key="${post.id}">
                    <div class="card-row card-category-block">
                        <span class="cell cell-header bold">Категорія:</span>
                        <span class="cell cell-content regular">${
                          post.category
                        }</span>
                    </div>
                    <div class="card-row card-subcategory-block">
                        <span class="cell cell-header bold">Підкатегорія:</span>
                        <span class="cell cell-content regular">${
                          post.subcategory
                        }</span>
                    </div>
                    <div class="card-row card-note-block">
                        <span class="cell cell-header bold">Опис:</span>
                        <span class="cell cell-content regular">${
                          post.text
                        }</span>
                    </div>
                    </div>
                    <div class="card-row buttons-row">
                        <i class="fas fa-trash-alt"></i>
                        ${
                          post.status === '1'
                            ? '<i class="fas flip fa-user-check"><span class="right-flip">ОПУБЛІКОВАНО</span></i>'
                            : '<i class="fas flip fa-user-clock"><span class="right-flip">НА ЗАТВЕРДЖЕННІ</span></i>'
                        }
                    </div>  
                </li>`
    }
    target.innerHTML = str
    EventGroup.addEventGroup(
      target.querySelectorAll('.fa-trash-alt'),
      'click',
      fn
    )
  }
}
