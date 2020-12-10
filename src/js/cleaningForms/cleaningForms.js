export function cleaningForms(form) {
  let inputs = form.querySelectorAll('input')
  let textarea = form.querySelectorAll('textarea')

  inputs.forEach((element) => {
    element.value = ''
  })
  textarea.forEach((element) => {
    element.value = ''
  })
}
