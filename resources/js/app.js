import './bootstrap';
import {NewsTicker} from './news_ticker.js';
import {UpdateTicker} from "./update_ticker.js";

window.customElements.define('news-ticker', NewsTicker);
window.customElements.define('update-ticker', UpdateTicker);
