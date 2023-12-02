import http_get_json from '../http/http_get_json';

async function api_parsers_list()
{
    return http_get_json('/api/v1/parsers.json');
}

export default api_parsers_list;
