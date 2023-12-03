import http_patch_json from '../http/http_patch_json';

async function api_parsers_update({parser})
{
    const {uid, label, engine, match,  config} = parser;
    return http_patch_json(`/api/v1/parsers/${uid}`, {parser: {label, engine, match, config}});
}

export default api_parsers_update;
