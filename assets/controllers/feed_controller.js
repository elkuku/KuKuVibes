import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        id: Number
    }
    async connect() {
        this.element.innerHTML = 'Loading '+this.idValue+' ...';
        console.log('feeeed '+this.idValue)

        try {
            const response = await fetch(`/feed/${this.idValue}/fetch`);
            if (!response.ok) {
                this.element.innerHTML=`Response status: ${response.status}`
                throw new Error(`Response status: ${response.status}`);
            }

            const text = await response.text();
            //console.log(text);
            this.element.innerHTML = text;
        } catch (error) {
            console.error(error.message);
            this.element.innerHTML='aaa'+error.message
        }
    }
}
