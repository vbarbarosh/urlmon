import Promise from 'bluebird';
import axios from 'axios';

function http_put_json(url, body, options)
{
    return Promise.resolve(axios.put(url, body, options)).then(v => v.data);
}

export default http_put_json;
