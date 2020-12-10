export default class Alert{
    constructor(options){
        this.options = options;
        this.$alert = this.render();
        this.go();
    }

    render(){
        const alert = document.createElement('div');
        alert.id = 'alert-window';
        alert.insertAdjacentHTML('afterbegin', `
                <div id="alert-timer" class="${this.options.status ? 'success' : 'denied'}"></div>
                <div id="alert-body">
                    <p id="alert-title" class='regular'>${this.options.response.message}</p>
                </div>
        `);
        document.body.appendChild(alert);
        return alert
    }

    delay = () =>  new Promise(r => setTimeout(()=>r(), 100))

    async go(){
        await this.delay();
        await this.open();
        await this.close();
        await this.destroy();
    }

    open(){
        this.$alert.classList.add('open');
    }

    close(){
        setTimeout(()=>{
            this.$alert.classList.remove('open');
        }, 4100)
    }

    destroy(){
        setTimeout(()=>{
            this.$alert.remove();
        }, 5100)
    }
}