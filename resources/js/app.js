import './bootstrap';

import Alpine from 'alpinejs';
import {NewsTicker} from './news_ticker.js';
import {UpdateTicker} from "./update_ticker.js";
import {Terminal} from "./terminal.js";

window.customElements.define('news-ticker', NewsTicker);
window.customElements.define('update-ticker', UpdateTicker);
window.customElements.define('virtual-terminal', Terminal);
window.Alpine = Alpine;

Alpine.start();
