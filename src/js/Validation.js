export default class Validation{
    constructor(form){
        this.form = form;
        this.valid = true;
        this.errorMsg = '';
        this.scanForm(this.form);
    }
    
    
    scanForm(form){
        let inputCollection = form.querySelectorAll('input');
        let textarea = form.querySelector('textarea');
        inputCollection = [...inputCollection];
        let newInputCollection = inputCollection.filter(item => {
            if(item.type == 'text' || item.type == 'password' || item.type == 'email'){
                return item;
            }
        });
        for(let item of newInputCollection){
            item.previousElementSibling.classList.remove('icon__err');
            this.validation(item);
        }
        if(textarea){
            this.validation(textarea);
        }
    }

    get status(){
        return {valid: this.valid, message: this.errorMsg};
    }
    
    validation = (input) => {
        switch (input.name){
            case 'email':
                this.checkEmail(input);
                break;
            case 'password':
                this.checkPassword(input);
                break;
            case 'phone':
                this.checkPhone(input);
                break;
            case 'data':
                this.checkData(input);
                break;
            case 'name':
                this.checkName(input);
                break;
            case 'company':
                this.checkCompany(input);
                break;
            case 'position':
                this.checkPosition(input);
                break;
            case 'category':
                this.checkCategory(input);
                break;
            case 'subcategory':
                this.checkSubcategory(input);
                break;
            case 'keyword':
                this.checkKeyword(input);
                break;
            case 'role':
                this.checkRole(input);
                break;
            case 'status':
                this.checkStatus(input);
                break;
            case 'textarea':
                this.checkTextarea(input);
                break;
        }
    }

    checkEmail = (input) => {
        if(/(\w+@[a-zA-Z_]+?\.[a-zA-Z]{2,6})/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += "Некоректна електронна пошта!</br>";
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkPassword = (input) => {
        if(/((?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{6,15})/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Пароль некоректний!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkPhone = (input) => {
        if(/(\(\d{3}\) \d{3}\-\d{2}\-\d{2})/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Некоректний формат телефону!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkDate = (input) => {
        if(/(\d{4}\-\d{1,2}\-\d{1,2})/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Некоректний формат дати!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkName = (input) => {
        if(input.value.trim().length >= 3){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Некоректний формат ім\'я!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkCompany = (input) => {
        if(input.value.trim().length >= 3){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += "Виберіть підприємство!</br>";
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkPosition = (input) => {
        if(input.value.trim().length >= 3){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += "Виберіть посаду!</br>";
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkCategory = (input) => {
        if(input.value.trim().length >= 3){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += "Виберіть категорію!</br>";
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkSubcategory = (input) => {
        if(input.value.trim().length !== 0){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += "Виберіть підкатегорію!</br>";
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkKeyword = (input) => {
        if(/^(.){0,100}$/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Максимальна довжина ключових слів 100 символів!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkRole = (input) => {
        if(/^(Адміністратор|Користувач)$/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Виберіть роль!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkStatus = (input) => {
        if(/^(1|2|3)$/.test(input.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Виберіть статус від 1 до 3!</br>';
            input.previousElementSibling.classList.add('icon__err');
        }
    }
    checkTextarea = (textarea) => {
        if(/^(.|\n){20,3000}$/.test(textarea.value)){
            return true;
        }else{
            this.valid = false;
            this.errorMsg += 'Довжина запису повинна бути від 20 до 3000 символів!</br>';
        }
    }
}

