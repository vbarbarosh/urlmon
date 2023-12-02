import Promise from 'bluebird';
import blocking from './blocking';

/**
 * Show `blocking` modal while executing `fn`.
 */
function m_blocking(fn)
{
    return function (...args) {
        return blocking(Promise.method(fn).apply(this, args));
    };
}

export default m_blocking;
