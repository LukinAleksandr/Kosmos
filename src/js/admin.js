import * as $ from 'jquery'
import mask from 'jquery-mask-plugin'

import UserPage from './pages/User/UserPage'
import AdminPage from './pages/Admin/AdminPages'

import '../sass/user.sass'
import '../sass/admin.sass'
import '../sass/modal.sass'
import '../sass/alert.sass'

$(document).ready(function () {
  $('.date').mask('00/00/0000')
  $('.phone').mask('(000) 000-00-00')
})

const page = new UserPage()
const admin = new AdminPage()
