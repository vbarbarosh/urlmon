import Promise from 'bluebird';
import modal_error from './modal/modal_error';

/**
 * Hide modal while executing `fn`
 */
function m_modal_hide(fn)
{
    return function (...args) {
        this.modal.hide();
        return Promise.method(fn).apply(this, args).catch(panic).finally(this.modal.show_if_pending);
    };

    async function panic(error) {
        console.log(error);
        await modal_error(error).promise();
    }
}

export default m_modal_hide;
