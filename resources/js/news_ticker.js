const template = document.createElement('template');
template.innerHTML = `
    <style>
        p {
            margin-left:auto;
            margin-right:auto;
            border:#fff solid 1px;
            text-align:center;
            background-color:#400040;
            color:#fff;
            padding:0;
            border-spacing:0;
            width:600px
        }
        a {
        color:#fff;
        }
    </style>
    <p>
        <span>Loading News, Please wait...</span>
    </p>
`;

export class NewsTicker extends HTMLElement {
    constructor() {
        super();
        this._shadowRoot = this.attachShadow({ 'mode': 'open' });
        this._shadowRoot.appendChild(template.content.cloneNode(true));
    }

    get interval() {
        return Number(this.getAttribute('interval') ?? 5) * 1000;
    }

    get items() {
        const items = this.getAttribute('items');
        return items ? JSON.parse(items) : [];
    }

    connectedCallback() {
        const items = this.items;
        let idx = 0;

        setInterval(() => {
            const item = items[idx];
            this.$span = this._shadowRoot.querySelector('span');

            if (item.url === null) {
                this.$span.innerHTML = item.text;
            } else {
                let $link = document.createElement('a');
                $link.href = item.url;
                $link.innerHTML = item.text;
                this.$span.replaceChildren($link);
            }

            if (items.length > 1) {
                if (idx < items.length - 1) {
                    idx++
                } else if (idx === items.length - 1) {
                    idx = 0;
                }
            }
        }, this.interval);
    }
}
