const template = document.createElement('template');
template.innerHTML = `<p><slot/></p>`;

export class UpdateTicker extends HTMLElement {
    constructor() {
        super();
        this._shadowRoot = this.attachShadow({ 'mode': 'open' });
        this._shadowRoot.appendChild(template.content.cloneNode(true));
    }

    get secondsLeft() {
        return Number(this.getAttribute('remainder'));
    }

    get maxSeconds() {
        return Number(this.getAttribute('max'));
    }

    get langRunningUpdate() {
        return this.getAttribute('l-running-update') ?? 'Running';
    }

    get langUntilUpdate() {
        return this.getAttribute('l-until-update') ?? 's until update';
    }

    connectedCallback() {
        let remainder = this.secondsLeft;
        let counter = document.createElement('strong');
        let attached = false;

        this.$p = this._shadowRoot.querySelector('p');

        setInterval(() => {
            if (remainder <= 0) {
                attached = false;
                this.$p.innerHTML = this.langRunningUpdate;
                remainder = this.maxSeconds - 1;
            } else {
                counter.innerHTML = remainder;
                if (!attached) {
                    this.$p.innerHTML = ` ${this.langUntilUpdate}`;
                    this.$p.prepend(counter);
                }
                remainder--;
            }
        }, 1000);

    }
}
