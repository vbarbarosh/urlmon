import http_patch_json from '../http/http_patch_json';

async function api_targets_patch({target})
{
    const {parser_uid, url, label} = target;
    return http_patch_json(`/api/v1/targets/${target.uid}`, {target: {parser_uid, url, label}});
}

export default api_targets_patch;
