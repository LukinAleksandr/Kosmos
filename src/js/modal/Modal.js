export default class Modal{
    constructor(options){
        this.options = options;
        this.$modal = this.render();
        this.setup();
        this.ANIMATION_SPEED = 200;
    }

    createModalFooter(btns){
        const wrap = document.createElement('div')
        wrap.id = 'modal-footer';

        btns.forEach(btn => {
            const $btn = document.createElement('button')
            $btn.textContent = btn.text
            $btn.classList.add('modal-btn')
            $btn.classList.add(btn.type)
            $btn.dataset.type = btn.type
            $btn.onclick = btn.handler || null

            wrap.appendChild($btn)
        })
        return wrap
    }

    render = () => {
        const modal = document.createElement('div');
        modal.classList.add('modal');
        modal.insertAdjacentHTML('afterbegin', `
            <div id="modal-overlay" data-type="denied">
                <div id="modal-window">
                    <div id="modal-body">
                        <p id="modal-title">${this.options.title}</p>
                        <p id="modal-message">${this.options.message}</p>
                    </div>
                </div>
            </div>
        `);
        const footer = this.createModalFooter(this.options.buttons);
        document.body.appendChild(modal);
        document.querySelector('#modal-body').appendChild(footer);

        return modal
    }

    setup(){
        this.clickHandler = this.clickHandler.bind(this);
        this.$modal.addEventListener('click', this.clickHandler)
    }

    clickHandler(event){
        const {type} = event.target.dataset;
        if(type === 'denied' || type === 'success'){
            this.close();
        }
    }

    open(){
        this.$modal.classList.add('open');
    }

    close(){
        this.$modal.classList.remove('open');
        this.$modal.classList.add('close');
        setTimeout(()=>{
            this.$modal.classList.remove('close');
            this.destroy();
        }, this.ANIMATION_SPEED)
    }

    destroy(){
        this.$modal.removeEventListener('click', this.clickHandler);
        this.$modal.remove();
    }
}

