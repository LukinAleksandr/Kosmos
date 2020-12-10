import Modal from './Modal'

export default confirm = function(options) {
    return new Promise((resolve, reject) => {
      const modal = new Modal({
        title: options.title,
        message: options.message,
        buttons: [
          {text: 'Так', type: 'success', handler() {
              resolve()
            }},
          {text: 'Ні', type: 'denied', handler() {
              reject()
            }}
        ]
      })
      setTimeout(() => modal.open(), 100)
    })
  }