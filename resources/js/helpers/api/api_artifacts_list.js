import http_get_json from '../http/http_get_json';
import urlmod from '../urlmod';

async function api_artifacts_list(filters)
{
    return http_get_json(urlmod('/api/v1/artifacts.json', filters));
}

export default api_artifacts_list;
