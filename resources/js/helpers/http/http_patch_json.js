import Promise from 'bluebird';
import axios from 'axios';

function http_patch_json(url, body, options)
{
    return Promise.resolve(axios.patch(url, body, options)).then(v => v.data);
}

export default http_patch_json;
