import {Controller} from '@hotwired/stimulus';

export default class extends Controller
{
    static values = {
        message: String
    }

    onClick(event) {
        event.stopPropagation();
        if (window.confirm(this.messageValue) === false) {
            event.preventDefault();
        }
    }
}
