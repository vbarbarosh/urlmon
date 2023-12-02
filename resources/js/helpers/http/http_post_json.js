import Promise from 'bluebird';
import axios from 'axios';

function http_post_json(url, body, options)
{
    return Promise.resolve(axios.post(url, body, options)).then(v => v.data);
}

export default http_post_json;
