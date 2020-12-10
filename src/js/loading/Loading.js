import Alert from '../Alert/Alert'
import Localstorage from '../localstorage/Localstorage'
import Post from '../posts/Post'

export default class Loading {
  constructor(c) {
    this.user = c
  }
  loadUserPage = () => {
    fetch('/user/load', {})
      .then((respo) => respo.json())
      .then((data) => {
        if (data.status) {
          Localstorage.clearLocalStorage()
          let companies = []
          let categories = []
          let subcategories = []
          data.response.companies.map((item) => {
            companies.push({
              name: item['company_name'],
              id: item['company_id'],
            })
          })
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
          Localstorage.setLocalStorage('companies', companies)

          this.user.posts = data.response.posts.map((item) => new Post(item))
          this.user.user_name = data.response.profile.user_name
          this.user.user_phone = data.response.profile.user_phone
          this.user.user_position = data.response.profile.user_position
          this.user.company_name = data.response.profile.company_name
          this.user.company_address = data.response.profile.company_address
          this.user.user_email = data.response.profile.user_email
          this.user.renderInfo()
          this.user.printerPost.toUserProfile(
            document.querySelector('.cards-list'),
            this.user.posts,
            this.user.removePost
          )
        } else {
          let alert = new Alert(data)
        }
      })
      .finally(() => {
        setTimeout(() => {
          document.querySelector('.loader').style.display = 'none'
        }, 1000)
      })
  }

  loadAdminPage = () => {
    setTimeout(() => {
      fetch(
        '/21232f297a57a5a743894a0e4a801fc3/ec4d1eb36b22d19728e9d1d23ca84d1c'
      )
        .then((respo) => respo.json())
        .then((data) => {
          if (data.status) {
            Localstorage.setLocalStorage('users', data.response.users)
            Localstorage.setLocalStorage(
              'checking_post',
              data.response.checking_posts
            )
            this.user.loadingData = data.response
            this.user.controlUsers.buildUsersList()
            this.user.controlCompanies.buildCompaniesList(
              this.user.loadingData.companies
            )
            this.user.controlCategories.buildCategoriesList()
            this.user.controlPosts.buildPostsList()
          } else {
            new Alert(data)
          }
        })
    }, 100)
  }
}
