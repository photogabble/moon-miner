const template = document.createElement('template');
template.innerHTML = `
    <style>
        div {
        font-family: monospace;
        text-shadow: 0 0.2rem 1rem #58412a;
      }

      .carat {
        animation: crt-carat 1000ms infinite;
        display: inline-block;
        height: 3px;
        width: 10px;
        margin: 0 4px -2px;
        background-color: #fb8337;
      }

      @keyframes crt-carat {
        50% {
          opacity: 0;
        }
        100% {
          opacity: 1;
        }
      }
    </style>
    <div id="domElement" aria-hidden="true"/>
`;

let dotsCallback = (endingText) => {
    return (config, terminal) => {
        let lineID = terminal.addLine(config.msg);
        let dots = ".";

        let incrementor = window.setInterval(() => {
            terminal._shadowRoot.getElementById(lineID).innerHTML = `${config.msg} ${dots}`;
            if (dots.length >= 3) {
                clearInterval(incrementor);
                const el = terminal._shadowRoot.getElementById(lineID);
                el.innerHTML = `${config.msg} ${dots} ${endingText}`;
                el.querySelector('strong').innerText = '[OK]';
                terminal.next();
            }
            dots += ".";
        }, 200);
    }
};

const sequence = [
    {
        "msg": "Ore Bios (C) 2086 Mining Corp, Ltd.",
        "delay": 500,
    },
    {
        "msg": "BIOS Date 01/01/2086 16:13:29 Ver: 114.00.09",
        "delay": 0,
    },
    {
        "msg": "CPU: PPC(R) CPU RedCore @ 40Mhz",
        "delay": 500,
    },
    {
        "msg": "<strong class=\"selected\">Memory Test:</strong>",
        "delay": 250,
        "callback": (config, terminal) => {
            let lineID = terminal.addLine(config.msg);
            let counter = 0;
            let incrementor = window.setInterval(() => {
                terminal._shadowRoot.getElementById(lineID).innerHTML = `${config.msg} ${counter} K`;

                counter++;
                if (counter === 640) {
                    clearInterval(incrementor)
                    terminal._shadowRoot.getElementById(lineID).innerHTML = `${config.msg} ${counter} K OK`;
                    terminal.next();
                }
            }, 10);
        }
    },
    {},
    {
        "delay": 300,
        "msg": "Booting from Hard Disk..."
    },
    {
        "delay": 300,
        "msg": "Starting ColonyOS v1.03",
    },
    {
        "delay": (200 * 3),
        "msg": "<strong>[...]</strong> Waiting for /dev to be fully populated",
        "callback": dotsCallback("done")
    },
    {
        "delay": (200 * 3),
        "msg": "<strong>[...]</strong> Detecting Network",
        "callback": dotsCallback("found")
    },
    {
        "delay": 300,
        "msg": "<strong class=\"selected\">[OK]</strong> Identifying Peripheral devices...done."
    },
    {
        "delay": 30,
        "msg": "<strong class=\"selected\">[OK]</strong> Harmonising Frequencies."
    },
    {
        "delay": 30,
        "msg": "<strong class=\"selected\">[OK]</strong> Identifying Lama Farmers"
    },
    {
        delay: (200 * 3),
        msg: "<strong>[...]</strong> Finding GOATs:",
        "callback": dotsCallback("found")
    },
    {
        "delay": 25,
        "msg": "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- found @cassidoo's discord, #FREETHEPLANT",
    },
    {
        "delay": 25,
        "msg": "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- found @weigert__, soil loaded",
    },
    {
        "delay": 25,
        "msg": "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- found Usborne Books",
    },
    {
        "delay": 30,
        "msg": "<strong class=\"selected\">[OK]</strong> Injecting Magic smoke...done."
    },
    {},
    {
        "delay": 300,
        "msg": "Mainframe Uplink Configured"
    },
    {},
    {
        "msg": "Starting OS/M"
    },
    {
        "msg": "System Specifications:",
        "delay": 250,
    },
    {
        "msg": "RAM: 640K",
        "delay": 250,
    },
    {
        "msg": "Hard Disk: 20 MB (BIOS Type 13)",
        "delay": 250,
    },
    {
        "msg": "Video Card: Enhanced Graphics Adapter",
        "delay": 250,
    },
    {
        "msg": "Floppy Drive A: 3.5\" 720k double-sided, double density",
        "delay": 250,
    },
    {
        "msg": "Floppy Drive B: Not installed",
        "delay": 250,
    },
    {},
    {
        "msg": "Installed Applications:",
        "delay": 250,
    },
    {
        "msg": "- RBASIC&nbsp;: Resource Harvesting SDK 1.1",
        "delay": 250,
    },
    {
        "msg": "- MCOMMS&nbsp;: Market Communications Systems",
        "delay": 250,
    },
    {
        "msg": "- REDIS&nbsp;&nbsp;: Proprietary Storage Automation",
        "delay": 250,
    },
    {},
    {
        "msg": "C:\\><span class=\"carat\"></span>"
    },
];

const defaultSequence = {
    "msg": "&nbsp;",
    "delay": 1000,
    "callback": (config, terminal) => {
        terminal.addLine(config.msg);
        terminal.next();
    },
};

export class Terminal extends HTMLElement {

    constructor() {
        super();

        this._shadowRoot = this.attachShadow({'mode': 'open'});
        this._shadowRoot.appendChild(template.content.cloneNode(true));

        this.totalLines = 0;
        this.maxLines = 25;
        this.lineCount = 0;

        this.timeOut = undefined;
        this.idx = 0;
    }

    connectedCallback() {
        this.run();
    }

    disconnectedCallback() {
        if (this.timeOut) clearTimeout(this.timeOut);
    }

    run() {
        const current = sequence[this.idx];
        if (!current) return;

        let config = {
            "msg": current.msg || defaultSequence.msg,
            "delay": current.delay || defaultSequence.delay,
            "callback": current.callback || defaultSequence.callback,
        };

        try {
            this.timeOut = window.setTimeout(config.callback, config.delay, config, this);
        } catch (e) {
            //
        }
    }

    next() {
        this.idx++;
        this.run();
    }

    addLine(line) {
        this.totalLines++;

        if (this.lineCount === this.maxLines) {
            this._shadowRoot.getElementById(`line-${this.totalLines - this.maxLines}`).remove();
        } else {
            this.lineCount++;
        }

        const node = document.createElement("p");
        node.id = `line-${this.totalLines}`;
        node.innerHTML = line;
        this._shadowRoot.appendChild(node);

        return 'line-' + this.lineCount;
    }
}
