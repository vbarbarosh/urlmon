import http_get_json from '../http/http_get_json';

async function api_urls_fetch({url_uid})
{
    return http_get_json(`/api/v1/urls/${url_uid}`);
}

export default api_urls_fetch;
