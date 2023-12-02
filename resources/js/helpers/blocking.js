import Promise from 'bluebird';
import cuid from 'cuid';
import modal_loading from './modal/modal_loading';

let blocking_counter = 0;
let blocking_modal = null;

function blocking(fn_or_promise)
{
    let status_uid;

    blocking_counter++;
    if (!blocking_modal) {
        blocking_modal = modal_loading([]);
    }

    if (typeof fn_or_promise == 'function') {
        return Promise.method(fn_or_promise).call(null, status).finally(status_end).finally(end);
    }
    return Promise.resolve(fn_or_promise).finally(end);

    function status(message) {
        if (status_uid) {
            status_update(message);
        }
        else {
            status_begin(message);
        }
    }
    function status_begin(message) {
        status_uid = cuid();
        blocking_modal.value.push({uid: status_uid, message});
    }
    function status_end() {
        const i = blocking_modal.value.findIndex(v => v.uid === status_uid);
        if (i !== -1) {
            blocking_modal.value.splice(i, 1);
        }
    }
    function status_update(message) {
        const item = blocking_modal.value.find(v => v.uid === status_uid);
        item.message = message;
    }
}

function end()
{
    blocking_counter--;
    if (blocking_counter === 0) {
        // To be able to call `blocking` one after the other.
        // await blocking(api_schedule_placeholders_multiple_insupdel(placeholders));
        // await blocking(this.load_schedules());
        //
        // Wait for 250ms to eliminate flickering when one preloader
        // is closed and another, almost immediately, opened.
        setTimeout(function () {
            if (blocking_modal && blocking_counter === 0) {
                blocking_modal.return();
                blocking_modal = null;
            }
        }, 250);
    }
}

export default blocking;
