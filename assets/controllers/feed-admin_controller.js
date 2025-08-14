import {Controller} from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        id: Number
    }

    static targets = ['container', 'tab']

    async connect() {
        console.log('feeeed-admin ')

    }

    async changeCategory(e){
        this.tabTargets.forEach(tab => {
            tab.classList.remove('active')
        })

        e.target.classList.add('active');

        this.containerTarget.innerHTML = `Loading category ${e.params.id} ...`;

        try {
            const response = await fetch(`/feed/${e.params.id}/fetch-category`);
            if (!response.ok) {
                this.containerTarget.innerHTML=`Response status: ${response.status}`
                throw new Error(`Response status: ${response.status}`);
            }

            this.containerTarget.innerHTML = await response.text();
        } catch (error) {
            this.containerTarget.innerHTML='ERROR> '+error.message
        }
    }
}
