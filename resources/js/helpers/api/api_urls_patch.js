import http_patch_json from '../http/http_patch_json';

async function api_urls_patch({url})
{
    const parser_uid = url.parser_uid || url.parser?.uid;
    return http_patch_json(`/api/v1/urls/${url.uid}`, {url: {parser_uid, url: url.url}});
}

export default api_urls_patch;
