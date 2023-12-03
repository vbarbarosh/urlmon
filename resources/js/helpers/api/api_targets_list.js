import http_get_json from '../http/http_get_json';

async function api_targets_list()
{
    return http_get_json('/api/v1/targets.json');
}

export default api_targets_list;
