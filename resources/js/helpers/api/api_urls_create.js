import http_post_json from '../http/http_post_json';

async function api_urls_create({url})
{
    const parser_uid = url.parser_uid || url.parser?.uid;
    return http_post_json('/api/v1/urls', {url: {parser_uid, url: url.url}});
}

export default api_urls_create;
