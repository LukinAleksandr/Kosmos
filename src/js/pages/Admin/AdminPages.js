import Page from '../Page'
import EventGroup from '../../eventGroup/EventGroup'
import Loading from '../../loading/Loading'

import ControlUsers from './ControlUsers'
import ControlCompanies from './ControlCompanies'
import ControlCategories from './ControlCategories'
import ControlPosts from './ControlPosts'

export default class AdminPage extends Page {
  constructor() {
    super()
    this.$adminMenuList = document.querySelectorAll(
      '#admin-nav .admin-nav_radio'
    )
    this.loading = new Loading(this)
    this.loadingData = []
    this.controlUsers = new ControlUsers()
    this.controlCompanies = new ControlCompanies()
    this.controlCategories = new ControlCategories()
    this.controlPosts = new ControlPosts()
    this.setup()
  }

  setup = () => {
    EventGroup.addEventGroup(this.$adminMenuList, 'click', this.adminSwitchMenu)
    this.loading.loadAdminPage()
  }

  adminSwitchMenu(ev) {
    const $sectionList = document.querySelectorAll('#article-admin .section')
    for (let i of $sectionList) {
      i.classList.contains(ev.target.value)
        ? i.classList.remove('hide')
        : i.classList.add('hide')
    }
  }
}
