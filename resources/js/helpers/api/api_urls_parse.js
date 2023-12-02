import http_post_json from '../http/http_post_json';

async function api_urls_parse({url})
{
    return http_post_json(`/api/v1/urls/${url.uid}/parse`);
}

export default api_urls_parse;
