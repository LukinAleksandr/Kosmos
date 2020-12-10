export default class EventGroup {
  //метод для вешания обработчика событий на группу объектов
  static addEventGroup(list, listener, callback) {
    for (let i of [...list]) {
      i.addEventListener(listener, callback)
    }
  }
  //метод для удаления обработчиков событий с группы объектов
  static removeEventGroup(list, listener, callback) {
    for (let i of [...list]) {
      i.removeEventListener(listener, callback)
    }
  }
}
