import http_delete from '../http/http_delete';

async function api_urls_delete({url})
{
    return http_delete(`/api/v1/urls/${url.uid}`);
}

export default api_urls_delete;
