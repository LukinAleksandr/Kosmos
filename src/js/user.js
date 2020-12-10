import * as $ from 'jquery'
import mask from 'jquery-mask-plugin'

import UserPage from './pages/UserPage'
import '../sass/user.sass'
import '../sass/modal.sass'
import '../sass/alert.sass'

$(document).ready(function () {
  $('.date').mask('00/00/0000')
  $('.phone').mask('(000) 000-00-00')
})

const page = new UserPage()
