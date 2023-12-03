import http_post_json from '../http/http_post_json';

async function api_targets_create({target})
{
    const {parser_uid, url, label} = target;
    return http_post_json('/api/v1/targets', {target: {parser_uid, url, label}});
}

export default api_targets_create;
