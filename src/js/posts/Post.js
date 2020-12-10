export default class Post {
  constructor(post) {
    this.id = post.post_id
    this.userId = post.user_id
    this.category = post.category
    this.subcategory = post.sub_category
    this.text = post.note
    this.status = post.status
  }
}
