import http_post_json from '../http/http_post_json';

async function api_targets_parse({target})
{
    return http_post_json(`/api/v1/targets/${target.uid}/parse`);
}

export default api_targets_parse;
