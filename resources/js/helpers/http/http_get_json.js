import Promise from 'bluebird';
import axios from 'axios';

function http_get_json(url, options)
{
    return Promise.resolve(axios.get(url, {responseType: 'json', ...options}).then(v => v.data));
}

export default http_get_json;
