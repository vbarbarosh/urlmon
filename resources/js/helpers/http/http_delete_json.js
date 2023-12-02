import Promise from 'bluebird';
import axios from 'axios';

function http_delete_json(url, json, options)
{
    return Promise.resolve(axios.delete(url, {data: json, responseType: 'json', ...options}).then(v => v.data));
}

export default http_delete_json;
