export default class Localstorage {
  static getLocalStorage = (name) => {
    return JSON.parse(localStorage.getItem(name))
  }
  static setLocalStorage = (name, value) => {
    localStorage.setItem(name, JSON.stringify(value))
  }

  static clearLocalStorage = () => {
    localStorage.clear()
  }
}
