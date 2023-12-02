import http_get_json from '../http/http_get_json';

async function api_urls_list()
{
    return http_get_json('/api/v1/urls.json');
}

export default api_urls_list;
