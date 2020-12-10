import Localstorage from '../localstorage/Localstorage'
import EventGroup from '../eventGroup/EventGroup'

export default class Page {
  //метод построения списка поля Select
  buildSelectList(ev, cls, items) {
    this.cleaningAllPageList()
    let selectList = ev.target.parentNode.parentNode.parentNode.getElementsByClassName(
      `${cls}-list`
    )
    let itemList = ev.target.classList.contains('search')
      ? `<li class="${cls}-item" key='0'>Всі</li>`
      : ``
    let ls = Localstorage.getLocalStorage(items)

    if (ev.type === 'input') {
      ls = ls.filter((item) =>
        item.name.toLowerCase().startsWith(ev.target.value.toLowerCase())
      )
    }

    if (cls === 'subcategory') {
      let catBlock =
        ev.target.parentNode.parentNode.parentNode.parentNode.querySelector(
          '.category-block'
        ) || null
      if (catBlock) {
        ls = ls.filter(
          (i) => i.category === catBlock.querySelector('.category-input').value
        )
      }
    }
    if (cls === 'category') {
      ev.target.parentNode.parentNode.querySelector(
        '.subcategory-block .subcategory-input'
      ).value = ''
    }

    ls.map(
      (item) =>
        (itemList += `<li class="${cls}-item" key='${item.id}'>${item.name}</li>`)
    )

    selectList[0].innerHTML = itemList
    selectList[0].style.overflowY = 'hidden'
    selectList[0].lastChild.style.marginBottom = '8px'
    let computedStyle = getComputedStyle(selectList[0]).height
    computedStyle = Number(computedStyle.substring(0, computedStyle.length - 2))
    if (computedStyle >= 200) {
      selectList[0].style.overflowY = 'scroll'
      selectList[0].lastChild.style.marginBottom = '0'
    }
    EventGroup.addEventGroup(
      [...selectList][0].querySelectorAll(`li`),
      'click',
      this.selectItemList
    )
  }
  callback = () => {}
  //метод построения списка ролей
  buildRoleList(ev) {
    this.cleaningAllPageList()
    let list = `<li class="user-role-item" key='user'>Користувач</li><li class="user-role-item" key='admin'>Адміністратор</li>`
    let roleBlock = ev.target.parentNode.parentNode
    roleBlock.querySelector('.user-role-list').innerHTML = list
    EventGroup.addEventGroup(
      roleBlock.querySelectorAll('.user-role-list li'),
      'click',
      this.selectItemList
    )
  }
  //очистка листов (категорий, подкатегорий, компаний, ролей) в критериях поиска
  clearItemList(ul) {
    let list = ul.querySelectorAll('li')
    for (let i = list.length; i >= 1; i--) {
      list[i - 1].remove()
    }
  }
  //метод выбора элемента из списка (категории, подкатегории, компаний, ролей)
  selectItemList = (ev) => {
    ev.target.parentNode.parentNode.querySelector('input').value =
      ev.target.innerHTML
    this.clearItemList(ev.target.parentNode)
  }
  // метод очистки листа элементов
  static clearList(list) {
    for (let item of list) {
      item.remove()
    }
  }
  cleaningAllPageList = () => {
    let selectLists = [...document.querySelectorAll('.input-block ul')]
    if (selectLists) {
      selectLists.forEach((item) => this.clearItemList(item))
    }
  }
}
