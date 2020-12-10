import Validation from '../../Validation'
import Alert from '../../Alert/Alert'
import Localstorage from '../../localstorage/Localstorage'
import Page from '../Page'
import EventGroup from '../../eventGroup/EventGroup'
import {
  disabledForm,
  enabledForm,
} from '../../enabled-disabled/enabled-disabled'
import confirm from '../../modal/confirm'

export default class ControlCategories extends Page {
  constructor() {
    super()
    this.$categoryCreatForm = document.querySelector('.creat-category-form')
    this.temp = {
      categoryName: null,
      subcategoryName: null,
    }
    this.setup()
    this.listener_saveCategoryChanges = this.saveCategoryChanges.bind(this)
    this.listener_editCategory = this.editCategory.bind(this)
    this.listener_removeCategory = this.removeCategory.bind(this)
    this.listener_addSubcategoryTableRow = this.addSubcategoryTableRow.bind(
      this
    )
  }

  setup = () => {
    this.$categoryCreatForm.addEventListener('submit', this.creatingСategory)
    this.$categoryCreatForm
      .querySelector('.add-subcategory-block')
      .addEventListener('click', this.addSubcatBlock)
  }

  localStorageUpdate = (data) => {
    let categories = []
    let subcategories = []
    data.response.categories.map((item) => {
      categories.push({
        name: item['category'],
        id: item['category_id'],
      })
    })
    data.response.subcategories.map((item) => {
      subcategories.push({
        category: item['category'],
        name: item['sub_category'],
        id: item['sub_category_id'],
      })
    })
    Localstorage.setLocalStorage('categories', categories)
    Localstorage.setLocalStorage('subcategories', subcategories)
  }

