import http_get_json from '../http/http_get_json';

async function api_targets_fetch({target_uid})
{
    return http_get_json(`/api/v1/targets/${target_uid}`);
}

export default api_targets_fetch;