  creatingСategory = (ev) => {
    ev.preventDefault()
    let formValidation = new Validation(ev.target)
    if (!formValidation.status.valid) {
      ev.target.querySelector('.error-message').innerHTML =
        formValidation.status.message
    } else {
      disabledForm(ev.target)
      let $categoryName = ev.target.querySelector('#creat-category-name').value
      let $subcatInputCollection = ev.target.querySelectorAll(
        '.subcategory-name-input'
      )
      let subcatCollection = [...$subcatInputCollection].map(
        (item) => item.value
      )
      fetch('/admin/creat', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json;charset=utf-8' },
        body: JSON.stringify({
          action: 'category',
          categoryName: $categoryName,
          subcategories: subcatCollection,
        }),
      })
        .then((respo) => respo.json())
        .then((data) => {
          new Alert(data)
          if (data.status) {
            this.localStorageUpdate(data)
            this.buildCategoriesList()
          } else {
            ev.target.querySelector('.error-message').innerHTML =
              data.response.message
          }
        })
        .catch((err) => alert(err))
        .finally(() => {
          setTimeout(() => {
            enabledForm(ev.target)
            ev.target.querySelector('#creat-category-name').value = ''
            ;[...$subcatInputCollection].forEach((item) => (item.value = ''))
          }, 1000)
        })
    }
  }
  addSubcatBlock = (ev) => {
    const $inputBlock = document.createElement('div')
    $inputBlock.className = 'input-block subcategory-name-block'
    const $inputBlock_Span = document.createElement('span')
    $inputBlock_Span.className = 'icon'
    const $inputBlock_Icon = document.createElement('i')
    $inputBlock_Icon.className = 'fas fa-check-double'
    $inputBlock_Span.appendChild($inputBlock_Icon)
    $inputBlock.appendChild($inputBlock_Span)
    const $inputBlok_Input = document.createElement('input')
    $inputBlok_Input.type = 'text'
    $inputBlok_Input.name = 'subcategory'
    $inputBlok_Input.className = 'subcategory-name-input'
    $inputBlok_Input.placeholder = 'Назва підкатегорії'
    $inputBlok_Input.autocorrect = 'off'
    $inputBlok_Input.autocomplete = 'off'
    $inputBlok_Input.autocapitalize = 'off'
    $inputBlock.appendChild($inputBlok_Input)

    const $buttonBlock = document.createElement('div')
    $buttonBlock.className = 'button-block del-subcategory-block'
    const $buttonBlock_Span = document.createElement('span')
    $buttonBlock_Span.className = 'icon2'
    const $buttonBlock_Icon = document.createElement('i')
    $buttonBlock_Icon.className = 'fas fa-minus-circle'
    $buttonBlock_Icon.addEventListener('click', this.deletSubcatBlock)
    $buttonBlock_Span.appendChild($buttonBlock_Icon)
    $buttonBlock.appendChild($buttonBlock_Span)

    let $formChildren = [...this.$categoryCreatForm.childNodes]
    $formChildren[$formChildren.length - 4].before($inputBlock)
    $formChildren[$formChildren.length - 4].before($buttonBlock)
  }

  deletSubcatBlock = (ev) => {
    const $buttonBlock = ev.target.parentNode.parentNode
    const $inputBlock = $buttonBlock.previousSibling
    $buttonBlock.remove()
    $inputBlock.remove()
  }

  buildCategoriesList() {
    Page.clearList(document.querySelectorAll('#categories-table tr'))
    let categories = Localstorage.getLocalStorage('categories')
    let subcategories = Localstorage.getLocalStorage('subcategories')

    let $list = `<tr class="table-row-header">
                        <th class="table-header">Категорія</th>
                        <th class="table-header">Підкатегорія</th>
                        <th class="table-header"></th>
                        <th class="table-header"></th>
                    </tr>`

    for (let category of categories) {
      $list += `<tr class="table-row category-row">
                        <td class="table-cell_category">
                            <input type="text" name="name" class="table-cell_edit-category hide" placeholder="Назва категорії" autocorrect="off" autocapitalize="off" autocomplete="off">
                            <span class="table-category_value" key="${
                              category.id
                            }">${category.name}</span>
                        </td>
                        ${
                          category.name === 'Загальна'
                            ? `<td class="table-cell_subcategory">
                                </td><td class="table-cell_edit"></td>
                                <td class="table-cell_delet"></td>
                                `
                            : `<td class="table-cell_subcategory">
                                    <i class="fas fa-plus-circle"></i>
                                </td>
                                <td class="table-cell_edit">
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-save hide"></i>
                                </td>
                                <td class="table-cell_delet">
                                    <i class="fas fa-trash"></i>
                                </td>`
                        }
                    </tr>`

      let result = subcategories.filter(
        (subcategory) => subcategory.category === category.name
      )
      for (let subcategory of result) {
        $list += `<tr class="table-row subcategory-row">
                        <td class="table-cell_category"></td>
                        <td class="table-cell_subcategory">
                            <input type="text" name="name" class="table-cell_edit-subcategory hide" placeholder="Назва підкатегорії" autocorrect="off" autocapitalize="off" autocomplete="off">
                            <span class="table-subcategory_value" category="${
                              category.name
                            }" key="${subcategory.id}">${
          subcategory.name
        }</span>
                        </td>
                        ${
                          category.category === 'Загальна'
                            ? `<td class="table-cell_edit">
                                </td>
                                <td class="table-cell_delet">
                                </td>`
                            : `<td class="table-cell_edit">
                                    <i class="fas fa-edit"></i>
                                    <i class="fas fa-save hide"></i>
                                </td>
                                <td class="table-cell_delet">
                                    <i class="fas fa-trash"></i>
                                </td>`
                        }
                        
                    </tr>`
      }
    }
    document.querySelector('#categories-table').innerHTML = $list
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-plus-circle'),
      'click',
      this.listener_addSubcategoryTableRow
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-save'),
      'click',
      this.listener_saveCategoryChanges
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-edit'),
      'click',
      this.listener_editCategory
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-trash'),
      'click',
      this.listener_removeCategory
    )
  }

  addSubcategoryTableRow = (ev) => {
    let $row = ev.target.parentNode.parentNode
    $row.insertAdjacentHTML(
      'afterEnd',
      `<tr class="table-row subcategory-row">
                        <td class="table-cell_category"></td>
                        <td class="table-cell_subcategory">
                            <input type="text" name="name" class="table-cell_edit-subcategory" placeholder="Назва підкатегорії" autocorrect="off" autocapitalize="off" autocomplete="off">
                            <span class="table-subcategory_value hide" key="null" category="${
                              $row.querySelector('.table-category_value')
                                .innerHTML
                            }"></span>
                        </td>
                        <td class="table-cell_edit">
                            <i class="fas fa-edit hide"></i>
                            <i class="fas fa-save"></i>
                        </td>
                        <td class="table-cell_delet">
                            <i class="fas fa-trash"></i>
                        </td>
                </tr>`
    )
    EventGroup.removeEventGroup(
      document.querySelectorAll('#categories-table .fa-save'),
      'click',
      this.listener_saveCategoryChanges
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-save'),
      'click',
      this.listener_saveCategoryChanges
    )
    EventGroup.removeEventGroup(
      document.querySelectorAll('#categories-table .fa-edit'),
      'click',
      this.listener_editCategory
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-edit'),
      'click',
      this.listener_editCategory
    )
    EventGroup.removeEventGroup(
      document.querySelectorAll('#categories-table .fa-trash'),
      'click',
      this.listener_removeCategory
    )
    EventGroup.addEventGroup(
      document.querySelectorAll('#categories-table .fa-trash'),
      'click',
      this.listener_removeCategory
    )
  }

  editCategory = (ev) => {
    let $row = ev.target.parentNode.parentNode
    let $inpCat = $row.querySelector('.table-cell_edit-category')
    let $spanCat = $row.querySelector('.table-category_value')
    let $inpSubcat = $row.querySelector('.table-cell_edit-subcategory')
    let $spanSubcat = $row.querySelector('.table-subcategory_value')
    let $buttonEdit = ev.target.parentNode.querySelector('.fa-edit')

    if ($row.classList.contains('category-row')) {
      $inpCat.value = $spanCat.innerHTML
      this.temp.categoryName = $spanCat.innerHTML
      $spanCat.classList.toggle('hide')
      $inpCat.classList.toggle('hide')
      $buttonEdit.classList.toggle('hide')
      ev.target.parentNode.querySelector('.fa-save').classList.toggle('hide')
    }
    if ($row.classList.contains('subcategory-row')) {
      $inpSubcat.value = $spanSubcat.innerHTML
      this.temp.subcategoryName = $spanSubcat.innerHTML
      $spanSubcat.classList.toggle('hide')
      $inpSubcat.classList.toggle('hide')
      $buttonEdit.classList.toggle('hide')
      ev.target.parentNode.querySelector('.fa-save').classList.toggle('hide')
    }
  }

  removeCategory = (ev) => {
    let options = {}

    if (ev.target.parentNode.parentNode.classList.contains('category-row')) {
      options = {
        action: 'category',
        id: ev.target.parentNode.parentNode.querySelector(
          '.table-category_value'
        ).attributes.key.value,
        name: ev.target.parentNode.parentNode.querySelector(
          '.table-category_value'
        ).innerHTML,
      }
    }
    if (ev.target.parentNode.parentNode.classList.contains('subcategory-row')) {
      options = {
        action: 'subcategory',
        id: ev.target.parentNode.parentNode.querySelector(
          '.table-subcategory_value'
        ).attributes.key.value,
        name: ev.target.parentNode.parentNode.querySelector(
          '.table-subcategory_value'
        ).innerHTML,
        categoryName: ev.target.parentNode.parentNode.querySelector(
          '.table-subcategory_value'
        ).attributes.category.value,
      }
    }
    confirm({
      title: 'Видалення категорії.',
      message:
        "Ви впевнені що бажаете видалити запис? Після видалення всі записи що відносяться до неї перейдуть в категорію 'Загальна'.",
    })
      .then(() => {
        fetch('/admin/delet', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify(options),
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              ev.target.parentNode.parentNode.remove()
              this.localStorageUpdate(data)
              this.buildCategoriesList()
            }
          })
          .catch((err) => alert(err))
      })
      .catch((data) => console.log('Отмена'))
  }

  saveCategoryChanges = (ev) => {
    let $row = ev.target.parentNode.parentNode
    let $inpCat = $row.querySelector('.table-cell_edit-category')
    let $spanCat = $row.querySelector('.table-category_value')
    let $inpSubcat = $row.querySelector('.table-cell_edit-subcategory')
    let $spanSubcat = $row.querySelector('.table-subcategory_value')
    let $buttonEdit = ev.target.parentNode.querySelector('.fa-edit')

    if ($row.classList.contains('category-row')) {
      $spanCat.innerHTML = $inpCat.value
      $spanCat.classList.toggle('hide')
      $inpCat.classList.toggle('hide')
      $buttonEdit.classList.toggle('hide')
      ev.target.parentNode.querySelector('.fa-save').classList.toggle('hide')

      if ($inpCat.value !== this.temp.categoryName) {
        fetch('/admin/chenge', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            action: 'category',
            key: $spanCat.attributes.key.value,
            newName: $inpCat.value,
            oldName: this.temp.categoryName,
          }),
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              this.localStorageUpdate(data)
              this.buildCategoriesList()
            }
          })
          .catch((err) => alert(err))
      }
    }
    if ($row.classList.contains('subcategory-row')) {
      $spanSubcat.innerHTML = $inpSubcat.value
      $spanSubcat.classList.toggle('hide')
      $inpSubcat.classList.toggle('hide')
      $buttonEdit.classList.toggle('hide')
      ev.target.parentNode.querySelector('.fa-save').classList.toggle('hide')

      if ($inpSubcat.value !== this.temp.subcategoryName) {
        fetch('/admin/chenge', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json;charset=utf-8' },
          body: JSON.stringify({
            action: 'subcategory',
            category: $spanSubcat.attributes.category.value,
            subcategoryKey: $spanSubcat.attributes.key.value,
            newName: $inpSubcat.value,
            oldName: this.temp.subcategoryName,
          }),
        })
          .then((respo) => respo.json())
          .then((data) => {
            new Alert(data)
            if (data.status) {
              this.localStorageUpdate(data)
              this.buildCategoriesList()
            }
          })
          .catch((err) => alert(err))
      }
    }
  }
}
